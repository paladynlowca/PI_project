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
        echo '<br><br><h3>Odpowiedzi udzielili:</h3><br>';
        $users = new SqlCall('GetResultUsersList', $_GET['form_id']);
        $users->do_quarry ();
        while($row = $users->next_row())
        {
            echo '<div>'.$row['login'].'</div>';
        }
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