# RocketWhisper ウェブサイト 引き継ぎ資料

## 概要

RocketWhisperプロモーションサイトの開発引き継ぎ資料です。

## 作業履歴

### 2026年1月19日 - デザイン大幅改修

#### 実施した主な変更

1. **グローバル星空背景の実装**
   - body::before, ::after を使用して全ページに星空背景を適用
   - position: fixed で固定配置
   - 3レイヤーの星（白、シアン、パープル）で奥行き感を演出

2. **流れ星アニメーションの追加**
   - 3つの流れ星（時差あり: 0s, 2s, 4s）
   - 左上から右下へ移動する6秒周期のアニメーション

3. **オーロラ効果の追加**
   - ヒーローセクションにグラデーションオーロラ
   - 8秒周期でゆらめくアニメーション

4. **カラーパレットの刷新**
   ```css
   --primary: #00D4FF;      /* シアン */
   --purple: #8B5CF6;       /* パープル */
   --pink: #EC4899;         /* ピンク */
   --gradient-primary: linear-gradient(135deg, #00D4FF 0%, #8B5CF6 50%, #EC4899 100%);
   ```

5. **ネオングロー効果**
   - カードホバー時にシアンのグロー
   - CTAボタンに脈動するグロー

6. **Glassmorphismの適用**
   - セクション背景を半透明化（rgba）
   - backdrop-filter: blur(5px) でぼかし効果

## 重要な技術的詳細

### 星空背景の実装方法

```css
body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image:
        radial-gradient(1px 1px at 10px 20px, #fff, transparent),
        radial-gradient(2px 2px at 100px 30px, #00D4FF, transparent),
        /* ... 複数の星を配置 */
    background-size: 200px 130px;
    animation: twinkle1 3s ease-in-out infinite;
    z-index: -2;
    pointer-events: none;
}
```

**ポイント:**
- `position: fixed` で全ページに固定
- `z-index: -2` でコンテンツの背後に配置
- `pointer-events: none` でクリック透過

### コンテンツを星空の上に表示する方法

```css
.section {
    position: relative;
    z-index: 1;
    background: rgba(10, 10, 26, 0.7);  /* 半透明背景 */
    backdrop-filter: blur(5px);          /* ぼかし効果 */
}
```

### 流れ星のHTML構造

```html
<div class="hero-bg">
    <div class="stars"></div>
    <div class="stars2"></div>
    <div class="stars3"></div>
    <!-- 流れ星 -->
    <div class="shooting-star"></div>
    <div class="shooting-star delay-1"></div>
    <div class="shooting-star delay-2"></div>
    <!-- オーロラ効果 -->
    <div class="aurora"></div>
</div>
```

## 既知の問題と対策

### GitHub Pagesのキャッシュ問題

**症状**: CSSを更新してもブラウザに反映されない

**対策**:
1. `Ctrl + Shift + R` でハードリロード
2. DevToolsの「Disable cache」を有効化
3. シークレットモードでアクセス
4. 5〜10分待ってCDN伝播を待つ

### backdrop-filterの非対応ブラウザ

**対策**: CSSで自動的にフォールバック
```css
/* backdrop-filter非対応ブラウザ用 */
@supports not (backdrop-filter: blur(5px)) {
    .card {
        background: rgba(10, 10, 26, 0.95);
    }
}
```

## ファイル構成と役割

| ファイル | 役割 | 編集頻度 |
|----------|------|----------|
| `css/style.css` | 全スタイル定義 | 高 |
| `index.html` | メインページ | 中 |
| `help.html` | ヘルプページ | 低 |
| `privacy.html` | プライバシーポリシー | 低 |
| `support.html` | サポートページ | 低 |
| `js/main.js` | インタラクション | 低 |

## CSS変数一覧

