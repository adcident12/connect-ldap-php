<?php
$part_include = str_replace("api", "", __DIR__);
require_once($part_include . "controllers\ActiveDirectoryController.php");

header('Content-Type: application/json; charset=utf-8');

$json =  file_get_contents('php://input');
$data = [];
$result = [];
$data = json_decode($json, true);

if (!empty($data)) {
    extract($data);
}

$ad = new ActiveDirectoryController();
$username = isset($username) ? $username : "";
$password = isset($password) ? $password : "";
$ad->setUsername($username);
$ad->setPassword($password);
$result = $ad->getEntries();

echo json_encode($result);
exit();
