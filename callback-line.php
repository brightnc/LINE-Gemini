<?php
$accessToken = "8FDKZxqpYX8DUVgaWvS/ebB9KuTzTImq6cwI/Kv/TcWvLh/uuf9eUpdCQjvd+XbhIKvY2lMmwfW2jhACIBMgm2gv4TxrlV30Aj0nn7VFvecdfIopS029F2dGmNAG5owrGFkdLje2pYp8jaZ+d2mbuAdB04t89/1O/w1cDnyilFU="; //copy Channel access token ตอนที่ตั้งค่ามาใส่

$content = file_get_contents('php://input');
$arrayJson = json_decode($content, true);

//Line
$arrayHeader = array();
$arrayHeader[] = "Content-Type: application/json";
$arrayHeader[] = "Authorization: Bearer {$accessToken}";
//Gemini
$arrayHeader_gemini = array();
$arrayHeader_gemini[] = "Content-Type: application/json";


$message = [];
//รับข้อความจากผู้ใช้
if (isset($arrayJson['events'][0]['message']['text'])) {
    $message =  $arrayJson['events'][0]['message']['text'];
} else {
    echo "empty text";
}

write_log($content);

#ตัวอย่าง Message Type "Text"
if ($message == "สวัสดี") {
    $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
    $arrayPostData['messages'][0]['type'] = "text";
    $arrayPostData['messages'][0]['text'] = "สวัสดีจ้าาา";
    replyMsg($arrayHeader, $arrayPostData);
}
#ตัวอย่าง Message Type "Sticker"
else if ($message == "ฝันดี") {
    $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
    $arrayPostData['messages'][0]['type'] = "sticker";
    $arrayPostData['messages'][0]['packageId'] = "2";
    $arrayPostData['messages'][0]['stickerId'] = "46";
    replyMsg($arrayHeader, $arrayPostData);
}
#ตัวอย่าง Message Type "Image"
else if ($message == "รูปน้องแมว") {
    $image_url = "https://i.pinimg.com/originals/cc/22/d1/cc22d10d9096e70fe3dbe3be2630182b.jpg";
    $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
    $arrayPostData['messages'][0]['type'] = "image";
    $arrayPostData['messages'][0]['originalContentUrl'] = $image_url;
    $arrayPostData['messages'][0]['previewImageUrl'] = $image_url;
    replyMsg($arrayHeader, $arrayPostData);
}
#ตัวอย่าง Message Type "Location"
else if ($message == "พิกัดสยามพารากอน") {
    $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
    $arrayPostData['messages'][0]['type'] = "location";
    $arrayPostData['messages'][0]['title'] = "สยามพารากอน";
    $arrayPostData['messages'][0]['address'] =   "13.7465354,100.532752";
    $arrayPostData['messages'][0]['latitude'] = "13.7465354";
    $arrayPostData['messages'][0]['longitude'] = "100.532752";
    replyMsg($arrayHeader, $arrayPostData);
}
#ตัวอย่าง Message Type "Text + Sticker ใน 1 ครั้ง"
else if ($message == "ลาก่อน") {
    $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
    $arrayPostData['messages'][0]['type'] = "text";
    $arrayPostData['messages'][0]['text'] = "อย่าทิ้งกันไป";
    $arrayPostData['messages'][1]['type'] = "sticker";
    $arrayPostData['messages'][1]['packageId'] = "8522";
    $arrayPostData['messages'][1]['stickerId'] = "16581266";
    replyMsg($arrayHeader, $arrayPostData);
} else {


    $arrayPostData_gemini = '{
    "contents": [
      {
        "parts": [
          {
            "text": "' . $message . '"
          }
        ]
      }
    ]
  }';
    $res_gemini = prompGemini($arrayHeader_gemini, $arrayPostData_gemini);

    $res_gemini_arr = json_decode($res_gemini, true);

    if (isset($res_gemini_arr["candidates"][0]["content"]["parts"][0]["text"])) {
        $message = $res_gemini_arr["candidates"][0]["content"]["parts"][0]["text"];
    }
    $arrayPostData['replyToken'] = $arrayJson['events'][0]['replyToken'];
    $arrayPostData['messages'][0]['type'] = "text";
    $arrayPostData['messages'][0]['text'] = $message;
    replyMsg($arrayHeader, $arrayPostData);
}
function replyMsg($arrayHeader, $arrayPostData)
{
    $strUrl = "https://api.line.me/v2/bot/message/reply";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $strUrl);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayPostData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
}

function prompGemini($arrayHeader_gemini, $arrayPostData_gemini)
{
    $key = "";
    $strUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $strUrl . $key);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader_gemini);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayPostData_gemini);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}


exit;

function write_log($msg)
{
    $top = "==========================================================================\n";
    $bot = "\n==========================================================================\n";
    file_put_contents('./log_' . date("Ymd") . '.log', $top . $msg . $bot, FILE_APPEND);
}
