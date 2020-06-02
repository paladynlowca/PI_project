<?php
include_once "statments.php";
/**
 * @file
 * Handling with received form creation data.
 */

if ($session->check_grants(50))
{
    $form = new Form();
    if($form->parse_create())
        $form->send_create();
    else
        http_response_code(400);
}
else
{
    http_response_code(400);
}