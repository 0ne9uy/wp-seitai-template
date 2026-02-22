<?php

/**
 * noras original theme
 * @author: shirako
 * @link: https://norasinc.jp
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

function enqueue_my_styles()
{
    $uri = get_template_directory_uri();

    // 全ページで読み込むCSS
    wp_enqueue_style('base-css', $uri . '/assets/css/base.css');
    wp_enqueue_style('shop-card-css', $uri . '/assets/css/shop-card.css');
    wp_enqueue_style('shop-modal-css', $uri . '/assets/css/components/shop-modal.css');

    // ========================================
    // コンポーネントCSS
    // ========================================

    // 症例報告カード・ビフォーアフタースライダー
    $needs_case_components = is_front_page()
        || is_post_type_archive('case')
        || is_singular('case')
        || is_singular('shop')
        || is_singular('symptoms')
        || is_page('first');

    if ($needs_case_components) {
        wp_enqueue_style('case-card-css', $uri . '/assets/css/components/case-card.css');
        wp_enqueue_style('beer-slider-css', $uri . '/assets/css/components/beer-slider.css');
    }

    // FAQ項目
    $needs_faq_item = is_page('faq')
        || is_singular('symptoms')
        || is_singular('shop');

    if ($needs_faq_item) {
        wp_enqueue_style('faq-item-css', $uri . '/assets/css/components/faq-item.css');
    }

    // 選ばれる理由カード
    $needs_reason_card = is_singular('symptoms')
        || is_singular('shop')
        || is_page('first');

    if ($needs_reason_card) {
        wp_enqueue_style('reason-card-css', $uri . '/assets/css/components/reason-card.css');
    }

    // お悩みセクション
    $needs_worry_section = is_singular('symptoms')
        || is_singular('shop');

    if ($needs_worry_section) {
        wp_enqueue_style('worry-section-css', $uri . '/assets/css/components/worry-section.css');
    }

    // TOPページでのみ読み込むCSS
    if (is_front_page()) {
        wp_enqueue_style('main-css', $uri . '/assets/css/main.css');
    }

    // 推薦者の声CSS（front-page, single-symptoms, single-shop）
    if (is_front_page() || is_singular('symptoms') || is_singular('shop')) {
        wp_enqueue_style('recommend-voice-css', $uri . '/assets/css/recommend-voice.css');
    }

    // メディア掲載CSS（front-page, page-first, single-symptoms, single-shop）
    if (is_front_page() || is_page('first') || is_singular('symptoms') || is_singular('shop')) {
        wp_enqueue_style('featured-css', $uri . '/assets/css/featured.css');
    }

    // 当院で対応している症状CSS（front-page, page-first, single-shop）
    if (is_front_page() || is_page('first') || is_singular('shop')) {
        wp_enqueue_style('shop-symptoms-css', $uri . '/assets/css/shop-symptoms.css');
    }

    // 症例報告スライダーCSS（front-page, single-shop, single-symptoms）
    if (is_front_page() || is_singular('shop') || is_singular('symptoms')) {
        wp_enqueue_style('case-slider-css', $uri . '/assets/css/case-slider.css');
    }

    // お客様の声スライダーCSS（front-page, single-shop, single-symptoms）
    if (is_front_page() || is_singular('shop') || is_singular('symptoms')) {
        wp_enqueue_style('voice-list-css', $uri . '/assets/css/voice-list.css');
    }

    // お知らせCSS（front-page）
    if (is_front_page()) {
        wp_enqueue_style('news-list-css', $uri . '/assets/css/news-list.css');
    }

    // TOPページ以外で読み込むCSS
    if (!is_front_page()) {
        wp_enqueue_style('subp-css', $uri . '/assets/css/subp.css');
    }

    // 初めての方へ追従ボタン（店舗詳細ページ、初めての方へページ以外）
    if (!is_singular('shop') && !is_page('first')) {
        wp_enqueue_style('side-first-btn-css', $uri . '/assets/css/side-first-btn.css');
    }

    // ========================================
    // お悩みスライダー用（Swiper）
    // 使用ページ: top, first, shop/xxxx, archive-symptoms, single-symptoms
    // ========================================
    $needs_symptoms_slider = is_front_page()
        || is_page('first')
        || is_singular('shop')
        || is_singular('symptoms')
        || is_post_type_archive('symptoms')
        || is_tax('symptoms_category');

    if ($needs_symptoms_slider) {
        wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
        wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], null, true);
    }

    // ========================================
    // アーカイブページ用CSS
    // ========================================

    // コラムアーカイブページ（/column/ = post のアーカイブのみ）
    if (is_post_type_archive('post')) {
        wp_enqueue_style('archive-column-css', $uri . '/assets/css/archive.css');
    }
    // お客様の声アーカイブページ
    if (is_post_type_archive('voice')) {
        wp_enqueue_style('archive-voice-css', $uri . '/assets/css/archive-voice.css');
    }
    // 推薦者の声アーカイブページ（投稿タイプアーカイブ + タクソノミーアーカイブ）
    if (is_post_type_archive('recommend') || is_tax('recommend_category')) {
        wp_enqueue_style('archive-recommend-css', $uri . '/assets/css/archive-recommend.css');
    }
    // 症例報告アーカイブページ・詳細ページ（bl_caseCardスタイル共有）
    if (is_post_type_archive('case') || is_singular('case')) {
        wp_enqueue_style('archive-case-css', $uri . '/assets/css/archive-case.css');
    }
    // メニュー案内（固定ページテンプレート）
    if (is_page_template('page-menu.php')) {
        wp_enqueue_style('page-menu-css', $uri . '/assets/css/pages/page-menu.css');
    }
    // お悩み一覧アーカイブページ（Swiperは上部で読み込み済み）
    if (is_post_type_archive('symptoms') || is_tax('symptoms_category')) {
        wp_enqueue_style('archive-symptoms-css', $uri . '/assets/css/archive-symptoms.css');
    }
    // 店舗一覧アーカイブページ
    if (is_post_type_archive('shop')) {
        wp_enqueue_style('archive-shop-css', $uri . '/assets/css/archive-shop.css');
    }
    // お知らせアーカイブページ（投稿タイプアーカイブ + タクソノミーアーカイブ）
    if (is_post_type_archive('info') || is_tax('info_category')) {
        wp_enqueue_style('archive-info-css', $uri . '/assets/css/archive-info.css');
    }

    // ========================================
    // シングルページ用CSS
    // ========================================
    if (is_singular()) {
        wp_enqueue_style('single-css', $uri . '/assets/css/single.css');
        wp_enqueue_style('wp-content-css', $uri . '/assets/css/wp-content.css');
    }
    // お悩み詳細ページ
    if (is_singular('symptoms')) {
        wp_enqueue_style('single-symptoms-css', $uri . '/assets/css/single-symptoms.css');
    }
    // 店舗詳細ページ
    if (is_singular('shop')) {
        wp_enqueue_style('single-shop-css', $uri . '/assets/css/single-shop.css');
    }
    // お知らせ詳細ページ
    if (is_singular('info')) {
        wp_enqueue_style('single-info-css', $uri . '/assets/css/single-info.css');
    }
    // 症例報告詳細ページ
    if (is_singular('case')) {
        wp_enqueue_style('single-case-css', $uri . '/assets/css/single-case.css');
    }

    // ========================================
    // 固定ページ用CSS
    // ========================================

    // お問い合わせ
    if (is_page('contact') || is_page_template('pages/page-contact.php')) {
        wp_enqueue_style('page-contact-css', $uri . '/assets/css/pages/page-contact.css');
    }
    // お問い合わせ完了（サンクスページ）
    if (is_page('contact-thanks') || is_page_template('pages/page-contact-thanks.php')) {
        wp_enqueue_style('page-contact-thanks-css', $uri . '/assets/css/pages/page-contact-thanks.css');
    }
    // サービス（既存）
    if (is_page('service')) {
        wp_enqueue_style('page-service-css', $uri . '/assets/css/pages/page-service.css');
    }
    // 初めての方へ
    // ※ single-symptoms-css, single-shop-css は不要（reason-card等コンポーネント化済み）
    if (is_page('first')) {
        wp_enqueue_style('page-first-css', $uri . '/assets/css/pages/page-first.css');
    }
    // 施術の流れ
    if (is_page('flow')) {
        wp_enqueue_style('page-flow-css', $uri . '/assets/css/pages/page-flow.css');
    }
    // よくある質問（FAQページ、店舗詳細ページ）
    if (is_page('faq') || is_singular('shop')) {
        wp_enqueue_style('page-faq-css', $uri . '/assets/css/pages/page-faq.css');
    }
    // コンテンツ制作・運営ポリシー
    if (is_page('content-policy')) {
        wp_enqueue_style('page-content-policy-css', $uri . '/assets/css/pages/page-content-policy.css');
    }
    // 利用規約
    if (is_page('sitepolicy')) {
        wp_enqueue_style('page-sitepolicy-css', $uri . '/assets/css/pages/page-sitepolicy.css');
    }
    // プライバシーポリシー
    if (is_page('privacypolicy')) {
        wp_enqueue_style('page-privacypolicy-css', $uri . '/assets/css/pages/page-privacypolicy.css');
    }
    // 採用情報
    if (is_page('recruit')) {
        wp_enqueue_style('page-recruit-css', $uri . '/assets/css/pages/page-recruit.css');
        // Swiper（先輩スタッフの声スライダー用）
        wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
        wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], null, true);
    }

    // ========================================
    // 404ページ
    // ========================================
    if (is_404()) {
        wp_enqueue_style('404-css', $uri . '/assets/css/404.css');
    }
}
add_action('wp_enqueue_scripts', 'enqueue_my_styles');
