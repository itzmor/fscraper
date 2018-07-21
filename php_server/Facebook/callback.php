<?php 
$client_id = '415374225575006';
$client_secret = '09e4f64c13d8ebdcf1599b2903e3115d';
$redirect_uri= "https://www.itzikm.co.il/callback.php";
$authorization_code = $_GET['code'];
if(!$authorization_code){
    die('something went wrong!');
}
$url = 'https://www.itzikm.co.il/Facebook/Authentication/AccessToken.php';
$data = array(
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'redirect_uri' => $redirect_uri,
     'code' => $authorization_code
 );
$options = array(
    'http' => array(
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
var_dump($result);
?>