```css
:root {
    /* カラー */
    --primary: #00D4FF;
    --primary-dark: #0099cc;
    --secondary: #1a1a2e;
    --accent: #FF6B35;
    --purple: #8B5CF6;
    --pink: #EC4899;
    --text: #ffffff;
    --text-muted: #a0a0b0;
    --bg-dark: #0a0a1a;
    --bg-card: rgba(26, 26, 46, 0.8);

    /* グラデーション */
    --gradient-primary: linear-gradient(135deg, #00D4FF 0%, #8B5CF6 50%, #EC4899 100%);
    --gradient-hero: linear-gradient(180deg, #0a0a1a 0%, #1a1a2e 50%, #0a0a1a 100%);
    --glow-primary: 0 0 20px rgba(0, 212, 255, 0.5);
    --glow-purple: 0 0 20px rgba(139, 92, 246, 0.5);

    /* サイズ */
    --nav-height: 70px;
    --container-width: 1200px;
    --border-radius: 16px;

    /* トランジション */
    --transition: all 0.3s ease;
}
```

---

# RocketWhisper アプリケーション 音声コマンド設計

## 概要

RocketWhisperの音声コマンド機能は、Whisperの認識結果に対してトリガーフレーズを検出し、対応するアクション（改行挿入、句読点挿入等）を実行する機能です。

## トリガーフレーズ選定基準

Whisperの特性を考慮し、以下の基準でトリガーフレーズを選定しています。

### 選定基準

| # | 基準 | 理由 | 例 |
|---|------|------|-----|
| 1 | 一般的な単語に含まれないこと | 誤発火防止 | ×「天」（天気に含まれる）、×「丸」（丸いに含まれる） |
| 2 | Whisperの誤認識パターンを含める | 認識精度向上 | 「改行」→「開業」「海洋」も対応 |
| 3 | カタカナ外来語は比較的安全 | 一般文に出現しにくい | 「コンマ」「ピリオド」等 |
| 4 | ひらがな・カタカナ両対応 | Whisper出力のばらつき対応 | 「こんま」「コンマ」両方 |

### 危険なトリガー（採用しない）

| トリガー | 理由 |
|---------|------|
| 「天」「てん」 | 「天気」「点数」等に含まれる |
| 「丸」「まる」 | 「丸い」「丸める」等に含まれる |
| 短い単語全般 | 部分一致で誤発火しやすい |

## デフォルト音声コマンド一覧

### 改行（NewLine）

```csharp
TriggerPhrases = new[] { "改行", "かいぎょう", "開業", "海洋", "カイギョウ", "エンター" }
```

**ポイント**: 「改行」はWhisperが「開業」「海洋」と誤認識することがあるため、これらも含める。

### 段落（Paragraph）

```csharp
TriggerPhrases = new[] { "段落", "だんらく", "新しい段落", "暖楽", "ダンラク" }
```

**動作**: 2回の改行を挿入（新しい段落を開始）

### 句点（Period）

```csharp
TriggerPhrases = new[] { "句点", "ピリオド", "ぴりおど" }
```

**ポイント**: 「まる」「丸」は「丸い」等に含まれるため**除外**。安全なトリガーのみ採用。

### 読点（Comma）

```csharp
TriggerPhrases = new[] { "読点", "コンマ", "カンマ", "こんま", "かんま" }
```

**ポイント**: 「てん」は「天気」「点数」等に含まれるため**除外**。「コンマ」「カンマ」両表記に対応。

### 疑問符（Question）

```csharp
TriggerPhrases = new[] { "疑問符", "ぎもんふ", "ギモンフ", "はてな", "ハテナ", "クエスチョン" }
```

### 感嘆符（Exclamation）

```csharp
TriggerPhrases = new[] { "感嘆符", "かんたんふ", "カンタンフ", "びっくり", "ビックリ", "エクスクラメーション" }
```

### 削除（Delete）

```csharp
TriggerPhrases = new[] { "削除", "さくじょ", "サクジョ", "取り消し", "とりけし", "デリート" }
```

**動作**: 直前の単語を削除

## 実装上の注意点

### 1. マッチング方式

部分一致ではなく、**単語境界を考慮したマッチング**が望ましい。

