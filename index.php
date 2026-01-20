<?php

/**
 * noras original theme
 * Index Template: フォールバックテンプレート（最新投稿一覧）
 * @author: shirako
 * @link: https://norasinc.jp
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 *
 * このテンプレートは、他のテンプレートがマッチしない場合のフォールバックとして使用されます。
 * 通常は front-page.php（トップページ）または archive.php（アーカイブ）が使用されます。
 */

if (!defined('ABSPATH')) exit;
include get_template_directory() . "/inc/link.php";

// ヘッダーを取得
get_template_part("parts/header/header");
?>

<main class="ly_subp">
    <section id="posts" class="ly_cont">
        <div class="bl_content">
            <?php
            if (have_posts()) :
                while (have_posts()) : the_post();
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
            else :
            ?>
                <p>投稿がありません。</p>
            <?php
            endif;
            ?>
        </div>
    </section>
</main>

<?php get_template_part("parts/footer/footer"); ?>
