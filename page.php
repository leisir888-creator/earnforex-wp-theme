<?php
/**
 * Page Template
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */

get_header(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('page-content wrapper'); ?>>
    <header class="page-header">
        <h1><?php the_title(); ?></h1>
    </header>

    <div class="page-content__inner">
        <?php the_content(); ?>
    </div>

    <?php if (comments_open() || get_comments_number()) : ?>
        <?php comments_template(); ?>
    <?php endif; ?>
</article>

<?php get_footer(); ?>