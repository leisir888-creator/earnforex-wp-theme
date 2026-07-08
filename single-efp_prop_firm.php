<?php
/**
 * Single Prop Firm Template
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */

get_header(); ?>

<?php while (have_posts()) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('single-prop-firm wrapper'); ?>>
        <header class="page-header">
            <?php if (has_category()) : ?>
                <div class="post-meta">
                    <?php the_category(' '); ?>
                </div>
            <?php endif; ?>
            <h1><?php the_title(); ?></h1>
        </header>

        <?php if (has_post_thumbnail()) : ?>
            <div class="post-thumbnail">
                <?php the_post_thumbnail('efp-hero'); ?>
            </div>
        <?php endif; ?>

        <div class="prop-firm-content">
            <div class="wrapper">
                <div class="prop-firm-content__grid">
                    <div class="prop-firm-content__main">
                        <?php the_content(); ?>
                    </div>

                    <aside class="prop-firm-content__sidebar">
                        <div class="prop-firm-card widget">
                            <h3 class="widget-title"><?php esc_html_e('Firm Details', 'earnforex-wp'); ?></h3>
                            <ul class="prop-firm-details">
                                <li><strong><?php esc_html_e('Status:', 'earnforex-wp'); ?></strong> Active</li>
                                <li><strong><?php esc_html_e('Type:', 'earnforex-wp'); ?></strong> Proprietary Trading</li>
                            </ul>
                            <div style="margin-top:1.5rem;">
                                <a href="#" class="btn btn--primary btn--lg" style="width:100%;"><?php esc_html_e('Apply Now', 'earnforex-wp'); ?></a>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </article>
<?php endwhile; ?>

<?php get_footer(); ?>