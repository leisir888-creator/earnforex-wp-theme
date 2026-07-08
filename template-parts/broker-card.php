<?php
/**
 * Broker Card Template Part
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */

$rating = get_post_meta(get_the_ID(), 'broker_rating', true);
$reviews = get_post_meta(get_the_ID(), 'broker_reviews', true);
$spread = get_post_meta(get_the_ID(), 'broker_avg_spread', true);
$spread_type = get_post_meta(get_the_ID(), 'broker_spread_type', true);
$founded = get_post_meta(get_the_ID(), 'broker_founded', true);
$regulation = get_post_meta(get_the_ID(), 'broker_regulation', true);
$platforms = get_post_meta(get_the_ID(), 'broker_platforms', true);
$affiliate_link = get_post_meta(get_the_ID(), 'broker_affiliate_link', true);
$permalink = get_permalink();
$link = $affiliate_link ?: $permalink;
?>
<article class="brokers-list__card" itemscope itemtype="https://schema.org/FinancialService">
    <header class="broker-card__header">
        <?php if (has_post_thumbnail()) : ?>
            <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener" class="broker-card__logo-link">
                <?php the_post_thumbnail('efp-broker-logo', ['class' => 'broker-card__logo', 'itemprop' => 'image']); ?>
            </a>
        <?php endif; ?>
        <h3 class="broker-card__name" itemprop="name">
            <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener"><?php the_title(); ?></a>
        </h3>
        <?php if ($rating) : ?>
            <div class="broker-card__rating" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                <div class="rating-stars" aria-hidden="true">
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="<?php echo $i <= $rating ? '#F6AD55' : '#E2E8F0'; ?>" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <?php endfor; ?>
                </div>
                <span class="rating-value" itemprop="ratingValue"><?php echo esc_html($rating); ?></span>
                <span class="rating-count" itemprop="reviewCount">(<?php echo esc_html($reviews ?: '0'); ?>)</span>
            </div>
        <?php endif; ?>
    </header>

    <div class="broker-card__body">
        <div class="broker-card__features">
            <?php if ($founded) : ?>
                <span class="feature-tag" title="<?php esc_attr_e('Year Founded', 'earnforex-wp'); ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <?php echo esc_html($founded); ?>
                </span>
            <?php endif; ?>
            <?php if ($spread_type) : ?>
                <span class="feature-tag" title="<?php esc_attr_e('Spread Type', 'earnforex-wp'); ?>">
                    <?php echo esc_html(ucfirst($spread_type)); ?>
                </span>
            <?php endif; ?>
            <?php if ($regulation) : ?>
                <span class="feature-tag" title="<?php esc_attr_e('Regulated', 'earnforex-wp'); ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Regulated
                </span>
            <?php endif; ?>
        </div>
    </div>

    <footer class="broker-card__footer">
        <?php if ($spread) : ?>
            <div class="broker-card__spread">
                <span class="broker-card__spread-label"><?php esc_html_e('From', 'earnforex-wp'); ?></span>
                <span class="broker-card__spread-value"><?php echo esc_html($spread); ?> pips</span>
            </div>
        <?php endif; ?>
        <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener" class="btn btn--primary btn--sm">
            <?php esc_html_e('Visit Broker', 'earnforex-wp'); ?>
        </a>
    </footer>
</article>