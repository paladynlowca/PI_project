<?php
include_once 'statments.php';


$form = new Form();
$form->load($_POST['form_id']);
if(!$form->parse_answers())
{
    http_response_code(400);
}
else
{
    $form->send_answers($_POST['form_password']);
}
/**
 * @file
 * Handling with received form answers data.
 */