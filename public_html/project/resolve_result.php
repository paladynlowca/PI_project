<?php
include_once 'statments.php';
/**
 * @file
 * Placing single form result.
 */

#Inserting overall header
$header->insert_header($sub_root);
if (array_key_exists('form_id', $_GET) and $session->check_grants(50))
{

    $form = new Form();
    if($form->load($_GET['form_id']) != "form_ok")
    {
        echo '<div>Ankieta nie istnieje.</div>';
    }
    elseif ($session->check_grants(90) or ($_SESSION['username'] == $form->get_creator() and $session->check_grants(50)))
    {
        $form->load_results();
        echo $form->build_results();
    }
    else
    {
        echo '<div>Brak dostępu.</div>';
    }
}
else
    echo '<div>Błąd dostępu.</div>';
#Inserting overall footer
$footer->insert_footer();