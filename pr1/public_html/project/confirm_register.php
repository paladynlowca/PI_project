<?php
include_once 'statments.php';
/**
 * @file
 * Handling with received user register data.
 */

$status = $session->register($_POST['login'], $_POST['password'], $_POST['repeat_password'], $_POST['email']);

echo $status;