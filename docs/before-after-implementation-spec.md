# ビフォアーアフター機能 完全実装仕様書

## 概要

ドラッグ操作で「Before」「After」画像を比較表示するスライダー機能。
オプションで動画モーダル表示にも対応。外部ライブラリ不要のVanilla JS実装。

---

## 目次

1. [必要な前提条件](#1-必要な前提条件)
2. [ACFカスタムフィールド設定](#2-acfカスタムフィールド設定)
3. [HTML構造](#3-html構造)
4. [CSS実装](#4-css実装)
5. [JavaScript実装](#5-javascript実装)
6. [動画モーダル（オプション）](#6-動画モーダルオプション)
7. [PHPテンプレート例](#7-phpテンプレート例)
8. [カスタマイズ方法](#8-カスタマイズ方法)

---

## 1. 必要な前提条件

### WordPress環境
- WordPress 5.0以上
- Advanced Custom Fields (ACF) プラグイン

### ブラウザ対応
- Pointer Events API対応ブラウザ（モダンブラウザ全般）
- CSS Custom Properties（CSS変数）対応

### 必要なCSS機能
- `aspect-ratio`
- `object-fit`
- CSS変数（`--slider-width`, `--beer-position`）

---

## 2. ACFカスタムフィールド設定

### フィールドグループ作成

ACF管理画面で以下のフィールドグループを作成：

| フィールドラベル | フィールド名 | フィールドタイプ | 必須 | 説明 |
|-----------------|-------------|----------------|------|------|
| ビフォー画像 | `example_before` | 画像 | Yes | 施術前の画像 |
| アフター画像 | `example_after` | 画像 | Yes | 施術後の画像 |
| 動画URL | `example_video` | URL | No | YouTube/Vimeo埋め込みURL |

### 画像フィールド設定
- **返り値**: 画像配列 または 画像URL（どちらでも対応可）
- **プレビューサイズ**: Medium
- **推奨アスペクト比**: 360:475（縦長）

---

## 3. HTML構造

### 基本構造（スライダーのみ）

```html
<div class="beer-slider bl_beforeAfter_slider" data-beer-label="After" data-start="50">
    <img src="[AFTER画像のURL]" alt="アフター画像">
    <div class="beer-reveal" data-beer-label="Before">
        <img src="[BEFORE画像のURL]" alt="ビフォー画像">
    </div>
</div>
```

### data属性の説明

| 属性 | 値 | 説明 |
|-----|---|------|
| `data-beer-label` | "After" | スライダー右下に表示されるラベル |
| `data-start` | "50" | 初期位置（0-100のパーセント値） |
| `data-beer-label`（.beer-reveal） | "Before" | スライダー左上に表示されるラベル |

### カード全体の構造

```html
<div class="bl_beforeAfterItem">
    <h3>[タイトル]</h3>

    <!-- ビフォアーアフタースライダー -->
    <div class="beer-slider bl_beforeAfter_slider" data-beer-label="After" data-start="50">
        <img src="[AFTER画像のURL]" alt="アフター画像">
        <div class="beer-reveal" data-beer-label="Before">
            <img src="[BEFORE画像のURL]" alt="ビフォー画像">
        </div>
    </div>

    <!-- 動画ボタン（オプション） -->
    <button type="button" class="el_beforeAfter_video js_openVideo" data-video="[動画URL]">
        <svg><!-- 再生アイコン --></svg>動画でも見る
    </button>

    <small class="hp_txtCenter">※効果には個人差があります。</small>
</div>
```

---

## 4. CSS実装

### 4.1 コアスタイル（必須）

```css
/* ===========================================
   ビフォアーアフタースライダー - コアスタイル
   =========================================== */

/* スライダーコンテナ */
.beer-slider {
  aspect-ratio: 360 / 475;        /* 縦長のアスペクト比 */
  position: relative;
  overflow: hidden;
  width: 800px;                   /* 最大幅 */
  max-width: 100%;
  margin: 0 auto;
  touch-action: none;             /* タッチスクロール無効化 */
  user-select: none;              /* テキスト選択無効化 */
}

/* After画像（背面・ベース画像） */
.beer-slider > img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  position: relative;
  z-index: 1;
}

/* Before画像コンテナ（前面・クリップ領域） */
.beer-reveal {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  width: 50%;                     /* JSで動的に変更される */
  overflow: hidden;
  z-index: 2;
}

/* Before画像 */
.beer-reveal > img {
  width: var(--slider-width, 800px);  /* CSS変数でスライダー幅を設定 */
  max-width: none;                     /* 親要素の幅制限を無視 */
  height: 100%;
  object-fit: cover;
  display: block;
  position: absolute;
  left: 0;
  top: 0;
}

/* ドラッグハンドル（JSで動的に生成） */
.beer-handle {
  position: absolute;
  top: 50%;
  left: var(--beer-position, 50%);    /* CSS変数で位置を制御 */
  transform: translate(-50%, -50%);
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #ffffff;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: ew-resize;                   /* 左右リサイズカーソル */
  touch-action: none;
  pointer-events: auto;
}

/* ハンドル内の矢印アイコン（CSS疑似要素） */
.beer-handle::before,
.beer-handle::after {
  content: '';
  display: block;
  width: 0;
  height: 0;
  border-top: 8px solid transparent;
  border-bottom: 8px solid transparent;
}

/* 左矢印 */
.beer-handle::before {
  border-right: 10px solid #333;
  margin-right: 6px;
}

/* 右矢印 */
.beer-handle::after {
  border-left: 10px solid #333;
  margin-left: 6px;
}

/* 中央の縦線 */
.beer-slider::before {
  content: '';
  position: absolute;
  top: 0;
  bottom: 0;
  left: var(--beer-position, 50%);
  width: 2px;
  background: #ffffff;
  z-index: 5;
  transform: translateX(-50%);
  pointer-events: none;
}
```

### 4.2 ラベルスタイル

```css
/* ===========================================
   Before/Afterラベル
   =========================================== */

/* Beforeラベル（左上） */
.beer-reveal[data-beer-label]::after {
  content: attr(data-beer-label);
  position: absolute;
  z-index: 3;
  color: #df6d14;                 /* オレンジ色 */
  font-size: 20px;
  background: #f8f5e9;            /* クリーム色背景 */
  top: 0;
  left: 0;
  padding: 12px 20px;
  font-weight: 700;
}

/* Afterラベル（右下） */
.beer-slider[data-beer-label]::after {
  content: attr(data-beer-label);
  position: absolute;
  z-index: 1;
  color: #df6d14;
  font-size: 20px;
  background: #f8f5e9;
  bottom: 0;
  right: 0;
  padding: 12px 20px;
  font-weight: 700;
}
```

### 4.3 カードレイアウト

```css
/* ===========================================
   カードレイアウト
   =========================================== */

/* セクションコンテナ */
.ly_beforeAfter {
  padding: 80px 20px;
}

/* カードグリッドコンテナ */
.bl_beforeAfterCont {
  display: flex;
  flex-direction: column;
  gap: 40px;
  padding-bottom: 40px;
}

/* 個別カード */
.bl_beforeAfterItem {
  background: #f8f5e9;            /* クリーム色背景 */
  border-radius: 8px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}
```

### 4.4 レスポンシブ対応

```css
/* ===========================================
   レスポンシブ対応
   =========================================== */

/* タブレット（768px以上） */
@media screen and (min-width: 768px) {
  .ly_beforeAfter {
    padding-inline: 20px;
    overflow: hidden;
  }

  .bl_beforeAfterCont {
    display: grid;
    grid-template-columns: 1fr 1fr;      /* 2カラム */
    justify-items: center;
    max-width: 1280px;
    margin: 0 auto;
    overflow-x: clip;
    padding-bottom: 80px;
  }

  .bl_beforeAfterItem {
    max-width: min(400px, 45vw);
  }

  .bl_beforeAfterItem h3 {
    text-align: center;
  }
}

/* デスクトップ（1280px以上） */
@media screen and (min-width: 1280px) {
  .bl_beforeAfterCont {
    grid-template-columns: 1fr 1fr 1fr;  /* 3カラム */
  }
}
```

---

## 5. JavaScript実装

### 5.1 スライダー操作スクリプト（必須）

以下のスクリプトをページ下部（`</body>`の直前）またはフッターテンプレートに配置：

```javascript
document.addEventListener("DOMContentLoaded", function () {
    var sliders = document.querySelectorAll(".beer-slider");

    sliders.forEach(function (slider) {
        var reveal = slider.querySelector(".beer-reveal");
        if (!reveal) return;

        // 既に初期化済みの場合はスキップ
        if (slider.querySelector(".beer-handle")) return;

        // ドラッグハンドルを動的に生成
        var handle = document.createElement("div");
        handle.className = "beer-handle";
        slider.appendChild(handle);

        // 初期位置を取得（デフォルト50%）
        var start = parseFloat(slider.dataset.start || "50");
        start = Math.min(100, Math.max(0, start));

        // スライダー幅をCSS変数に設定（Before画像の正確な表示に必要）
        function updateSliderWidth() {
            var sliderWidth = slider.getBoundingClientRect().width;
            slider.style.setProperty("--slider-width", sliderWidth + "px");
        }
        updateSliderWidth();
        window.addEventListener("resize", updateSliderWidth);

        // 位置をパーセントで設定
        function setPositionPercent(p) {
            reveal.style.width = p + "%";
            slider.style.setProperty("--beer-position", p + "%");
        }
        setPositionPercent(start);

        // ドラッグ状態管理
        var dragging = false;
        var currentSlider = slider;

        // マウス/タッチ位置からパーセントを計算して設定
        function pointerMove(clientX) {
            var rect = currentSlider.getBoundingClientRect();
            var pos = ((clientX - rect.left) / rect.width) * 100;
            pos = Math.min(100, Math.max(0, pos));
            setPositionPercent(pos);
        }

        // ドラッグ開始
        handle.addEventListener("pointerdown", function (e) {
            dragging = true;
            handle.setPointerCapture(e.pointerId);
            e.preventDefault();
            e.stopPropagation();
        });

        // ドラッグ中
        handle.addEventListener("pointermove", function (e) {
            if (!dragging) return;
            e.preventDefault();
            pointerMove(e.clientX);
        });

        // ドラッグ終了
        handle.addEventListener("pointerup", function (e) {
            dragging = false;
            e.preventDefault();
        });

        // ドラッグキャンセル
        handle.addEventListener("pointercancel", function () {
            dragging = false;
        });

        // スライダー上をクリックした場合もその位置に移動
        slider.addEventListener("click", function (e) {
            if (e.target === handle || handle.contains(e.target)) return;
            pointerMove(e.clientX);
        });
    });
});
```

### 5.2 スクリプトの配置方法

**方法1: インラインスクリプト（推奨）**

フッターテンプレート（`footer.php`等）に`<script>`タグで直接記述：

```php
<script>
// 上記のJavaScriptコードをここに配置
</script>
<?php wp_footer(); ?>
</body>
</html>
```

**方法2: 外部ファイル**

```php
// functions.php または enqueue用ファイル
function enqueue_before_after_scripts() {
    wp_enqueue_script(
        'before-after-slider',
        get_template_directory_uri() . '/assets/js/before-after-slider.js',
        array(),
        '1.0.0',
        true  // フッターで読み込み
    );
}
add_action('wp_enqueue_scripts', 'enqueue_before_after_scripts');
```

---

## 6. 動画モーダル（オプション）

### 6.1 モーダルHTML構造

ページ内に1つだけ配置（複数のボタンで共有）：

```html
<div class="bl_videoModal js_videoModal">
    <div class="bl_videoModal_overlay js_closeVideo"></div>
    <div class="bl_videoModal_inner">
        <button class="bl_videoModal_close js_closeVideo" aria-label="閉じる">&times;</button>
        <div class="bl_videoModal_content">
            <iframe src="" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
</div>
```

### 6.2 動画ボタンHTML

```html
<button type="button" class="el_beforeAfter_video js_openVideo" data-video="https://www.youtube.com/embed/VIDEO_ID">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
        <path d="M8 5v14l11-7z"/>
    </svg>
    動画でも見る
</button>
```

### 6.3 モーダルCSS

```css
/* ===========================================
   動画モーダル
   =========================================== */

/* 動画再生ボタン */
.el_beforeAfter_video {
  max-width: 224px;
  margin-inline: auto;
  display: flex;
  justify-content: center;
  gap: 10px;
  border-radius: 50px;
  padding: 8px 40px;
  background: #6fc424;            /* 緑色 */
  color: #fff;
  font-weight: 700;
  line-height: 120%;
  border: none;
  cursor: pointer;
}

.el_beforeAfter_video:hover {
  opacity: 0.9;
}

/* モーダル背景 */
.bl_videoModal {
  position: fixed;
  inset: 0;
  display: none;
  justify-content: center;
  align-items: center;
  background: rgba(0, 0, 0, 0.7);
  z-index: 9999;
}

/* モーダル表示時 */
.bl_videoModal.isOpen {
  display: flex;
}

/* モーダル内部コンテナ */
.bl_videoModal_inner {
  position: relative;
  width: 90%;
  max-width: 800px;
  background: #000;
  border-radius: 8px;
  overflow: hidden;
}

/* 動画iframe */
.bl_videoModal_content iframe {
  width: 100%;
  aspect-ratio: 16/9;
}

/* 閉じるボタン */
.bl_videoModal_close {
  position: absolute;
  top: 8px;
  right: 12px;
  background: none;
  border: none;
  color: #fff;
  font-size: 28px;
  cursor: pointer;
}

/* オーバーレイ（クリックで閉じる用） */
.bl_videoModal_overlay {
  position: absolute;
  inset: 0;
}

/* スクロール無効化（bodyに適用） */
body.noScroll {
  overflow: hidden;
}
```

### 6.4 モーダルJavaScript

```javascript
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.querySelector('.js_videoModal');
    if (!modal) return;

    const iframe = modal.querySelector('iframe');
    const openButtons = document.querySelectorAll('.js_openVideo');
    const closeButtons = modal.querySelectorAll('.js_closeVideo');

    // モーダルを開く
    openButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const videoUrl = btn.getAttribute('data-video');
            // 自動再生&ミュートパラメータを追加
            const sep = videoUrl.includes('?') ? '&' : '?';
            if (iframe) iframe.src = `${videoUrl}${sep}autoplay=1&mute=1`;
            modal.classList.add('isOpen');
            document.body.classList.add('noScroll');
        });
    });

    // モーダルを閉じる
    closeButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            modal.classList.remove('isOpen');
            if (iframe) iframe.src = '';  // 動画停止
            document.body.classList.remove('noScroll');
        });
    });
});
```

---

## 7. PHPテンプレート例

### 7.1 アーカイブページテンプレート（archive-example.php）

```php
<?php
/**
 * 施術事例アーカイブテンプレート
 */

if (!defined('ABSPATH')) exit;

get_header();
?>

<main class="ly_subp">
    <section class="ly_pageTop">
        <div class="ly_subpHeader">
            <p>Example</p>
            <h1>施術事例</h1>
        </div>
    </section>

    <section class="ly_beforeAfter">
        <?php
        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        $example_args = [
            'paged' => $paged,
            'post_type' => 'example',
            'posts_per_page' => 12,
            'post_status' => 'publish',
        ];
        $example_query = new WP_Query($example_args);

        if ($example_query->have_posts()): ?>
            <div class="bl_beforeAfterCont">
                <?php while ($example_query->have_posts()):
                    $example_query->the_post();

                    // ACFフィールド取得
                    $before_img = get_field('example_before');
                    $after_img = get_field('example_after');
                    $video = get_field('example_video');

                    // 画像URLを取得（配列/文字列両対応）
                    $before_img_url = is_array($before_img) ? $before_img['url'] : $before_img;
                    $after_img_url = is_array($after_img) ? $after_img['url'] : $after_img;
                    ?>

                    <div class="bl_beforeAfterItem">
                        <h3><?php the_title(); ?></h3>

                        <!-- ビフォアーアフタースライダー -->
                        <div class="beer-slider bl_beforeAfter_slider" data-beer-label="After" data-start="50">
                            <img src="<?php echo esc_url($after_img_url); ?>" alt="アフター画像">
                            <div class="beer-reveal" data-beer-label="Before">
                                <img src="<?php echo esc_url($before_img_url); ?>" alt="ビフォー画像">
                            </div>
                        </div>

                        <?php if ($video): ?>
                            <!-- 動画ボタン -->
                            <button type="button" class="el_beforeAfter_video js_openVideo"
                                data-video="<?php echo esc_url($video); ?>">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                                動画でも見る
                            </button>
                        <?php endif; ?>

                        <small class="hp_txtCenter">※効果には個人差があります。</small>
                    </div>

                <?php endwhile; ?>

                <!-- 動画モーダル（ページ内に1つ） -->
                <div class="bl_videoModal js_videoModal">
                    <div class="bl_videoModal_overlay js_closeVideo"></div>
                    <div class="bl_videoModal_inner">
                        <button class="bl_videoModal_close js_closeVideo" aria-label="閉じる">&times;</button>
                        <div class="bl_videoModal_content">
                            <iframe src="" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            // ページネーション
            if (function_exists('wp_pagenavi')) {
                wp_pagenavi(['query' => $example_query]);
            }
            wp_reset_postdata();
        endif;
        ?>
    </section>
</main>

<?php get_footer(); ?>
```

### 7.2 単一表示用パーツテンプレート

```php
<?php
/**
 * ビフォアーアフター単体表示パーツ
 *
 * 使用方法:
 * get_template_part('parts/before-after', null, [
 *     'before_url' => $before_url,
 *     'after_url' => $after_url,
 *     'title' => $title,
 *     'video_url' => $video_url, // オプション
 *     'start' => 50, // オプション（初期位置）
 * ]);
 */

$before_url = $args['before_url'] ?? '';
$after_url = $args['after_url'] ?? '';
$title = $args['title'] ?? '';
$video_url = $args['video_url'] ?? '';
$start = $args['start'] ?? 50;

if (!$before_url || !$after_url) return;
?>

<div class="bl_beforeAfterItem">
    <?php if ($title): ?>
        <h3><?php echo esc_html($title); ?></h3>
    <?php endif; ?>

    <div class="beer-slider bl_beforeAfter_slider"
         data-beer-label="After"
         data-start="<?php echo esc_attr($start); ?>">
        <img src="<?php echo esc_url($after_url); ?>" alt="アフター画像">
        <div class="beer-reveal" data-beer-label="Before">
            <img src="<?php echo esc_url($before_url); ?>" alt="ビフォー画像">
        </div>
    </div>

    <?php if ($video_url): ?>
        <button type="button" class="el_beforeAfter_video js_openVideo"
            data-video="<?php echo esc_url($video_url); ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M8 5v14l11-7z"/>
            </svg>
            動画でも見る
        </button>
    <?php endif; ?>

    <small class="hp_txtCenter">※効果には個人差があります。</small>
</div>
```

---

## 8. カスタマイズ方法

### 8.1 アスペクト比の変更

```css
.beer-slider {
  aspect-ratio: 16 / 9;   /* 横長に変更 */
  /* または */
  aspect-ratio: 1 / 1;    /* 正方形に変更 */
}
```

### 8.2 初期位置の変更

```html
<!-- 30%から開始 -->
<div class="beer-slider" data-start="30">

<!-- 70%から開始 -->
<div class="beer-slider" data-start="70">
```

### 8.3 ラベルテキストの変更

```html
<!-- 日本語ラベル -->
<div class="beer-slider" data-beer-label="施術後">
    <div class="beer-reveal" data-beer-label="施術前">
```

### 8.4 カラースキームの変更

```css
/* ラベルの色変更 */
.beer-reveal[data-beer-label]::after,
.beer-slider[data-beer-label]::after {
  color: #e74c3c;           /* 赤に変更 */
  background: #ffffff;      /* 白背景に変更 */
}

/* ハンドルの色変更 */
.beer-handle {
  background: #3498db;      /* 青に変更 */
}

.beer-handle::before {
  border-right-color: #fff; /* 矢印を白に */
}

.beer-handle::after {
  border-left-color: #fff;
}
```

### 8.5 ハンドルサイズの変更

```css
.beer-handle {
  width: 40px;   /* 小さくする */
  height: 40px;
}

/* 矢印も調整 */
.beer-handle::before,
.beer-handle::after {
  border-top: 6px solid transparent;
  border-bottom: 6px solid transparent;
}

.beer-handle::before {
  border-right: 8px solid #333;
}

.beer-handle::after {
  border-left: 8px solid #333;
}
```

### 8.6 中央線を非表示

```css
.beer-slider::before {
  display: none;
}
```

---

## 実装チェックリスト

実装時に確認すべき項目：

- [ ] ACFフィールドグループが作成されている
- [ ] CSSファイルが読み込まれている
- [ ] JavaScriptがページ下部で読み込まれている
- [ ] 画像が正しく表示されている
- [ ] ドラッグ操作が機能している
- [ ] クリック操作が機能している
- [ ] タッチデバイスで動作確認
- [ ] レスポンシブ表示を確認
- [ ] 動画モーダルが開閉する（使用時）
- [ ] ページ遷移後も正常に動作する

---

## トラブルシューティング

### スライダーが動かない

1. JavaScriptがエラーなく読み込まれているか確認
2. `.beer-slider`クラスが正しく付与されているか確認
3. `.beer-reveal`要素が存在するか確認

### Before画像が見切れる

1. CSS変数`--slider-width`が正しく設定されているか確認
2. `updateSliderWidth()`が実行されているか確認
3. ウィンドウリサイズ時にも更新されるか確認

### タッチ操作が効かない

1. `touch-action: none`がスライダーに設定されているか確認
2. Pointer Events APIがサポートされているか確認

### 動画が再生されない

1. YouTube/Vimeoの埋め込みURLが正しいか確認
2. `iframe`のsrc属性が更新されているか確認
3. 自動再生がブロックされていないか確認

---

## ライセンス・クレジット

このビフォアーアフター機能はVanilla JavaScriptで実装されており、外部ライブラリへの依存はありません。自由に使用・改変が可能です。
