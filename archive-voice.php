<?php

/**
 * noras original theme
 * Archive Template: お客様の声
 * @author: shirako
 * @link: https://norasinc.jp
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

if (!defined('ABSPATH')) exit;
include get_template_directory() . "/inc/link.php";

// ヘッダーを取得
get_template_part("parts/header/header");
?>

<main class="ly_subp">
    <section id="voice" class="ly_cont">
        <div class="bl_content">
            <?php
            $args = array(
                'post_type' => 'voice',
                'posts_per_page' => 16,
                'orderby' => 'date',
                'order' => 'DESC',
            );
            $the_query = new WP_Query($args);
            if ($the_query->have_posts()) :
                while ($the_query->have_posts()) : $the_query->the_post();
            ?>
                    <article class="bl_column">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail(); ?>
                        <?php else : ?>
                            <img src="<?= esc_url(get_template_directory_uri()); ?>/assets/img/no-image.png" alt="">
                        <?php endif; ?>
                        <div class="bl_column_content">
                            <h3 class="bl_column_title"><?php the_title(); ?></h3>
                            <?php if (has_excerpt()) : ?>
                                <p class="bl_column_excerpt"><?php the_excerpt(); ?></p>
                            <?php endif; ?>
                        </div>
                    </article>
            <?php
                endwhile;
            else :
            ?>
                <p>お客様の声はまだありません。</p>
            <?php
            endif;
            wp_reset_postdata();
            ?>
        </div>
    </section>
</main>

<?php get_template_part("parts/footer/footer"); ?>
