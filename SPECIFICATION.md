# WordPressテーマ「noras original template」仕様書

## 1. テーマ概要

### 1.1 基本情報
- **テーマ名**: noras original template
- **バージョン**: 1.2
- **開発者**: noras inc. (shirako)
- **開発者URL**: https://norasinc.jp
- **ライセンス**: GNU General Public License v2 or later
- **説明**: ノラスオリジナルテンプレート

### 1.2 テーマの特徴
- モダンなWordPressテーマ構造
- カスタム投稿タイプ対応
  - デフォルト投稿に`/column/`アーカイブを追加
  - お客様の声（`/voice/`）
  - 推薦者の声（`/recommend/`）
- OGP（Open Graph Protocol）対応
- レスポンシブデザイン対応（モバイルファースト設計）
- BEM記法によるCSS設計（FLOCSS風の命名規則）
- モジュール化されたファイル構成
- Noto Sans JP日本語フォント内蔵

## 2. ディレクトリ構造

```
wp_template-main starter/
├── 404.php                    # 404エラーページテンプレート
├── archive.php                # アーカイブページテンプレート（/column/）
├── archive-voice.php          # お客様の声アーカイブテンプレート
├── archive-recommend.php      # 推薦者の声アーカイブテンプレート
├── front-page.php             # トップページテンプレート
├── index.php                  # フォールバックテンプレート
├── single.php                 # 投稿詳細ページテンプレート
├── functions.php              # テーマの機能定義ファイル
├── style.css                  # テーマ情報定義ファイル
├── readme.md                  # 使用方法ドキュメント
├── screenshot.png             # テーマのスクリーンショット
├── LICENSE                    # GPLライセンスファイル
├── SPECIFICATION.md           # このファイル
│
├── assets/                    # アセットファイル
│   ├── css/                   # スタイルシート
│   │   ├── base.css           # 全ページ共通スタイル（リセット、レイアウト、モジュール、カード）
│   │   ├── main.css           # トップページ専用スタイル
│   │   ├── subp.css           # サブページ共通スタイル
│   │   ├── archive.css        # コラムアーカイブページスタイル（拡張用）
│   │   ├── archive-voice.css  # お客様の声アーカイブスタイル（拡張用）
│   │   ├── archive-recommend.css # 推薦者の声アーカイブスタイル（拡張用）
│   │   ├── single.css         # 投稿詳細ページスタイル
│   │   ├── 404.css            # 404ページスタイル
│   │   └── pages/             # 固定ページ専用スタイル
│   │       ├── page-contact.css
│   │       └── page-service.css
│   ├── js/                    # JavaScriptファイル
│   │   ├── index.js           # 全ページ共通JS（ハンバーガーメニュー）
│   │   └── subp.js            # サブページ共通JS
│   ├── fonts/                 # フォントファイル
│   │   ├── NotoSansJP-Bold.woff
│   │   ├── NotoSansJP-ExtraBold.woff
│   │   ├── NotoSansJP-Light.woff
│   │   ├── NotoSansJP-Medium.woff
│   │   └── NotoSansJP-Regular.woff
│   └── img/                   # 画像ファイル
│       ├── icons/
│       │   └── external-link.svg
│       └── no-image.png       # デフォルト画像（OGP/サムネイル用）
│
├── inc/                       # 機能モジュール
│   ├── theme-support.php      # テーマサポート機能
│   ├── custom-post-types.php  # カスタム投稿タイプ設定
│   ├── my-ogp.php             # OGP設定
│   ├── enque-my-styles.php    # CSS読み込み管理
│   ├── enque-my-scripts.php   # JavaScript読み込み管理
│   └── link.php               # URL定義
│
├── parts/                     # パーツテンプレート
│   ├── header/
│   │   ├── header.php         # ヘッダー（DOCTYPE宣言〜bodyタグ開始を含む）
│   │   └── navigation.php     # 追加ナビゲーション（プレースホルダー）
│   └── footer/
│       └── footer.php         # フッター（bodyタグ閉じ〜html閉じを含む）
│
├── pages/                     # 固定ページテンプレート
│   ├── page-contact.php       # お問い合わせページ
│   └── page-service.php       # サービスページ
│
└── stubs/                     # スタブファイル（プラグイン未インストール時のエラー回避用）
    ├── get_field.php          # ACF用スタブ
    └── wp-pagenavi.php        # WP-PageNavi用スタブ
```

