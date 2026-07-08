<?php
/**
 * EarnForex WP Theme - Functions
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Theme constants
define('EFP_THEME_VERSION', '1.0.5');
define('EFP_THEME_DIR', get_template_directory());
define('EFP_THEME_URI', get_template_directory_uri());
define('EFP_ASSETS_URI', EFP_THEME_URI . '/assets');

/**
 * Theme Setup
 */
function efp_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', [
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style',
    ]);
    add_theme_support('custom-logo', [
        'height'      => 40,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
        'header-text' => ['site-title', 'site-description'],
    ]);
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-style.css');
    add_theme_support('wp-block-styles');

    register_nav_menus([
        'primary' => __('Primary Menu', 'earnforex-wp'),
        'footer'  => __('Footer Menu', 'earnforex-wp'),
    ]);

    add_image_size('efp-broker-logo', 120, 120, true);
    add_image_size('efp-card-image', 400, 250, true);
    add_image_size('efp-hero', 1200, 600, true);

    load_theme_textdomain('earnforex-wp', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'efp_theme_setup');

/**
 * Enqueue Scripts & Styles
 */
function efp_enqueue_assets() {
    wp_enqueue_style('efp-main', get_stylesheet_uri(), [], EFP_THEME_VERSION);
    wp_add_inline_style('efp-main', efp_get_css_variables());
    wp_enqueue_script('efp-main', EFP_ASSETS_URI . '/js/main.js', [], EFP_THEME_VERSION, true);
    wp_localize_script('efp-main', 'efp_ajax', [
        'ajax_url'    => admin_url('admin-ajax.php'),
        'nonce'       => wp_create_nonce('efp_nonce'),
        'site_url'    => home_url(),
        'theme_uri'   => EFP_THEME_URI,
        'is_rtl'      => is_rtl(),
    ]);
}
add_action('wp_enqueue_scripts', 'efp_enqueue_assets');

/**
 * Enqueue Admin & Login Styles - Replace WP Logo
 */
function efp_enqueue_admin_styles() {
    wp_enqueue_style('efp-admin', get_template_directory_uri() . '/assets/css/admin.css', [], EFP_THEME_VERSION);
}
add_action('admin_enqueue_scripts', 'efp_enqueue_admin_styles');
add_action('login_enqueue_scripts', 'efp_enqueue_admin_styles');


function efp_get_css_variables() {
    return '
    :root {
        --color-primary: #4FD1C5;
        --color-primary-dark: #38B2AC;
        --color-dark: #001A38;
        --color-text: #2D3748;
        --color-bg: #F7FAFC;
        --color-white: #FFFFFF;
        --container-max: 1200px;
        --header-height: 72px;
    }
    ';
}

/**
 * Register Custom Post Types
 */
function efp_register_post_types() {
    register_post_type('efp_broker', [
        'labels' => [
            'name'               => __('Brokers', 'earnforex-wp'),
            'singular_name'      => __('Broker', 'earnforex-wp'),
            'add_new'            => __('Add New Broker', 'earnforex-wp'),
            'add_new_item'       => __('Add New Broker', 'earnforex-wp'),
            'edit_item'          => __('Edit Broker', 'earnforex-wp'),
            'new_item'           => __('New Broker', 'earnforex-wp'),
            'view_item'          => __('View Broker', 'earnforex-wp'),
            'search_items'       => __('Search Brokers', 'earnforex-wp'),
            'not_found'          => __('No brokers found', 'earnforex-wp'),
            'not_found_in_trash' => __('No brokers found in Trash', 'earnforex-wp'),
        ],
        'public'              => true,
        'has_archive'         => true,
        'rewrite'             => ['slug' => 'brokers', 'with_front' => false],
        'supports'            => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions'],
        'menu_icon'           => 'dashicons-building',
        'show_in_rest'        => true,
    ]);

    register_post_type('efp_tool', [
        'labels' => [
            'name'               => __('Forex Tools', 'earnforex-wp'),
            'singular_name'      => __('Tool', 'earnforex-wp'),
        ],
        'public'              => true,
        'has_archive'         => true,
        'rewrite'             => ['slug' => 'tools', 'with_front' => false],
        'supports'            => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions'],
        'menu_icon'           => 'dashicons-calculator',
        'show_in_rest'        => true,
    ]);

    register_post_type('efp_prop_firm', [
        'labels' => [
            'name'               => __('Prop Firms', 'earnforex-wp'),
            'singular_name'      => __('Prop Firm', 'earnforex-wp'),
        ],
        'public'              => true,
        'has_archive'         => true,
        'rewrite'             => ['slug' => 'prop-firms', 'with_front' => false],
        'supports'            => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions'],
        'menu_icon'           => 'dashicons-chart-bar',
        'show_in_rest'        => true,
    ]);
}
add_action('init', 'efp_register_post_types');

