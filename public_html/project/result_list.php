<?php
include_once 'statments.php';
/**
 * @file
 * Listing all available forms result.
 */

#Inserting overall header
$header->insert_header($sub_root);
$test = true;
if ($_SESSION['islogged'] and $_SESSION['usergrant'] >= 90)
{
    $forms = new SqlCall('GetFormsResults',0);
}
elseif ($_SESSION['islogged'] and $_SESSION['usergrant'] >= 50)
{
    $forms = new SqlCall('GetFormsResults', $_SESSION['userid']);
}
else
{
    echo '<div>Brak uprawnień</div>';
    $test = false;
}
if($test)
{
    $forms->do_quarry();
    if ($forms->get_length() > 0)
    {
        echo "<h3>Dostępne rozwiązania:</h3>";
    }
    else
    {
        echo "<h3>Brak dostępnych rozwiązań.</h3>";
    }
    while ($form = $forms->next_row())
    {
        echo"
        <div class='form_list_row'>
            <div class='form_list_title'>
                <a href='resolve_result.php?form_id={$form['id']}'>{$form['title']}</a>
            </div>
        </div>
        ";
    }
}
#Inserting overall footer
$footer->insert_footer();