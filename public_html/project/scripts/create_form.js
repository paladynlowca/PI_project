$(document).ready(function(){
    $("#add_form").submit(send_data)
});


/**
 * @file
 * Scripts with available creating new form.
 */


/**
 * Send form data, run after submit create form..
 *
 * @param e Event data.
 */
function send_data(e) {
    e.preventDefault();

    //do your own request an handle the results
    $.ajax({
        url: e.currentTarget.action,
        type: 'post',
        dataType: 'text',
        data: $("#add_form").serialize(),
        success: function(data){
            alert("Ankieta poprawnie dodana.");
            window.location.href = "forms_list.php";
        },
        error: function (data) {
            alert('Błąd wysyłania');
        }
    });
}
let counter = 0;
let answer_counter = 0;


/**
 * Inserting choose question type buttons.
 */
function question_type()
{
    return "" +
        "<div id='_q_type_" + counter + "' class='create_form_question_element'>" +
        "   <p>Wybierz typ pytania:</p>" +
        "   <button form='none' onclick='select_type(\"single\", " + counter + ")'>Jednokrotnego wyboru</button> " +
        "   <button form='none' onclick='select_type(\"multi\", " + counter + ")'>Wielokrotnego wyboru</button> " +
        "   <button form='none' onclick='select_type(\"text\", " + counter + ")'>Tekstowe</button> " +
        "   <button form='none' onclick='select_type(\"numeric\", " + counter + ")'>Liczbowe</button> " +
        "</div>";
}


/**
 * Inserting specified question type input elements.
 *
 * @param type Type of question.
 * @param id Id of question.
 */
function select_type(type, id)
{
    $("#_q_type_" + id).remove();
    let html = "";
    switch (type) {
        case "single":
            html += "" +
                "<p>Pytanie jednokrotnego wyboru</p>" +
                "<div id='_q_body_" + id + "' class='create_form_question_element'>" +
                "   <button id='_q_answ_add_" + id + "' form='none' onclick='add_answer(" + id + ", \"single\")'>Dodaj odpowiedź</button>" +
                "   <input type='radio' name='_q_" + id + "_type' value='single' checked style='display: none'>" +
                "</div>";
            break;
        case "multi":
            html += "" +
                "<div>Pytanie wielokrotnego wyboru</div>" +
                "<div id='_q_body_" + id + "' class='create_form_question_element'>" +
                "   <button id='_q_answ_add_" + id + "' form='none' onclick='add_answer(" + id + ", \"multi\")'>Dodaj odpowiedź</button>" +
                "   <input type='radio' name='_q_" + id + "_type' value='multi' checked style='display: none'>" +
                "</div>";
            break;
        case "text":
            html += "" +
                "<div>Pytanie tekstowe</div>" +
                "<div id='_q_body_" + id + "' class='create_form_question_element'>" +
                "   <input type='radio' name='_q_" + id + "_type' value='text' checked style='display: none'>" +
                "</div>";
            break;
        case "numeric":
            html += "" +
                "<div>Pytanie liczbowe</div>" +
                "<div id='_q_body_" + id + "' class='create_form_question_element'>" +
                "   <input type='number' name='_q_" + id + "_sett_MIN' value='0'><label for='_q_" + id + "_sett_MIN'>Wartość minimalna</label>" +
                "</div>" +
                "<div class='create_form_question_element'>" +
                "   <input type='number' name='_q_" + id + "_sett_MAX' value='6'><label for='_q_" + id + "_sett_MAX'>Wartość maksymalna</label>" +
                "</div>" +
                "<div class='create_form_question_element'>" +
                "   <input type='radio' name='_q_" + id + "_type' value='numeric' checked style='display: none'>" +
                "</div>";
            break;
    }
    $("#_q_" + id + "_desc").parent().after(html)
}


/**
 * Inserting new answer into single and multi choose type question.
 *
 * @param type Type of question.
 * @param id Id of question.
 */
function add_answer(id, type)
{
    answer_counter++;
    let aid = answer_counter;
    $("#_q_answ_add_" + id).before("" +
        "<div id='_q_answ_body" + aid + "'>" +
        "   <input type='" + type + "' id='_q_answ_" + id + "_" + aid + "' name='_q_" + id + "_answ_" + aid + "' required>" +
        "   <button form='none' onclick='$(\"#_q_answ_body" + aid + "\").remove();'>Usuń</button>" +
        "</div>")
}


/**
 * Inserting new question block.
 */
function add_question()
{
    counter++;
    let id = counter;
    $("#create_form_questions").append(
        "<div id='_q_block_" + id + "' class='create_form_question'>" +
        "   <div class='create_form_question_element'>" +
        "       <input type='text' size='50' name='_q_" + id + "_name' id='_q_" + id + "_name' required> <label for='_q_" + id + "_name'>Pytanie</label>" +
        "   </div> " +
        "   <div class='create_form_question_element'>" +
        "       <input type='text' size='50' name='_q_" + id + "_desc'  id='_q_" + id + "_desc'> <label for='_q_" + id + "_name'>Opis pytania</label>" +
        "   </div> " +
        this.question_type() +
        "   <div class='create_form_question_element'>" +
        "       <input type='checkbox' checked name='_q_" + id + "_sett_REQ'  id='_q_" + id + "_sett_REQ'> <label for='_q_" + id + "_sett_REQ'>Odpowiedź wymagana</label>" +
        "   </div>" +
        "   <div class='create_form_question_element'>" +
        "       <button form='none' onclick='delete_question(_q_block_" + id + ")'>Usuń pytanie</button>" +
        "   </div>" +
        "   <hr>" +
        "</div>"
    );
}


/**
 * Deleting question block.
 *
 * @param question_id Id of question.
 */
function delete_question(question_id)
{
    $(question_id).remove();
}
