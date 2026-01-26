<?php
/**
 * PayPal IPN (Instant Payment Notification) Handler
 *
 * RocketWhisper対応版
 * - MJ010: RocketWhisper 個人版
 * - MJ011: RocketWhisper 法人版
 */

require_once('q_mail.php');

header('Content-Type: text/html; charset=UTF-8');
mb_language('ja');
mb_internal_encoding("UTF-8");

/**
 * 製品番号に応じたライセンスキーを取得
 *
 * @param string $n 製品番号 (item_number)
 * @param string $q 数量
 * @return string ライセンスキー
 */
function getKey($n, $q) {
    if (strcmp($q, "") == 0 || is_null($q)) {
        $qu = 0;
    } else {
        $qu = (int)$q - 1;
        if ($qu >= 5) {
            $qu = 4;
        }
    }

    // テスト用キー
    $arr_test_key = array("111|aaa", "222|bbb", "333|ccc", "444|ddd", "555|eee");

    // RocketMouse Pro キー
    $arr_rm_key = array(
        "ユーザーID：RXMZAEC5592　パスワード：D9F418F74C00",
        "ユーザーID：RXNWLNJ4345　パスワード：D832A36F9A00",
        "ユーザーID：RXCFVGV2354　パスワード：80FBFF071600",
        "ユーザーID：RXFDBWQ8482　パスワード：597202EEDC00",
        "ユーザーID：RXBZMMK9222　パスワード：04D7D5E96F00"
    );

    // RocketPlayer Pro キー
    $arr_rp_key = array(
        "ユーザーID：RGMEVAG5979　パスワード：51D7BE70AD00",
        "ユーザーID：RGWMYUG5479　パスワード：7CD6194B2A00",
        "ユーザーID：RGZGJFN7245　パスワード：921B64560A00",
        "ユーザーID：RGTDJVX6589　パスワード：3A4B68F98600",
        "ユーザーID：RGYFJRE8972　パスワード：32026435F100"
    );

    // RocketMouse Pro アップグレードキー
    $arr_rm_up_key = array(
        "ユーザーID：RWUGWCH3775　パスワード：FD6C32AF4400",
        "ユーザーID：RWMJKED9535　パスワード：E784EAD70A00",
        "ユーザーID：RWUCMJA5874　パスワード：59DEF2CDE800",
        "ユーザーID：RWEZPSR4969　パスワード：B2A06A1A4C00",
        "ユーザーID：RWZWGCA5228　パスワード：2F7CC8626900"
    );

    // RocketPlayer Pro アップグレードキー
    $arr_rp_up_key = array(
        "ユーザーID：RCJUURR6265　パスワード：264536C33F00",
        "ユーザーID：RCHZPTY8475　パスワード：66BBBF7E1C00",
        "ユーザーID：RCHFWAX6429　パスワード：21C1F1D6CE00",
        "ユーザーID：RCMUVER2892　パスワード：B81B02084F00",
        "ユーザーID：RCVJXRK9485　パスワード：26AE7AD56E00"
    );

    // 製品番号に応じてキーを返す
    if (strcmp($n, "MJ000") == 0) {
        return $arr_test_key[$qu];
    } else if (strcmp($n, "MJ001") == 0 || strcmp($n, "MJ009") == 0) {
        return $arr_rm_key[$qu];
    } else if (strcmp($n, "MJ002") == 0) {
        return $arr_rp_key[$qu];
    } else if (strcmp($n, "MJ003") == 0) {
        return $arr_rm_up_key[$qu];
    } else if (strcmp($n, "MJ004") == 0) {
        return $arr_rp_up_key[$qu];
    } else if (strcmp($n, "MJ010") == 0) {
        // RocketWhisper 個人版 - ライセンスキーは手動発行
        return "ROCKETWHISPER_PERSONAL_PENDING";
    } else if (strcmp($n, "MJ011") == 0) {
        // RocketWhisper 法人版 - ライセンスキーは手動発行
        return "ROCKETWHISPER_BUSINESS_PENDING";
    } else {
        return "none";
    }
}

// PayPalシステムへポストバックして検証
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
    $value = urlencode(stripslashes($value));
    $req .= "&$key=$value";
}

