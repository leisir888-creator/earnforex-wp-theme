<?php
/**
 * Affiliate Link Manager
 * 联盟链接管理核心类
 *
 * @package EarnForex_WP
 * @since 1.0.5
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
        add_action('template_redirect', [$this, 'handle_redirect']);
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
            'archives'           => __('Affiliate Link Archives', 'earnforex-wp'),
            'insert_into_item'   => __('Insert into link', 'earnforex-wp'),
        ];

        $args = [
            'labels'              => $labels,
            'public'              => false,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => false,
            'can_export'          => true,
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => 25,
            'menu_icon'           => 'dashicons-link',
            'supports'            => ['title', 'editor', 'custom-fields'],
            'rewrite'             => ['slug' => self::REDIRECT_BASE, 'with_front' => false],
            'capability_type'     => 'post',
            'capabilities'        => [
                'create_posts'        => 'manage_options',
                'edit_posts'          => 'manage_options',
                'edit_others_posts'   => 'manage_options',
                'publish_posts'       => 'manage_options',
                'read_post'           => 'manage_options',
                'read_private_posts'  => 'manage_options',
                'delete_posts'        => 'manage_options',
            ],
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
     * 处理跳转
     */
    public function handle_redirect() {
        if (!isset($_GET['efp_aff_slug'])) {
            return;
        }

        $slug = sanitize_title($_GET['efp_aff_slug']);

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

            // 检查链接是否启用
            $status = get_post_meta($post_id, self::META_PREFIX . 'status', true);
            if ($status !== 'active') {
                wp_redirect(home_url(), 302);
                exit;
            }

            // 获取目标 URL
            $target_url = get_post_meta($post_id, self::META_PREFIX . 'target_url', true);
            if (empty($target_url)) {
                wp_redirect(home_url(), 302);
                exit;
            }

            // 记录点击
            $this->record_click($post_id);

            // 302 重定向
            wp_redirect($target_url, 302);
            exit;
        }

        // 未找到，重定向到首页
        wp_redirect(home_url(), 302);
        exit;
    }

    /**
     * 记录点击
     */
    private function record_click($post_id) {
        $clicks = get_option(self::OPTION_CLICKS, []);
        $today = current_time('Y-m-d');
        $key = $post_id . '_' . $today;

        if (!isset($clicks[$post_id])) {
            $clicks[$post_id] = [
                'total' => 0,
                'daily' => [],
            ];
        }

        $clicks[$post_id]['total']++;
        $clicks[$post_id]['daily'][$today] = ($clicks[$post_id]['daily'][$today] ?? 0) + 1;
        $clicks[$post_id]['last_click'] = current_time('mysql');

        // 保留最近 90 天
        $cutoff = date('Y-m-d', strtotime('-90 days'));
        $clicks[$post_id]['daily'] = array_filter($clicks[$post_id]['daily'], function($date) use ($cutoff) {
            return $date >= $cutoff;
        });

        update_option(self::OPTION_CLICKS, $clicks);
    }

    /**
     * 后台菜单
     */
    public function admin_menu() {
        add_menu_page(
            __('Affiliate Links', 'earnforex-wp'),
            __('Affiliate Links', 'earnforex-wp'),
            'manage_options',
            'edit.php?post_type=' . self::POST_TYPE,
            '',
            'dashicons-link',
            25
        );

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

        wp_enqueue_script('efp-aff-admin', get_template_directory_uri() . '/assets/js/affiliate-admin.js', ['jquery', 'wp-util'], '1.0.5', true);
        wp_localize_script('efp-aff-admin', 'efpAff', [
            'ajaxurl'      => admin_url('admin-ajax.php'),
            'nonce'        => wp_create_nonce('efp_aff_nonce'),
            'postType'     => self::POST_TYPE,
            'strings'      => [
                'confirmDelete' => __('Are you sure you want to delete this link?', 'earnforex-wp'),
                'confirmBulk'   => __('Are you sure you want to perform this action on selected items?', 'earnforex-wp'),
            ],
        ]);

        wp_enqueue_style('efp-aff-admin', get_template_directory_uri() . '/assets/css/affiliate-admin.css', [], '1.0.5');
    }

    /**
     * AJAX: 拖拽排序
     */
    public function ajax_sort() {
        check_ajax_referer('efp_aff_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'earnforex-wp')]);
        }

        $order = isset($_POST['order']) ? array_map('intval', $_POST['order']) : [];

        foreach ($order as $index => $post_id) {
            update_post_meta($post_id, self::META_PREFIX . 'menu_order', $index);
        }

        wp_send_json_success(['message' => __('Order updated', 'earnforex-wp')]);
    }

    /**
     * AJAX: 切换状态
     */
    public function ajax_toggle_status() {
        check_ajax_referer('efp_aff_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'earnforex-wp')]);
        }

        $post_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : 'active';

        if ($post_id && in_array($status, ['active', 'inactive'])) {
            update_post_meta($post_id, self::META_PREFIX . 'status', $status);
            wp_send_json_success(['status' => $status]);
        }

        wp_send_json_error(['message' => __('Invalid request', 'earnforex-wp')]);
    }

    /**
     * AJAX: 批量操作
     */
    public function ajax_bulk_action() {
        check_ajax_referer('efp_aff_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'earnforex-wp')]);
        }

        $action = sanitize_text_field($_POST['action'] ?? '');
        $ids = array_map('intval', $_POST['ids'] ?? []);

        if (empty($ids)) {
            wp_send_json_error(['message' => __('No items selected', 'earnforex-wp')]);
        }

        $count = 0;
        foreach ($ids as $id) {
            switch ($action) {
                case 'activate':
                    update_post_meta($id, self::META_PREFIX . 'status', 'active');
                    $count++;
                    break;
                case 'deactivate':
                    update_post_meta($id, self::META_PREFIX . 'status', 'inactive');
                    $count++;
                    break;
                case 'delete':
                    wp_delete_post($id, true);
                    $count++;
                    break;
            }
        }

        wp_send_json_success(['message' => sprintf(_n('%d item processed.', '%d items processed.', $count, 'earnforex-wp'), $count)]);
    }

    /**
     * 关键词自动链接
     */
    public function auto_link_keywords($content) {
        if (!is_singular() || is_admin() || is_feed() || is_preview()) {
            return $content;
        }

        $settings = $this->get_settings();

        if (empty($settings['auto_link_enabled'])) {
            return $content;
        }

        // 获取所有启用的联盟链接
        $links = $this->get_active_links();
        if (empty($links)) {
            return $content;
        }

        $max_links = intval($settings['max_links_per_post'] ?? 3);
        $first_only = !empty($settings['first_occurrence_only']);
        $link_count = 0;
        $replaced_keywords = [];

        // 保护不应替换的标签
        $protected_tags = ['code', 'pre', 'script', 'style', 'textarea'];
        $placeholder_prefix = '{{EFP_AFF_PROTECTED_';
        $placeholder_suffix = '}}';
        $protected_content = [];

        // 先保护代码块等
        foreach ($protected_tags as $tag) {
            $pattern = "/<{$tag}[^>]*>.*?<\/{$tag}>/is";
            $content = preg_replace_callback($pattern, function($matches) use (&$protected_content, $placeholder_prefix, $placeholder_suffix) {
                $placeholder = $placeholder_prefix . count($protected_content) . $placeholder_suffix;
                $protected_content[] = $matches[0];
                return $placeholder;
            }, $content);
        }

        // 替换关键词
        foreach ($links as $link) {
            if ($link_count >= $max_links) {
                break;
            }

            $keywords = array_map('trim', explode(',', $link['keywords']));
            $target_url = $link['target_url'];
            $slug = $link['slug'];

            foreach ($keywords as $keyword) {
                $keyword = trim($keyword);
                if (empty($keyword) || strlen($keyword) < 2) {
                    continue;
                }

                // 检查是否已替换过
                $keyword_key = md5(strtolower($keyword));
                if ($first_only && isset($replaced_keywords[$keyword_key])) {
                    continue;
                }

                // 替换（不区分大小写，词边界）
                $pattern = '/\b(' . preg_quote($keyword, '/') . ')\b/i';
                $replacement = '<a href="' . esc_url(home_url('/') . self::REDIRECT_BASE . '/' . $slug . '/') . '" class="efp-aff-link" data-aff-id="' . $link['id'] . '" target="_blank" rel="noopener sponsored">$1</a>';

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

        // 恢复受保护内容
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
     * 短代码
     */
    public function shortcode_aff_link($atts) {
        $atts = shortcode_atts([
            'id'    => '',
            'text'  => '',
            'class' => 'efp-aff-link',
        ], $atts, 'aff_link');

        if (empty($atts['id'])) {
            return '';
        }

        $post_id = intval($atts['id']);
        $post = get_post($post_id);

        if (!$post || $post->post_type !== self::POST_TYPE || $post->post_status !== 'publish') {
            return '';
        }

        $meta_status = get_post_meta($post_id, self::META_PREFIX . 'status', true);
        if ($meta_status !== 'active') {
            return '';
        }

        $slug = get_post_field('post_name', $post_id);
        $url = home_url('/') . self::REDIRECT_BASE . '/' . $slug . '/';
        $link_text = $atts['text'] ?: get_the_title($post_id);

        return '<a href="' . esc_url($url) . '" class="' . esc_attr($atts['class']) . '" data-aff-id="' . esc_attr($post_id) . '" target="_blank" rel="noopener sponsored">' . esc_html($link_text) . '</a>';
    }

    /**
     * 注册设置
     */
    public function register_settings() {
        register_setting('efp_aff_settings', self::OPTION_SETTINGS, [
            'type'              => 'array',
            'sanitize_callback' => [$this, 'sanitize_settings'],
            'default'           => [
                'auto_link_enabled'      => true,
                'max_links_per_post'     => 3,
                'first_occurrence_only'  => true,
                'link_style'             => 'default',
                'track_clicks'           => true,
            ],
        ]);

        add_settings_section(
            'efp_aff_general',
            __('General Settings', 'earnforex-wp'),
            [$this, 'settings_section_general_cb'],
            'efp-affiliate-settings'
        );

        add_settings_field(
            'auto_link_enabled',
            __('Enable Auto-linking', 'earnforex-wp'),
            [$this, 'field_auto_link_enabled'],
            'efp-affiliate-settings',
            'efp_aff_general'
        );

        add_settings_field(
            'max_links_per_post',
            __('Max Links Per Post', 'earnforex-wp'),
            [$this, 'field_max_links_per_post'],
            'efp-affiliate-settings',
            'efp_aff_general'
        );

        add_settings_field(
            'first_occurrence_only',
            __('First Occurrence Only', 'earnforex-wp'),
            [$this, 'field_first_occurrence_only'],
            'efp-affiliate-settings',
            'efp_aff_general'
        );

        add_settings_field(
            'link_style',
            __('Link Style', 'earnforex-wp'),
            [$this, 'field_link_style'],
            'efp-affiliate-settings',
            'efp_aff_general'
        );
    }

    public function sanitize_settings($input) {
        $defaults = [
            'auto_link_enabled'      => true,
            'max_links_per_post'     => 3,
            'first_occurrence_only'  => true,
            'link_style'             => 'default',
            'track_clicks'           => true,
        ];

        $output = [];
        foreach ($defaults as $key => $default) {
            if (isset($input[$key])) {
                if (is_bool($default)) {
                    $output[$key] = !empty($input[$key]);
                } elseif (is_int($default)) {
                    $output[$key] = max(1, intval($input[$key]));
                } else {
                    $output[$key] = sanitize_text_field($input[$key]);
                }
            } else {
                $output[$key] = $default;
            }
        }

        return $output;
    }

    public function get_settings() {
        return get_option(self::OPTION_SETTINGS, [
            'auto_link_enabled'      => true,
            'max_links_per_post'     => 3,
            'first_occurrence_only'  => true,
            'link_style'             => 'default',
            'track_clicks'           => true,
        ]);
    }

    public function settings_section_general_cb() {
        echo '<p>' . __('Configure how affiliate links are automatically inserted into your content.', 'earnforex-wp') . '</p>';
    }

    public function field_auto_link_enabled() {
        $settings = $this->get_settings();
        echo '<label><input type="checkbox" name="efp_aff_settings[auto_link_enabled]" value="1" ' . checked($settings['auto_link_enabled'], true, false) . '> ' . __('Automatically convert keywords to affiliate links in post content.', 'earnforex-wp') . '</label>';
    }

    public function field_max_links_per_post() {
        $settings = $this->get_settings();
        echo '<input type="number" name="efp_aff_settings[max_links_per_post]" value="' . esc_attr($settings['max_links_per_post']) . '" min="1" max="20" class="small-text"> ' . __('Maximum number of auto-links per post.', 'earnforex-wp');
    }

    public function field_first_occurrence_only() {
        $settings = $this->get_settings();
        echo '<label><input type="checkbox" name="efp_aff_settings[first_occurrence_only]" value="1" ' . checked($settings['first_occurrence_only'], true, false) . '> ' . __('Only link the first occurrence of each keyword.', 'earnforex-wp') . '</label>';
    }

    public function field_link_style() {
        $settings = $this->get_settings();
        $styles = [
            'default'   => __('Default (underline)', 'earnforex-wp'),
            'button'    => __('Button style', 'earnforex-wp'),
            'badge'     => __('Badge style', 'earnforex-wp'),
            'minimal'   => __('Minimal (icon only)', 'earnforex-wp'),
        ];
        echo '<select name="efp_aff_settings[link_style]">';
        foreach ($styles as $value => $label) {
            echo '<option value="' . esc_attr($value) . '"' . selected($settings['link_style'], $value, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
    }

    /**
     * 设置页面
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('efp_aff_settings');
                do_settings_sections('efp-affiliate-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * 管理列表列
     */
    public function admin_columns($columns) {
        $new_columns = [];
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key === 'title') {
                $new_columns['keywords']   = __('Keywords', 'earnforex-wp');
                $new_columns['target_url'] = __('Target URL', 'earnforex-wp');
                $new_columns['status']     = __('Status', 'earnforex-wp');
                $new_columns['clicks']     = __('Clicks', 'earnforex-wp');
                $new_columns['order']      = __('Order', 'earnforex-wp');
            }
        }
        return $new_columns;
    }

    public function admin_column_content($column, $post_id) {
        switch ($column) {
            case 'keywords':
                echo esc_html(get_post_meta($post_id, self::META_PREFIX . 'keywords', true));
                break;
            case 'target_url':
                $url = get_post_meta($post_id, self::META_PREFIX . 'target_url', true);
                echo '<a href="' . esc_url($url) . '" target="_blank" class="efp-aff-target-url">' . esc_html(wp_trim_words($url, 10)) . '</a>';
                break;
            case 'status':
                $status = get_post_meta($post_id, self::META_PREFIX . 'status', true) ?: 'active';
                $label = $status === 'active' ? __('Active', 'earnforex-wp') : __('Inactive', 'earnforex-wp');
                $class = $status === 'active' ? 'efp-status-active' : 'efp-status-inactive';
                echo '<span class="efp-status-badge ' . esc_attr($class) . '" data-id="' . esc_attr($post_id) . '" data-status="' . esc_attr($status) . '">' . esc_html($label) . '</span>';
                break;
            case 'clicks':
                $clicks = $this->get_click_stats($post_id);
                echo '<span class="efp-clicks" data-id="' . esc_attr($post_id) . '">' . number_format_i18n($clicks['total']) . '</span>';
                break;
            case 'order':
                $order = get_post_meta($post_id, self::META_PREFIX . 'menu_order', true);
                echo '<input type="number" class="efp-order-input" value="' . esc_attr($order ?: 0) . '" data-id="' . esc_attr($post_id) . '" min="0" style="width:60px">';
                break;
        }
    }

    public function sortable_columns($columns) {
        $columns['order'] = 'menu_order';
        return $columns;
    }

    public function custom_orderby($vars) {
        if (isset($vars['post_type']) && $vars['post_type'] === self::POST_TYPE) {
            if (empty($vars['orderby']) || $vars['orderby'] === 'menu_order') {
                $vars['meta_key'] = self::META_PREFIX . 'menu_order';
                $vars['orderby']  = 'meta_value_num';
                $vars['order']    = $vars['order'] ?: 'ASC';
            }
        }
        return $vars;
    }

    /**
     * 获取点击统计
     */
    public function get_click_stats($post_id) {
        $clicks = get_option(self::OPTION_CLICKS, []);
        $data = $clicks[$post_id] ?? [];
        return [
            'total'      => $data['total'] ?? 0,
            'daily'      => $data['daily'] ?? [],
            'last_click' => $data['last_click'] ?? null,
        ];
    }
}

// 初始化
EFP_Affiliate_Link_Manager::instance();

// 主题激活/切换时刷新重写规则
// function efp_aff_theme_setup() {  // 已移除，CPT 在 init 注册
    EFP_Affiliate_Link_Manager::instance();
    // flush_rewrite_rules();  // 不再需要，init 会自动处理
}
// 已移除 after_switch_theme，CPT 已在 init 注册

// 可选：主题停用时清理（可选）
// function efp_aff_theme_cleanup() {  // 不再需要
    // flush_rewrite_rules();  // 不再需要，init 会自动处理
}
// add_action('switch_theme', 'efp_aff_theme_cleanup');  // 不再需要
