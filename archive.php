<?php

/**
 * noras original theme
 * Archive Template: コラム（投稿）一覧
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
    <section id="column" class="ly_cont">
        <div class="bl_content">
            <?php
            $args = array(
                'post_type' => 'post',
                'posts_per_page' => 16,
                'orderby' => 'date',
                'order' => 'DESC',
            );
            $the_query = new WP_Query($args);
            if ($the_query->have_posts()) :
                while ($the_query->have_posts()) : $the_query->the_post();
            ?>
                    <article class="bl_column">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail(); ?>
                            <?php else : ?>
                                <img src="<?= esc_url(get_template_directory_uri()); ?>/assets/img/no-image.png" alt="">
                            <?php endif; ?>
                            <p><?php the_title(); ?></p>
                        </a>
                    </article>
            <?php
                endwhile;
            endif;
            wp_reset_postdata();
            ?>
        </div>

    </section>
</main>


<?php get_template_part("parts/footer/footer"); ?>