<?php
include_once 'sql_call.php';
/**
 * @file
 * Question and QuestionHandler classes file.
 */

/**
 * Class handling with questions and solutions for them. For use only as a part of Form.
 *
 * There is four mode of use:
 * 1. Prepare form question HTML code, use: load(), build_html().
 * 2. Add answer into database, use: load(), parse_answer(), optionally check_answer(), send_answer().
 * 3. Display all question answers, use: load(), load_results(), build_results().
 * 4. Create new question into database, use: parse_create(), send_create().
 *
 * @see Form
 * @see QuestionHandler
 */
class Question
{
    /**
     * @var int Question id number.
     */
    private $id;
    /**
     * @var string SQL quarry, getting question data from database.
     */
    private $quarry;
    /**
     * @var QuestionHandler Specific question type handler.
     */
    private $handler;

    /**
     * @var string Question title.
     */
    private $q_title;
    /**
     * @var string Question type.
     */
    private $q_type;
    /**
     * @var string Question comment.
     */
    private $q_comment;
    /**
     * @var string Raw question setting data.
     */
    private $q_settings_raw;
    /**
     * @var string[] Array of question settings.
     */
    private $q_settings = array();
    /**
     * @var string Raw answers setting data.
     */
    private $q_answers_raw;

    /**
     * @var bool User answer parse proceed status.
     */
    private $a_exist = false;
    /**
     * @var string User answer value
     */
    private $a_value;

    /**
     * @var string[] List of all question answers.
     */
    private $r_answers = array();

    /**
     * Loading questions data from database.
     *
     * @param int $question_id Form id.
     */
    function load($question_id)
    {
        $this->id = $question_id;
        $this->quarry = new SqlCall('GetQuestion', $this->id);
        $this->quarry->do_quarry();
        $raw_data = $this->quarry->next_row();
        $this->q_type = $raw_data['type'];
        $this->q_title = $raw_data['title'];
        $this->q_comment = $raw_data['comment'];
        $this->q_answers_raw = $raw_data['answers'];
        $this->q_settings_raw = $raw_data['settings'];
        $this->parse_setting();
        switch ($this->q_type) {
            case 'single':
                $this->handler = new SingleQH();
                break;
            case 'multi':
                $this->handler = new MultiQH();
                break;
            case 'text':
                $this->handler = new TextQH();
                break;
            case 'numeric':
                $this->handler = new NumericQH();
                break;
        }
    }

    /**
     * Creating question HTML code.
     *
     * @return string HTML code.
     */
    function build_html()
    {
        $attr = (array_key_exists('REQ', $this->q_settings) and $this->q_settings['REQ']) ? "data-req='true'" : " data-req='false'";
        $html = "
    <div class='main_form_question' $attr>
        <h3 class='main_form_question_title'>$this->q_title</h3>
        <p class='main_form_question_comment'>$this->q_comment</p>";
        $html .= $this->handler->built_question($this->q_answers_raw, $this->id, $this->q_settings);

        $html .= '
    </div>';
        return $html;
    }

    /**
     * Creating settings array from raw data string.
     */
    private function parse_setting()
    {
        $setting_raw = explode("|", $this->q_settings_raw);
        foreach ($setting_raw as $setting) {
            switch (substr($setting, 0, 3)) {
                case 'REQ':
                    $this->q_settings['REQ'] = true;
                    break;
                case 'MIN':
                    $this->q_settings['MIN'] = (int)substr($setting, 3);
                    break;
                case 'MAX':
                    $this->q_settings['MAX'] = (int)substr($setting, 3);
                    break;
            }
        }
        if (!array_key_exists('REQ', $this->q_settings)) {
            $this->q_settings['REQ'] = false;
        }
    }

    /**
     * Validating user answer.
     *
     * @param string $answer User answer.
     * @return bool Result of validate.
     */
    function parse_answer($answer)
    {
        if ($parsed = $this->handler->valid_answer($answer, $this->q_answers_raw, $this->q_settings)) {
            $this->a_value = $parsed[true];
            $this->a_exist = true;
            return true;
        }
        return false;
    }

    /**
     * Sending user answer into database.
     *
     * @param int $solution Solution id.
     * @param string $password User password for confirm process.
     */
    function send_answer($solution, $password)
    {
        $sql = new SqlCall('AddAnswer', $this->id, $solution, quote($password), quote($this->a_value));
        $sql->do_quarry();
    }

    /**
     * Checking if answer for question is required.
     *
     * @return bool Require status.
     */
    function check_answer()
    {
        if (!$this->a_exist and $this->q_settings['REQ'] == true) {
            return false;
        }
        return true;
    }

