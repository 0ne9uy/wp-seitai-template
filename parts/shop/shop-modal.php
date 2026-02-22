<?php

/**
 * 店舗一覧モーダル
 * ヘッダーの「店舗を探す・予約する」ボタンで開くモーダル
 */

include get_template_directory() . "/inc/link.php";

// エリアタクソノミー取得
$modal_areas = get_terms([
    'taxonomy' => 'shop_area',
    'hide_empty' => true,
    'orderby' => 'term_order',
    'order' => 'ASC',
]);

if (!empty($modal_areas) && !is_wp_error($modal_areas)) :
    $modal_shops_by_area = [];
    foreach ($modal_areas as $area) {
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
            $modal_shops_by_area[$area->term_id] = [
                'term' => $area,
                'shops' => [],
            ];

            while ($shop_query->have_posts()) {
                $shop_query->the_post();
                $modal_shops_by_area[$area->term_id]['shops'][] = [
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'permalink' => get_permalink(),
                    'address' => get_field('shop_address'),
                    'phone' => get_field('shop_phone'),
                    'web_booking_url' => get_field('web_booking_url'),
                    'line_url' => get_field('line_url'),
                    'map_embed' => get_field('shop_map_embed'),
                ];
            }
            wp_reset_postdata();
        }
    }

    if (!empty($modal_shops_by_area)) :
?>
<div class="bl_shopModal js_shopModal">
    <div class="bl_shopModal_overlay js_shopModalClose"></div>
    <div class="bl_shopModal_content">
        <button type="button" class="bl_shopModal_close js_shopModalClose">
            <svg width="24" height="24" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.578 3.435a.801.801 0 0 0-1.133 1.133L6.88 8l-3.432 3.435a.801.801 0 0 0 1.132 1.133l3.433-3.435 3.435 3.432a.801.801 0 0 0 1.132-1.132L9.145 8l3.433-3.435a.801.801 0 0 0-1.133-1.132L8.013 6.868z" fill="#333"/></svg>
        </button>

        <div class="bl_shopModal_header">
            <div class="bl_sectionTtlWrap bl_sectionTtlWrap--left">
                <p class="bl_sectionLabel lato"><span class="bl_sectionLabel_circle"></span>SHOP LIST</p>
                <h2 class="el_sectionTtl zenmaru">店舗一覧</h2>
            </div>
        </div>

        <!-- エリアタブ -->
        <div class="bl_shopModal_tabs" role="tablist">
            <?php $is_first = true; ?>
            <?php foreach ($modal_shops_by_area as $term_id => $area_data) : ?>
                <button type="button" class="bl_shopList_tab<?= $is_first ? ' is_active' : '' ?>" role="tab"
                    aria-selected="<?= $is_first ? 'true' : 'false' ?>"
                    aria-controls="shopModal-<?= esc_attr($term_id) ?>"
                    data-area="<?= esc_attr($term_id) ?>">
                    <?= esc_html($area_data['term']->name) ?>
                </button>
                <?php $is_first = false; ?>
            <?php endforeach; ?>
        </div>

        <!-- 店舗パネル -->
        <div class="bl_shopModal_panels">
            <?php $is_first = true; ?>
            <?php foreach ($modal_shops_by_area as $term_id => $area_data) : ?>
                <div id="shopModal-<?= esc_attr($term_id) ?>"
                    class="bl_shopList_panel<?= $is_first ? ' is_active' : '' ?>" role="tabpanel">
                    <div class="bl_shopList_grid">
                        <?php foreach ($area_data['shops'] as $s) : ?>
                            <article class="bl_shopCard">
                                <header class="bl_shopCard_header">
                                    <h3 class="bl_shopCard_name zenmaru"><?= esc_html($s['title']) ?></h3>
                                </header>
                                <div class="bl_shopCard_body">
                                    <?php if ($s['map_embed']) : ?>
                                        <div class="bl_shopCard_map"><?= $s['map_embed'] ?></div>
                                    <?php endif; ?>
                                    <?php if ($s['address']) : ?>
                                        <div class="bl_shopCard_address">
                                            <img src="<?= esc_url($uri) ?>/assets/img/icons/mappin.svg" alt="" width="16" height="16">
                                            <p><?= nl2br($s['address']) ?></p>
                                        </div>
                                    <?php endif; ?>
                                    <div class="bl_shopCard_actions">
                                        <div class="bl_shopCard_btns">
                                            <?php if ($s['web_booking_url']) : ?>
                                                <a href="<?= esc_url($s['web_booking_url']) ?>" class="bl_shopCard_btn bl_shopCard_btn--web" target="_blank" rel="noopener noreferrer">
                                                    <img src="<?= esc_url($uri) ?>/assets/img/icons/webyoyaku.svg" alt="" width="20" height="20">
                                                    <span>WEBで予約する</span>
                                                </a>
                                            <?php endif; ?>
                                            <div class="bl_shopCard_btnRow">
                                                <?php if ($s['phone']) : ?>
                                                    <a href="tel:<?= esc_attr(str_replace('-', '', $s['phone'])) ?>" class="bl_shopCard_btn bl_shopCard_btn--phone">
                                                        <img src="<?= esc_url($uri) ?>/assets/img/cta/cta-call.svg" alt="" width="20" height="20">
                                                        <span><?= esc_html($s['phone']) ?></span>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if ($s['line_url']) : ?>
                                                    <a href="<?= esc_url($s['line_url']) ?>" class="bl_shopCard_btn bl_shopCard_btn--line" target="_blank" rel="noopener noreferrer">
                                                        <img src="<?= esc_url($uri) ?>/assets/img/icons/line-icon.svg" alt="" width="24" height="24">
                                                        <span>LINE予約</span>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <a href="<?= esc_url($s['permalink']) ?>" class="bl_shopCard_link">
                                            店舗の詳細を見る
                                            <img src="<?= esc_url($uri) ?>/assets/img/icons/shop-arrow.svg" alt="" width="16" height="16">
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
</div>
<?php endif;
endif; ?>
