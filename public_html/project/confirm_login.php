<?php
include_once 'statments.php';
/**
 * @file
 * Handling with received use login data.
 */

$status = $session->login($_POST['login'], $_POST['password']);

echo $status;