<?php
require_once 'app/config/config.php';
require_once 'app/models/Auth.php';

$auth = new Auth();
$auth->logout();
?>