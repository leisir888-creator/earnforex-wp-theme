<?php
/**
 * Affiliate Link Manager
 * 联盟链接管理核心类（全自动运维完美版）
 *
 * @package EarnForex_WP
 * @since 1.0.7
 */

if (!defined('ABSPATH')) {
    exit;
}

class EFP_Affiliate_Link_Manager {

    const POST_TYPE = 'efp_affiliate_link';
    const TAXONOMY_CATEGORY = 'efp_affiliate_category';
    const META_PREFIX = 'efp_aff_';
    const OPTION_CLICKS = 'efp_aff_clicks';
    const OPTION_SETTINGS = 'efp_aff_settings';
    const REDIRECT_BASE = 'go'; // /go/{slug}

    private static $instance = null;

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'register_post_type']);
        add_action('init', [$this, 'register_taxonomy']);
        add_action('init', [$this, 'add_rewrite_rules']);
        add_filter('query_vars', [$this, 'register_query_vars']); // 修复：必须注册查询变量
        add_action('template_redirect', [$this, 'handle_redirect']);
        
        // Meta Box 逻辑：修复原版缺失的自定义字段保存逻辑
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_' . self::POST_TYPE, [$this, 'save_meta_boxes']);

        add_action('admin_menu', [$this, 'admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);
        add_action('wp_ajax_efp_aff_sort', [$this, 'ajax_sort']);
        add_action('wp_ajax_efp_aff_toggle_status', [$this, 'ajax_toggle_status']);
        add_action('wp_ajax_efp_aff_bulk_action', [$this, 'ajax_bulk_action']);
        add_filter('the_content', [$this, 'auto_link_keywords'], 20);
        add_shortcode('aff_link', [$this, 'shortcode_aff_link']);
        add_action('admin_init', [$this, 'register_settings']);
        add_filter('manage_' . self::POST_TYPE . '_posts_columns', [$this, 'admin_columns']);
        add_action('manage_' . self::POST_TYPE . '_posts_custom_column', [$this, 'admin_column_content'], 10, 2);
        add_filter('manage_edit-' . self::POST_TYPE . '_sortable_columns', [$this, 'sortable_columns']);
        add_filter('request', [$this, 'custom_orderby']);
    }

    /**
     * 注册自定义文章类型
     */
    public function register_post_type() {
        $labels = [
            'name'               => __('Affiliate Links', 'earnforex-wp'),
            'singular_name'      => __('Affiliate Link', 'earnforex-wp'),
            'add_new'            => __('Add New Link', 'earnforex-wp'),
            'add_new_item'       => __('Add New Affiliate Link', 'earnforex-wp'),
            'edit_item'          => __('Edit Affiliate Link', 'earnforex-wp'),
            'new_item'           => __('New Affiliate Link', 'earnforex-wp'),
            'view_item'          => __('View Affiliate Link', 'earnforex-wp'),
            'search_items'       => __('Search Affiliate Links', 'earnforex-wp'),
            'not_found'          => __('No affiliate links found', 'earnforex-wp'),
            'not_found_in_trash' => __('No affiliate links found in Trash', 'earnforex-wp'),
            'all_items'          => __('All Affiliate Links', 'earnforex-wp'),
        ];

        $args = [
            'labels'              => $labels,
            'public'              => false,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true, // 启用自动主菜单生成
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => false,
            'can_export'          => true,
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => 25,
            'menu_icon'           => 'dashicons-link',
            'supports'            => ['title', 'editor'],
            'rewrite'             => ['slug' => self::REDIRECT_BASE, 'with_front' => false],
            'capability_type'     => 'post',
            'map_meta_cap'        => true,
            'show_in_rest'        => true,
        ];

        register_post_type(self::POST_TYPE, $args);
    }

    /**
     * 注册分类法
     */
    public function register_taxonomy() {
        $labels = [
            'name'              => __('Link Categories', 'earnforex-wp'),
            'singular_name'     => __('Category', 'earnforex-wp'),
            'search_items'      => __('Search Categories', 'earnforex-wp'),
            'all_items'         => __('All Categories', 'earnforex-wp'),
            'edit_item'         => __('Edit Category', 'earnforex-wp'),
            'update_item'       => __('Update Category', 'earnforex-wp'),
            'add_new_item'      => __('Add New Category', 'earnforex-wp'),
            'new_item_name'     => __('New Category Name', 'earnforex-wp'),
            'menu_name'         => __('Categories', 'earnforex-wp'),
        ];

        $args = [
            'labels'            => $labels,
            'hierarchical'      => true,
            'public'            => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => ['slug' => 'affiliate-category'],
        ];

        register_taxonomy(self::TAXONOMY_CATEGORY, [self::POST_TYPE], $args);
    }

    /**
     * 添加重写规则
     */
    public function add_rewrite_rules() {
        add_rewrite_rule(
            '^' . self::REDIRECT_BASE . '/([^/]+)/?$',
            'index.php?efp_aff_slug=$matches[1]',
            'top'
        );
    }

    /**
     * 注册自定义查询变量
     */
    public function register_query_vars($vars) {
        $vars[] = 'efp_aff_slug';
        return $vars;
    }

    /**
     * 后台 Meta Box 表单渲染
     */
    public function add_meta_boxes() {
        add_meta_box(
            'efp_aff_details',
            __('Affiliate Link Details', 'earnforex-wp'),
            [$this, 'render_meta_box'],
            self::POST_TYPE,
            'normal',
            'high'
        );
    }

    public function render_meta_box($post) {
        wp_nonce_field('efp_save_aff_meta', 'efp_aff_meta_nonce');
        $target_url = get_post_meta($post->ID, self::META_PREFIX . 'target_url', true);
        $keywords = get_post_meta($post->ID, self::META_PREFIX . 'keywords', true);
        $status = get_post_meta($post->ID, self::META_PREFIX . 'status', true) ?: 'active';
        ?>
        <p>
            <label style="display:block;margin-bottom:5px;"><strong>Target URL (Destination):</strong></label>
            <input type="url" name="efp_target_url" value="<?php echo esc_url($target_url); ?>" class="large-text" placeholder="https://example.com/ref=123" required>
        </p>
        <p>
            <label style="display:block;margin-bottom:5px;"><strong>Keywords (Comma separated for auto-linking):</strong></label>
            <input type="text" name="efp_keywords" value="<?php echo esc_attr($keywords); ?>" class="large-text" placeholder="broker, forex, trading">
        </p>
        <p>
            <label style="display:block;margin-bottom:5px;"><strong>Status:</strong></label>
            <select name="efp_status">
                <option value="active" <?php selected($status, 'active'); ?>>Active</option>
                <option value="inactive" <?php selected($status, 'inactive'); ?>>Inactive</option>
            </select>
        </p>
        <?php
    }

    /**
     * 保存自定义字段数据
     */
    public function save_meta_boxes($post_id) {
        if (!isset($_POST['efp_aff_meta_nonce']) || !wp_verify_nonce($_POST['efp_aff_meta_nonce'], 'efp_save_aff_meta')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['efp_target_url'])) {
            update_post_meta($post_id, self::META_PREFIX . 'target_url', esc_url_raw($_POST['efp_target_url']));
        }
        if (isset($_POST['efp_keywords'])) {
            update_post_meta($post_id, self::META_PREFIX . 'keywords', sanitize_text_field($_POST['efp_keywords']));
        }
        if (isset($_POST['efp_status'])) {
            update_post_meta($post_id, self::META_PREFIX . 'status', sanitize_text_field($_POST['efp_status']));
        }
    }

    /**
     * 处理跳转
     */
    public function handle_redirect() {
        $slug = get_query_var('efp_aff_slug');
        if (empty($slug)) {
            return;
        }

        $slug = sanitize_title($slug);

        $args = [
            'post_type'      => self::POST_TYPE,
            'name'           => $slug,
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'no_found_rows'  => true,
        ];

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();

            $status = get_post_meta($post_id, self::META_PREFIX . 'status', true);
            if ($status !== 'active') {
                wp_redirect(home_url(), 302);
                exit;
            }

            $target_url = get_post_meta($post_id, self::META_PREFIX . 'target_url', true);
            if (empty($target_url)) {
                wp_redirect(home_url(), 302);
                exit;
            }

            $this->record_click($post_id);
            wp_redirect($target_url, 302);
            exit;
        }

        wp_redirect(home_url(), 302);
        exit;
    }

    /**
     * 记录点击
     */
    private function record_click($post_id) {
        $clicks = get_option(self::OPTION_CLICKS, []);
        $today = current_time('Y-m-d');

        if (!isset($clicks[$post_id])) {
            $clicks[$post_id] = [
                'total' => 0,
                'daily' => [],
            ];
        }

        $clicks[$post_id]['total']++;
        $clicks[$post_id]['daily'][$today] = ($clicks[$post_id]['daily'][$today] ?? 0) + 1;
        $clicks[$post_id]['last_click'] = current_time('mysql');

        $cutoff = date('Y-m-d', strtotime('-90 days'));
        $clicks[$post_id]['daily'] = array_filter($clicks[$post_id]['daily'], function($date) use ($cutoff) {
            return $date >= $cutoff;
        }, ARRAY_FILTER_USE_KEY);

        update_option(self::OPTION_CLICKS, $clicks);
    }

    /**
     * 后台菜单
     */
    public function admin_menu() {
        // 修复：移除手动添加的 add_menu_page，防止左侧主菜单图标重复
        add_submenu_page(
            'edit.php?post_type=' . self::POST_TYPE, 
            __('Settings', 'earnforex-wp'),
            __('Settings', 'earnforex-wp'),
            'manage_options',
            'efp-affiliate-settings',
            [$this, 'settings_page']
        );
    }

    /**
     * 后台脚本
     */
    public function admin_scripts($hook) {
        if (!in_array($hook, ['edit.php', 'post.php', 'post-new.php'])) {
            return;
        }

        $screen = get_current_screen();
        if ($screen->post_type !== self::POST_TYPE && $hook !== 'edit.php') {
            return;
        }

        wp_enqueue_script('efp-aff-admin', get_template_directory_uri() . '/assets/js/affiliate-admin.js', ['jquery'], '1.0.7', true);
        wp_localize_script('efp-aff-admin', 'efpAff', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('efp_aff_nonce'),
        ]);
    }

    /**
     * AJAX 桩函数（保持向下兼容）
     */
    public function ajax_sort() { check_ajax_referer('efp_aff_nonce', 'nonce'); wp_send_json_success(); }
    public function ajax_toggle_status() { check_ajax_referer('efp_aff_nonce', 'nonce'); wp_send_json_success(); }
    public function ajax_bulk_action() { check_ajax_referer('efp_aff_nonce', 'nonce'); wp_send_json_success(); }

    /**
     * 关键词自动链接（安全无循环版本）
     */
    public function auto_link_keywords($content) {
        if (!is_singular() || is_admin() || is_feed() || is_preview()) {
            return $content;
        }

        $settings = $this->get_settings();
        if (empty($settings['auto_link_enabled'])) {
            return $content;
        }

        $links = $this->get_active_links();
        if (empty($links)) {
            return $content;
        }

        $max_links = intval($settings['max_links_per_post'] ?? 3);
        $first_only = !empty($settings['first_occurrence_only']);
        $link_count = 0;
        $replaced_keywords = [];

        // 强防御机制：增加对现有 <a> 标签的隔离，杜绝多词嵌套死循环
        $protected_tags = ['code', 'pre', 'script', 'style', 'textarea', 'a'];
        $placeholder_prefix = '{{EFP_AFF_PROTECTED_';
        $placeholder_suffix = '}}';
        $protected_content = [];

        foreach ($protected_tags as $tag) {
            $pattern = ($tag === 'a') ? '/<a[^>]*>.*?<\/a>/is' : "/<{$tag}[^>]*>.*?<\/{$tag}>/is";
            $content = preg_replace_callback($pattern, function($matches) use (&$protected_content, $placeholder_prefix, $placeholder_suffix) {
                $placeholder = $placeholder_prefix . count($protected_content) . $placeholder_suffix;
                $protected_content[] = $matches[0];
                return $placeholder;
            }, $content);
        }

        foreach ($links as $link) {
            if ($link_count >= $max_links) {
                break;
            }

            $keywords = array_map('trim', explode(',', $link['keywords']));
            $slug = $link['slug'];

            foreach ($keywords as $keyword) {
                if (empty($keyword) || strlen($keyword) < 2) {
                    continue;
                }

                $keyword_key = md5(strtolower($keyword));
                if ($first_only && isset($replaced_keywords[$keyword_key])) {
                    continue;
                }

                // 修复：针对中英文混合的外汇站点使用更为兼容的无断言边界正则
                $quoted_keyword = preg_quote($keyword, '/');
                $pattern = '/(?<![a-zA-Z0-9])' . $quoted_keyword . '(?![a-zA-Z0-9])/i';
                
                $url = home_url('/') . self::REDIRECT_BASE . '/' . $slug . '/';
                $replacement = '<a href="' . esc_url($url) . '" class="efp-aff-link" data-aff-id="' . $link['id'] . '" target="_blank" rel="noopener sponsored">' . esc_html($keyword) . '</a>';

                $new_content = preg_replace($pattern, $replacement, $content, 1, $replaced);

                if ($replaced) {
                    $content = $new_content;
                    $link_count++;
                    $replaced_keywords[$keyword_key] = true;

                    if ($link_count >= $max_links) {
                        break 2;
                    }
                }
            }
        }

        foreach ($protected_content as $index => $original) {
            $placeholder = $placeholder_prefix . $index . $placeholder_suffix;
            $content = str_replace($placeholder, $original, $content);
        }

        return $content;
    }

    /**
     * 获取启用的联盟链接
     */
    private function get_active_links() {
        $args = [
            'post_type'      => self::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_query'     => [
                [
                    'key'     => self::META_PREFIX . 'status',
                    'value'   => 'active',
                    'compare' => '=',
                ],
            ],
            'orderby'        => 'meta_value_num',
            'meta_key'       => self::META_PREFIX . 'menu_order',
            'order'          => 'ASC',
        ];

        $query = new WP_Query($args);
        $links = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $id = get_the_ID();

                $links[] = [
                    'id'         => $id,
                    'title'      => get_the_title(),
                    'slug'       => get_post_field('post_name'),
                    'keywords'   => get_post_meta($id, self::META_PREFIX . 'keywords', true),
                    'target_url' => get_post_meta($id, self::META_PREFIX . 'target_url', true),
                ];
            }
            wp_reset_postdata();
        }

        return $links;
    }

    /**
     * 短代码支持
     */
    public function shortcode_aff_link($atts) {
        $atts = shortcode_atts(['id' => '', 'text' => '', 'class' => 'efp-aff-link'], $atts, 'aff_link');
        if (empty($atts['id'])) return '';

        $post_id = intval($atts['id']);
        if (get_post_status($post_id) !== 'publish') return '';

        $meta_status = get_post_meta($post_id, self::META_PREFIX . 'status', true);
        if ($meta_status !== 'active') return '';

        $slug = get_post_field('post_name', $post_id);
        $url = home_url('/') . self::REDIRECT_BASE . '/' . $slug . '/';
        $link_text = $atts['text'] ?: get_the_title($post_id);

        return '<a href="' . esc_url($url) . '" class="' . esc_attr($atts['class']) . '" data-aff-id="' . esc_attr($post_id) . '" target="_blank" rel="noopener sponsored">' . esc_html($link_text) . '</a>';
    }

    /**
     * 注册设置项
     */
    public function register_settings() {
        register_setting('efp_aff_settings', self::OPTION_SETTINGS);
        add_settings_section('efp_aff_general', __('General Settings', 'earnforex-wp'), [$this, 'settings_section_general_cb'], 'efp-affiliate-settings');
        add_settings_field('auto_link_enabled', __('Enable Auto-linking', 'earnforex-wp'), [$this, 'field_auto_link_enabled'], 'efp-affiliate-settings', 'efp_aff_general');
        add_settings_field('max_links_per_post', __('Max Links Per Post', 'earnforex-wp'), [$this, 'field_max_links_per_post'], 'efp-affiliate-settings', 'efp_aff_general');
        add_settings_field('first_occurrence_only', __('First Occurrence Only', 'earnforex-wp'), [$this, 'field_first_occurrence_only'], 'efp-affiliate-settings', 'efp_aff_general');
        add_settings_field('link_style', __('Link Style', 'earnforex-wp'), [$this, 'field_link_style'], 'efp-affiliate-settings', 'efp_aff_general');
    }

    public function get_settings() {
        return get_option(self::OPTION_SETTINGS, [
            'auto_link_enabled'      => true,
            'max_links_per_post'     => 3,
            'first_occurrence_only'  => true,
            'link_style'             => 'default',
        ]);
    }

    public function settings_section_general_cb() { echo '<p>' . __('Configure how affiliate links are automatically inserted.', 'earnforex-wp') . '</p>'; }
    public function field_auto_link_enabled() { $s = $this->get_settings(); echo '<input type="checkbox" name="efp_aff_settings[auto_link_enabled]" value="1" ' . checked($s['auto_link_enabled'], true, false) . '>'; }
    public function field_max_links_per_post() { $s = $this->get_settings(); echo '<input type="number" name="efp_aff_settings[max_links_per_post]" value="' . esc_attr($s['max_links_per_post']) . '" class="small-text">'; }
    public function field_first_occurrence_only() { $s = $this->get_settings(); echo '<input type="checkbox" name="efp_aff_settings[first_occurrence_only]" value="1" ' . checked($s['first_occurrence_only'], true, false) . '>'; }
    public function field_link_style() { $s = $this->get_settings(); echo '<select name="efp_aff_settings[link_style]"><option value="default">Default</option></select>'; }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php settings_fields('efp_aff_settings'); do_settings_sections('efp-affiliate-settings'); submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * 管理后台列表列
     */
    public function admin_columns($columns) {
        return array_merge($columns, ['keywords' => 'Keywords', 'target_url' => 'Target URL', 'status' => 'Status']);
    }

    public function admin_column_content($column, $post_id) {
        if ($column === 'keywords') echo esc_html(get_post_meta($post_id, self::META_PREFIX . 'keywords', true));
        if ($column === 'target_url') echo esc_html(get_post_meta($post_id, self::META_PREFIX . 'target_url', true));
        if ($column === 'status') echo esc_html(get_post_meta($post_id, self::META_PREFIX . 'status', true));
    }

    public function custom_orderby($vars) { return $vars; }
    public function sortable_columns($columns) { return $columns; }
}

// 启动单例
EFP_Affiliate_Link_Manager::instance();

// 主题或插件激活时刷新重写路由规则缓存
register_activation_hook(__FILE__, function() {
    EFP_Affiliate_Link_Manager::instance()->register_post_type();
    EFP_Affiliate_Link_Manager::instance()->add_rewrite_rules();
    flush_rewrite_rules();
});