<?php
//include_once 'Sql.php';
//
//
//class Solution
//{
//    function __construct($form_id, $question_id, $answers, $password)
//    {
//        $user_id = $_SESSION['userid'];
//        $form = new Sql('select * from pi_forms where id='.$form_id.';');
//        if ($_SESSION['islogged'] and $form->data_size())
//        {
//            if (strtotime($form->next_line()['time_limit']) - time() > 0)
//            {
//                new Sql("insert into pi_solutions (form, user) values ($form_id, $user_id)");
//                foreach ($answers as $answer)
//                {
//                    $line = new Sql("CALL AddAnswer($question_id, $form_id, $password, $answers)");
//                }
//            }
//        }
//    }
//}