// PayPalシステムへポストバックして検証（SSL）
$header = "POST /cgi-bin/webscr HTTP/1.1\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Host: www.paypal.com\r\n";
$header .= "Connection: close\r\n\r\n";
$fp = fsockopen('ssl://www.paypal.com', 443, $errno, $errstr, 120);

// ポストされた変数をローカル変数に割り当て
// お客様情報
$payer_name = $_POST['last_name'] . ' ' . $_POST['first_name'];
$payer_biz_name = $_POST['payer_business_name'];
$payer_email = $_POST['payer_email'];

// 購入商品情報
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$quantity = $_POST['quantity'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];

// 取引ID(ユニーク)
$txn_id = $_POST['txn_id'];

// 自分のメモリ
$receiver_email = $_POST['receiver_email'];

// 支払いステータス
$payment_status = $_POST['payment_status'];

// カスタムフィールド（ハードウェアID等）
$custom_id = isset($_POST['custom']) ? $_POST['custom'] : '';

// fpの結果返し用変数
$result_pp = '';

if (!$fp) {
    // HTTPエラー
    $result_pp = 'HTTP_ERROR';
} else {
    fputs($fp, $header . $req);
    while (!feof($fp)) {
        $res = fgets($fp, 1024);
        $res = trim($res);
        if (strcmp($res, "VERIFIED") == 0) {
            $result_pp = 'VERIFIED';
        } else if (strcmp($res, "INVALID") == 0) {
            $result_pp = 'INVALID';
        }
    }
    fclose($fp);
}

// メールを作成
// 自分への通知メール用
$self_to = 'support@mojosoft.biz';
$self_subject = '[PayPal] IPN通知がありました';
$self_mes = '';

// お客様へのメール用
$payer_to = $payer_email;
$payer_subject = '[' . $item_name . '] ご登録完了のお知らせ';
$payer_mes = '';

// 共通ヘッダー
$from = 'support@mojosoft.biz';
$reply_to = 'support@mojosoft.biz';
$mail_header = "From: $from\n";
$mail_header .= "Reply-To: $reply_to\n";
$mail_header .= "X-Mailer: PMail " . phpversion() . "\n";

// ライセンスキー
$key = getKey($item_number, $quantity);

// RocketPlayer Proの案内文
$rp_info = '';
if (strcmp($item_number, "MJ001") == 0) {
    $rp_info = "

【RocketPlayer Proのご案内】

RocketMouse Pro で作成したマクロファイル(テキスト形式)を、単体で
実行可能なライセンスフリーのEXEファイルに変換することができます。

企業向けのご利用などオートメーション対象のPC台数が多い場合は、
このツールで作成したEXEを配布することで、導入費用を格段に抑える
ことが可能です。複数のPCでご利用の場合は、ぜひお試しください。

▼ 試用版のダウンロードは以下からお願いします。
http://mojosoft.biz/products/rocketplayerpro/#download

▼ 以下からご購入はご可能です。
http://mojosoft.biz/products/rocketplayerpro/#buy
";
}

// お客様メール送信結果
$result_payer_mail = false;

