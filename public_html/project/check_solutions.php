<?php
include_once 'statments.php';
/**
 * @file
 * Listing all user solution with their verification status.
 */

#Inserting overall header
$header->insert_header($sub_root);

if ($session->check_login())
{
    $login = new SqlCall('CheckPass', quote($_SESSION['username']), quote($_POST['password']));
    $login->do_quarry();
    if ($login->get_length() == 0)
    {
        echo 'Niepoprawne hasło';
    }
    else
    {
        $sql = new SqlCall("GetSolutionsByUser", $_SESSION['userid']);
        $sql->do_quarry();
        while ($row = $sql->next_row()) {
            $sql2 = new SqlCall("GetAnswersBySolution", $row['id'], quote($_POST['password']));
            $sql2->do_quarry();
            $string = '';
            while ($row2 = $sql2->next_row()) {
                $string .= $row2['answers'];
            }
            $sql3 = new SqlCall("CheckSolution", $row['id'], quote($_POST['password']), quote($string));
            $sql3->do_quarry();
            $form = new SqlCall('GetFormBySolution', $row['id']);
            $form->do_quarry();
            if ($sql3->next_row()['result'] == 1)
            {
                echo $form->next_row()['title'].' - Poprawny<br>';
            }
            else
            {
                echo $form->next_row()['title'].' - Błąd<br>';
            }
        }
    }
}



#Inserting overall footer
$footer->insert_footer();