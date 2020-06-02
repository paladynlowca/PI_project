<?php
//include_once 'config.php';
//
//class form
//{
//    private $id = null;
//    public $title = null;
//    public $intro = null;
//    public $time_limit = null;
//    public $creator = null;
//    public $questions = [];
//    private $debug;
//
//    function __construct($form)
//    {
//        $result_questions = new Sql('SELECT pi_questions.id, pi_question_type.type, pi_questions.title, pi_questions.comment, pi_questions.settings, pi_questions.answers, pi_questions.comment_answer FROM pi_questions join pi_question_type on pi_questions.type=pi_question_type.id where pi_questions.form = '.$form.';');
//        $result_form = new Sql('SELECT pi_forms.id, pi_forms.title, pi_forms.intro, pi_forms.time_limit, pi_users.login FROM pi_forms JOIN pi_users ON pi_forms.creator=pi_users.id where pi_forms.id = '.$form.';');
//        if(!$result_form->data_size())
//        {
//            return 'form_not_exist';
//        }
//        if(!$result_questions->data_size())
//        {
//            return 'form_empty';
//        }
//        $row = $result_form->line();
//
//        $this->id = $row['id'];
//        $this->title = $row['title'];
//        $this->intro = $row['intro'];
//        $this->creator = $row['login'];
//        $this->time_limit = $row['time_limit'];
//
//        $count = 1;
//        while($row = $result_questions->next_line())
//        {
//            switch ($row['type']) {
//                case 'single':
//                    array_push($this->questions, $this->built_single($row['id'], $row['title'], $row['comment'], $row['answers'], $row['comment_answer']));
//                    break;
//                case 'multi':
//                    array_push($this->questions, $this->built_multi($row['id'], $row['title'], $row['comment'], $row['settings'], $row['answers'], $row['comment_answer']));
//                    break;
//                case 'text':
//                    array_push($this->questions, $this->built_text($row['id'], $row['title'], $row['comment'], $row['settings']));
//                    break;
//                case 'numeric':
//                    array_push($this->questions, $this->built_numeric($row['id'], $row['title'], $row['comment'], $row['settings']));
//                    break;
//            }
//            $count += 1;
//        }
//        return "form_ok";
//    }
//
//    private function built_single($id, $title, $comment, $answers, $comment_answer)
//    {
//        $answers_arr = explode("|", $answers);
//        $html = '
//    <div>
//        <h3>'.$title.'</h3>
//        <p>'.$comment.'</p>
//    ';
//        $count = 0;
//        foreach ($answers_arr as $answer)
//        {
//            $count++;
//            $id_ = 'q_'.$id.'_a_'.$count;
//            $html = $html.'
//    <input id="'.$id_.'"  type="radio" name="question_'.$id.'" value="'.$answer.'">
//    <label for="'.$id_.'">'.$answer.'</label><br>
//        ';
//        }
//        $html = $html.'
//        </div>';
//        return $html;
//    }
//
//    private function built_multi($id, $title, $comment, $settings, $answers, $comment_answer)
//    {
//        $answers_arr = explode("|", $answers);
//        $html = '
//    <div>
//        <h3>'.$title.'</h3>
//        <p>'.$comment.'</p>
//    ';
//        $count = 0;
//        foreach ($answers_arr as $answer)
//        {
//            $count++;
//            $id_ = 'q_'.$id.'_a_'.$count;
//            $html = $html.'
//    <input id="'.$id_.'" type="checkbox" name="mquestion_'.$id.'[]" value="'.$answer.'">
//    <label for="'.$id_.'">'.$answer.'</label><br>
//        ';
//        }
//        $html = $html.'
//        </div>';
//        return $html;
//    }
//
//    private function built_text($id, $title, $comment, $settings)
//    {
//        $html = '
//    <div>
//        <h3>'.$title.'</h3>
//        <p>'.$comment.'</p>
//        <input type="text"  name="question_'.$id.'">
//    </div>
//    ';
//        return $html;
//    }
//
//    private function built_numeric($id, $title, $comment, $settings)
//    {
//        $range_html = '';
//        if ($settings != '')
//        {
//            $range = explode("|", $settings);
//            $range_html = ' min="'.$range[0].'" max="'.$range[1].'"';
//        }
//        $html = '
//    <div class="form_question_block">
//        <h3>'.$title.'</h3>
//        <p>'.$comment.'</p>
//        <input type="number"  name="question_'.$id.'"'.$range_html.'>
//    </div>
//    ';
//        return $html;
//    }
//
//    function build()
//    {
//        echo '
//<form id="main_form" method="post" action="confirm_form.php" data-formid="'.$this->id.'">
//    <h2>'.$this->title.'</h2>
//    <p>'.$this->intro.'</p>
//    ';
//
//        foreach ($this->questions as $element)
//        {
//            echo $element;
//        }
//
//        echo '<div><label for="form_password">Hasło dla potwierdzenia:<br></label><input id="form_password" name="form_password" type="password"></div>
//              <input type="submit"></form>';
//        echo $this->debug;
//    }
//}