if (strcmp($result_pp, "VERIFIED") == 0) {
    $self_mes .= 'VERIFIED!!' . PHP_EOL;

    // 支払い状況を確認してOKならお客様にメールを送る
    if (strcmp($payment_status, "Completed") == 0 && strcmp($receiver_email, "support@mojosoft.biz") == 0) {

        // ========================================
        // RocketWhisper 個人版 (MJ010)
        // ========================================
        if (strcmp($item_number, "MJ010") == 0) {
            $payer_subject = '[RocketWhisper 個人版] ご購入ありがとうございます';
            $payer_mes = "
$payer_name 様

お世話になっております。Mojosoftサポートセンターです。

RocketWhisper 個人版ライセンスをご購入いただき、
誠にありがとうございました。

ご入金を確認いたしました。

【ご購入情報】
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
製品名: RocketWhisper 個人版ライセンス
金額: ¥{$payment_amount}
ハードウェアID: {$custom_id}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

ライセンスキーは24時間以内にこのメールアドレス宛に
お届けいたします。今しばらくお待ちください。

【ライセンスキーの入力方法】
1. RocketWhisperを起動
2. 設定画面（歯車アイコン）を開く
3. 「ライセンス」タブを選択
4. ライセンスキーを入力して「登録」をクリック

▼ ダウンロードは以下からお願いいたします。
https://mojosoftjp.github.io/rocketwhisper/

ご不明な点などございましたら、このメールアドレスまで
ご連絡ください。今後ともどうぞよろしくお願いいたします。

---
Mojosoft Co., Ltd.
URL: https://mojosoft.co.jp/
E-Mail: support@mojosoft.biz
";
        }
        // ========================================
        // RocketWhisper 法人版 (MJ011)
        // ========================================
        else if (strcmp($item_number, "MJ011") == 0) {
            $payer_subject = '[RocketWhisper 法人版] ご購入ありがとうございます';
            $payer_mes = "
$payer_name 様

お世話になっております。Mojosoftサポートセンターです。

RocketWhisper 法人版ライセンス（PC台数無制限）をご購入いただき、
誠にありがとうございました。

ご入金を確認いたしました。

【ご購入情報】
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
製品名: RocketWhisper 法人版ライセンス（PC台数無制限）
金額: ¥{$payment_amount}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

ライセンスキーは24時間以内にこのメールアドレス宛に
お届けいたします。今しばらくお待ちください。

※ 法人版ライセンスはPC台数無制限でご利用いただけます。
※ 同一法人内であれば、何台でもインストール可能です。

【ライセンスキーの入力方法】
1. RocketWhisperを起動
2. 設定画面（歯車アイコン）を開く
3. 「ライセンス」タブを選択
4. ライセンスキーを入力して「登録」をクリック

▼ ダウンロードは以下からお願いいたします。
https://mojosoftjp.github.io/rocketwhisper/

ご不明な点などございましたら、このメールアドレスまで
ご連絡ください。今後ともどうぞよろしくお願いいたします。

---
Mojosoft Co., Ltd.
URL: https://mojosoft.co.jp/
E-Mail: support@mojosoft.biz
";
        }
        // ========================================
        // 既存製品（RocketMouse Pro等）
        // ========================================
        else {
            $payer_mes = "
$payer_name 様

お世話になっております。Mojosoftサポートセンターです。

$item_name にご登録いただき、誠にありがとうございました。

ご入金を確認しましたので、下記にユーザーIDおよびパスワードを
発行させていただきます。起動時に表示される入力画面に、すべて
半角大文字で入力してください。このキーでPC" . (string)$quantity . "台までインストール・
使用が可能となります。

【$item_name xPC" . (string)$quantity . "台用ライセンス】
$key

▼ ダウンロードは以下からお願いいたします。
(IDとパスワードを入力すると製品版となります)
http://mojosoft.biz/products/rocketmousepro/#download

ご不明な点などございましたら、このメールアドレスまでご返信く
ださい。今後ともどうぞよろしくお願いいたします。
$rp_info

---
Mojosoft Co., Ltd.
URL: http://mojosoft.biz/
E-Mail: support@mojosoft.biz
";
        }

        // メール送信
        $result_payer_mail = mailsender($payer_to, $payer_subject, $payer_mes, 'Mojosoft Co., Ltd.', 'support@mojosoft.biz');

    } else {
        $self_mes .= $payment_status . 'のため、お客様にライセンスキーメールは送っていません。' . PHP_EOL;
    }
} else {
    $self_mes .= 'result_pp=' . $result_pp . ' 通知を受けましたが調査が必要です。' . PHP_EOL;
}

if (!$result_payer_mail) {
    $self_mes .= "お客様メール送信に失敗(or 未送信)" . $payer_mes . PHP_EOL;
} else {
    $self_mes .= "お客様メール送信に成功！" . $payer_mes . PHP_EOL;
}

// IPN通知があれば、常に自分へはメールを送る
$self_mes .= "
---
payer_name : $payer_name
payer_biz_name : $payer_biz_name
payer_email : $payer_email
item_name : $item_name
item_number : $item_number
quantity : $quantity
payment_amount : $payment_amount
payment_currency : $payment_currency
payment_status : $payment_status
txn_id : $txn_id
receiver_email : $receiver_email
custom_id (HardwareID) : $custom_id
\n";

$result_self_mail = mailsender($self_to, $self_subject, $self_mes, 'Mojosoft Co., Ltd.', 'support@mojosoft.biz');
?>
