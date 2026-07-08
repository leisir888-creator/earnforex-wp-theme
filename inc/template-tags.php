<?php
/**
 * Template Tags
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */

if (!defined('ABSPATH')) { exit; }

/**
 * Get social icon SVG
 */
function efp_get_social_icon($network) {
    $icons = [
        'twitter' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>',
        'facebook' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>',
        'linkedin' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>',
        'youtube' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48"/></svg>',
        'telegram' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>',
    ];
    return $icons[$network] ?? '';
}

/**
 * Get broker rating stars
 */
function efp_get_rating_stars($rating, $max = 5) {
    $rating = floatval($rating);
    $html = '<div class="rating-stars" aria-hidden="true">';
    for ($i = 1; $i <= $max; $i++) {
        $fill = $i <= $rating ? '#F6AD55' : '#E2E8F0';
        $html .= '<svg width="14" height="14" viewBox="0 0 24 24" fill="' . esc_attr($fill) . '" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
    }
    $html .= '</div>';
    return $html;
}

/**
 * Get broker meta value with fallback
 */
function efp_get_broker_meta($key, $default = '') {
    $value = get_post_meta(get_the_ID(), $key, true);
    return $value !== '' ? $value : $default;
}

/**
 * Check if current page is broker archive
 */
function efp_is_broker_archive() {
    return is_post_type_archive('efp_broker');
}

/**
 * Get theme mod with default
 */
function efp_get_theme_mod($name, $default = '') {
    return get_theme_mod($name, $default);
}

/**
 * Output schema.org structured data for broker
 */
function efp_output_broker_schema($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    $rating = get_post_meta($post_id, 'broker_rating', true);
    $reviews = get_post_meta($post_id, 'broker_reviews', true);
    $website = get_post_meta($post_id, 'broker_website', true);
    $founded = get_post_meta($post_id, 'broker_founded', true);

    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'FinancialService',
        'name'     => get_the_title($post_id),
        'url'      => get_permalink($post_id),
    ];

    if ($website) { $schema['sameAs'] = $website; }
    if ($rating) {
        $schema['aggregateRating'] = [
            '@type'       => 'AggregateRating',
            'ratingValue' => floatval($rating),
            'reviewCount' => intval($reviews ?: 0),
            'bestRating'  => 5,
            'worstRating' => 0,
        ];
    }
    if ($founded) { $schema['foundingDate'] = $founded; }

    echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>';
}

/**
 * Breadcrumb trail
 */
function efp_breadcrumbs() {
    $home_url = home_url('/');
    $home_name = get_bloginfo('name');

    echo '<nav class="bread-crumbs" aria-label="Breadcrumb">';
    echo '<ol class="bread-crumbs__list">';
    echo '<li class="bread-crumbs__list-item"><a href="' . esc_url($home_url) . '" class="bread-crumbs__item"><span>' . esc_html($home_name) . '</span></a></li>';

    if (is_category() || is_single()) {
        $category = get_the_category();
        if ($category) {
            $cat = $category[0];
            echo '<li class="bread-crumbs__list-item"><a href="' . esc_url(get_category_link($cat->term_id)) . '" class="bread-crumbs__item"><span>' . esc_html($cat->name) . '</span></a></li>';
        }
        if (is_single()) {
            echo '<li class="bread-crumbs__list-item"><span class="bread-crumbs__item"><span>' . get_the_title() . '</span></span></li>';
        }
    } elseif (is_page()) {
        echo '<li class="bread-crumbs__list-item"><span class="bread-crumbs__item"><span>' . get_the_title() . '</span></span></li>';
    } elseif (is_post_type_archive()) {
        $post_type = get_post_type_object(get_post_type());
        echo '<li class="bread-crumbs__list-item"><span class="bread-crumbs__item"><span>' . esc_html($post_type->labels->name) . '</span></span></li>';
    } elseif (is_search()) {
        echo '<li class="bread-crumbs__list-item"><span class="bread-crumbs__item"><span>' . sprintf(__('Search: %s', 'earnforex-wp'), get_search_query()) . '</span></span></li>';
    }

    echo '</ol>';
    echo '</nav>';
}

/**
 * Fallback menu
 */
function efp_fallback_menu() {
    echo '<ul class="menu__list">';
    echo '<li class="menu__item"><a href="' . esc_url(home_url('/')) . '" class="menu__link">' . __('Home', 'earnforex-wp') . '</a></li>';
    echo '<li class="menu__item"><a href="' . esc_url(home_url('/brokers/')) . '" class="menu__link">' . __('Brokers', 'earnforex-wp') . '</a></li>';
    echo '<li class="menu__item"><a href="' . esc_url(home_url('/tools/')) . '" class="menu__link">' . __('Tools', 'earnforex-wp') . '</a></li>';
    echo '<li class="menu__item"><a href="' . esc_url(home_url('/education/')) . '" class="menu__link">' . __('Education', 'earnforex-wp') . '</a></li>';
    echo '<li class="menu__item"><a href="' . esc_url(home_url('/about/')) . '" class="menu__link">' . __('About', 'earnforex-wp') . '</a></li>';
    echo '<li class="menu__item"><a href="' . esc_url(home_url('/contact/')) . '" class="menu__link">' . __('Contact', 'earnforex-wp') . '</a></li>';
    echo '</ul>';
}

/**
 * Custom Menu Walker
 */
class EFP_Menu_Walker extends Walker_Nav_Menu {
    function start_lvl(&$output, $depth = 0, $args = null) {
        if ($depth === 0) {
            $output .= '<div class="menu__submenu" role="menu"><ul class="menu__submenu-list">';
        } else {
            $output .= '<ul class="menu__submenu-list">';
        }
    }

    function end_lvl(&$output, $depth = 0, $args = null) {
        $output .= '</ul>';
        if ($depth === 0) { $output .= '</div>'; }
    }

    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        $classes[] = 'menu__item';
        if ($depth > 0) { $classes[] = 'menu__submenu-item'; }

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        $output .= '<li' . $id . $class_names . '>';

        $atts = [];
        $atts['href'] = !empty($item->url) ? $item->url : '#';
        $atts['class'] = $depth === 0 ? 'menu__link' : 'menu__submenu-link';
        if ($item->current) { $atts['aria-current'] = 'page'; }

        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);
        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) { $attributes .= ' ' . $attr . '="' . esc_attr($value) . '"'; }
        }

        $title = apply_filters('the_title', $item->title, $item->ID);
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        $output .= '<div class="menu__item_wrap"><a' . $attributes . '>';
        $output .= $title;

        // Add arrow for items with children
        if ($args->walker->has_children) {
            $output .= ' <svg width="16" height="8" viewBox="0 5 20 10" fill="none" xmlns="http://www.w3.org/2000/svg" class="menu__arrow" aria-hidden="true"><path d="M10.0006 10.9766L14.1256 6.85156L15.3039 8.0299L10.0006 13.3332L4.69727 8.0299L5.8756 6.85156L10.0006 10.9766Z" fill="#001A38"></path></svg>';
        }
        $output .= '</a></div>';
    }
}
