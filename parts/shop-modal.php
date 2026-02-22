<?php

/**
 * Template Part: 店舗一覧モーダル
 * 使用: CTAボタンクリック時に表示
 * デザイン: トップページのbl_shopList_tabContainerと同じ
 */

if (!defined('ABSPATH')) exit;

$template_uri = get_template_directory_uri();

// エリアタクソノミー（shop_area）のターム一覧を取得
$areas = get_terms([
    'taxonomy' => 'shop_area',
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC',
]);

$has_areas = !empty($areas) && !is_wp_error($areas);

// 各エリアの店舗を取得
$shops_by_area = [];
if ($has_areas) {
    foreach ($areas as $area) {
        $shop_query = new WP_Query([
            'post_type' => 'shop',
            'posts_per_page' => -1,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'tax_query' => [
                [
                    'taxonomy' => 'shop_area',
                    'field' => 'term_id',
                    'terms' => $area->term_id,
                ],
            ],
        ]);

        if ($shop_query->have_posts()) {
            $shops_by_area[$area->term_id] = [
                'term' => $area,
                'shops' => [],
            ];

            while ($shop_query->have_posts()) {
                $shop_query->the_post();
                // 地図埋め込みコードを取得し、loading="lazy"を追加
                $map_embed = get_field('shop_map_embed');
                if ($map_embed && strpos($map_embed, 'loading=') === false) {
                    $map_embed = str_replace('<iframe', '<iframe loading="lazy"', $map_embed);
                }
                $shops_by_area[$area->term_id]['shops'][] = [
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'permalink' => get_permalink(),
                    'address' => get_field('shop_address'),
                    'phone' => get_field('shop_phone'),
                    'web_booking_url' => get_field('web_booking_url'),
                    'line_url' => get_field('line_url'),
                    'map_embed' => $map_embed,
                ];
            }
            wp_reset_postdata();
        }
    }
}

$has_shops = !empty($shops_by_area);
?>