```csharp
// 悪い例：部分一致
if (text.Contains("天")) // 「天気」でも発火してしまう

// 良い例：単語として独立しているかチェック
// または文末・文頭にあるかチェック
```

### 2. 大文字小文字・ひらがなカタカナの正規化

Whisperの出力はひらがな/カタカナがばらつくため、比較前に正規化を推奨。

### 3. コマンドの優先順位

長いフレーズを先にマッチングする（「新しい段落」→「段落」の順）。

## ファイル構成

| ファイル | 説明 |
|----------|------|
| `Models/VoiceCommand.cs` | コマンド定義・設定クラス |
| `Services/VoiceCommandService.cs` | コマンド検出・実行ロジック |

---

# 音声コマンド ポーズ検出方式（2026年1月19日実装）

## 概要

従来の正規表現による単語境界検出では、「開業届を出す」の「開業」が改行コマンドとして誤認識される問題があった。
ポーズ検出方式では、Whisperのセグメントタイムスタンプを活用し、**発話間の無音区間（ポーズ）** を検出することで、コマンドの意図的な発声を識別する。

## 検出モード

| モード | 説明 | 精度 | 自然さ |
|-------|------|-----|-------|
| `Legacy` | 従来方式（正規表現） | 中 | 低 |
| `PauseDetection` | ポーズ検出方式 | 高 | 高 |
| `Hybrid` | ポーズ + 文末 + 信頼度計算 | 最高 | 高 |

## ポーズ検出の仕組み

```
話している... [0.3秒以上の無音] 改行 [0.2秒以上の無音] ...続き
              ↑ポーズ検出         ↑コマンド認識
```

### 設定パラメータ

```csharp
// VoiceCommandSettings
public double PauseThresholdBefore { get; set; } = 0.3;  // コマンド前の無音（秒）
public double PauseThresholdAfter { get; set; } = 0.2;   // コマンド後の無音（秒）
public double ConfidenceThreshold { get; set; } = 0.5;   // ハイブリッドモードの閾値
```

## コアロジック

### SegmentInfo クラス

Whisperの認識結果からタイムスタンプ情報を保持：

```csharp
public class SegmentInfo
{
    public TimeSpan Start { get; set; }  // 開始時間
    public TimeSpan End { get; set; }    // 終了時間
    public string Text { get; set; }     // 認識テキスト
}
```

### ポーズ検出メソッド

```csharp
private bool HasPauseBefore(SegmentInfo current, SegmentInfo? previous)
{
    if (previous == null) return true;  // 最初のセグメント
    var gap = (current.Start - previous.End).TotalSeconds;
    return gap >= _settings.PauseThresholdBefore;
}

private bool HasPauseAfter(SegmentInfo current, SegmentInfo? next)
{
    if (next == null) return true;  // 最後のセグメント
    var gap = (next.Start - current.End).TotalSeconds;
    return gap >= _settings.PauseThresholdAfter;
}
```

### ハイブリッドモードの信頼度計算

```csharp
double confidence = 0.0;

// 1. ポーズ検出（+40%）
if (HasPauseBefore(current, previous)) confidence += 0.20;
if (HasPauseAfter(current, next)) confidence += 0.20;

// 2. 文末検出（+30%）
if (next == null) confidence += 0.30;
else if (IsNewSentenceStart(next.Text)) confidence += 0.20;

// 3. 完全一致（+20%）
if (IsExactCommandMatch(text)) confidence += 0.20;

// 4. 安全なトリガー（+10%）
if (IsSafeTrigger(text)) confidence += 0.10;  // カタカナ外来語

return Math.Min(confidence, 1.0);
```

## 使用方法

### 呼び出し側（MainViewModel等）での使用

```csharp
// 認識結果からセグメント情報を変換
var segments = SegmentInfo.FromSegmentDataList(result.Segments);

// ポーズ検出方式でコマンドを処理
var processedText = _voiceCommandService.ProcessCommandsWithSegments(segments);
```

### 設定の変更

