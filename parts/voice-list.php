<?php

/**
 * Template Part: お客様の声スライダーセクション
 * 使用ページ: front-page, single-shop, single-symptoms
 *
 * @param array $args {
 *     @type WP_Query $query      カスタムクエリ（省略時は全件取得）
 *     @type string   $title      セクションタイトル（省略時: 'お客様の声'）
 *     @type bool     $show_shops 店舗名を表示するか（省略時: true）
 *     @type bool     $use_modal  モーダル表示を使用するか（省略時: false）
 *     @type bool     $show_button 一覧ボタンを表示するか（省略時: true）
 * }
 */

if (!defined('ABSPATH')) exit;

$template_uri = get_template_directory_uri();

// 引数のデフォルト値を設定
$defaults = [
    'query' => null,
    'title' => 'お客様の声',
    'show_shops' => true,
    'use_modal' => false,
    'show_button' => true,
];
$args = wp_parse_args($args ?? [], $defaults);

// クエリが渡されない場合はデフォルトクエリを使用
if ($args['query'] instanceof WP_Query) {
    $voice_query = $args['query'];
} else {
    $voice_query = new WP_Query([
        'post_type' => 'voice',
        'posts_per_page' => 6,
        'orderby' => 'date',
        'order' => 'DESC',
    ]);
}

if (!$voice_query->have_posts()) return;

// スライドデータを配列に格納
$slides = [];
while ($voice_query->have_posts()) {
    $voice_query->the_post();
    $voice_name = get_field('name');

    // 関連症状を取得
    $related_symptoms = get_field('related_symptoms');
    $symptom_names = [];
    if ($related_symptoms) {
        if (!is_array($related_symptoms)) $related_symptoms = [$related_symptoms];
        foreach ($related_symptoms as $symptom) {
            if (is_object($symptom)) {
                $symptom_names[] = $symptom->post_title;
            } elseif (is_numeric($symptom)) {
                $p = get_post($symptom);
                if ($p) $symptom_names[] = $p->post_title;
            }
        }
    }

    // 関連店舗を取得（show_shopsがtrueの場合のみ）
    $shop_names = [];
    if ($args['show_shops']) {
        $related_shops = get_field('related_shops');
        if ($related_shops) {
            if (!is_array($related_shops)) $related_shops = [$related_shops];
            foreach ($related_shops as $shop) {
                if (is_object($shop)) {
                    $shop_names[] = $shop->post_title;
                } elseif (is_numeric($shop)) {
                    $p = get_post($shop);
                    if ($p) $shop_names[] = $p->post_title;
                }
            }
        }
    }

    $slides[] = [
        'id' => get_the_ID(),
        'title' => get_the_title(),
        'content' => apply_filters('the_content', get_the_content()),
        'has_thumbnail' => has_post_thumbnail(),
        'thumbnail' => has_post_thumbnail() ? get_the_post_thumbnail(null, 'medium') : null,
        'thumbnail_large' => has_post_thumbnail() ? get_the_post_thumbnail(null, 'large') : null,
        'voice_name' => $voice_name,
        'symptom_names' => $symptom_names,
        'shop_names' => $shop_names,
    ];
}
wp_reset_postdata();

// ループに必要な最小スライド数
$min_slides = 8;
$slide_count = count($slides);
$loop_count = ($slide_count < $min_slides) ? ceil($min_slides / $slide_count) : 1;
?>

