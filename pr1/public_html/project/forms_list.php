<?php
include_once 'statments.php';
/**
 * @file
 * Listing all available forms to answer.
 */

#Inserting overall header
$header->insert_header($sub_root);
if ($session->check_login())
{
    $forms = new SqlCall('GetFormsList', $_SESSION['userid']);
    if ($forms->do_quarry()) {
        if($forms->get_length() == 0)
        {
            echo 'Nie ma dostępnych formularzy.';
        }
        while ($row = $forms->next_row()) {
            if (strtotime($row['time_limit']) - time() > 0) {
                echo '<a href="resolve_form.php?form_id=' . $row['id'] . '" <div>' . $row['title'] . '</div></a><br>';
            }
        }
    }
}
else
{
    echo 'Niezalogowano';
}
#Inserting overall footer
$footer->insert_footer();