## 3. 主要ファイルの詳細仕様

### 3.1 functions.php
テーマのエントリーポイント。以下のモジュールを読み込む：

```php
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
```

**注意**: `inc/link.php`はfunctions.phpでは読み込まれず、各テンプレートファイルで個別にインクルードされる。

### 3.2 inc/theme-support.php
WordPressのテーマサポート機能を有効化：

| 機能 | 関数/設定 | 説明 |
|------|----------|------|
| 投稿サムネイル | `add_theme_support('post-thumbnails')` | アイキャッチ画像機能 |
| カスタムメニュー | `add_theme_support('menus')` | メニュー管理機能 |
| タイトルタグ | `add_theme_support('title-tag')` | 自動タイトルタグ生成 |
| ウィジェット | `register_sidebar()` | Main Sidebar（ID: `main-sidebar`）を登録 |

### 3.3 inc/custom-post-types.php
カスタム投稿タイプの設定：

#### デフォルト投稿（post）のアーカイブ追加
- **アーカイブスラッグ**: `column`
- **管理画面表示名**: 「投稿」
- **URL構造**: `/column/` でアクセス可能

#### お客様の声（voice）
| 項目 | 値 |
|------|-----|
| 投稿タイプ名 | `voice` |
| ラベル | お客様の声 |
| アーカイブURL | `/voice/` |
| アイコン | `dashicons-format-quote` |
| サポート機能 | title, editor, thumbnail, excerpt |
| **個別ページ** | **404にリダイレクト（一覧のみ表示）** |

#### 推薦者の声（recommend）
| 項目 | 値 |
|------|-----|
| 投稿タイプ名 | `recommend` |
| ラベル | 推薦者の声 |
| アーカイブURL | `/recommend/` |
| アイコン | `dashicons-star-filled` |
| サポート機能 | title, editor, thumbnail, excerpt |
| **個別ページ** | **404にリダイレクト（一覧のみ表示）** |

### 3.4 inc/my-ogp.php
OGP（Open Graph Protocol）とTwitterカードの設定：

#### 初期設定
| 項目 | 値 | 説明 |
|------|-----|------|
| デフォルト画像 | `assets/img/no-image.png` | アイキャッチ未設定時の代替画像 |
| Twitterアカウント | `@norasinc` | Twitter:site用 |
| Twitterカードタイプ | `summary_large_image` | 大きな画像カード |
| Facebook APP ID | 空 | 設定可能（必要に応じて） |

### 3.5 inc/enque-my-styles.php
CSSファイルの読み込み管理：

| 条件 | ファイル | ハンドル名 |
|------|---------|-----------|
| 全ページ | `base.css` | `base-css` |
| 全ページ | `main.css` | `main-css` |
| トップ以外 | `subp.css` | `subp-css` |
| コラムアーカイブ（`is_post_type_archive('post')`） | `archive.css` | `news-archive-css` |
| お客様の声アーカイブ（`is_post_type_archive('voice')`） | `archive-voice.css` | `archive-voice-css` |
| 推薦者の声アーカイブ（`is_post_type_archive('recommend')`） | `archive-recommend.css` | `archive-recommend-css` |
| 個別投稿/固定ページ | `single.css` | `single-css` |
| 固定ページ（contact） | `pages/page-contact.css` | `contact-css` |
| 固定ページ（service） | `pages/page-service.css` | `service-css` |
| 404ページ | `404.css` | `404-css` |

### 3.6 inc/enque-my-scripts.php
JavaScriptファイルの読み込み管理：

| 条件 | ファイル | ハンドル名 |
|------|---------|-----------|
| 全ページ | `index.js` | `index-js` |
| トップ以外 | `subp.js` | `subp-js` |

### 3.7 inc/link.php
サイト内の主要URLを定義（各テンプレートで使用）：

```php
$uri = get_template_directory_uri();
$home = esc_url(home_url("/"));
$service = esc_url(home_url("/service/"));
$membership = esc_url(home_url("/membership/"));
$medicine = esc_url(home_url("/medicine/"));
$column = esc_url(home_url("/column/"));
$company = esc_url(home_url("/company/"));
$contact = esc_url(home_url("/contact/"));
$voice = esc_url(home_url("/voice/"));
$recommend = esc_url(home_url("/recommend/"));
```

