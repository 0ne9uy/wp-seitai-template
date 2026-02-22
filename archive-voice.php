<?php
/**
 * Archive Template: お客様の声
 *
 * @package noras theme
 * @author shirako
 * @link https://norasinc.jp
 */

if (!defined('ABSPATH')) exit;

include get_template_directory() . '/inc/link.php';
get_template_part('parts/header/header');

// 症状一覧を取得（symptoms_categoryタクソノミーでグループ化）
$symptoms_categories = get_terms([
    'taxonomy' => 'symptoms_category',
    'parent' => 0,
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC',
]);

// カテゴリーごとに症状を取得
$symptoms_grouped = [];
if (!empty($symptoms_categories) && !is_wp_error($symptoms_categories)) {
    foreach ($symptoms_categories as $category) {
        $symptoms_in_category = get_posts([
            'post_type' => 'symptoms',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'tax_query' => [
                [
                    'taxonomy' => 'symptoms_category',
                    'field' => 'term_id',
                    'terms' => $category->term_id,
                ],
            ],
        ]);
        if (!empty($symptoms_in_category)) {
            $symptoms_grouped[] = [
                'category' => $category,
                'posts' => $symptoms_in_category,
            ];
        }
    }
}

// カテゴリーに属さない症状も取得
$symptoms_uncategorized = get_posts([
    'post_type' => 'symptoms',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
    'tax_query' => [
        [
            'taxonomy' => 'symptoms_category',
            'operator' => 'NOT EXISTS',
        ],
    ],
]);

// 店舗一覧を取得（shop_areaタクソノミーでグループ化）
$shop_areas = get_terms([
    'taxonomy' => 'shop_area',
    'parent' => 0,
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC',
]);

// エリアごとに店舗を取得
$shops_grouped = [];
if (!empty($shop_areas) && !is_wp_error($shop_areas)) {
    foreach ($shop_areas as $area) {
        $shops_in_area = get_posts([
            'post_type' => 'shop',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'tax_query' => [
                [
                    'taxonomy' => 'shop_area',
                    'field' => 'term_id',
                    'terms' => $area->term_id,
                ],
            ],
        ]);
        if (!empty($shops_in_area)) {
            $shops_grouped[] = [
                'area' => $area,
                'posts' => $shops_in_area,
            ];
        }
    }
}

// エリアに属さない店舗も取得
$shops_uncategorized = get_posts([
    'post_type' => 'shop',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
    'tax_query' => [
        [
            'taxonomy' => 'shop_area',
            'operator' => 'NOT EXISTS',
        ],
    ],
]);

// URLパラメータからフィルター情報を取得
$filter_type = isset($_GET['filter_type']) ? sanitize_text_field($_GET['filter_type']) : 'symptom';
$filter_term = isset($_GET['filter_term']) ? sanitize_text_field($_GET['filter_term']) : '';

// 選択されたカテゴリー名を取得
$selected_category_name = '';
$filter_post_id = 0;
if ($filter_term) {
    if ($filter_type === 'symptom') {
        $symptom_post = get_page_by_path($filter_term, OBJECT, 'symptoms');
        if ($symptom_post) {
            $selected_category_name = $symptom_post->post_title;
            $filter_post_id = $symptom_post->ID;
        }
    } else {
        $shop_post = get_page_by_path($filter_term, OBJECT, 'shop');
        if ($shop_post) {
            $selected_category_name = $shop_post->post_title;
            $filter_post_id = $shop_post->ID;
        }
    }
}

// サブページヘッダー
global $subp_header_label;
$subp_header_label = 'VOICE';
get_template_part('parts/subp-header');

// クエリ設定
$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$args = [
    'post_type' => 'voice',
    'posts_per_page' => 6,
    'paged' => $paged,
    'orderby' => 'date',
    'order' => 'DESC',
];

// フィルター適用（複数選択対応：serialized array検索）
if ($filter_post_id) {
    $field_key = $filter_type === 'symptom' ? 'related_symptoms' : 'related_shops';
    $args['meta_query'] = [
        'relation' => 'OR',
        // 単一値として保存されている場合
        [
            'key' => $field_key,
            'value' => $filter_post_id,
            'compare' => '=',
        ],
        // 配列（シリアライズ）として保存されている場合
        [
            'key' => $field_key,
            'value' => '"' . $filter_post_id . '"',
            'compare' => 'LIKE',
        ],
    ];
}

$the_query = new WP_Query($args);
?>

