<?php
include_once 'sql_call.php';
include_once 'question.php';

/**
 * @file
 * Form class file.
 */

/**
 * Class handling with forms and solutions for them.
 *
 * There is four mode of use:
 * 1. Prepare form HTML code, use: load(), build_form().
 * 2. Add answers into database, use: load(), parse_answers(), send_answers().
 * 3. Display all form answers, use: load(), load_results(), build_results().
 * 4. Create new form into database, use: parse_create(), send_create().
 *
 * @see Question
 */
class Form
{

    /**
     * @var int Form id number.
     */
    private $id;
    /**
     * @var string Form title.
     */
    private $title;
    /**
     * @var string Form description.
     */
    private $description;
    /**
     * @var int Form expiration date timestamp.
     */
    private $time_limit;
    /**
     * @var string Creator login.
     */
    private $creator;
    /**
     * @var Question[] List of form questions.
     */
    private $questions = [];

    /**
     * Loading form data and questions from database.
     *
     * @param int $form_id Form id.
     * @return string Result code
     */
    public function load($form_id)
    {
        $this->id = $form_id;
        $result_questions = new SqlCall('GetFormQuestions', $this->id);
        $result_questions->do_quarry();
        $result_form = new SqlCall('GetForm', $this->id);
        $result_form->do_quarry();
        if(!$result_form->get_length())
        {
            return 'form_not_exist';
        }
        if(!$result_questions->get_length())
        {
            return 'form_empty';
        }
        $row = $result_form->next_row();

        $this->id = $row['id'];
        $this->title = $row['title'];
        $this->description = $row['intro'];
        $this->creator = $row['login'];
        $this->time_limit = $row['time_limit'];

        while ($row =$result_questions->next_row())
        {
            $question = new Question();
            $question->load($row['id']);
            $this->questions[$row['id']] = $question;
        }

        return 'form_ok';
    }

    /**
     * Creating form HTML code.
     *
     * Use after load().
     *
     * @return string Form HTML code.
     */
    function build_form()
    {
        $html = "
<form id='main_form' method='post' action='confirm_form.php' data-formid='$this->id'>
    <h2>$this->title</h2>
    <p>$this->description</p>
    ";
        foreach ($this->questions as $question)
        {
            $html.=$question->build_html();
        }
        $html .= "
    <div class='main_form_question'>
        <label for='form_password'>Hasło dla potwierdzenia:</label><br>
        <input id='form_password' name='form_password' type='password' required>
    </div>
    <input type='submit'>
</form>";
        return $html;
    }

    /**
     * Parsing http post data and generating structure of form.
     *
     * @return bool Result of parsing.
     */
    function parse_create()
    {
        $form = new SqlCall('AddForm', $_SESSION['userid'], quote($_POST['_f_name']), quote($_POST['_f_desc']), quote($_POST['_f_date']));
        $form->do_quarry();
        $this->id = $form->next_row()['id'];
        $this->load($this->id);

        $questions = array();
        $questions_data = array();
        foreach ($_POST as $key=>$value)
        {
            $data = substr($key, 0, 5);
            if(substr($key, 0, 2) == '_q')
            {
                if(!in_array($data, $questions))
                {
                    array_push($questions, $data);
                    $questions_data[$data] = array();
                    $question[$data] = $data;
                }
                $questions_data[$data][substr($key, 5)] = $value;
            }
        }
        $count = 0;
        foreach ($questions_data as $key => $value)
        {
            $this->questions[$count] = new Question();
            if(!$this->questions[$count]->parse_create($value))
            {
                $cancel = new SqlCall('DeleteForm', $this->id);
                $cancel->do_quarry();
                return false;
            }
        }
        return true;
    }

    /**
     * Creating form in database.
     *
     * Use after parse_create().
     */
    function send_create()
    {
        foreach ($this->questions as $question)
        {
            $question->send_create($this->id);
        }
    }

    /**
     * Parsing http post data and generating structure of form.
     *
     * Use after load().
     *
     * @return bool Result of parsing.
     */
    function parse_answers()
    {
        $answers = array();
        foreach ($_POST as $key=>$value)
        {
            if (substr($key, 0, 8) == 'question' and $value != '')
            {
                $answers[$key] = $value;
            }
        }

        $solution_exist = new SqlCall('CheckSolutionExist', $_SESSION['userid'], $this->id);
        $solution_exist->do_quarry();
        $check_password = new SqlCall('CheckPass', quote($_SESSION['username']), quote($_POST['form_password']));
        $check_password->do_quarry();

        if($solution_exist->get_length() > 0 or $check_password->get_length() == 0)
        {
            return false;
        }
        foreach ($answers as $key=>$answer)
        {
            try
            {
                if(!$this->questions[substr($key, 9)]->parse_answer($answer))
                {
                    return false;
                }
            }
            catch (OutOfBoundsException $exception)
            {
                return false;
            }
            echo '{===}';
        }
        foreach ($this->questions as $question)
        {
            if(!$question->check_answer())
            {
                return false;
            }
            echo '{---}';
        }
        return true;
    }

    /**
     * Creating solution into database.
     *
     * Use after parse_answers().
     *
     * @param string $password User password for confirm process.
     */
    function send_answers($password)
    {
        $sql = new SqlCall('AddSolution', $this->id, $_SESSION['userid']);
        $sql->do_quarry();
        $solution = $sql->next_row()['id'];
        foreach ($this->questions as $question)
        {
            $question->send_answer($solution, $password);
        }
        $answers = new SqlCall("GetAnswersBySolution", $solution, quote($password));
        $answers->do_quarry();
        $string = '';
        while ($answer = $answers->next_row())
        {
            $string .= $answer['answers'];
        }
        $sql3 = new SqlCall("ValidateSolution", $solution, quote($password), quote($string));
        $sql3->do_quarry();
    }

    /**
     * Loading users answers from database.
     *
     * Use after load()
     */
    function load_results()
    {
        foreach ($this->questions as $question)
        {
            $question->load_results();
        }
    }

    /**
     * Creating result HTML code.
     *
     * Use after load_results().
     *
     * @return string HTML code.
     */
    function build_results()
    {
        $html = "
    <div class='form_result'>
        <div class='form_result_title'>
            <h2>{$this->title}</h2>
        </div>
    ";
        foreach ($this->questions as $question)
        {
            $html .= $question->built_results();
        }
        $html .= "
    </div>
        ";
        return $html;
    }

    /**
     * @return null
     */
    public function get_creator()
    {
        return $this->creator;
    }
}