## 4. テンプレートファイルの詳細

### 4.1 front-page.php（トップページ）

**ファイルパス**: `/front-page.php`
**用途**: フロントページ（ホームページ）の表示

**HTML構造**:
```html
<main class="">
  <section id="fv" class="ly_fv">
    <div class="ly_cont">
      <div class="bl_fvCont">
        <h1 class="bl_fvCont_ttl hp_txtCenter">...</h1>
        <div class="bl_fvContExp">
          <a href="..." class="el_btn el_btn_withIcon">document</a>
          <a href="<?= $contact; ?>" class="el_btn">Contact</a>
        </div>
      </div>
    </div>
  </section>
</main>
```

### 4.2 index.php（フォールバックテンプレート）

**ファイルパス**: `/index.php`
**用途**: 他のテンプレートがマッチしない場合のフォールバック

**機能**: WordPressの標準ループを使用して投稿一覧を表示

### 4.3 archive.php（コラムアーカイブページ）

**ファイルパス**: `/archive.php`
**用途**: 投稿一覧ページ（`/column/`）

**投稿取得クエリ**:
```php
$args = array(
    'post_type' => 'post',
    'posts_per_page' => 16,
    'orderby' => 'date',
    'order' => 'DESC',
);
```

**使用クラス**:
- `ly_subp`: サブページレイアウト
- `bl_content`: コンテンツブロック（グリッドレイアウト）
- `bl_column`: コラム記事カード（リンク付き）

### 4.4 archive-voice.php（お客様の声アーカイブ）

**ファイルパス**: `/archive-voice.php`
**用途**: お客様の声一覧ページ（`/voice/`）

**特徴**:
- リンクなし（個別ページは404にリダイレクトされるため）
- タイトルと抜粋を表示

**使用クラス**:
- `bl_column_content`: カードコンテンツ部
- `bl_column_title`: カードタイトル
- `bl_column_excerpt`: カード抜粋文

### 4.5 archive-recommend.php（推薦者の声アーカイブ）

**ファイルパス**: `/archive-recommend.php`
**用途**: 推薦者の声一覧ページ（`/recommend/`）

**特徴**: archive-voice.phpと同様の構造

### 4.6 single.php（投稿詳細ページ）

**ファイルパス**: `/single.php`
**用途**: 個別投稿の表示

**注意**: voice/recommend投稿タイプの個別ページは404にリダイレクトされる

### 4.7 404.php（404エラーページ）

**ファイルパス**: `/404.php`
**用途**: ページが見つからない場合の表示

## 5. パーツテンプレート

### 5.1 parts/header/header.php
ヘッダー部分（DOCTYPE〜header要素まで）

### 5.2 parts/header/navigation.php
追加ナビゲーション用プレースホルダー

### 5.3 parts/footer/footer.php
フッター部分（footer要素〜html閉じタグまで）

## 6. CSS設計

### 6.1 命名規則
FLOCSS風のBEM記法を採用：

| プレフィックス | 用途 | 例 |
|--------------|------|-----|
| `ly_` | レイアウト | `ly_header`, `ly_footer`, `ly_cont`, `ly_fv`, `ly_subp` |
| `bl_` | ブロックモジュール | `bl_fvCont`, `bl_header_logo`, `bl_content`, `bl_column` |
| `el_` | エレメントモジュール | `el_btn`, `el_postTtl`, `el_humb` |
| `hp_` | ヘルパー/ユーティリティ | `hp_txtCenter`, `hp_mt20`, `hp_bgBlack` |
| `sm_`, `md_`, `lg_` | レスポンシブ表示制御 | `sm_only`, `md_only`, `lg_only` |
| `js_` | JavaScript用フック | `js_humb` |
| `is_` | 状態クラス | `is_active`, `is_open`, `is_menuOpen` |

### 6.2 共通カードスタイル（base.css）

**グリッドレイアウト（.bl_content）**:
| ブレイクポイント | カラム数 | ギャップ |
|----------------|---------|---------|
| 〜767px | 2列 | 20px |
| 768px〜 | 3列 | 30px |
| 1280px〜 | 4列 | 30px |

