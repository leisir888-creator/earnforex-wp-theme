<?php
/**
 * Broker Archive Template Part
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */
?>
<div class="brokers-archive">
    <!-- Search & Filter Bar -->
    <div class="brokers__search">
        <div class="expanding-search">
            <form class="expanding-search__field" role="search" id="broker-search-form">
                <label for="broker-search" class="visually-hidden"><?php esc_html_e('Search brokers', 'earnforex-wp'); ?></label>
                <input
                    type="search"
                    id="broker-search"
                    class="expanding-search__input"
                    placeholder="<?php esc_attr_e('Search for brokers', 'earnforex-wp'); ?>"
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
    </div>

    <!-- Filter Sidebar (Mobile: Collapsible) -->
    <div class="brokers-filter" id="brokers-filter">
        <button class="brokers-filter__toggle" aria-expanded="false" aria-controls="filter-panel">
            <svg width="18" height="14" viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M0 0H18V2H0V0ZM0 6H14V8H0V6ZM0 12H10V14H0V12Z" fill="#2D3748"></path>
            </svg>
            <span><?php esc_html_e('Filters', 'earnforex-wp'); ?></span>
        </button>
        <div class="brokers-filter__panel" id="filter-panel" hidden>
            <div class="brokers-filter__section">
                <h4><?php esc_html_e('Categories', 'earnforex-wp'); ?></h4>
                <?php
                $categories = get_terms(['taxonomy' => 'efp_broker_category', 'hide_empty' => true]);
                if ($categories) :
                ?>
                    <ul class="brokers-filter__list">
                        <?php foreach ($categories as $cat) : ?>
                            <li>
                                <label>
                                    <input type="checkbox" name="category" value="<?php echo esc_attr($cat->slug); ?>">
                                    <span><?php echo esc_html($cat->name); ?> <span class="count">(<?php echo $cat->count; ?>)</span></span>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="brokers-filter__section">
                <h4><?php esc_html_e('Features', 'earnforex-wp'); ?></h4>
                <?php
                $features = get_terms(['taxonomy' => 'efp_broker_feature', 'hide_empty' => true]);
                if ($features) :
                ?>
                    <ul class="brokers-filter__list">
                        <?php foreach ($features as $feature) : ?>
                            <li>
                                <label>
                                    <input type="checkbox" name="feature[]" value="<?php echo esc_attr($feature->slug); ?>">
                                    <span><?php echo esc_html($feature->name); ?> <span class="count">(<?php echo $feature->count; ?>)</span></span>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <button type="button" class="btn btn--secondary btn--sm brokers-filter__reset"><?php esc_html_e('Reset Filters', 'earnforex-wp'); ?></button>
        </div>
    </div>

    <!-- Results -->
    <div class="brokers__results">
        <div class="brokers-list__sorting">
            <div class="brokers-list__sorting-btns">
                <button type="button" class="brokers-list__sorting-open-btn" aria-expanded="false" aria-controls="sorting-list">
                    <svg width="18" height="14" viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M0 0H18V2H0V0ZM0 6H14V8H0V6ZM0 12H10V14H0V12Z" fill="#2D3748"></path>
                    </svg>
                    <span><?php esc_html_e('Sorting', 'earnforex-wp'); ?></span>
                </button>
                <button type="button" class="brokers-list__sorting-close-btn" hidden> × </button>
            </div>
            <div class="brokers-list__sorting-list" id="sorting-list" hidden>
                <button type="button" class="brokers-list__sorting-item --btn" data-sort="date"><?php esc_html_e('Newest First', 'earnforex-wp'); ?></button>
                <button type="button" class="brokers-list__sorting-item --btn --asc" data-sort="rating_asc"><?php esc_html_e('Rating: Low to High', 'earnforex-wp'); ?></button>
                <button type="button" class="brokers-list__sorting-item --btn --desc" data-sort="rating_desc"><?php esc_html_e('Rating: High to Low', 'earnforex-wp'); ?></button>
                <button type="button" class="brokers-list__sorting-item --btn" data-sort="spread_asc"><?php esc_html_e('Spread: Low to High', 'earnforex-wp'); ?></button>
                <button type="button" class="brokers-list__sorting-item --btn" data-sort="reviews_desc"><?php esc_html_e('Most Reviews', 'earnforex-wp'); ?></button>
            </div>
        </div>

        <div id="brokers-list" class="brokers-list">
            <div class="brokers-list__grid">
                <?php
                if (have_posts()) :
                    while (have_posts()) : the_post();
                        get_template_part('template-parts/broker', 'card');
                    endwhile;
                else :
                    echo '<p class="no-results">' . __('No brokers found.', 'earnforex-wp') . '</p>';
                endif;
                ?>
            </div>

            <?php
            // Load More Button
            global $wp_query;
            if ($wp_query->max_num_pages > 1) :
            ?>
                <div class="brokers-list__load-more">
                    <button type="button" class="btn btn--outline btn--lg" data-page="2" data-max-pages="<?php echo $wp_query->max_num_pages; ?>">
                        <?php esc_html_e('Load More Brokers', 'earnforex-wp'); ?>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>