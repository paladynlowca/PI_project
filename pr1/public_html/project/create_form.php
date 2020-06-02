<?php
include_once 'statments.php';
/**
 * @file
 * Creating new form page.
 */

#Inserting overall header
$header->insert_header($sub_root);
if($session->check_grants(50))
{
    $date = date('Y-m-d');
    echo "
<div id='create_form'>
    <form id='add_form' method='post' action='confirm_create.php'>
        <div id='create_form_header'>
            <div class='create_form_question_element'>
                <input type='text' size='50' name='_f_name' id='_f_name' required> <label for='_f_name'>Tytuł ankiety</label>
            </div>
            <div class='create_form_question_element'>
                <input type='text' size='50' name='_f_desc'  id='_f_desc'> <label for='_f_desc'>Opis ankiety</label>
            </div>
            <div class='create_form_question_element'>
                <input type='date' size='50' name='_f_date' value='{$date}' id='_f_date' required> <label for='_f_date'>Limit czasu</label>
            </div>
            
            <hr>
        </div>
        <div id='create_form_questions'>
        </div>
        <div id='create_form_footer'>
            <button form='none' onclick='add_question()'>Dodaj pytanie</button>
            <button form='add_form'>Wyślij</button>
        </div>
    </form>
</div>
";
}
else
{
    echo "Brak uprawnień";
}

#Inserting overall footer
$footer->insert_footer();
if($session->check_grants(50))
{
    echo '<script type="text/javascript" src="scripts/create_form.js"></script>';
}
