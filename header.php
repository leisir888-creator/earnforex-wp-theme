<?php
/**
 * Header Template
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, shrink-to-fit=no">
    <meta name="format-detection" content="telephone=no">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main-content"><?php esc_html_e('Skip to main content', 'earnforex-wp'); ?></a>

<header class="header" role="banner">
    <div class="header__top wrapper">
        <div class="header__logo">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="logo" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
                    <span class="logo__text"><?php bloginfo('name'); ?></span>
                </a>
            <?php endif; ?>
        </div>

        <div class="header__menu_content">
            <nav class="menu" role="navigation" aria-label="<?php esc_attr_e('Primary Menu', 'earnforex-wp'); ?>">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'menu_class'     => 'menu__list',
                    'container'      => false,
                    'fallback_cb'    => 'efp_fallback_menu',
                    'walker'         => new EFP_Menu_Walker(),
                ]);
                ?>
            </nav>

            <?php if (get_theme_mod('efp_header_search', true)) : ?>
                <div class="expanding-search" role="search">
                    <form class="expanding-search__field" action="<?php echo esc_url(home_url('/')); ?>" method="get">
                        <label for="header-search" class="visually-hidden"><?php esc_html_e('Search brokers', 'earnforex-wp'); ?></label>
                        <input
                            type="search"
                            id="header-search"
                            class="expanding-search__input"
                            name="s"
                            placeholder="<?php esc_attr_e('Search for brokers', 'earnforex-wp'); ?>"
                            value="<?php echo get_search_query(); ?>"
                            autocomplete="off"
                        >
                        <button type="submit" class="expanding-search__submit" aria-label="<?php esc_attr_e('Search', 'earnforex-wp'); ?>">
                            <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M16.2552 14.8177L12.4206 10.9831C13.3711 9.82422 13.944 8.33984 13.944 6.722C13.944 3.01107 10.9329 0 7.222 0C3.50781 0 0.5 3.01107 0.5 6.722C0.5 10.4329 3.50781 13.444 7.222 13.444C8.83984 13.444 10.321 12.8743 11.4798 11.9238L15.3145 15.7552C15.5749 16.0156 15.9948 16.0156 16.2552 15.7552C16.5156 15.498 16.5156 15.0749 16.2552 14.8177ZM7.222 12.1061C4.25 12.1061 1.83464 9.69075 1.83464 6.722C1.83464 3.75326 4.25 1.33464 7.222 1.33464C10.1908 1.33464 12.6094 3.75326 12.6094 6.722C12.6094 9.69075 10.1908 12.1061 7.222 12.1061Z" fill="#2D3748"></path>
                            </svg>
                        </button>
                    </form>
                    <div class="expanding-search__link">
                        <a href="<?php echo esc_url(home_url('/search-forex-brokers/')); ?>" class="link"><?php esc_html_e('Switch to advanced search', 'earnforex-wp'); ?></a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="header__user_actions">
            <?php if (is_user_logged_in()) : ?>
                <div class="user-menu">
                    <button class="user-menu__trigger" aria-expanded="false" aria-haspopup="true" aria-label="<?php esc_attr_e('User menu', 'earnforex-wp'); ?>">
                        <span class="user-avatar">
                            <?php echo get_avatar(get_current_user_id(), 28); ?>
                        </span>
                        <span class="user-name"><?php echo wp_get_current_user()->display_name; ?></span>
                        <svg class="user-menu__arrow" width="16" height="8" viewBox="0 5 20 10" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M10.0006 10.9766L14.1256 6.85156L15.3039 8.0299L10.0006 13.3332L4.69727 8.0299L5.8756 6.85156L10.0006 10.9766Z" fill="#001A38"></path>
                        </svg>
                    </button>
                    <div class="user-menu__dropdown" role="menu">
                        <a href="<?php echo esc_url(get_dashboard_url()); ?>" class="user-menu__item" role="menuitem">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            <?php esc_html_e('Dashboard', 'earnforex-wp'); ?>
                        </a>
                        <a href="<?php echo esc_url(get_edit_profile_url()); ?>" class="user-menu__item" role="menuitem">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <?php esc_html_e('Profile', 'earnforex-wp'); ?>
                        </a>
                        <?php if (current_user_can('edit_posts')) : ?>
                            <a href="<?php echo esc_url(admin_url('post-new.php')); ?>" class="user-menu__item" role="menuitem">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                <?php esc_html_e('Add New Post', 'earnforex-wp'); ?>
                            </a>
                        <?php endif; ?>
                        <hr class="user-menu__divider">
                        <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="user-menu__item user-menu__item--logout" role="menuitem">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            <?php esc_html_e('Logout', 'earnforex-wp'); ?>
                        </a>
                    </div>
                </div>
            <?php else : ?>
                <div class="auth-links">
                    <a href="<?php echo esc_url(wp_login_url()); ?>" class="btn btn--outline btn--sm"><?php esc_html_e('Login', 'earnforex-wp'); ?></a>
                    <a href="<?php echo esc_url(wp_registration_url()); ?>" class="btn btn--primary btn--sm"><?php esc_html_e('Register', 'earnforex-wp'); ?></a>
                </div>
            <?php endif; ?>
        </div>

        <button class="menu-toggle" aria-expanded="false" aria-controls="primary-menu" aria-label="<?php esc_attr_e('Toggle menu', 'earnforex-wp'); ?>">
            <span class="menu-toggle__icon" aria-hidden="true"></span>
        </button>
    </div>
</header>

<?php if (is_front_page() && has_header_image()) : ?>
    <div class="header-banner">
        <img src="<?php header_image(); ?>" alt="" class="header-banner__img">
    </div>
<?php endif; ?>

<main id="main-content" class="main" role="main">