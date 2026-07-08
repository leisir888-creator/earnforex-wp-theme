<?php
/**
 * Main Template File
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */

get_header(); ?>

<?php if (have_posts()) : ?>
    <?php if (is_home() && !is_front_page()) : ?>
        <header class="page-header wrapper">
            <h1><?php single_post_title(); ?></h1>
        </header>
    <?php endif; ?>

    <div class="wrapper">
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
    </div>

<?php else : ?>
    <div class="wrapper">
        <?php get_template_part('template-parts/content', 'none'); ?>
    </div>
<?php endif; ?>

<?php get_footer(); ?>