    /**
     * Loading all question answers from database.
     */
    function load_results()
    {
        $results = new SqlCall('GetQuestionResults', $this->id);
        $results->do_quarry();
        while ($result = $results->next_row()) {
            $this->r_answers[$result['user_link']] = $result['answers'];
        }

    }

    /**
     * Creating question result HTML code.
     *
     * @return string HTML code.
     */
    function built_results()
    {
        $html = "
        <div class='form_result_question'>
            <div class='form_result_question_title'>
                <h3>{$this->q_title}</h3>
        ";
        if (count($this->r_answers) == 0)
        {
            $results_total = 1;
        }
        else
        {
            $results_total = count($this->r_answers);
        }
        $html .= $this->handler->built_result($this->r_answers, $results_total, $this->q_answers_raw);
        $html .= "
            </div>
        </div>
        ";
        return $html;
    }

    /**
     * Validating question from form creation process and building object structure.
     *
     * @param string[] $datalist Array of question parameters.
     * @return bool Validating status.
     */
    function parse_create($datalist)
    {
        if ($datalist['name'] == '')
            return false;
        else
            $this->q_title = quote($datalist['name']);
        if (in_array($datalist['type'], ['single', 'multi', 'numeric', 'text']))
            $this->q_type = quote($datalist['type']);
        else
            return false;
        $answers = '';
        $settings = '';
        foreach ($datalist as $ident => $item)
        {
            if(substr($ident, 0, 4) == 'answ')
            {
                $answers .= '|'.$item;
            }
            elseif (substr($ident, 0, 4) == 'sett')
            {
                $type = substr($ident, 5);
                $settings .= '|'.$type;
                if ($type != 'REQ')
                    $settings .= $item;
            }
        }

        if ($datalist['desc'] == '')
            $this->q_comment = 'NULL';
        else
            $this->q_comment = quote($datalist['desc']);

        if ($answers == '')
        {
            if($datalist['type'] = 'single' or $datalist['type'] = 'multi')
                return false;
            $this->q_answers_raw = 'NULL';
        }
        else
            $this->q_answers_raw = quote(substr($answers, 1));

        if ($settings == '')
            $this->q_settings_raw = 'NULL';
        else
            $this->q_settings_raw = quote(substr($settings, 1));

        return true;
    }

    /**
     * Inserting question into database.
     * @param $id
     */
    function send_create($id)
    {
        $questions = new SqlCall('AddQuestion', $id, $this->q_type, $this->q_title, $this->q_comment, $this->q_answers_raw, $this->q_settings_raw);
        $questions->do_quarry();
    }
}


/**
 * Strategy class for Question, making action for specific question type.
 *
 * @see Question
 * @see SingleQH
 * @see MultiQH
 * @see TextQH
 * @see NumericQH
 */
abstract class QuestionHandler
{
    /**
     * Creating question HTML code for specific type of them.
     *
     * @param string $answer_raw Raw answers data.
     * @param int $id Question id.
     * @param string[] $settings Array of question settings.
     * @return string Question HTML code.
     */
    public abstract function built_question($answer_raw, $id, $settings);

    /**
     * Checking correct of answer.
     *
     * @param string $answer User answer for question.
     * @param string $answers_raw Raw answers data.
     * @param string[] $settings Array of question settings.
     * @return mixed
     */
    public abstract function valid_answer($answer, $answers_raw, $settings);

    /**
     * Creating result HTML code for specific type of question.
     *
     * @param string[] $results Array of all users answers (only SingleQH and MultiQH).
     * @param int $results_total Number of all answers.
     * @param string[] $answers Array of all possible answers (only SingleQH and MultiQH).
     * @return string Result HTML code.
     */
    public abstract function built_result($results, $results_total, $answers);
}

/**
 * Strategy for single choice questions.
 *
 * @see QuestionHandler
 */
class SingleQH extends QuestionHandler
{
    public function built_question($answer_raw, $id, $settings)
    {
        $attr = '';
        if ($settings['REQ'])
        {
            $attr .= ' required';
        }
        $answers_arr = explode("|", $answer_raw);
        $html = '';
        $count = 0;
        foreach ($answers_arr as $answer) {
            $count++;
            $id_ = 'q_' . $id . '_a_' . $count;
            $html = $html . "
        <div class='main_form_question_choice'>
            <input id='$id_'  type='radio' name='question_$id' value='{$answer}'{$attr}>
            <label for='$id_'>$answer</label><br>
        </div>";
        }
        return $html;
    }