<main class="ly_subp ly_subp__voice">
    <section id="voice" class="ly_cont ly_voice">
        <div class="bl_voiceCont">

            <!-- タブ型フィルター -->
            <div class="bl_voiceFilter">
                <div class="bl_voiceFilter_tabs">
                    <button type="button"
                            class="bl_voiceFilter_tab <?= $filter_type === 'symptom' ? 'is_active' : ''; ?>"
                            data-tab="symptom">
                        症状から探す
                    </button>
                    <button type="button"
                            class="bl_voiceFilter_tab <?= $filter_type === 'shop' ? 'is_active' : ''; ?>"
                            data-tab="shop">
                        店舗から探す
                    </button>
                </div>
                <div class="bl_voiceFilter_dropdown">
                    <!-- 症状ドロップダウン -->
                    <div class="bl_voiceFilter_select <?= $filter_type === 'symptom' ? 'is_active' : ''; ?>" data-dropdown="symptom">
                        <button type="button" class="bl_voiceFilter_selectBtn">
                            <span class="bl_voiceFilter_selectText">
                                <?= $filter_type === 'symptom' && $selected_category_name ? esc_html($selected_category_name) : '症状から探す'; ?>
                            </span>
                            <img src="<?= esc_url(get_template_directory_uri()); ?>/assets/img/icons/cat-pulldown-btn.svg" alt="" class="bl_voiceFilter_selectIcon">
                        </button>
                        <ul class="bl_voiceFilter_selectList">
                            <li>
                                <a href="<?= esc_url(add_query_arg(['filter_type' => 'symptom', 'filter_term' => ''], get_post_type_archive_link('voice'))); ?>">
                                    すべて
                                </a>
                            </li>
                            <?php if (!empty($symptoms_grouped)) : ?>
                                <?php foreach ($symptoms_grouped as $group) : ?>
                                    <li class="bl_voiceFilter_selectCategory">
                                        <span class="bl_voiceFilter_selectCategoryName"><?= esc_html($group['category']->name); ?></span>
                                        <ul class="bl_voiceFilter_selectChildren">
                                            <?php foreach ($group['posts'] as $symptom) : ?>
                                                <li>
                                                    <a href="<?= esc_url(add_query_arg(['filter_type' => 'symptom', 'filter_term' => $symptom->post_name], get_post_type_archive_link('voice'))); ?>">
                                                        <?= esc_html($symptom->post_title); ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php if (!empty($symptoms_uncategorized)) : ?>
                                <?php foreach ($symptoms_uncategorized as $symptom) : ?>
                                    <li>
                                        <a href="<?= esc_url(add_query_arg(['filter_type' => 'symptom', 'filter_term' => $symptom->post_name], get_post_type_archive_link('voice'))); ?>">
                                            <?= esc_html($symptom->post_title); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <!-- 店舗ドロップダウン -->
                    <div class="bl_voiceFilter_select <?= $filter_type === 'shop' ? 'is_active' : ''; ?>" data-dropdown="shop">
                        <button type="button" class="bl_voiceFilter_selectBtn">
                            <span class="bl_voiceFilter_selectText">
                                <?= $filter_type === 'shop' && $selected_category_name ? esc_html($selected_category_name) : '店舗から探す'; ?>
                            </span>
                            <img src="<?= esc_url(get_template_directory_uri()); ?>/assets/img/icons/cat-pulldown-btn.svg" alt="" class="bl_voiceFilter_selectIcon">
                        </button>
                        <ul class="bl_voiceFilter_selectList">
                            <li>
                                <a href="<?= esc_url(add_query_arg(['filter_type' => 'shop', 'filter_term' => ''], get_post_type_archive_link('voice'))); ?>">
                                    すべて
                                </a>
                            </li>
                            <?php if (!empty($shops_grouped)) : ?>
                                <?php foreach ($shops_grouped as $group) : ?>
                                    <li class="bl_voiceFilter_selectCategory">
                                        <span class="bl_voiceFilter_selectCategoryName"><?= esc_html($group['area']->name); ?></span>
                                        <ul class="bl_voiceFilter_selectChildren">
                                            <?php foreach ($group['posts'] as $shop) : ?>
                                                <li>
                                                    <a href="<?= esc_url(add_query_arg(['filter_type' => 'shop', 'filter_term' => $shop->post_name], get_post_type_archive_link('voice'))); ?>">
                                                        <?= esc_html($shop->post_title); ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php if (!empty($shops_uncategorized)) : ?>
                                <?php foreach ($shops_uncategorized as $shop) : ?>
                                    <li>
                                        <a href="<?= esc_url(add_query_arg(['filter_type' => 'shop', 'filter_term' => $shop->post_name], get_post_type_archive_link('voice'))); ?>">
                                            <?= esc_html($shop->post_title); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- カテゴリータイトル（選択時のみ表示） -->
            <?php if ($selected_category_name) : ?>
                <div class="bl_voiceGroup_header">
                    <span class="bl_voiceGroup_circle"></span>
                    <h3 class="bl_voiceGroup_ttl"><?= esc_html($selected_category_name); ?></h3>
                </div>
            <?php endif; ?>

            <!-- お客様の声リスト -->
            <?php if ($the_query->have_posts()) : ?>
                <div class="bl_voiceList">
                    <?php while ($the_query->have_posts()) : $the_query->the_post(); ?>
                        <?php
                        // カスタムフィールドを取得
                        $name = get_field('name');

                        // 症状（複数選択対応）
                        $related_symptoms = get_field('related_symptoms');
                        $symptom_names = [];
                        if ($related_symptoms) {
                            // 配列でない場合は配列に変換
                            if (!is_array($related_symptoms)) {
                                $related_symptoms = [$related_symptoms];
                            }
                            foreach ($related_symptoms as $symptom) {
                                if (is_object($symptom)) {
                                    $symptom_names[] = $symptom->post_title;
                                } elseif (is_numeric($symptom)) {
                                    $post = get_post($symptom);
                                    if ($post) {
                                        $symptom_names[] = $post->post_title;
                                    }
                                }
                            }
                        }

                        // 店舗（複数選択対応）
                        $related_shops = get_field('related_shops');
                        $shop_names = [];
                        if ($related_shops) {
                            // 配列でない場合は配列に変換
                            if (!is_array($related_shops)) {
                                $related_shops = [$related_shops];
                            }
                            foreach ($related_shops as $shop) {
                                if (is_object($shop)) {
                                    $shop_names[] = $shop->post_title;
                                } elseif (is_numeric($shop)) {
                                    $post = get_post($shop);
                                    if ($post) {
                                        $shop_names[] = $post->post_title;
                                    }
                                }
                            }
                        }
                        ?>
                        <article id="voice-<?php echo get_the_ID(); ?>" class="bl_voiceCard bl_voiceCard--list">
                            <div class="bl_voiceCard_header">
                                <div class="bl_voiceCard_thumb">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium'); ?>
                                    <?php else : ?>
                                        <img src="<?= esc_url(get_template_directory_uri()); ?>/assets/img/no-image.png" alt="">
                                    <?php endif; ?>
                                </div>
                                <div class="bl_voiceCard_info">
                                    <h3 class="bl_voiceCard_ttl"><?php the_title(); ?></h3>
                                    <?php if ($name) : ?>
                                        <p class="bl_voiceCard_name"><?= esc_html($name); ?></p>
                                    <?php endif; ?>
                                    <dl class="bl_voiceCard_meta">
                                        <?php if (!empty($symptom_names)) : ?>
                                            <div class="bl_voiceCard_metaRow">
                                                <dt>症状</dt>
                                                <dd><?= esc_html(implode('、', $symptom_names)); ?></dd>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($shop_names)) : ?>
                                            <div class="bl_voiceCard_metaRow">
                                                <dt>対応店舗</dt>
                                                <dd><?= esc_html(implode('、', $shop_names)); ?></dd>
                                            </div>
                                        <?php endif; ?>
                                    </dl>
                                </div>
                            </div>
                            <div class="bl_voiceCard_body">
                                <?php the_content(); ?>
                                <p class="bl_voiceCard_note">※効果には個人差があります。</p>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <p class="bl_voiceList_empty">お客様の声はまだありません。</p>
            <?php endif; ?>

            <?php get_template_part('parts/elements/pagination', null, ['query' => $the_query]); ?>
            <?php wp_reset_postdata(); ?>

        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // タブ切り替え
    const tabs = document.querySelectorAll('.bl_voiceFilter_tab');
    const dropdowns = document.querySelectorAll('.bl_voiceFilter_select');

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');

            // タブのアクティブ状態を切り替え
            tabs.forEach(function(t) {
                t.classList.remove('is_active');
            });
            this.classList.add('is_active');

            // ドロップダウンの表示を切り替え
            dropdowns.forEach(function(dropdown) {
                if (dropdown.getAttribute('data-dropdown') === targetTab) {
                    dropdown.classList.add('is_active');
                } else {
                    dropdown.classList.remove('is_active');
                }
            });
        });
    });

    // ドロップダウン開閉
    const selectBtns = document.querySelectorAll('.bl_voiceFilter_selectBtn');

    selectBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const selectWrapper = this.closest('.bl_voiceFilter_select');
            const isOpen = selectWrapper.classList.contains('is_open');

            // 他のドロップダウンを閉じる
            document.querySelectorAll('.bl_voiceFilter_select').forEach(function(s) {
                s.classList.remove('is_open');
            });

            // 現在のドロップダウンを開閉
            if (!isOpen) {
                selectWrapper.classList.add('is_open');
            }
        });
    });

    // 外側クリックで閉じる
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.bl_voiceFilter_select')) {
            document.querySelectorAll('.bl_voiceFilter_select').forEach(function(s) {
                s.classList.remove('is_open');
            });
        }
    });
});
</script>

<?php get_template_part('parts/breadcrumb'); ?>

<?php get_template_part('parts/cta'); ?>

<?php get_template_part('parts/footer/footer'); ?>
