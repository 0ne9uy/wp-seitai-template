# スマホ追従ボタン（フローティングボタン）実装仕様書

## 概要

スマートフォン表示時に画面下部に固定表示されるCTAボタン群の実装仕様。
iOS Safari の viewport 問題に対応し、キーボード表示時やアドレスバー変動時も正しく追従する。

---

## 1. ファイル構成

```
theme-root/
├── assets/
│   ├── css/
│   │   └── base.css              # グローバルCSS（追従ボタンのスタイル含む）
│   └── js/
│       └── floating-buttons.js   # iOS Safari対応のJS
├── inc/
│   ├── enqueue-styles.php        # CSS読み込み
│   └── enqueue-scripts.php       # JS読み込み
├── parts/
│   └── snippets/
│       └── floating-btns.php     # ボタンのHTML（テンプレートパーツ）
└── footer.php                    # テンプレートパーツの呼び出し
```

---

## 2. HTML/PHP構造

### 2.1 テンプレートパーツ (`parts/snippets/floating-btns.php`)

```php
<?php
/**
 * スマホ追従ボタン
 *
 * @description スマートフォン表示時のみ画面下部に固定表示されるCTAボタン
 * @usage footer.php で get_template_part() で呼び出す
 */

// 外部ファイルからの直接アクセス防止
if (!defined('ABSPATH')) {
    exit;
}

// リンク先の設定（カスタマイズ可能）
$buttons = [
    [
        'url'   => '/job-description/',  // または get_post_type_archive_link('job_description')
        'label' => '募集要項',
        'class' => 'is-primary',         // ボタンの色分け用
    ],
    [
        'url'   => '/entry/',
        'label' => 'フォームから<br>応募',
        'class' => 'is-accent',
    ],
    [
        'url'   => 'https://line.me/R/ti/p/xxxxx',  // LINEリンク
        'label' => 'LINEで応募',
        'class' => 'is-line',
        'target' => '_blank',
        'rel'   => 'noopener noreferrer',
    ],
];
?>

<section class="ly_floatingBtns sm_only" aria-label="お問い合わせボタン">
    <?php foreach ($buttons as $btn) : ?>
        <a
            href="<?= esc_url($btn['url']) ?>"
            class="ly_floatingBtns_btn <?= esc_attr($btn['class'] ?? '') ?>"
            <?php if (!empty($btn['target'])) : ?>
                target="<?= esc_attr($btn['target']) ?>"
            <?php endif; ?>
            <?php if (!empty($btn['rel'])) : ?>
                rel="<?= esc_attr($btn['rel']) ?>"
            <?php endif; ?>
        >
            <?= $btn['label'] ?>
        </a>
    <?php endforeach; ?>
</section>
```

### 2.2 footer.php での呼び出し

```php
<?php get_template_part('parts/snippets/floating-btns'); ?>
</body>
</html>
```

---

## 3. CSS実装

### 3.1 基本スタイル（モバイルファースト）

```css
/* ================================================
   スマホ追従ボタン
   ================================================ */

/**
 * position: sticky を使用する理由
 * - fixed だと iOS Safari で画面外に飛ぶ問題がある
 * - sticky なら親要素の範囲内で追従し、安定動作する
 * - ただし親要素（body/html）に overflow: hidden がないこと
 */

.ly_floatingBtns {
    /* 配置 */
    position: sticky;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 990;  /* ヘッダー(1000)より低く、コンテンツより高く */

    /* レイアウト */
    display: flex;
    height: 56px;  /* タップしやすい高さ */

    /* iOS Safari対応: transform で位置調整するため */
    transition: transform 0.3s ease;
    will-change: transform;
}

.ly_floatingBtns_btn {
    /* サイズ */
    width: calc(100% / 3);  /* ボタン数で割る */
    height: 100%;

    /* レイアウト */
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    /* テキスト */
    color: #fff;
    font-weight: 700;
    font-size: 12px;
    line-height: 1.3;
    text-align: center;
    text-decoration: none;

    /* ボーダー */
    border: 1px solid #fff;
    border-bottom: 0;
    border-radius: 8px 8px 0 0;

    /* タップ時のハイライト無効化（iOS） */
    -webkit-tap-highlight-color: transparent;
}

/* ボタンカラーバリエーション */
.ly_floatingBtns_btn.is-primary {
    background: #251b13;  /* ダークカラー */
}

.ly_floatingBtns_btn.is-accent {
    background: #fd5500;  /* アクセントカラー */
}

.ly_floatingBtns_btn.is-line {
    background: #00b900;  /* LINE公式カラー */
}

/* ホバー状態（PC向け、モバイルでは不要だが念のため） */
@media (hover: hover) {
    .ly_floatingBtns_btn:hover {
        opacity: 0.9;
    }
}
```

