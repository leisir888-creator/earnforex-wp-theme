<?php
/**
 * Archive Template
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */

get_header(); ?>

<header class="page-header wrapper">
    <?php the_archive_title('<h1>', '</h1>'); ?>
    <?php the_archive_description('<div class="archive-description">', '</div>'); ?>
</header>

<div class="wrapper">
    <?php if (have_posts()) : ?>
        <?php if (is_post_type_archive('efp_broker')) : ?>
            <?php get_template_part('template-parts/archive', 'broker'); ?>
        <?php elseif (is_post_type_archive('efp_tool')) : ?>
            <?php get_template_part('template-parts/archive', 'tool'); ?>
        <?php elseif (is_post_type_archive('efp_prop_firm')) : ?>
            <?php get_template_part('template-parts/archive', 'prop-firm'); ?>
        <?php else : ?>
            <div class="posts-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/content', get_post_type()); ?>
                <?php endwhile; ?>
            </div>

            <?php the_posts_navigation([
                'prev_text' => __('Previous', 'earnforex-wp'),
                'next_text' => __('Next', 'earnforex-wp'),
            ]); ?>
        <?php endif; ?>
    <?php else : ?>
        <?php get_template_part('template-parts/content', 'none'); ?>
    <?php endif; ?>
</div>

<?php get_footer(); ?>