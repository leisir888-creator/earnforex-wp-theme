<?php
/**
 * Block Patterns
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */

if (!defined('ABSPATH')) { exit; }

/**
 * Register Block Patterns
 */
function efp_register_block_patterns() {
    // Hero Section Pattern
    register_block_pattern('earnforex-wp/hero-section', [
        'title'       => __('EarnForex Hero Section', 'earnforex-wp'),
        'categories'  => ['header', 'hero'],
        'description' => __('Full-width hero section with CTA', 'earnforex-wp'),
        'content'     => '<!-- wp:group {"className":"hero-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group hero-section"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":1,"className":"hero-section__title"} -->
<h1 class="hero-section__title"><span class="accent">' . __('Forex Trading Information', 'earnforex-wp') . '</span></h1>
<!-- /wp:heading --><!-- wp:paragraph {"className":"hero-section__subtitle"} -->
<p class="hero-section__subtitle">' . __('Compare brokers, tools, and education for successful trading', 'earnforex-wp') . '</p>
<!-- /wp:paragraph --><!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"btn btn--primary btn--lg"} -->
<div class="wp-block-button"><a class="wp-block-button__link btn btn--primary btn--lg" href="' . esc_url(home_url('/brokers/')) . '">' . __('View All Brokers', 'earnforex-wp') . '</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->',
    ]);

    // Broker Grid Pattern
    register_block_pattern('earnforex-wp/broker-grid', [
        'title'       => __('Broker Comparison Grid', 'earnforex-wp'),
        'categories'  => ['featured', 'broker'],
        'description' => __('Grid of broker cards with rating and features', 'earnforex-wp'),
        'content'     => '<!-- wp:group {"className":"brokers-list","layout":{"type":"constrained"}} -->
<div class="wp-block-group brokers-list"><!-- wp:heading {"level":2,"className":"section-block__title"} -->
<h2 class="section-block__title">' . __('Top Rated Brokers', 'earnforex-wp') . '</h2>
<!-- /wp:heading --><!-- wp:query {"query":{"perPage":4,"postType":"efp_broker","orderBy":"meta_value_num","order":"DESC","metaKey":"broker_rating"},"displayLayout":{"type":"grid","columns":4},"templateLock":"all"} -->
<div class="wp-block-query"><!-- wp:post-template -->
<!-- wp:group {"className":"brokers-list__card","layout":{"type":"constrained"}} -->
<div class="wp-block-group brokers-list__card"><!-- wp:post-featured-image {"width":48,"height":48,"className":"broker-card__logo"} /-->
<!-- wp:post-title {"level":3,"className":"broker-card__name"} /-->
<!-- wp:post-meta {"className":"broker-card__rating"} /-->
<!-- wp:group {"className":"broker-card__body","layout":{"type":"constrained"}} -->
<div class="wp-block-group broker-card__body"><!-- wp:post-terms {"term":"feature","className":"broker-card__features"} /--></div>
<!-- /wp:group --><!-- wp:group {"className":"broker-card__footer","layout":{"type":"flex","justifyContent":"space-between"}} -->
<div class="wp-block-group broker-card__footer"><!-- wp:button {"text":"' . __('Visit Broker', 'earnforex-wp') . '","className":"btn btn--primary btn--sm","linkTarget":"_blank"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --><!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->',
    ]);

    // Tool Card Pattern
    register_block_pattern('earnforex-wp/tool-card', [
        'title'       => __('Forex Tool Card', 'earnforex-wp'),
        'categories'  => ['featured', 'tool'],
        'description' => __('Card for forex calculator or tool', 'earnforex-wp'),
        'content'     => '<!-- wp:group {"className":"tool-card","layout":{"type":"constrained"}} -->
<div class="wp-block-group tool-card"><!-- wp:post-featured-image {"className":"tool-card__thumbnail"} /-->
<!-- wp:group {"className":"tool-card__content","layout":{"type":"constrained"}} -->
<div class="wp-block-group tool-card__content"><!-- wp:post-terms {"term":"category","className":"tool-card__category"} /-->
<!-- wp:post-title {"level":3,"className":"tool-card__title"} /-->
<!-- wp:post-excerpt {"className":"tool-card__excerpt"} /-->
<!-- wp:button {"text":"' . __('Use Tool', 'earnforex-wp') . '","className":"btn btn--outline btn--sm"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->',
    ]);

    // Section Block Pattern
    register_block_pattern('earnforex-wp/section-block', [
        'title'       => __('Section Block with Grid', 'earnforex-wp'),
        'categories'  => ['layout', 'section'],
        'description' => __('Section with title, description, grid and CTA', 'earnforex-wp'),
        'content'     => '<!-- wp:group {"className":"section-block","layout":{"type":"constrained"}} -->
<div class="wp-block-group section-block"><!-- wp:group {"className":"section-block__header","layout":{"type":"constrained"}} -->
<div class="wp-block-group section-block__header"><!-- wp:heading {"level":2,"className":"section-block__title"} -->
<h2 class="section-block__title">' . __('Section Title', 'earnforex-wp') . '</h2>
<!-- /wp:heading --><!-- wp:paragraph {"className":"section-block__description"} -->
<p class="section-block__description">' . __('Section description goes here.', 'earnforex-wp') . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --><!-- wp:group {"className":"section-block__grid","layout":{"type":"grid","columns":4}} -->
<div class="wp-block-group section-block__grid"><!-- wp:post-template -->
<!-- wp:group {"className":"post-card","layout":{"type":"constrained"}} -->
<div class="wp-block-group post-card"><!-- wp:post-featured-image {"className":"post-card__thumbnail"} /-->
<!-- wp:group {"className":"post-card__content","layout":{"type":"constrained"}} -->
<div class="wp-block-group post-card__content"><!-- wp:post-terms {"term":"category","className":"post-card__category"} /-->
<!-- wp:post-title {"level":3,"className":"post-card__title"} /-->
<!-- wp:post-excerpt {"className":"post-card__excerpt"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --><!-- /wp:post-template --></div>
<!-- /wp:group --><!-- wp:group {"className":"section-block__cta","layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group section-block__cta"><!-- wp:button {"text":"' . __('View All', 'earnforex-wp') . '","className":"btn btn--outline"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->',
    ]);

    // Broker Review Summary Pattern
    register_block_pattern('earnforex-wp/broker-review-summary', [
        'title'       => __('Broker Review Summary', 'earnforex-wp'),
        'categories'  => ['broker', 'review'],
        'description' => __('Summary box for broker reviews', 'earnforex-wp'),
        'content'     => '<!-- wp:group {"className":"broker-review-summary","layout":{"type":"constrained"}} -->
<div class="wp-block-group broker-review-summary"><!-- wp:heading {"level":3,"className":"broker-review-summary__title"} -->
<h3 class="broker-review-summary__title">' . __('Quick Summary', 'earnforex-wp') . '</h3>
<!-- /wp:heading --><!-- wp:group {"className":"broker-review-summary__rating","layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group broker-review-summary__rating"><!-- wp:paragraph {"className":"rating-display"} -->
<p class="rating-display"><span class="rating-value">4.5</span> <span class="rating-label">' . __('out of 5', 'earnforex-wp') . '</span></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --><!-- wp:list {"className":"broker-review-summary__pros"} -->
<ul class="broker-review-summary__pros"><!-- wp:list-item --><strong>' . __('Pros:', 'earnforex-wp') . '</strong> Low spreads, fast execution, regulated<!-- /wp:list-item --><!-- wp:list-item -->Multi-platform support<!-- /wp:list-item --><!-- wp:list-item -->Excellent support<!-- /wp:list-item --></ul>
<!-- /wp:list --><!-- wp:list {"className":"broker-review-summary__cons"} -->
<ul class="broker-review-summary__cons"><!-- wp:list-item --><strong>' . __('Cons:', 'earnforex-wp') . '</strong> Limited exotic pairs<!-- /wp:list-item --><!-- wp:list-item -->High minimum deposit<!-- /wp:list-item --></ul>
<!-- /wp:list --><!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"text":"' . __('Read Full Review', 'earnforex-wp') . '","className":"btn btn--primary"} /--></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->',
    ]);
}
add_action('init', 'efp_register_block_patterns');

/**
 * Register Block Pattern Categories
 */
function efp_register_block_pattern_categories() {
    register_block_pattern_category('broker', [
        'label'       => __('Broker', 'earnforex-wp'),
        'description' => __('Patterns for broker listings and reviews', 'earnforex-wp'),
    ]);
    register_block_pattern_category('tool', [
        'label'       => __('Tool', 'earnforex-wp'),
        'description' => __('Patterns for forex tools and calculators', 'earnforex-wp'),
    ]);
    register_block_pattern_category('review', [
        'label'       => __('Review', 'earnforex-wp'),
        'description' => __('Patterns for review summaries', 'earnforex-wp'),
    ]);
}
add_action('init', 'efp_register_block_pattern_categories');