### 3.2 レスポンシブ対応（非表示）

```css
/* スマホのみ表示するためのヘルパークラス */
.sm_only {
    display: block !important;
}

/* 768px以上で非表示 */
@media screen and (min-width: 768px) {
    .sm_only {
        display: none !important;
    }
}
```

### 3.3 セーフエリア対応（iPhone X以降のノッチ対応）

```css
/* iPhone X以降のセーフエリア対応 */
@supports (padding-bottom: env(safe-area-inset-bottom)) {
    .ly_floatingBtns {
        /* セーフエリア分の余白を追加 */
        padding-bottom: env(safe-area-inset-bottom);
        /* 高さを調整 */
        height: calc(56px + env(safe-area-inset-bottom));
    }

    .ly_floatingBtns_btn {
        /* ボタン自体の高さは固定 */
        height: 56px;
    }
}
```

### 3.4 アニメーション対応（アクセシビリティ）

```css
/* ユーザーがアニメーション軽減を設定している場合 */
@media (prefers-reduced-motion: reduce) {
    .ly_floatingBtns {
        transition: none;
    }
}
```

---

## 4. JavaScript実装（iOS Safari対応）

### 4.1 メインスクリプト (`assets/js/floating-buttons.js`)

```javascript
/**
 * スマホ追従ボタン - iOS Safari対応
 *
 * @description
 * iOS Safariではキーボード表示時やアドレスバーの表示/非表示で
 * window.innerHeight と実際の表示領域にズレが生じる。
 * visualViewport API を使用してこの問題を解決する。
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/API/Visual_Viewport_API
 */

(function() {
    'use strict';

    // DOM要素の取得
    const floatingBtns = document.querySelector('.ly_floatingBtns');

    // 要素が存在しない場合は終了
    if (!floatingBtns) {
        return;
    }

    // モバイル判定（768px未満）
    const isMobile = () => window.innerWidth < 768;

    // iOS判定
    // - navigator.userAgent での判定
    // - iPad (iPadOS 13+) は MacIntel と報告されるため maxTouchPoints で補完
    const isIOS = () => {
        return /iPad|iPhone|iPod/.test(navigator.userAgent) ||
               (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    };

    /**
     * 追従ボタンの位置を更新
     *
     * visualViewport API を使用して、実際の表示領域に基づいて
     * ボタンの位置を調整する。
     */
    function updatePosition() {
        // モバイル以外では何もしない
        if (!isMobile()) {
            floatingBtns.style.transform = '';
            return;
        }

        // visualViewport API が利用可能な場合のみ処理
        if (window.visualViewport) {
            // visualViewport の高さとオフセット
            const vvHeight = window.visualViewport.height;
            const vvOffsetTop = window.visualViewport.offsetTop;

            // window.innerHeight との差分を計算
            // この差分がキーボードやアドレスバーによる「隠れた領域」
            const winHeight = window.innerHeight;
            const offsetY = winHeight - vvHeight - vvOffsetTop;

            // 差分がある場合は transform で上にずらす
            if (offsetY > 0) {
                floatingBtns.style.transform = `translateY(-${offsetY}px)`;
            } else {
                floatingBtns.style.transform = '';
            }
        }
    }

    // visualViewport API のイベントリスナー設定
    if (window.visualViewport) {
        // resize: キーボードの表示/非表示、画面回転
        window.visualViewport.addEventListener('resize', updatePosition);

        // scroll: ピンチズームやページスクロール時のビューポート移動
        window.visualViewport.addEventListener('scroll', updatePosition);
    }

    // 従来のイベントリスナー（フォールバック）
    window.addEventListener('resize', updatePosition);
    window.addEventListener('scroll', updatePosition, { passive: true });

    // 画面回転時の対応
    window.addEventListener('orientationchange', () => {
        // 回転後に少し遅延を入れてから更新（レイアウト完了を待つ）
        setTimeout(updatePosition, 100);
    });

    // 初期実行
    updatePosition();

    // DOMContentLoaded 後に再実行（レイアウトシフト対策）
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updatePosition);
    }

    // ページ読み込み完了後に再実行（画像読み込みなどによるリフロー対策）
    window.addEventListener('load', () => {
        setTimeout(updatePosition, 100);
    });

})();
```

### 4.2 visualViewport API の解説

