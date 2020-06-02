<?php

/**
 * @file
 * Starting session and make main include.
 */
// Start PHP Session.
session_start();

// Include all files.
include_once 'header.php';
include_once 'footer.php';
include_once 'session.php';
include_once "sql_call.php";
include_once "form.php";