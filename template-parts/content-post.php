<?php
/**
 * Post Content Template Part
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <a href="<?php the_permalink(); ?>" class="post-card__thumbnail">
            <?php the_post_thumbnail('efp-card-image'); ?>
        </a>
    <?php endif; ?>
    <div class="post-card__content">
        <?php if (has_category()) : ?>
            <div class="post-card__category">
                <?php the_category(' '); ?>
            </div>
        <?php endif; ?>
        <h3 class="post-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        <div class="post-card__excerpt">
            <?php the_excerpt(); ?>
        </div>
        <div class="post-card__meta">
            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo get_the_date(); ?></time>
        </div>
    </div>
</article>