```csharp
// 検出モードを変更
_voiceCommandService.DetectionMode = VoiceCommandDetectionMode.Hybrid;

// ポーズ閾値を調整（早口のユーザー向け）
_voiceCommandService.PauseThresholdBefore = 0.2;
_voiceCommandService.PauseThresholdAfter = 0.15;
```

## 動作例

### ケース1: 通常テキスト（コマンドとして認識しない）

```
入力: 「開業届を出す」
セグメント: [開業届を出す] (連続した発話)
結果: 「開業届を出す」 ← そのまま出力
```

### ケース2: コマンド（認識する）

```
入力: 「こんにちは [ポーズ] 改行」
セグメント: [こんにちは][改行] (0.3秒以上の間)
結果: 「こんにちは\n」 ← 改行に変換
```

### ケース3: 曖昧なケース（ハイブリッドモードで判定）

```
入力: 「明日は開業 」
セグメント: [明日は開業] (文末、ポーズなし)
信頼度: 0.50 (文末30% + 完全一致20%)
結果: 閾値0.5と同値なので、コマンドとして認識
```

## ファイル構成

| ファイル | 変更内容 |
|----------|---------|
| `Models/VoiceCommand.cs` | `VoiceCommandDetectionMode` enum追加、設定プロパティ追加 |
| `Services/VoiceCommandService.cs` | ポーズ検出ロジック追加、`ProcessCommandsWithSegments()`メソッド追加、`SegmentInfo`クラス追加 |

## 今後の改善案

1. **ユーザーカスタマイズ**: 設定画面でポーズ閾値を調整可能に
2. **学習機能**: 誤発火パターンを学習して閾値を自動調整
3. **音声波形解析**: Whisperのタイムスタンプだけでなく、実際の音声波形からポーズを検出

---

# ウェブサイト 今後の拡張ポイント

1. **ダークモード切り替え**: 現在は常にダークテーマ。ライトモード追加可能
2. **多言語対応**: 現在は日本語のみ。英語版の追加を検討
3. **パフォーマンス最適化**: 画像の遅延読み込み、CSS/JSの圧縮
4. **アクセシビリティ**: スクリーンリーダー対応、キーボードナビゲーション改善
5. **アニメーション減少モード**: `prefers-reduced-motion` メディアクエリ対応

## デプロイ手順

```bash
# 変更をコミット
git add .
git commit -m "変更内容の説明"

# mainブランチにプッシュ（自動デプロイ）
git push origin main

# デプロイ状況確認
gh api repos/mojosoftjp/rocketwhisper/deployments --jq '.[0]'
```

## 連絡先・リソース

- **GitHub Repository**: https://github.com/mojosoftjp/rocketwhisper
- **GitHub Pages**: https://mojosoftjp.github.io/rocketwhisper/
- **Issues**: https://github.com/mojosoftjp/rocketwhisper/issues

## 参考資料