<!-- お客様の声 -->
<section id="voice" class="ly_voiceList">
    <div class="bl_voiceList_inner">
        <?php get_template_part('parts/elements/section-header', null, [
            'label' => 'Testimonial',
            'title' => $args['title'],
            'description' => '心身堂グループで施術を受けたお客様の生の声をお届けします。',
            'is_light' => true,
        ]); ?>
        <div class="bl_voiceList_sliderWrap">
            <button type="button" class="bl_voiceList_btn bl_voiceList_btn__prev" aria-label="前へ">
                <?php get_template_part('parts/elements/arrow-btn', null, ['direction' => 'left']); ?>
            </button>
            <div class="bl_voiceList_slider">
                <div class="swiper bl_voiceListSwiper">
                    <div class="swiper-wrapper">
                        <?php for ($i = 0; $i < $loop_count; $i++) : ?>
                            <?php foreach ($slides as $slide) : ?>
                                <div class="swiper-slide bl_voiceList_slide">
                                    <article class="bl_voiceCard bl_voiceCard--slider">
                                        <div class="bl_voiceCard_thumb">
                                            <?php if ($slide['has_thumbnail']) : ?>
                                                <?php echo $slide['thumbnail']; ?>
                                            <?php else : ?>
                                                <img src="<?php echo esc_url($template_uri); ?>/assets/img/no-image.png" alt="">
                                            <?php endif; ?>
                                        </div>
                                        <div class="bl_voiceCard_info">
                                            <h3 class="bl_voiceCard_ttl"><?php echo esc_html($slide['title']); ?></h3>
                                            <?php if ($slide['voice_name']) : ?>
                                                <p class="bl_voiceCard_name"><?php echo esc_html($slide['voice_name']); ?></p>
                                            <?php endif; ?>
                                            <dl class="bl_voiceCard_meta">
                                                <?php if (!empty($slide['symptom_names'])) : ?>
                                                    <div class="bl_voiceCard_metaRow">
                                                        <dt>症状</dt>
                                                        <dd><?php echo esc_html(implode('、', $slide['symptom_names'])); ?></dd>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($slide['shop_names'])) : ?>
                                                    <div class="bl_voiceCard_metaRow">
                                                        <dt>対応店舗</dt>
                                                        <dd><?php echo esc_html(implode('、', $slide['shop_names'])); ?></dd>
                                                    </div>
                                                <?php endif; ?>
                                            </dl>
                                        </div>
                                        <?php if ($args['use_modal']) : ?>
                                            <button type="button" class="bl_voiceCard_btn js_voiceModalOpen" data-voice-id="<?php echo esc_attr($slide['id']); ?>">詳しく見る</button>
                                        <?php else : ?>
                                            <a href="<?php echo esc_url(get_post_type_archive_link('voice') . '#voice-' . $slide['id']); ?>" class="bl_voiceCard_btn">詳しく見る</a>
                                        <?php endif; ?>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
            <button type="button" class="bl_voiceList_btn bl_voiceList_btn__next" aria-label="次へ">
                <?php get_template_part('parts/elements/arrow-btn', null, ['direction' => 'right']); ?>
            </button>
        </div>
        <?php if ($args['show_button']) : ?>
            <div class="bl_voiceList_btnWrap">
                <a href="<?php echo esc_url(get_post_type_archive_link('voice')); ?>" class="el_greenBtn">
                    <span>他のお声も見る</span>
                    <img src="<?php echo esc_url($template_uri); ?>/assets/img/icons/greenArrow-whitebg.svg" alt="" width="24" height="24">
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if ($args['use_modal']) : ?>
<!-- お客様の声モーダル -->
<div class="bl_voiceModal js_voiceModal" aria-hidden="true">
    <div class="bl_voiceModal_overlay js_voiceModalClose"></div>
    <div class="bl_voiceModal_container">
        <button type="button" class="bl_voiceModal_close js_voiceModalClose" aria-label="閉じる">
            <img src="<?php echo esc_url($template_uri); ?>/assets/img/icons/batu.svg" alt="" width="24" height="24">
        </button>
        <div class="bl_voiceModal_inner">
            <!-- 動的に挿入 -->
        </div>
    </div>
</div>

<script>
window.voiceModalData = window.voiceModalData || [];
window.voiceModalData.push(<?php echo json_encode(array_map(function($slide) {
    return [
        'id' => $slide['id'],
        'title' => $slide['title'],
        'content' => $slide['content'],
        'has_thumbnail' => $slide['has_thumbnail'],
        'thumbnail_large' => $slide['thumbnail_large'],
        'voice_name' => $slide['voice_name'],
        'symptom_names' => $slide['symptom_names'],
        'shop_names' => $slide['shop_names'],
    ];
}, $slides), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>);
window.voiceModalTemplateUri = '<?php echo esc_url($template_uri); ?>';
</script>
<?php endif; ?>