```
┌─────────────────────────────────────┐
│         window.innerHeight          │  ← ブラウザが報告する高さ
│  ┌─────────────────────────────┐   │
│  │     アドレスバー（Safari）    │   │
│  ├─────────────────────────────┤   │
│  │                             │   │
│  │                             │   │
│  │   visualViewport.height     │   │  ← 実際に見えている領域
│  │                             │   │
│  │                             │   │
│  ├─────────────────────────────┤   │
│  │      キーボード（表示時）     │   │
│  └─────────────────────────────┘   │
└─────────────────────────────────────┘

offsetY = window.innerHeight - visualViewport.height - visualViewport.offsetTop
```

---

## 5. WordPress統合

### 5.1 スクリプト読み込み (`inc/enqueue-scripts.php`)

```php
<?php
/**
 * スクリプトの読み込み
 */

if (!defined('ABSPATH')) {
    exit;
}

function theme_enqueue_scripts() {
    $uri = get_template_directory_uri();
    $version = wp_get_theme()->get('Version');

    // 追従ボタン用JS（全ページで読み込み）
    wp_enqueue_script(
        'floating-buttons',
        $uri . '/assets/js/floating-buttons.js',
        [],           // 依存なし（Vanilla JS）
        $version,
        true          // フッターで読み込み
    );
}

add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');
```

### 5.2 defer属性の追加（推奨）

```php
<?php
/**
 * スクリプトにdefer属性を追加
 */

function add_defer_attribute($tag, $handle) {
    // 対象のスクリプト
    $defer_scripts = ['floating-buttons'];

    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }

    return $tag;
}

add_filter('script_loader_tag', 'add_defer_attribute', 10, 2);
```

### 5.3 条件分岐の例

```php
<?php
// 特定のページでボタンを非表示にする場合
// footer.php で条件分岐

// 404ページとプライバシーポリシーでは非表示
if (!is_404() && !is_page('privacy-policy')) {
    get_template_part('parts/snippets/floating-btns');
}
```

---

## 6. iOS Safari 対応の重要ポイント

### 6.1 避けるべきCSS

```css
/* ❌ 避けるべき */
html, body {
    overflow: hidden;      /* sticky が効かなくなる */
    height: 100%;          /* iOS で問題を起こす可能性 */
    position: fixed;       /* スクロールできなくなる */
}

.ly_floatingBtns {
    position: fixed;       /* iOS でアドレスバーに追従しない */
    bottom: 0;
    /* 100vh を使う */
    height: 100vh;         /* iOS Safari で実際より大きくなる */
}
```

### 6.2 推奨するCSS

```css
/* ✅ 推奨 */
html, body {
    /* overflow は設定しない、または visible */
    min-height: 100%;      /* 100vh ではなく % を使用 */
}

.ly_floatingBtns {
    position: sticky;      /* fixed より安定 */
    bottom: 0;
}
```

### 6.3 100vh問題への対応

```css
/* iOS Safari では 100vh がアドレスバーを含まないため */
/* 代替手段として CSS変数を使用 */

:root {
    --vh: 1vh;
}

/* JSで実際の高さを設定 */
```

```javascript
// 実際のビューポート高さを計算してCSS変数に設定
function setVhProperty() {
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
}

window.addEventListener('resize', setVhProperty);
setVhProperty();

// 使用例: height: calc(var(--vh, 1vh) * 100);
```

### 6.4 タッチイベントの最適化

```javascript
// passive: true でスクロールパフォーマンスを向上
window.addEventListener('scroll', handler, { passive: true });
window.addEventListener('touchmove', handler, { passive: true });
```

---

## 7. カスタマイズオプション

### 7.1 ボタン数の変更

```css
/* 2ボタンの場合 */
.ly_floatingBtns_btn {
    width: calc(100% / 2);
}

/* 4ボタンの場合 */
.ly_floatingBtns_btn {
    width: calc(100% / 4);
    font-size: 10px;  /* 文字を小さく */
}
```

### 7.2 高さの変更

```css
.ly_floatingBtns {
    height: 64px;  /* デフォルト: 56px */
}

@supports (padding-bottom: env(safe-area-inset-bottom)) {
    .ly_floatingBtns {
        height: calc(64px + env(safe-area-inset-bottom));
    }

    .ly_floatingBtns_btn {
        height: 64px;
    }
}
```

### 7.3 アイコン付きボタン

```html
<a href="/entry/" class="ly_floatingBtns_btn is-accent">
    <svg class="ly_floatingBtns_icon" width="20" height="20" viewBox="0 0 24 24">
        <!-- アイコンSVG -->
    </svg>
    <span>応募</span>
</a>
```

```css
.ly_floatingBtns_icon {
    width: 20px;
    height: 20px;
    margin-bottom: 2px;
    fill: currentColor;
}

.ly_floatingBtns_btn span {
    font-size: 10px;
}
```