/**
 * Register Custom Taxonomies
 */
function efp_register_taxonomies() {
    register_taxonomy('efp_broker_category', 'efp_broker', [
        'labels'       => [
            'name'          => __('Broker Categories', 'earnforex-wp'),
            'singular_name' => __('Category', 'earnforex-wp'),
        ],
        'hierarchical' => true,
        'public'       => true,
        'show_ui'      => true,
        'show_in_rest' => true,
        'rewrite'      => ['slug' => 'broker-category', 'with_front' => false],
    ]);

    register_taxonomy('efp_broker_feature', 'efp_broker', [
        'labels'       => [
            'name'          => __('Broker Features', 'earnforex-wp'),
            'singular_name' => __('Feature', 'earnforex-wp'),
        ],
        'hierarchical' => false,
        'public'       => true,
        'show_in_rest' => true,
        'rewrite'      => ['slug' => 'feature', 'with_front' => false],
    ]);

    register_taxonomy('efp_tool_category', 'efp_tool', [
        'labels'       => [
            'name'          => __('Tool Categories', 'earnforex-wp'),
            'singular_name' => __('Category', 'earnforex-wp'),
        ],
        'hierarchical' => true,
        'public'       => true,
        'show_in_rest' => true,
        'rewrite'      => ['slug' => 'tool-category', 'with_front' => false],
    ]);
}
add_action('init', 'efp_register_taxonomies');

/**
 * Register Meta Fields for Broker
 */
function efp_register_meta_fields() {
    $broker_fields = [
        'broker_rating'       => ['type' => 'number', 'min' => 0, 'max' => 5],
        'broker_reviews'      => ['type' => 'number'],
        'broker_founded'      => ['type' => 'number'],
        'broker_headquarters' => ['type' => 'string'],
        'broker_regulation'   => ['type' => 'string'],
        'broker_min_deposit'  => ['type' => 'string'],
        'broker_max_leverage' => ['type' => 'string'],
        'broker_spread_type'  => ['type' => 'string'],
        'broker_avg_spread'   => ['type' => 'string'],
        'broker_commission'   => ['type' => 'string'],
        'broker_platforms'    => ['type' => 'string'],
        'broker_payment_methods' => ['type' => 'string'],
        'broker_website'      => ['type' => 'string'],
        'broker_affiliate_link'  => ['type' => 'string'],
    ];

    foreach ($broker_fields as $key => $field) {
        register_post_meta('efp_broker', $key, [
            'type'         => $field['type'],
            'single'       => true,
            'show_in_rest' => true,
            'sanitize_callback' => function($value) use ($field) {
                if ($field['type'] === 'number') return floatval($value);
                return sanitize_text_field($value);
            },
            'auth_callback' => function() { return current_user_can('edit_posts'); },
        ]);
    }
}
add_action('init', 'efp_register_meta_fields');

/**
 * Admin Columns for Broker
 */
function efp_broker_columns($columns) {
    return [
        'cb'         => '<input type="checkbox" />',
        'logo'       => __('Logo', 'earnforex-wp'),
        'title'      => __('Broker', 'earnforex-wp'),
        'rating'     => __('Rating', 'earnforex-wp'),
        'regulation' => __('Regulation', 'earnforex-wp'),
        'spread'     => __('Avg Spread', 'earnforex-wp'),
        'date'       => __('Date', 'earnforex-wp'),
    ];
}
add_filter('manage_efp_broker_posts_columns', 'efp_broker_columns');

function efp_broker_column_content($column, $post_id) {
    switch ($column) {
        case 'logo':
            echo get_the_post_thumbnail($post_id, [40, 40]);
            break;
        case 'rating':
            $rating = get_post_meta($post_id, 'broker_rating', true);
            echo $rating ? esc_html($rating) . ' / 5' : '&mdash;';
            break;
        case 'regulation':
            $reg = get_post_meta($post_id, 'broker_regulation', true);
            echo esc_html(wp_trim_words($reg, 5));
            break;
        case 'spread':
            $spread = get_post_meta($post_id, 'broker_avg_spread', true);
            echo $spread ? esc_html($spread) . ' pips' : '&mdash;';
            break;
    }
}
add_action('manage_efp_broker_posts_custom_column', 'efp_broker_column_content', 10, 2);

