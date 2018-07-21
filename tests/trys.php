<?php
require_once __DIR__ . '/fbscraper.php';

$fbs = new fbscraper();
$allFriends = $fbs->getFriends("inbal.stern.5");

$totalForUser = array();
foreach ($allFriends as $key) {
        if (array_key_exists($key, $totalForUser)) {
                $totalForUser[$key]++;
        } else {
                $totalForUser[$key]=1;
        }
}
var_dump($totalForUser);
var_dump($allFriends);
var_dump(count($totalForUser));
var_dump(count($allFriends));
