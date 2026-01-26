# RocketWhisper プロモーションウェブサイト

RocketWhisper の公式プロモーションサイトです。GitHub Pages でホスティングされています。

## 公開URL

**https://mojosoftjp.github.io/rocketwhisper/**

## プロジェクト構成

```
rocketwhisper-web/
├── index.html          # メインページ（ランディングページ）
├── help.html           # 使い方ガイド
├── privacy.html        # プライバシーポリシー
├── support.html        # サポートページ
├── css/
│   └── style.css       # メインスタイルシート
├── js/
│   └── main.js         # インタラクション用JavaScript
└── images/
    └── RocketWhisper3.png  # アプリアイコン
```

## デザインコンセプト

### テーマ: 宇宙 × サイバーパンク

RocketWhisperの「ロケット」をモチーフに、宇宙空間を旅するようなデザインを採用しています。

### カラーパレット

| 色 | HEX | 用途 |
|----|-----|------|
| シアン | `#00D4FF` | プライマリカラー、ハイライト |
| パープル | `#8B5CF6` | アクセント、グラデーション |
| ピンク | `#EC4899` | CTAボタン、強調 |
| ディープスペース | `#0a0a1a` | 背景 |

### 視覚効果

1. **星空背景**: 全ページに渡る動的な星空（CSS pseudo-elements使用）
2. **流れ星**: 3つの流れ星アニメーション（6秒周期、時差あり）
3. **オーロラ効果**: ヒーローセクションのグラデーション背景
4. **ネオングロー**: カード、ボタンのホバーエフェクト
5. **Glassmorphism**: 半透明カード + backdrop-filter blur

### アニメーション

| アニメーション | 説明 | 周期 |
|--------------|------|------|
| `twinkle1/2/3` | 星の瞬き | 3-5秒 |
| `shooting` | 流れ星 | 6秒 |
| `aurora-shift` | オーロラのゆらめき | 8秒 |
| `float` | アイコンの浮遊 | 6秒 |
| `pulse-glow` | CTAボタンの脈動 | 2秒 |
| `shine` | ボタンの光沢 | 3秒 |
| `gradient-shift` | テキストグラデーション | 3秒 |

## 技術仕様

### CSS機能

- **CSS Variables**: カラーパレット、スペーシングの一元管理
- **CSS Animations**: @keyframesによるアニメーション定義
- **Pseudo-elements**: ::before, ::afterによる装飾
- **Backdrop Filter**: Glassmorphismの実現
- **Radial Gradient**: 星の描画

### レスポンシブ対応

| ブレークポイント | 対象 |
|----------------|------|
| 768px以下 | タブレット・スマートフォン |
| 480px以下 | 小型スマートフォン |

### ブラウザ対応

- Chrome (推奨)
- Firefox
- Edge
- Safari

※ backdrop-filter非対応ブラウザでは自動的にフォールバック

## ページ構成

### index.html（メインページ）

1. **ナビゲーション**: 固定ヘッダー、スクロール時背景変化
2. **ヒーローセクション**: キャッチコピー、統計表示、CTAボタン
3. **課題提起セクション**: ユーザーの悩みを列挙
4. **ソリューションセクション**: RocketWhisperの解決策
5. **機能紹介セクション**: 8つの主要機能
6. **使い方セクション**: 3ステップガイド
7. **対象ユーザーセクション**: ターゲット層の紹介
8. **テクニカルスペック**: 動作要件、対応形式
9. **ダウンロードセクション**: GitHub Releasesへのリンク
10. **フッター**: リンク、コピーライト

### help.html（ヘルプ）

基本的な使い方、設定、トラブルシューティングを記載。

### privacy.html（プライバシーポリシー）

データの取り扱い方針。完全オフライン動作を強調。

### support.html（サポート）

FAQ、問い合わせ方法、バグ報告手順を記載。

## 開発・デプロイ

### ローカルプレビュー

```bash
# VS Code Live Server拡張機能を使用
# または
npx serve
```

### デプロイ

mainブランチにpushすると自動的にGitHub Pagesにデプロイされます。

```bash
git add .
git commit -m "Update website"
git push origin main
```

### キャッシュに関する注意

GitHub Pagesはキャッシュが強いため、変更が反映されない場合は以下を試してください：

1. **Ctrl + Shift + R** (ハードリロード)
2. **DevToolsのDisable cache** (開発者ツール > Network > Disable cache)
3. **シークレットモード** でアクセス
4. 5〜10分待つ（CDN伝播時間）

## 関連リンク

- [RocketWhisper GitHub Repository](https://github.com/mojosoftjp/rocketwhisper)
- [Releases](https://github.com/mojosoftjp/rocketwhisper/releases)
- [Issues](https://github.com/mojosoftjp/rocketwhisper/issues)

## ライセンス

Copyright 2026 Mojosoft Co., Ltd. All rights reserved.

---

*最終更新: 2026年1月19日*
