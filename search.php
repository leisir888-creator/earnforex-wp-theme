<?php
/**
 * Search Results Template
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */

get_header(); ?>

<header class="page-header wrapper">
    <h1><?php printf(__('Search Results for: %s', 'earnforex-wp'), '<span class="search-query">' . get_search_query() . '</span>'); ?></h1>
</header>

<div class="wrapper">
    <?php if (have_posts()) : ?>
        <div class="posts-grid">
            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part('template-parts/content', get_post_type()); ?>
            <?php endwhile; ?>
        </div>

        <?php the_posts_navigation([
            'prev_text' => __('Previous', 'earnforex-wp'),
            'next_text' => __('Next', 'earnforex-wp'),
        ]); ?>

    <?php else : ?>
        <?php get_template_part('template-parts/content', 'none'); ?>
    <?php endif; ?>
</div>

<?php get_footer(); ?>