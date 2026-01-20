<?php

/**
 * noras original theme
 * Template Name: お問い合わせ
 * @author: shirako
 * @link: https://norasinc.jp
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

if (!defined('ABSPATH')) exit;
include get_template_directory() . "/inc/link.php";

get_template_part("parts/header/header");

?>

<main class="ly_cont">
    <div class="bl_contactformWrap">
        <?= do_shortcode('[contact-form-7 id="cb3bf6e" title="お問い合わせフォーム"]') ?>
    </div>
</main>

<?php get_template_part("parts/footer/footer"); ?>