<?php

/**
 * noras original theme
 * @author: shirako
 * @link: https://norasinc.jp
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

if (!defined('ABSPATH')) exit;
include get_template_directory() . "/inc/link.php";

get_template_part("parts/header/header");
?>

<main class="">
    <section id="fv" class="ly_fv">
        <div class="ly_cont">
            <div class="bl_fvCont">
                <h1 class="bl_fvCont_ttl hp_txtCenter">
                    Thanks for using noras original theme ! 🥳
                </h1>
                <div class="bl_fvContExp">
                    <a href="https://norasinc.notion.site/27c1410a8a964240bb8ad8a11903f771?pvs=4" class="el_btn el_btn_withIcon" target="_blank">
                        document
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="el_btn_withIcon_img">
                            <path d="M15 3h6v6" />
                            <path d="M10 14 21 3" />
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                        </svg>
                    </a>
                    <a href="<?= $contact; ?>" class="el_btn">
                        Contact
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_template_part("parts/footer/footer"); ?>

