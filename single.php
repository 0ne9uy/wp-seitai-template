<?php

/**
 * noras original theme
 * Template Name: 投稿
 * @author: shirako
 * @link: https://norasinc.jp
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

if (!defined('ABSPATH')) exit;
include get_template_directory() . "/inc/link.php";

get_template_part("parts/header/header");
?>

<main class="ly_cont">

    <?php
    if (have_posts()) :
        while (have_posts()) : the_post(); // ループ開始
    ?>

            <article class="bl_post">
                <div class="bl_postHeader">
                    <h1 class="el_postTtl">
                        <?php the_title(); ?>
                    </h1>
                    <div class="el_postMeta">
                        <time datetime="<?php the_time('Y-m-d'); ?>"><?php the_time('Y.m.d'); ?></time>
                        <span class="el_postCat">
                            <?php the_category(', '); ?>
                        </span>
                    </div>
                </div>
                <div class="bl_postContent">
                    <?php the_content(); ?>
                </div>
            </article>

    <?php
        endwhile; // ループ終了
    endif;
    wp_reset_postdata(); // クエリのリセット
    ?>
</main>

<?php get_template_part("parts/footer/footer"); ?>