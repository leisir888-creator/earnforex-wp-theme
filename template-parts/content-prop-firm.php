<?php
/**
 * Prop Firm Content Template Part
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('prop-firm-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <a href="<?php the_permalink(); ?>" class="prop-firm-card__thumbnail">
            <?php the_post_thumbnail('efp-card-image'); ?>
        </a>
    <?php endif; ?>
    <div class="prop-firm-card__content">
        <?php if (has_category()) : ?>
            <div class="prop-firm-card__category">
                <?php the_category(' '); ?>
            </div>
        <?php endif; ?>
        <h3 class="prop-firm-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        <div class="prop-firm-card__excerpt">
            <?php the_excerpt(); ?>
        </div>
        <a href="<?php the_permalink(); ?>" class="btn btn--outline btn--sm"><?php esc_html_e('Read Review', 'earnforex-wp'); ?></a>
    </div>
</article>