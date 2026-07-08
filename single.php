<?php
/**
 * Single Post Template
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */

get_header(); ?>

<?php while (have_posts()) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('single-post wrapper'); ?>>
        <header class="page-header">
            <?php if (has_category()) : ?>
                <div class="post-meta">
                    <?php the_category(' '); ?>
                </div>
            <?php endif; ?>
            <h1><?php the_title(); ?></h1>
            <div class="post-meta">
                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo get_the_date(); ?></time>
                <?php if (get_the_author_meta('display_name')) : ?>
                    <span class="post-author"><?php the_author(); ?></span>
                <?php endif; ?>
            </div>
        </header>

        <?php if (has_post_thumbnail()) : ?>
            <div class="post-thumbnail">
                <?php the_post_thumbnail('efp-hero'); ?>
            </div>
        <?php endif; ?>

        <div class="post-content">
            <?php the_content(); ?>
        </div>

        <footer class="post-footer">
            <?php
            $tags = get_the_tags();
            if ($tags) :
            ?>
                <div class="post-tags">
                    <?php the_tags('<span class="tags-label">' . __('Tags:', 'earnforex-wp') . '</span> ', ', ', ''); ?>
                </div>
            <?php endif; ?>

            <?php
            $social_networks = ['twitter', 'facebook', 'linkedin'];
            foreach ($social_networks as $network) :
                $url = get_theme_mod("efp_social_{$network}");
                if ($url) :
            ?>
                <a href="<?php echo esc_url($url); ?>" class="post-share-link" target="_blank" rel="noopener">
                    <?php echo efp_get_social_icon($network); ?>
                </a>
            <?php
                endif;
            endforeach;
            ?>
        </footer>

        <?php if (comments_open() || get_comments_number()) : ?>
            <?php comments_template(); ?>
        <?php endif; ?>
    </article>

    <?php
    the_post_navigation([
        'prev_text' => '<span class="nav-label">' . __('Previous', 'earnforex-wp') . '</span> %title',
        'next_text' => '<span class="nav-label">' . __('Next', 'earnforex-wp') . '</span> %title',
    ]);
    ?>
<?php endwhile; ?>

<?php get_footer(); ?>