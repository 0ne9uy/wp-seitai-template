<?php

/**
 * noras original theme
 * @author: shirako
 * @link: https://norasinc.jp
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

require get_template_directory() . '/inc/theme-support.php';
require get_template_directory() . '/inc/custom-post-types.php';
require get_template_directory() . '/inc/my-ogp.php';
require get_template_directory() . '/inc/enque-my-styles.php';
require get_template_directory() . '/inc/enque-my-scripts.php';

// スタブファイル（プラグイン未インストール時のエラー回避）
if (!function_exists('get_field')) {
    require get_template_directory() . '/stubs/get_field.php';
}
if (!function_exists('wp_pagenavi')) {
    require get_template_directory() . '/stubs/wp-pagenavi.php';
}
