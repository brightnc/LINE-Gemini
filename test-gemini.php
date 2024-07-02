<?php

//Gemini
$arrayHeader_gemini = array();
$arrayHeader_gemini[] = "Content-Type: application/json";


$message = "เย็นนี้กินอะไรดี";

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
print_r($res_gemini);


function prompGemini($arrayHeader_gemini, $arrayPostData_gemini)
{
    $key = "AIzaSyAxjmrN0_LvFfC-35IPymaooE_d2-IPOkI";
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