- [CSS Animations MDN](https://developer.mozilla.org/ja/docs/Web/CSS/CSS_Animations)
- [backdrop-filter MDN](https://developer.mozilla.org/ja/docs/Web/CSS/backdrop-filter)
- [GitHub Pages Documentation](https://docs.github.com/ja/pages)

---

# 2026年1月24日 - インストーラー作成・ダウンロードページ更新

## 概要

RocketWhisperのインストーラー（Lite版/Full版）を作成し、Webサイトのダウンロードページを更新しました。

## 1. インストーラー作成

### 修正した問題

#### build_installers.bat が実行されない問題
- **原因**: 改行コードがLF（Unix形式）だった
- **解決**: CRLF（Windows形式）に変換

#### Inno Setupコンパイルエラー（String error）
- **原因**: .issファイルのエンコーディングが不正（日本語が文字化け）
- **解決**: UTF-8 with BOM + CRLF形式で再作成

### .issファイルの正しいエンコーディング

```python
# Pythonで正しいエンコーディングで保存する方法
def write_iss_file(filename, content):
    content_crlf = content.replace('\n', '\r\n')
    with open(filename, 'wb') as f:
        f.write(b'\xef\xbb\xbf')  # UTF-8 BOM
        f.write(content_crlf.encode('utf-8'))
```

**重要**: Inno Setupスクリプトは必ず「UTF-8 with BOM + CRLF」で保存すること。

### インストーラー仕様

| エディション | ファイル名 | サイズ | 内容 |
|-------------|-----------|--------|------|
| Lite版 | `RocketWhisper_Lite_Setup_1.0.0.exe` | 約50MB | アプリ本体のみ |
| Full版 | `RocketWhisper_Full_Setup_1.0.0.exe` | 約3GB | アプリ + large-v3モデル + .NET Runtime |

### ダウンロードURL

- Lite版: http://mojosoft.biz/RocketWhisper_Lite_Setup_1.0.0.exe
- Full版: http://mojosoft.biz/RocketWhisper_Full_Setup_1.0.0.exe

## 2. Webサイト更新

### index.html の変更

#### ダウンロードセクションの刷新

従来の単一ダウンロードボタンから、Lite版/Full版を選べるエディションカード形式に変更。

**追加したHTML構造**:
- `.edition-grid`: 2カラムのグリッドレイアウト
- `.edition-card`: 各エディションのカード
- `.edition-ribbon`: Full版の「おすすめ」リボン
- `.comparison-table`: Lite版 vs Full版の比較表

#### ヒーローセクションのスタッツ変更

| 変更前 | 変更後 |
|--------|--------|
| 5 AIプロバイダ | 12 プレミアム機能 |

#### その他の修正

| 箇所 | 変更前 | 変更後 |
|------|--------|--------|
| Lite版の説明 | 5種類のモデルから選択可能 | 3種類のモデルから選択可能 |
| GitHub リンク | 表示あり | 削除 |

### css/style.css の変更

エディションカード用のスタイルを追加（約180行）:

```css
/* 主なクラス */
.edition-grid { ... }        /* 2カラムグリッド */
.edition-card { ... }        /* カード本体 */
.edition-card.recommended { ... }  /* おすすめカード */
.edition-ribbon { ... }      /* おすすめリボン */
.edition-header { ... }      /* ヘッダー（バッジ、タイトル、サイズ） */
.edition-badge { ... }       /* Lite/Fullバッジ */
.edition-body { ... }        /* 本文エリア */
.edition-features { ... }    /* 機能リスト */
.edition-note { ... }        /* 注意書きボックス */
.edition-footer { ... }      /* ダウンロードボタン */
.comparison-table { ... }    /* 比較表 */
```

## 3. 正しい仕様情報

### 対応モデル（3種類）

| モデル | サイズ | 精度 |
|--------|--------|------|
| small | 466MB | 良好 |
| medium | 1.5GB | 高精度 |
| large-v3 | 2.9GB | 最高精度 |

**注意**: tiny, baseモデルは現在サポートされていません。

### 現在のヒーロースタッツ

- 12 プレミアム機能
- 73 ハルシネーション対策
- 100% オフライン動作

## 4. Git コミット履歴

```
922d5fc プレミアム機能数を20+から12に修正
35640f5 ヒーローセクションのスタッツを「5 AIプロバイダ」から「20+ プレミアム機能」に変更
55d5006 モデル数を5種類から3種類に修正（small/medium/large-v3）
4f71314 GitHubリンクを削除
9bfaa4c ダウンロードページにLite版/Full版のエディションカードと比較表を追加
```

## 5. 関連ファイル

| ファイル | 役割 |
|----------|------|
| `Installer/build_installers.bat` | インストーラービルドスクリプト |
| `Installer/RocketWhisper_Lite.iss` | Lite版Inno Setupスクリプト |
| `Installer/RocketWhisper_Full.iss` | Full版Inno Setupスクリプト |
| `rocketwhisper-web/index.html` | トップページ |
| `rocketwhisper-web/css/style.css` | スタイルシート |

---

*更新日: 2026年1月24日*
*作成者: Claude (Anthropic) - Opus 4.5*
