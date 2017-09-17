<?php
header('Content-Type: application/json');
if (empty($global['systemRootPath'])) {
    $global['systemRootPath'] = '../';
}
require_once $global['systemRootPath'].'videos/configuration.php';
require_once $global['systemRootPath'] . 'objects/user.php';
if (!User::isAdmin() || empty($_POST['id'])) {
    die('{"error":"'.__("Permission denied").'"}');
}
require_once 'category.php';
$obj = new Category($_POST['id']);
echo '{"status":"'.$obj->delete().'"}';
