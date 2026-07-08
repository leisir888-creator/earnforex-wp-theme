<?php
/**
 * Prop Firm Archive Template Part
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */
?>
<div class="prop-firms-archive wrapper">
    <div class="prop-firms__grid">
        <?php
        if (have_posts()) :
            while (have_posts()) : the_post();
                get_template_part('template-parts/content', 'prop-firm');
            endwhile;
        else :
            echo '<p class="no-results">' . __('No prop firms found.', 'earnforex-wp'); . '</p>';
        endif;
        ?>
    </div>
    <?php the_posts_navigation(); ?>
</div>