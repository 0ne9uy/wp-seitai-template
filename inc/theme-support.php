<?php

/**
 * noras original theme
 * @author: shirako
 * @link: https://norasinc.jp
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 *     
 */

// サムネイルをサポート
add_theme_support('post-thumbnails');
// カスタムメニューをサポート
add_theme_support('menus');
// タイトルタグをサポート
add_theme_support('title-tag');
// ウィジェットをサポート
function my_theme_widgets_init()
{
    register_sidebar(array(
        'name' => 'Main Sidebar',
        'id' => 'main-sidebar',
    ));
}
add_action('widgets_init', 'my_theme_widgets_init');
