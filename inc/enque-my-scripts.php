<?php

/**
 * noras original theme
 * @author: shirako
 * @link: https://norasinc.jp
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 *     
 */

function enqueue_my_scripts()
{
    $uri = get_template_directory_uri();

    // 全ページで読み込むJS
    wp_enqueue_script('index-js', $uri . '/assets/js/index.js');

    // TOPページ以外で読み込むJS
    if (!is_front_page()) {
        wp_enqueue_script('subp-js', $uri . '/assets/js/subp.js');
    }
}
add_action('wp_enqueue_scripts', 'enqueue_my_scripts');
