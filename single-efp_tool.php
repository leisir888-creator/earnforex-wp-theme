<?php
/**
 * Single Tool Template
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */

get_header(); ?>

<?php while (have_posts()) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('single-tool wrapper'); ?>>
        <header class="page-header">
            <?php if (has_category()) : ?>
                <div class="post-meta">
                    <?php the_category(' '); ?>
                </div>
            <?php endif; ?>
            <h1><?php the_title(); ?></h1>
            <div class="post-meta">
                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo get_the_date(); ?></time>
            </div>
        </header>

        <?php if (has_post_thumbnail()) : ?>
            <div class="post-thumbnail">
                <?php the_post_thumbnail('efp-hero'); ?>
            </div>
        <?php endif; ?>

        <div class="tool-content">
            <div class="wrapper">
                <div class="tool-content__grid">
                    <div class="tool-content__main">
                        <?php the_content(); ?>
                    </div>

                    <aside class="tool-content__sidebar">
                        <div class="tool-card widget">
                            <h3 class="widget-title"><?php esc_html_e('Tool Info', 'earnforex-wp'); ?></h3>
                            <ul class="tool-info">
                                <li><strong><?php esc_html_e('Type:', 'earnforex-wp'); ?></strong> Calculator</li>
                                <li><strong><?php esc_html_e('Updated:', 'earnforex-wp'); ?></strong> <?php echo get_the_modified_date(); ?></li>
                            </ul>
                            <div style="margin-top:1.5rem;">
                                <a href="#" class="btn btn--primary btn--lg" style="width:100%;"><?php esc_html_e('Launch Tool', 'earnforex-wp'); ?></a>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </article>
<?php endwhile; ?>

<?php get_footer(); ?>