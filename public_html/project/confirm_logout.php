<?php
include_once 'statments.php';
/**
 * @file
 * Handling with received user logout data.
 */

$status = $session->logout();

echo $status;