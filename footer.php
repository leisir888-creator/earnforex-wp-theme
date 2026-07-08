<?php
/**
 * Footer Template
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */
?>
</main><!-- #main-content -->

<footer class="footer" role="contentinfo">
    <div class="footer__main wrapper">
        <div class="footer__main-info">
            <?php if (has_custom_logo()) : ?>
                <div class="footer__logo"><?php the_custom_logo(); ?></div>
            <?php else : ?>
                <div class="footer__logo">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="footer__logo-link">
                        <span class="footer__logo-text"><?php bloginfo('name'); ?></span>
                    </a>
                </div>
            <?php endif; ?>

            <p class="footer__description"><?php bloginfo('description'); ?></p>

            <div class="footer__social">
                <?php
                $social_networks = ['twitter' => 'Twitter', 'facebook' => 'Facebook', 'linkedin' => 'LinkedIn', 'youtube' => 'YouTube', 'telegram' => 'Telegram'];
                foreach ($social_networks as $key => $label) :
                    $url = get_theme_mod("efp_social_{$key}");
                    if ($url) :
                ?>
                    <a href="<?php echo esc_url($url); ?>" class="footer__social-link" aria-label="<?php echo esc_attr($label); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo efp_get_social_icon($key); ?>
                    </a>
                <?php
                    endif;
                endforeach;
                ?>
            </div>
        </div>

        <div class="footer__column">
            <?php dynamic_sidebar('footer-1'); ?>
        </div>

        <div class="footer__column">
            <?php dynamic_sidebar('footer-2'); ?>
        </div>

        <div class="footer__column">
            <?php dynamic_sidebar('footer-3'); ?>
        </div>
    </div>

    <div class="footer__bottom wrapper">
        <p class="footer__copyright">
            <?php echo wp_kses_post(get_theme_mod('efp_footer_copyright', sprintf(__('&copy; %s %s. All rights reserved.', 'earnforex-wp'), date('Y'), get_bloginfo('name')))); ?>
        </p>

        <nav class="footer__legal" aria-label="<?php esc_attr_e('Legal links', 'earnforex-wp'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => 'footer',
                'container'      => false,
                'menu_class'     => '',
                'echo'           => true,
                'fallback_cb'    => false,
                'items_wrap'     => '%3$s',
            ]);
            ?>
        </nav>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>