    public function valid_answer($answer, $answers_raw, $settings)
    {
        $answers = explode('|', $answers_raw);
        if (!in_array($answer, $answers)) {
            return false;
        }
        return array(true => $answer);
    }

    public function built_result($results, $results_total, $answers)
    {
        $answers = explode('|', $answers);
        $counter = array();
        foreach ($answers as $answer) {
            $counter[$answer] = 0;
        }
        foreach ($results as $result) {
            $counter[$result]++;
        }
        $html = "";
        foreach ($counter as $answer => $value) {
            $percent = round(($value / $results_total) * 100, 2);
            $html .= "
                <div class='form_result_question_row'>
                    {$value} ({$percent}%) - {$answer}
                </div>
            ";
        }
        return $html;
    }
}

/**
 * Strategy for multi choice questions.
 *
 * @see QuestionHandler
 */
class MultiQH extends QuestionHandler
{
    public function built_question($answer_raw, $id, $settings)
    {
        $answers_arr = explode("|", $answer_raw);
        $html = '';
        $count = 0;
        foreach ($answers_arr as $answer) {
            $count++;
            $id_ = 'q_' . $id . '_a_' . $count;
            $html = $html . "
        <div class='main_form_question_choice'>
            <input id='$id_'  type='checkbox' name=question_{$id}[]' value='{$answer}'>
            <label for='$id_'>$answer</label><br>
        </div>";
        }
        return $html;
    }

    public function valid_answer($answer, $answers_raw, $settings)
    {
        $answers = explode('|', $answers_raw);
        $items = array_unique($answer, SORT_STRING);
        foreach ($items as $item) {
            if (!in_array($item, $answers)) {
                return false;
            }
        }

        return array(true => implode('|', $items));
    }

    public function built_result($results, $results_total, $answers)
    {
        $answers = explode('|', $answers);
        $counter = array();
        foreach ($answers as $answer) {
            $counter[$answer] = 0;
        }
        foreach ($results as $result) {
            foreach (explode('|', $result) as $item) {
                $counter[$item]++;
            }
        }
        $html = "";
        foreach ($counter as $answer => $value) {
            $percent = round(($value / $results_total) * 100, 2);
            $html .= "
                <div class='form_result_question_row'>
                    {$value} ({$percent}%) - {$answer}
                </div>
            ";
        }
        return $html;
    }
}

/**
 * Strategy for text questions.
 *
 * @see QuestionHandler
 */
class TextQH extends QuestionHandler
{
    public function built_question($answer_raw, $id, $settings)
    {
        $attr = '';
        if ($settings['REQ'])
        {
            $attr .= ' required';
        }
        return "
        <div class='main_form_question_write'>
            <input type='text'  name='question_{$id}'{$attr}>
        </div>";
    }

    public function valid_answer($answer, $answers_raw, $settings)
    {
        return array(true => $answer);
    }

    public function built_result($results, $results_total, $answers)
    {
        $html = "";
        foreach ($results as $result) {
            $html .= "
                <div class='form_result_question_row'>
                    {$result}
                </div>
            ";
        }
        return $html;
    }
}

/**
 * Strategy for numeric questions.
 *
 * @see QuestionHandler
 */
class NumericQH extends QuestionHandler
{
    public function built_question($answer_raw, $id, $settings)
    {
        $attr = '';
        if (array_key_exists('MIN', $settings)) {
            $attr .= " min='{$settings['MIN']}'";
        }
        if (array_key_exists('MAX', $settings)) {
            $attr .= " max='{$settings['MAX']}'";
        }
        if ($settings['REQ'])
        {
            $attr .= ' required';
        }
        return "
        <div class='main_form_question_write'>
            <input type='number'  name='question_{$id}'{$attr}>
        </div>";
    }

    public function valid_answer($answer, $answers_raw, $settings)
    {
        if (array_key_exists('MIN', $settings)) {
            if ($settings['MIN'] > (int)$answer) {
                return false;
            }
        }
        if (array_key_exists('MAX', $settings)) {
            if ($settings['MAX'] < (int)$answer) {
                return false;
            }
        }
        return array(true => (int)$answer);
    }

    public function built_result($results, $results_total, $answers)
    {
        $counter = array();
        foreach ($results as $result) {
            if (!array_key_exists($result, $counter)) {
                $counter[$result] = 0;
            }
            $counter[$result]++;
        }
        ksort($counter);
        $html = "";
        foreach ($counter as $answer => $value) {
            $percent = round(($value / $results_total) * 100, 2);
            $html .= "
                <div class='form_result_question_row'>
                    {$value} ({$percent}%) - {$answer}
                </div>
            ";
        }
        return $html;
    }
}