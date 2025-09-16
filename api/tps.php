<?php
$response = file_get_contents("http://basic4.gachcloud.net:12345/api/tps");
$data = json_decode($response, true);
echo $data["tps"] ?? "0.0";
?>