### 7.4 スクロール時の表示/非表示

```javascript
// 下スクロールで非表示、上スクロールで表示
(function() {
    const floatingBtns = document.querySelector('.ly_floatingBtns');
    if (!floatingBtns) return;

    let lastScrollY = 0;
    let ticking = false;

    function updateVisibility() {
        const currentScrollY = window.scrollY;

        // ページトップ付近では常に表示
        if (currentScrollY < 100) {
            floatingBtns.classList.remove('is-hidden');
        }
        // 下スクロール
        else if (currentScrollY > lastScrollY) {
            floatingBtns.classList.add('is-hidden');
        }
        // 上スクロール
        else {
            floatingBtns.classList.remove('is-hidden');
        }

        lastScrollY = currentScrollY;
        ticking = false;
    }

    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(updateVisibility);
            ticking = true;
        }
    }, { passive: true });
})();
```

```css
.ly_floatingBtns.is-hidden {
    transform: translateY(100%);
}
```

---

## 8. 実装チェックリスト

### 8.1 HTML/PHP

- [ ] セマンティックな要素を使用（`<section>` or `<nav>`）
- [ ] `aria-label` でアクセシビリティ対応
- [ ] 外部リンクに `target="_blank"` と `rel="noopener noreferrer"`
- [ ] URLは `esc_url()` でエスケープ
- [ ] テキストは必要に応じて `esc_html()` でエスケープ

### 8.2 CSS

- [ ] `position: sticky` を使用（`fixed` は避ける）
- [ ] `z-index` が適切（ヘッダーより低く、コンテンツより高く）
- [ ] セーフエリア対応（`env(safe-area-inset-bottom)`）
- [ ] タップターゲットサイズ（最低44x44px推奨）
- [ ] `-webkit-tap-highlight-color: transparent` でハイライト無効化
- [ ] `@media (prefers-reduced-motion)` 対応

### 8.3 JavaScript

- [ ] `visualViewport` API を使用
- [ ] `passive: true` でスクロールイベント最適化
- [ ] デバイス判定（iOS, モバイル）
- [ ] フォールバック処理（API非対応ブラウザ向け）
- [ ] メモリリーク防止（イベントリスナーの適切な管理）

### 8.4 テスト

- [ ] iOS Safari（iPhone）で動作確認
- [ ] iOS Safari（iPad）で動作確認
- [ ] Android Chrome で動作確認
- [ ] キーボード表示時の挙動確認
- [ ] 画面回転時の挙動確認
- [ ] ピンチズーム時の挙動確認
- [ ] 768px以上で非表示になることを確認
- [ ] セーフエリア（ノッチ）対応の確認

---

## 9. トラブルシューティング

### 9.1 ボタンが画面外に飛ぶ

**原因**: `position: fixed` を使用している、または親要素に `overflow: hidden` がある

**解決策**:
```css
/* fixed を sticky に変更 */
.ly_floatingBtns {
    position: sticky;  /* fixed から変更 */
}

/* 親要素の overflow を確認 */
html, body {
    overflow-x: hidden;  /* overflow: hidden は避ける */
}
```

### 9.2 キーボード表示時にボタンが見えない

**原因**: visualViewport の位置調整が効いていない

**解決策**: JavaScript が正しく動作しているか確認
```javascript
// デバッグ用ログを追加
console.log('visualViewport available:', !!window.visualViewport);
if (window.visualViewport) {
    console.log('height:', window.visualViewport.height);
    console.log('offsetTop:', window.visualViewport.offsetTop);
}
```

### 9.3 ボタンがガタつく

**原因**: transition が適用されていない、または頻繁な再描画

**解決策**:
```css
.ly_floatingBtns {
    transition: transform 0.3s ease;
    will-change: transform;
}
```

### 9.4 z-index が効かない

**原因**: 親要素に `transform` や `opacity` が設定されている

**解決策**: z-index を調整するか、DOMの配置を変更

---

## 10. 参考リンク

- [Visual Viewport API - MDN](https://developer.mozilla.org/en-US/docs/Web/API/Visual_Viewport_API)
- [The trick to viewport units on mobile - CSS-Tricks](https://css-tricks.com/the-trick-to-viewport-units-on-mobile/)
- [Safe area insets - WebKit](https://webkit.org/blog/7929/designing-websites-for-iphone-x/)
- [position: sticky - MDN](https://developer.mozilla.org/en-US/docs/Web/CSS/position#sticky)

---

## 11. 変更履歴

| バージョン | 日付 | 変更内容 |
|-----------|------|---------|
| 1.0.0 | 2024-XX-XX | 初版作成 |

