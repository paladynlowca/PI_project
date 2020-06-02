<?php
include_once 'statments.php';
/**
 * @file
 * Placing form to resolve.
 */

#Inserting overall header
$header->insert_header($sub_root);
if (array_key_exists('form_id', $_GET) and $session->check_login())
{
    $form = new Form();
    if($form->load($_GET['form_id']) == "form_ok")
    {
        echo $form->build_form();
    }
    else
    {
        echo 'Ankieta nie istnieje.';
    }
}
#Inserting overall footer
$footer->insert_footer();