function efp_broker_sortable_columns($columns) {
    $columns['rating'] = 'broker_rating';
    return $columns;
}
add_filter('manage_edit-efp_broker_sortable_columns', 'efp_broker_sortable_columns');

/**
 * Widget Areas
 */
function efp_widgets_init() {
    register_sidebar([
        'name'          => __('Sidebar', 'earnforex-wp'),
        'id'            => 'sidebar-1',
        'description'   => __('Main sidebar', 'earnforex-wp'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);

    for ($i = 1; $i <= 3; $i++) {
        register_sidebar([
            'name'          => sprintf(__('Footer Column %d', 'earnforex-wp'), $i),
            'id'            => "footer-$i",
            'description'   => sprintf(__('Footer column %d', 'earnforex-wp'), $i),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="footer__column-title">',
            'after_title'   => '</h4>',
        ]);
    }
}
add_action('widgets_init', 'efp_widgets_init');

function efp_excerpt_length($length) { return 25; }
add_filter('excerpt_length', 'efp_excerpt_length');

function efp_excerpt_more($more) { return '&hellip;'; }
add_filter('excerpt_more', 'efp_excerpt_more');

function efp_body_classes($classes) {
    if (is_page_template()) {
        $template = get_page_template_slug();
        $classes[] = 'page-template-' . sanitize_html_class(str_replace('.php', '', basename($template)));
    }
    if (is_post_type_archive('efp_broker')) { $classes[] = 'brokers-archive'; }
    return $classes;
}
add_filter('body_class', 'efp_body_classes');

/**
 * Customizer Options
 */
function efp_customize_register($wp_customize) {
    $wp_customize->add_panel('efp_theme_options', [
        'title'    => __('EarnForex Theme Options', 'earnforex-wp'),
        'priority' => 30,
    ]);

    $wp_customize->add_section('efp_header', [
        'title'    => __('Header', 'earnforex-wp'),
        'panel'    => 'efp_theme_options',
        'priority' => 10,
    ]);
    $wp_customize->add_setting('efp_header_search', [
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ]);
    $wp_customize->add_control('efp_header_search', [
        'type'    => 'checkbox',
        'label'   => __('Show Search in Header', 'earnforex-wp'),
        'section' => 'efp_header',
    ]);

    $wp_customize->add_section('efp_footer', [
        'title'    => __('Footer', 'earnforex-wp'),
        'panel'    => 'efp_theme_options',
        'priority' => 20,
    ]);
    $wp_customize->add_setting('efp_footer_copyright', [
        'default'           => sprintf(__('&copy; %s %s. All rights reserved.', 'earnforex-wp'), date('Y'), get_bloginfo('name')),
        'sanitize_callback' => 'wp_kses_post',
    ]);
    $wp_customize->add_control('efp_footer_copyright', [
        'type'    => 'text',
        'label'   => __('Copyright Text', 'earnforex-wp'),
        'section' => 'efp_footer',
    ]);

    $wp_customize->add_section('efp_social', [
        'title'    => __('Social Links', 'earnforex-wp'),
        'panel'    => 'efp_theme_options',
        'priority' => 30,
    ]);
    $social_networks = ['twitter' => 'Twitter', 'facebook' => 'Facebook', 'linkedin' => 'LinkedIn', 'youtube' => 'YouTube', 'telegram' => 'Telegram'];
    foreach ($social_networks as $key => $label) {
        $wp_customize->add_setting("efp_social_{$key}", [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $wp_customize->add_control("efp_social_{$key}", [
            'type'    => 'url',
            'label'   => $label,
            'section' => 'efp_social',
        ]);
    }
}
add_action('customize_register', 'efp_customize_register');

/**
 * AJAX Filter Brokers
 */
function efp_ajax_filter_brokers() {
    check_ajax_referer('efp_nonce', 'nonce');

    $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $per_page = 12;
    $args = [
        'post_type'      => 'efp_broker',
        'posts_per_page' => $per_page,
        'paged'          => $paged,
        'post_status'    => 'publish',
    ];

    if (!empty($_POST['category'])) {
        $args['tax_query'][] = [
            'taxonomy' => 'efp_broker_category',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($_POST['category']),
        ];
    }
    if (!empty($_POST['feature'])) {
        $args['tax_query'][] = [
            'taxonomy' => 'efp_broker_feature',
            'field'    => 'slug',
            'terms'    => array_map('sanitize_text_field', (array) $_POST['feature']),
            'operator' => 'IN',
        ];
    }

    $sort = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'date';
    switch ($sort) {
        case 'rating_desc':
            $args['meta_key'] = 'broker_rating'; $args['orderby'] = 'meta_value_num'; $args['order'] = 'DESC'; break;
        case 'rating_asc':
            $args['meta_key'] = 'broker_rating'; $args['orderby'] = 'meta_value_num'; $args['order'] = 'ASC'; break;
        case 'spread_asc':
            $args['meta_key'] = 'broker_avg_spread'; $args['orderby'] = 'meta_value_num'; $args['order'] = 'ASC'; break;
        case 'reviews_desc':
            $args['meta_key'] = 'broker_reviews'; $args['orderby'] = 'meta_value_num'; $args['order'] = 'DESC'; break;
        default:
            $args['orderby'] = 'date'; $args['order'] = 'DESC';
    }

    $query = new WP_Query($args);
    ob_start();

    if ($query->have_posts()) {
        while ($query->have_posts()) { $query->the_post(); get_template_part('template-parts/broker', 'card'); }
        wp_reset_postdata();
    } else {
        echo '<p class="no-results">' . __('No brokers found matching your criteria.', 'earnforex-wp') . '</p>';
    }

    $html = ob_get_clean();
    wp_send_json_success([
        'html'      => $html,
        'has_more'  => $query->max_num_pages > $paged,
        'next_page' => $paged + 1,
    ]);
}
add_action('wp_ajax_efp_filter_brokers', 'efp_ajax_filter_brokers');
add_action('wp_ajax_nopriv_efp_filter_brokers', 'efp_ajax_filter_brokers');

/**
 * AJAX Search Brokers
 */
function efp_ajax_search_brokers() {
    check_ajax_referer('efp_nonce', 'nonce');
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $limit = 10;

    $args = [
        'post_type'      => 'efp_broker',
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        's'              => $search,
    ];
    $query = new WP_Query($args);
    $results = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $results[] = [
                'id'     => get_the_ID(),
                'title'  => get_the_title(),
                'url'    => get_permalink(),
                'logo'   => get_the_post_thumbnail_url(get_the_ID(), [40, 40]),
                'rating' => get_post_meta(get_the_ID(), 'broker_rating', true),
            ];
        }
        wp_reset_postdata();
    }
    wp_send_json_success($results);
}
add_action('wp_ajax_efp_search_brokers', 'efp_ajax_search_brokers');
add_action('wp_ajax_nopriv_efp_search_brokers', 'efp_ajax_search_brokers');

/**
 * Include template tags
 */
require_once get_template_directory() . '/inc/template-tags.php';
require_once get_template_directory() . '/inc/block-patterns.php';


/**
 * Replace WordPress "W" Logo with custom site icon everywhere
 */
function efp_replace_wp_logos() {
    $icon_url = 'https://www.fxtraderskit.com/wp-content/uploads/2026/07/favicon-512-new.png';
    ?>
    <style type="text/css">
        /* 1. Admin Bar (前台顶部工具栏) */
        #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
            content: "" !important;
            background-image: url("<?php echo esc_url($icon_url); ?>") !important;
            background-size: contain !important;
            background-repeat: no-repeat !important;
            background-position: center !important;
            width: 20px !important;
            height: 20px !important;
            top: 2px !important;
        }
        #wpadminbar #wp-admin-bar-wp-logo.hover > .ab-item .ab-icon:before {
            background-image: url("<?php echo esc_url($icon_url); ?>") !important;
        }
        
        /* 隐藏原有的 W 字符 */
        #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon {
            font-size: 0 !important;
        }
        
        /* 2. 后台仪表盘左上角 Logo */
        #wp-admin-bar-wp-logo > .ab-item .ab-icon:before,
        #wpbody-content .wrap h1:before,
        .wp-header-end:before {
            content: "" !important;
        }
        
        /* 3. 登录页面 Logo */
        body.login #login h1 a {
            background-image: url("<?php echo esc_url($icon_url); ?>") !important;
            background-size: contain !important;
            background-repeat: no-repeat !important;
            background-position: center !important;
            width: 200px !important;
            height: 200px !important;
        }
        
        /* 登录页面 Logo 链接指向首页 */
        body.login #login h1 a {
            pointer-events: none;
        }
        body.login #login h1 a:hover {
            opacity: 0.8;
        }
    </style>
    <?php
}
add_action('admin_head', 'efp_replace_wp_logos');
add_action('login_head', 'efp_replace_wp_logos');

/* 登录页面 Logo 链接改为首页 */
add_filter('login_headerurl', function() {
    return home_url('/');
});
add_filter('login_headertext', function() {
    return get_bloginfo('name');
});


/**
 * Affiliate Link Manager
 */
require_once get_template_directory() . '/includes/AffiliateLinkManager.php';

