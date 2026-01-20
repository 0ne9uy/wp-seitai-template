<?php

/**
 * noras original theme
 * @author: shirako
 * @link: https://norasinc.jp
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 *     
 */

// デフォルトの投稿タイプにアーカイブページを追加
add_filter('register_post_type_args', function ($args, $post_type) {
    if ('post' == $post_type) {
        global $wp_rewrite;
        $archive_slug = 'column'; //URLスラッグ
        $args['label'] = '投稿'; //管理画面左ナビに「投稿」の代わりに表示される
        $args['has_archive'] = $archive_slug;
        $archive_slug = $wp_rewrite->root . $archive_slug;
        $feeds = '(' . trim(implode('|', $wp_rewrite->feeds)) . ')';
        add_rewrite_rule("{$archive_slug}/?$", "index.php?post_type={$post_type}", 'top');
        add_rewrite_rule("{$archive_slug}/feed/{$feeds}/?$", "index.php?post_type={$post_type}" . '&feed=$matches[1]', 'top');
        add_rewrite_rule("{$archive_slug}/{$feeds}/?$", "index.php?post_type={$post_type}" . '&feed=$matches[1]', 'top');
        add_rewrite_rule("{$archive_slug}/{$wp_rewrite->pagination_base}/([0-9]{1,})/?$", "index.php?post_type={$post_type}" . '&paged=$matches[1]', 'top');
    }
    return $args;
}, 10, 2);

// お客様の声（voice）カスタム投稿タイプ
function register_voice_post_type() {
    $args = array(
        'label' => 'お客様の声',
        'labels' => array(
            'name' => 'お客様の声',
            'singular_name' => 'お客様の声',
            'add_new' => '新規追加',
            'add_new_item' => '新しいお客様の声を追加',
            'edit_item' => 'お客様の声を編集',
            'new_item' => '新しいお客様の声',
            'view_item' => 'お客様の声を表示',
            'search_items' => 'お客様の声を検索',
            'not_found' => 'お客様の声が見つかりませんでした',
            'not_found_in_trash' => 'ゴミ箱にお客様の声はありませんでした',
        ),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'voice'),
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-format-quote',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest' => true,
    );
    register_post_type('voice', $args);
}
add_action('init', 'register_voice_post_type');

// 推薦者の声（recommend）カスタム投稿タイプ
function register_recommend_post_type() {
    $args = array(
        'label' => '推薦者の声',
        'labels' => array(
            'name' => '推薦者の声',
            'singular_name' => '推薦者の声',
            'add_new' => '新規追加',
            'add_new_item' => '新しい推薦者の声を追加',
            'edit_item' => '推薦者の声を編集',
            'new_item' => '新しい推薦者の声',
            'view_item' => '推薦者の声を表示',
            'search_items' => '推薦者の声を検索',
            'not_found' => '推薦者の声が見つかりませんでした',
            'not_found_in_trash' => 'ゴミ箱に推薦者の声はありませんでした',
        ),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'recommend'),
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-star-filled',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest' => true,
    );
    register_post_type('recommend', $args);
}
add_action('init', 'register_recommend_post_type');

// voice と recommend の個別ページへのアクセスを404にリダイレクト（一覧ページのみ表示）
function redirect_voice_recommend_single_to_404() {
    if (is_singular('voice') || is_singular('recommend')) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        include(get_query_template('404'));
        exit;
    }
}
add_action('template_redirect', 'redirect_voice_recommend_single_to_404');