**カードスタイル（.bl_column）**:
```css
background: #fff
border-radius: 8px
box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1)
/* ホバー時 */
transform: translateY(-4px)
box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15)
```

### 6.3 ブレイクポイント

| ブレイクポイント | 対象 |
|----------------|------|
| 〜767px | モバイル（デフォルト） |
| 768px〜 | タブレット/デスクトップ |
| 1280px〜 | ワイドデスクトップ |

## 7. JavaScript機能

### 7.1 index.js
- **用途**: 全ページ共通のJavaScript
- **実装機能**: ハンバーガーメニュー制御

**状態クラス**:
| クラス | 適用先 | 効果 |
|--------|--------|------|
| `.is_active` | `.el_humb` | 3本線を×に変形 |
| `.is_open` | `.bl_header_nav` | モバイルメニュー表示 |
| `.is_menuOpen` | `body` | スクロール無効化 |

### 7.2 subp.js
- **用途**: サブページ共通のJavaScript
- **読み込み条件**: トップページ以外
- **現在の実装**: 拡張用プレースホルダー

## 8. URL構造

| ページ | URL | テンプレート |
|--------|-----|-------------|
| トップページ | `/` | front-page.php |
| コラム一覧 | `/column/` | archive.php |
| コラム詳細 | `/column/{slug}/` | single.php |
| お客様の声一覧 | `/voice/` | archive-voice.php |
| お客様の声詳細 | `/voice/{slug}/` | **404にリダイレクト** |
| 推薦者の声一覧 | `/recommend/` | archive-recommend.php |
| 推薦者の声詳細 | `/recommend/{slug}/` | **404にリダイレクト** |
| お問い合わせ | `/contact/` | pages/page-contact.php |
| サービス | `/service/` | pages/page-service.php |

## 9. スタブファイル

### 9.1 stubs/get_field.php
**用途**: Advanced Custom Fieldsプラグイン未インストール時のエラー回避

### 9.2 stubs/wp-pagenavi.php
**用途**: WP-PageNaviプラグイン未インストール時のエラー回避

**読み込み**: `functions.php`で条件付き読み込み（関数が存在しない場合のみ）

## 10. 推奨プラグイン

| プラグイン名 | 用途 | 必須度 |
|-------------|------|--------|
| **Contact Form 7** | お問い合わせフォーム作成 | 必須（page-contact.php使用時） |
| Classic Editor | クラシックエディタ | 推奨 |
| All-in-One WP Security | セキュリティ強化 | 推奨 |
| WP-PageNavi | ページネーション | 任意（スタブあり） |
| Advanced Custom Fields | カスタムフィールド作成 | 任意（スタブあり） |

## 11. セキュリティ対策

| 対策 | 実装箇所 | 説明 |
|------|---------|------|
| `ABSPATH`チェック | 全テンプレート | 直接アクセス防止 |
| `esc_url()` | link.php, my-ogp.php | URL出力のエスケープ |
| `esc_attr()` | my-ogp.php | 属性値のエスケープ |
| `wp_reset_postdata()` | archive*.php | クエリのリセット |
| 個別ページリダイレクト | custom-post-types.php | voice/recommend個別ページを404に |

## 12. 注意事項

### 12.1 依存関係
- **Contact Form 7プラグイン**: お問い合わせページで必須
- **パーマリンク設定**: カスタム投稿タイプ追加後は「設定」→「パーマリンク」で「変更を保存」が必要

### 12.2 未実装機能
- ページネーション
- パンくずリスト
- サイドバーウィジェット

## 13. バージョン履歴

| バージョン | 日付 | 変更内容 |
|-----------|------|---------|
| v1.0 | - | 初回リリース |
| v1.1 | 2026年1月 | バグ修正: 404 CSSパス、.bl_columnクラス名、スタブファイル読み込み |
| v1.2 | 2026年1月 | 機能追加: voice/recommendカスタム投稿タイプ、front-page.php追加、CSS共通化、archive.phpリファクタリング |

---

**最終更新日**: 2026年1月
**作成者**: AI Assistant（テーマ詳細調査に基づく）
