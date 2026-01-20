<?php

/**
 * noras original theme
 * @author: shirako
 * @link: https://norasinc.jp
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 *     
 */


// OGP設定
function my_meta_ogp()

{
    $uri = get_template_directory_uri();

    /*初期設定*/
    // 画像 （アイキャッチ画像が無い時に使用する代替画像URL）
    $ogp_image = "$uri/assets/img/no-image.png";
    // Twitterのアカウント名 (@xxx)
    $twitter_site = '@norasinc';
    // Twitterカードの種類（summary_large_image または summary を指定）
    $twitter_card = 'summary_large_image';
    // Facebook APP ID
    $facebook_app_id = '';

    /*初期設定 ここまで*/

    global $post;
    $ogp_title = '';
    $ogp_description = '';
    $ogp_url = '';
    $html = '';
    if (is_singular()) {
        // 記事＆固定ページ
        setup_postdata($post);
        $ogp_title = $post->post_title;
        $ogp_description = mb_substr(get_the_excerpt(), 0, 100);
        $ogp_url = get_permalink();
        wp_reset_postdata();
    } else {
        // トップページ
        $ogp_title = get_bloginfo('name');
        $ogp_description = get_bloginfo('description');
        $ogp_url = home_url();
    }

    // og:type
    $ogp_type = (is_front_page() || is_home()) ? 'website' : 'article';

    // og:image
    if (is_singular() && has_post_thumbnail()) {
        $ps_thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
        $ogp_image = $ps_thumb[0];
    }

    // 出力するOGPタグをまとめる
    $html = "\n";
    $html .= '<meta property="og:title" content="' . esc_attr($ogp_title) . '">' . "\n";
    $html .= '<meta property="og:description" content="' . esc_attr($ogp_description) . '">' . "\n";
    $html .= '<meta property="og:type" content="' . $ogp_type . '">' . "\n";
    $html .= '<meta property="og:url" content="' . esc_url($ogp_url) . '">' . "\n";
    $html .= '<meta property="og:image" content="' . esc_url($ogp_image) . '">' . "\n";
    $html .= '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    $html .= '<meta name="twitter:card" content="' . $twitter_card . '">' . "\n";
    $html .= '<meta name="twitter:site" content="' . $twitter_site . '">' . "\n";
    $html .= '<meta property="og:locale" content="ja_JP">' . "\n";

    if ($facebook_app_id != "") {
        $html .= '<meta property="fb:app_id" content="' . $facebook_app_id . '">' . "\n";
    }

    echo $html;
}
// headタグ内にOGPを出力する
add_action('wp_head', 'my_meta_ogp');