<!-- 店舗一覧モーダル -->
<div class="bl_shopModal" id="shopModal" aria-hidden="true">
    <div class="bl_shopModal_overlay"></div>
    <div class="bl_shopModal_container">
        <div class="bl_shopModal_inner">
            <!-- ヘッダー -->
            <div class="bl_shopModal_header">
                <h2 class="bl_shopModal_ttl zenmaru">店舗を探す</h2>
                <button type="button" class="bl_shopModal_close" aria-label="閉じる">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <?php if ($has_shops) : ?>
                <!-- タブコンテナ（トップページと同じデザイン） -->
                <div class="bl_shopModal_tabContainer">
                    <!-- エリアタブ -->
                    <div class="bl_shopList_tabs" role="tablist">
                        <?php $is_first = true; ?>
                        <?php foreach ($shops_by_area as $term_id => $area_data) : ?>
                            <button type="button" class="bl_shopList_tab<?php echo $is_first ? ' is_active' : ''; ?>" role="tab"
                                aria-selected="<?php echo $is_first ? 'true' : 'false'; ?>"
                                aria-controls="shopModalPanel-<?php echo esc_attr($term_id); ?>"
                                data-area="<?php echo esc_attr($term_id); ?>">
                                <?php echo esc_html($area_data['term']->name); ?>
                            </button>
                            <?php $is_first = false; ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- 店舗パネルラッパー -->
                    <div class="bl_shopList_panelWrap">
                        <?php $is_first = true; ?>
                        <?php foreach ($shops_by_area as $term_id => $area_data) : ?>
                            <div id="shopModalPanel-<?php echo esc_attr($term_id); ?>"
                                class="bl_shopList_panel<?php echo $is_first ? ' is_active' : ''; ?>" role="tabpanel">
                                <div class="bl_shopList_grid">
                                    <?php foreach ($area_data['shops'] as $shop) : ?>
                                        <article class="bl_shopCard">
                                            <!-- 店舗名ヘッダー -->
                                            <header class="bl_shopCard_header">
                                                <h3 class="bl_shopCard_name zenmaru"><?php echo esc_html($shop['title']); ?></h3>
                                            </header>

                                            <!-- 店舗情報 -->
                                            <div class="bl_shopCard_body">
                                                <!-- マップ（遅延読み込み） -->
                                                <?php if ($shop['map_embed']) : ?>
                                                    <div class="bl_shopCard_map">
                                                        <?php echo $shop['map_embed']; ?>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- 住所 -->
                                                <?php if ($shop['address']) : ?>
                                                    <div class="bl_shopCard_address">
                                                        <img src="<?php echo esc_url($template_uri); ?>/assets/img/icons/mappin.svg"
                                                            alt="" class="bl_shopCard_addressIcon" width="16" height="16">
                                                        <p><?php echo wp_kses($shop['address'], ['br' => []]); ?></p>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- ボタン・リンクまとめ -->
                                                <div class="bl_shopCard_actions">
                                                    <!-- ボタン群 -->
                                                    <div class="bl_shopCard_btns">
                                                        <!-- WEB予約ボタン -->
                                                        <?php if ($shop['web_booking_url']) : ?>
                                                            <a href="<?php echo esc_url($shop['web_booking_url']); ?>"
                                                                class="bl_shopCard_btn bl_shopCard_btn--web" target="_blank"
                                                                rel="noopener noreferrer">
                                                                <img src="<?php echo esc_url($template_uri); ?>/assets/img/icons/webyoyaku.svg"
                                                                    alt="" width="20" height="20">
                                                                <span>WEBで予約する</span>
                                                            </a>
                                                        <?php endif; ?>

                                                        <!-- 電話・LINE -->
                                                        <div class="bl_shopCard_btnRow">
                                                            <?php if ($shop['phone']) : ?>
                                                                <a href="tel:<?php echo esc_attr(str_replace('-', '', $shop['phone'])); ?>"
                                                                    class="bl_shopCard_btn bl_shopCard_btn--phone">
                                                                    <img src="<?php echo esc_url($template_uri); ?>/assets/img/cta/cta-call.svg"
                                                                        alt="" width="20" height="20">
                                                                    <span><?php echo esc_html($shop['phone']); ?></span>
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if ($shop['line_url']) : ?>
                                                                <a href="<?php echo esc_url($shop['line_url']); ?>"
                                                                    class="bl_shopCard_btn bl_shopCard_btn--line" target="_blank"
                                                                    rel="noopener noreferrer">
                                                                    <img src="<?php echo esc_url($template_uri); ?>/assets/img/icons/line-icon.svg"
                                                                        alt="" width="24" height="24">
                                                                    <span>LINE予約</span>
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>

                                                    <!-- 詳細リンク -->
                                                    <a href="<?php echo esc_url($shop['permalink']); ?>" class="bl_shopCard_link">
                                                        店舗の詳細を見る
                                                        <img src="<?php echo esc_url($template_uri); ?>/assets/img/icons/shop-arrow.svg"
                                                            alt="" width="16" height="16">
                                                    </a>
                                                </div>
                                            </div>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php $is_first = false; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else : ?>
                <!-- 店舗がない場合 -->
                <div class="bl_shopModal_body">
                    <p class="bl_shopModal_empty">店舗情報を準備中です。<br>お問い合わせはお電話にてお願いいたします。</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('shopModal');
    if (!modal) return;

    var triggers = document.querySelectorAll('[data-shop-modal-trigger]');
    var closeBtn = modal.querySelector('.bl_shopModal_close');
    var overlay = modal.querySelector('.bl_shopModal_overlay');
    var tabs = modal.querySelectorAll('.bl_shopList_tab');
    var panels = modal.querySelectorAll('.bl_shopList_panel');
    var body = document.body;

    // モーダルを開く
    function openModal() {
        modal.setAttribute('aria-hidden', 'false');
        modal.classList.add('is_active');
        body.style.overflow = 'hidden';
    }

    // モーダルを閉じる
    function closeModal() {
        modal.setAttribute('aria-hidden', 'true');
        modal.classList.remove('is_active');
        body.style.overflow = '';
    }

    // トリガーボタンでモーダルを開く
    triggers.forEach(function(trigger) {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            openModal();
        });
    });

    // 閉じるボタン
    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            closeModal();
        });
    }

    // オーバーレイクリックで閉じる
    if (overlay) {
        overlay.addEventListener('click', closeModal);
    }

    // ESCキーで閉じる
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('is_active')) {
            closeModal();
        }
    });

    // タブ切り替え
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            var areaId = this.getAttribute('data-area');

            // タブのアクティブ状態を更新
            tabs.forEach(function(t) {
                t.classList.remove('is_active');
                t.setAttribute('aria-selected', 'false');
            });
            this.classList.add('is_active');
            this.setAttribute('aria-selected', 'true');

            // パネルの表示を切り替え
            panels.forEach(function(panel) {
                panel.classList.remove('is_active');
            });
            var targetPanel = document.getElementById('shopModalPanel-' + areaId);
            if (targetPanel) {
                targetPanel.classList.add('is_active');
            }
        });
    });
});
</script>
