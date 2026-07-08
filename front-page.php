<?php
/**
 * Front Page Template
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */

get_header(); ?>

<?php
// Hero Section
$hero_title = get_theme_mod('efp_hero_title', __('Forex Trading Information', 'earnforex-wp'));
$hero_subtitle = get_theme_mod('efp_hero_subtitle', __('Compare brokers, tools, and education for successful trading', 'earnforex-wp'));
$hero_cta_text = get_theme_mod('efp_hero_cta_text', __('View All Brokers', 'earnforex-wp'));
$hero_cta_url = get_theme_mod('efp_hero_cta_url', home_url('/brokers/'));
?>

<section class="hero-section">
    <div class="hero-section__bg" aria-hidden="true"></div>
    <div class="wrapper hero-section__content">
        <h1 class="hero-section__title"><span class="accent"><?php echo esc_html($hero_title); ?></span></h1>
        <p class="hero-section__subtitle"><?php echo esc_html($hero_subtitle); ?></p>
        <div class="hero-section__cta">
            <a href="<?php echo esc_url($hero_cta_url); ?>" class="btn btn--primary btn--lg"><?php echo esc_html($hero_cta_text); ?></a>
        </div>
    </div>
</section>

<?php
// Main Content Blocks
$sections = [
    'brokers' => [
        'title' => __('Forex Brokers', 'earnforex-wp'),
        'description' => __('Find and compare the best Forex brokers', 'earnforex-wp'),
        'url' => home_url('/brokers/'),
        'cta' => __('View All Brokers', 'earnforex-wp'),
        'post_type' => 'efp_broker',
    ],
    'tools' => [
        'title' => __('Forex Tools', 'earnforex-wp'),
        'description' => __('Calculators, strategies, and reports', 'earnforex-wp'),
        'url' => home_url('/tools/'),
        'cta' => __('View All Tools', 'earnforex-wp'),
        'post_type' => 'efp_tool',
    ],
    'prop_firms' => [
        'title' => __('Prop Firms', 'earnforex-wp'),
        'description' => __('Best proprietary trading firms', 'earnforex-wp'),
        'url' => home_url('/prop-firms/'),
        'cta' => __('View All Firms', 'earnforex-wp'),
        'post_type' => 'efp_prop_firm',
    ],
    'education' => [
        'title' => __('Education', 'earnforex-wp'),
        'description' => __('Learn Forex trading from experts', 'earnforex-wp'),
        'url' => home_url('/education/'),
        'cta' => __('Read Articles', 'earnforex-wp'),
        'post_type' => 'post',
        'category' => 'education',
    ],
];

foreach ($sections as $key => $section) :
    $args = [
        'post_type'      => $section['post_type'],
        'posts_per_page' => 4,
        'post_status'    => 'publish',
    ];
    if (isset($section['category'])) {
        $args['category_name'] = $section['category'];
    }
    $query = new WP_Query($args);
    if ($query->have_posts()) :
?>
    <section class="section-block" id="<?php echo esc_attr($key); ?>">
        <div class="wrapper">
            <header class="section-block__header">
                <h2 class="section-block__title"><?php echo esc_html($section['title']); ?></h2>
                <p class="section-block__description"><?php echo esc_html($section['description']); ?></p>
            </header>
            <div class="section-block__grid">
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php get_template_part('template-parts/content', $section['post_type'] === 'post' ? 'post' : $section['post_type']); ?>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>
            <div class="section-block__cta">
                <a href="<?php echo esc_url($section['url']); ?>" class="btn btn--outline"><?php echo esc_html($section['cta']); ?></a>
            </div>
        </div>
    </section>
<?php
    endif;
endforeach;
?>

<?php get_footer(); ?>