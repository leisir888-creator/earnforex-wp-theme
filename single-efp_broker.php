<?php
/**
 * Single Broker Template
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */

get_header(); ?>

<?php while (have_posts()) : the_post(); ?>
    <?php efp_output_broker_schema(); ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class('single-broker wrapper'); ?>>
        <header class="page-header">
            <div class="post-meta">
                <?php
                $categories = get_the_terms(get_the_ID(), 'efp_broker_category');
                if ($categories) :
                    foreach ($categories as $cat) :
                ?>
                    <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="post-category"><?php echo esc_html($cat->name); ?></a>
                <?php
                    endforeach;
                endif;
                ?>
            </div>
            <h1><?php the_title(); ?></h1>
            <div class="post-meta">
                <?php
                $rating = get_post_meta(get_the_ID(), 'broker_rating', true);
                $reviews = get_post_meta(get_the_ID(), 'broker_reviews', true);
                if ($rating) :
                ?>
                    <div class="broker-rating" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                        <?php echo efp_get_rating_stars($rating); ?>
                        <span class="rating-value" itemprop="ratingValue"><?php echo esc_html($rating); ?></span>
                        <span class="rating-count" itemprop="reviewCount">(<?php echo esc_html($reviews ?: '0'); ?> <?php esc_html_e('reviews', 'earnforex-wp'); ?>)</span>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <?php if (has_post_thumbnail()) : ?>
            <div class="post-thumbnail">
                <?php the_post_thumbnail('efp-hero'); ?>
            </div>
        <?php endif; ?>

        <div class="broker-summary">
            <div class="wrapper">
                <div class="broker-summary__grid">
                    <div class="broker-summary__main">
                        <div class="broker-summary__description">
                            <?php the_content(); ?>
                        </div>
                    </div>

                    <aside class="broker-summary__sidebar">
                        <div class="broker-card brokers-list__card">
                            <div class="broker-card__header">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('efp-broker-logo', ['class' => 'broker-card__logo']); ?>
                                <?php endif; ?>
                                <h3><?php the_title(); ?></h3>
                                <?php if ($rating) : ?>
                                    <div class="broker-card__rating">
                                        <?php echo efp_get_rating_stars($rating); ?>
                                        <span class="rating-value"><?php echo esc_html($rating); ?></span>
                                        <span class="rating-count">(<?php echo esc_html($reviews ?: '0'); ?>)</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="broker-card__body">
                                <?php
                                $fields = [
                                    'broker_founded' => ['label' => __('Founded', 'earnforex-wp'), 'icon' => 'calendar'],
                                    'broker_headquarters' => ['label' => __('Headquarters', 'earnforex-wp'), 'icon' => 'map-pin'],
                                    'broker_regulation' => ['label' => __('Regulation', 'earnforex-wp'), 'icon' => 'shield'],
                                    'broker_min_deposit' => ['label' => __('Min. Deposit', 'earnforex-wp'), 'icon' => 'dollar-sign'],
                                    'broker_max_leverage' => ['label' => __('Max Leverage', 'earnforex-wp'), 'icon' => 'arrow-up'],
                                    'broker_spread_type' => ['label' => __('Spread Type', 'earnforex-wp'), 'icon' => 'percent'],
                                    'broker_avg_spread' => ['label' => __('Avg Spread (EUR/USD)', 'earnforex-wp'), 'icon' => 'chart-line'],
                                    'broker_commission' => ['label' => __('Commission', 'earnforex-wp'), 'icon' => 'credit-card'],
                                ];

                                foreach ($fields as $key => $field) :
                                    $value = get_post_meta(get_the_ID(), $key, true);
                                    if ($value) :
                                ?>
                                    <div class="broker-detail">
                                        <span class="broker-detail__label"><?php echo esc_html($field['label']); ?></span>
                                        <span class="broker-detail__value"><?php echo esc_html($value); ?></span>
                                    </div>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </div>

                            <div class="broker-card__footer">
                                <?php
                                $affiliate_link = get_post_meta(get_the_ID(), 'broker_affiliate_link', true);
                                $website = get_post_meta(get_the_ID(), 'broker_website', true);
                                $link = $affiliate_link ?: $website ?: get_permalink();
                                ?>
                                <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener" class="btn btn--primary btn--lg" style="width:100%;">
                                    <?php esc_html_e('Visit Broker', 'earnforex-wp'); ?>
                                </a>
                                <?php if ($website && $affiliate_link) : ?>
                                    <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener" class="btn btn--outline btn--sm" style="width:100%;margin-top:0.5rem;">
                                        <?php esc_html_e('Official Website', 'earnforex-wp'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="widget">
                            <h3 class="widget-title"><?php esc_html_e('Quick Links', 'earnforex-wp'); ?></h3>
                            <ul>
                                <li><a href="#reviews"><?php esc_html_e('Read Reviews', 'earnforex-wp'); ?></a></li>
                                <li><a href="#trading-conditions"><?php esc_html_e('Trading Conditions', 'earnforex-wp'); ?></a></li>
                                <li><a href="#platforms"><?php esc_html_e('Platforms', 'earnforex-wp'); ?></a></li>
                                <li><a href="#deposit-withdrawal"><?php esc_html_e('Deposit & Withdrawal', 'earnforex-wp'); ?></a></li>
                            </ul>
                        </div>
                    </aside>
                </div>
            </div>
        </div>

        <div class="wrapper">
            <?php if (comments_open() || get_comments_number()) : ?>
                <section id="reviews" class="broker-reviews">
                    <?php comments_template(); ?>
                </section>
            <?php endif; ?>
        </div>
    </article>
<?php endwhile; ?>

<?php get_footer(); ?>