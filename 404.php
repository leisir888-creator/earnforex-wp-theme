<?php
/**
 * 404 Template
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */

get_header(); ?>

<div class="wrapper" style="text-align:center;padding:4rem 1.5rem;">
    <h1 style="font-size:6rem;color:var(--color-primary);margin-bottom:1rem;">404</h1>
    <h2><?php esc_html_e('Page Not Found', 'earnforex-wp'); ?></h2>
    <p style="color:var(--color-text-light);margin:2rem 0;"><?php esc_html_e("Sorry, the page you're looking for doesn't exist or has been moved.", 'earnforex-wp'); ?></p>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn--primary btn--lg"><?php esc_html_e('Go Home', 'earnforex-wp'); ?></a>
    <div style="margin-top:2rem;">
        <?php get_search_form(); ?>
    </div>
</div>

<?php get_footer(); ?>