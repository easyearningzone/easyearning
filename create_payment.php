<?php
// ইউজার থেকে আসা ডাটা (যেমন অ্যামাউন্ট) রিসিভ করা
$amount = $_POST['amount'] ?? '10'; 
$phone = $_POST['phone'] ?? '01739971738';
$order_id = "EEZ" . time();

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://secure-pay.nagorikpay.com/api/payment/create',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode(array(
        "success_url" => "https://yourdomain.com/success.php?order_id=" . $order_id,
        "cancel_url"  => "https://yourdomain.com/cancel.php",
        "webhook_url" => "https://yourdomain.com/webhook.php",
        "metadata"    => array(
            "phone"    => $phone,
            "order_id" => $order_id
        ),
        "amount"      => $amount
    )),
    CURLOPT_HTTPHEADER => array(
        'API-KEY: gnXi7etgWNhFyFGZFrOMYyrmnF4A1eGU5SC2QRmUvILOlNc2Ef', // আপনার অরিজিনাল কি
        'Content-Type: application/json'
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    $result = json_decode($response, true);
    
    // যদি রেসপন্স সফল হয়, তবে ইউজারকে পেমেন্ট লিংকে পাঠিয়ে দিন
    if (isset($result['payment_url'])) {
        header("Location: " . $result['payment_url']);
        exit();
    } else {
        echo "পেমেন্ট তৈরি করা সম্ভব হয়নি: " . ($result['message'] ?? 'Unknown Error');
    }
}
?>
