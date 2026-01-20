<?php

/**
 * noras original theme
 * @author: shirako
 * @link: https://norasinc.jp
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

include get_template_directory() . "/inc/link.php";

?>
<!DOCTYPE html>
<html lang="ja" class="html">

<head prefix="og: http://ogp.me/ns# website: http://ogp.me/ns/website#">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo wp_get_document_title(); ?></title>
    <meta name="description" content="<?php echo wp_get_document_title(); ?>">
    <link rel="shortcut icon" href="<?php echo $uri; ?>/favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $uri; ?>/assets/img/favicon.png" />
    <!-- <script defer src="<?php echo $uri; ?>/assets/js/index.js" type="module"></script> -->
    <?php wp_head(); ?>
</head>

<body>
    <header class="ly_header hp_borderYellow">
        <div class="bl_headerCont">
            <a href="<?= $home; ?>" class="bl_header_logo">
                noras template
            </a>
            <div class="bl_header_nav">
                <ul class="bl_header_nav_ul">
                    <li class="bl_header_nav_ul_li"><a href='<?= $home  ?>'>TOP</a></li>
                    <li class="bl_header_nav_ul_li"><a href='<?= $service; ?>'>サービス</a></li>
                    <li class="bl_header_nav_ul_li"><a href='<?= $column  ?>'>コラム</a></li>
                    <li class="bl_header_nav_ul_li"><a href='<?= $contact  ?>'>お問い合わせ</a></li>
                </ul>
            </div>
            <div class="sm_only">
                <div class="el_humb  js_humb">

                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </header>

    <?php echo get_template_part("parts/header/navigation") ?>