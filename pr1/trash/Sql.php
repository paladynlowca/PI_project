<?php
//# include_once "config.php";
//include_once "sql_data.php";
//
//class Sql
//{
//    private $connection;
//    private $result;
//    private $data = [];
//    private $result_length;
//    private $current_line = 0;
//
//
//    function __construct($quarry)
//    {
//        global $sql_host, $sql_user, $sql_password , $sql_database;
//        $this->connection = new mysqli($sql_host, $sql_user, $sql_password , $sql_database);
//        $this->result = $this->connection -> query($quarry);
//        $this->connection->close();
//        if($this->is_correct() and $this->result->num_rows !== 0)
//        {
//            while($row = $this->result->fetch_assoc())
//            {
//                array_push($this->data, $row);
//            }
//            $this->result_length = (int)array_key_last($this->data);
//        }
//    }
//
//    public function is_correct()
//    {
//        if($this->result)
//        {
//            return true;
//        }
//        else
//        {
//            return false;
//        }
//    }
//
//    public function data_size()
//    {
//        return $this->result->num_rows;
//    }
//
//    public function get_row($number)
//    {
//        if(0 <= $number and $number <= $this->result_length)
//        {
//            return $this->data[$number];
//        }
//        else
//        {
//            return false;
//        }
//    }
//
//    public function next_line()
//    {
//        if($this->current_line > $this->result_length)
//        {
//            return false;
//        }
//        else
//        {
//            $line = $this->current_line;
//            $this->current_line ++;
//            return $this->get_row($line);
//        }
//    }
//
//    public function line()
//    {
//        return $this->get_row($this->current_line);
//    }
//
//    public function prev_line()
//    {
//        if($this->current_line == 0)
//        {
//            return false;
//        }
//        else
//        {
//            $line = $this->current_line;
//            $this->current_line --;
//            return $this->get_row($line);
//        }
//    }
//}