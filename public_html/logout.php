<?php
define(PERMISSION_LEVEL, -1);
include("../includes/common.php");

$_SESSION = array();
$params = session_get_cookie_params();
setcookie(session_name(), '', time() - 42000, $params["httponly"]);

session_destroy();
$referer = tools_get_referer("index.php");
throw_msg(102, $referer);
?>
