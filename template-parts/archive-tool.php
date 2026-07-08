<?php
/**
 * Tool Archive Template Part
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */
?>
<div class="tools-archive wrapper">
    <div class="tools__grid">
        <?php
        if (have_posts()) :
            while (have_posts()) : the_post();
                get_template_part('template-parts/content', 'tool');
            endwhile;
        else :
            echo '<p class="no-results">' . __('No tools found.', 'earnforex-wp') . '</p>';
        endif;
        ?>
    </div>
    <?php the_posts_navigation(); ?>
</div>