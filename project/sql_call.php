<?php
include_once "sql_data.php";
/**
 * @file
 * SqlCall class and quote() function file.
 */

/**
 * Calling SQL stored procedure and parsing their results.
 */
class SqlCall
{
    /**
     * @var bool Connection result status.
     */
    private $status;
    /**
     * @var mixed[] Arguments for procedure.
     */
    private $arguments;
    /**
     * @var string Procedure name.
     */
    private $procedure;
    /**
     * @var string SQL quarry.
     */
    private $quarry;
    /**
     * @var mysqli_result Data received from mysqli quarry.
     */
    private $raw_data;
    /**
     * @var int Current raw counter.
     */
    private $current_row = -1;

    /**
     * @var int Numbers of data result rows.
     */
    private $rows_number;
    /**
     * @var array[] Array of all rows.
     */
    private $rows = [];

    /**
     * SqlCall constructor.
     *
     * @param string $procedure  Procedure name.
     * @param mixed ...$arguments  Arguments for procedure.
     */
    function __construct($procedure, ...$arguments)
    {
        $this->procedure = $procedure;
        $this->arguments = $arguments;
    }

    /**
     * Building an SQL quarry.
     */
    public function build()
    {
        $quarry = "CALL $this->procedure(";
        $first_flag = true;
        foreach ($this->arguments as $argument)
        {
            if(!$first_flag)
                $quarry = $quarry.', ';
            else
                $first_flag = false;
            $quarry = $quarry.$argument;
        }
        $quarry = $quarry.');';
        $this->quarry = $quarry;
    }

    /**
     * Connecting to database and make quarry.
     *
     * @return bool Result of connection.
     */
    function connect()
    {
        global $sql_host, $sql_user, $sql_password , $sql_database;
        $connection = new mysqli($sql_host, $sql_user, $sql_password , $sql_database);
        $raw_data = $connection -> query($this->quarry);
        $connection->close();
        if($raw_data)
        {
            $this->raw_data = $raw_data;
            $this->status = true;
        }
        else
        {
            $this->status = false;
        }
        return $this->status;
    }

    /**
     * Displaying sql quarry (debug only).
     */
    function view_quarry()
    {
        $this->build();
        echo $this->quarry;
    }

    /**
     * Converting mysqli result into class data structure.
     *
     * @return bool Result of conversion.
     */
    function parse_answer()
    {
        if($this->status) {
            $this->rows_number = $this->raw_data->num_rows;
            if ($this->rows_number !== 0) {
                while ($row = $this->raw_data->fetch_assoc()) {
                    array_push($this->rows, $row);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Method aggregate built(), connect() and parse_answer() methods for simplified code.
     *
     * @return bool result of connect() method.
     */
    function do_quarry()
    {
        $this->build();
        if(!$this->connect())
        {
            return false;
        }
        $this->parse_answer();
        return true;
    }

    /**
     * Returning number of rows.
     *
     * @return int Number of rows.
     */
    function get_length()
    {
        return $this->rows_number;
    }

    /**
     * Return row by number.
     *
     * @param int $number Row number.
     * @return bool|mixed[] Row array if exist, else false.
     */
    function get_row($number)
    {
        if ($this->status and $number >= 0 and $number < $this->rows_number)
        {
            return$this->rows[$number];
        }
        return false;
    }

    /**
     * Return next row.
     *
     * @return bool|mixed[] Row array if exist, else false.
     */
    function next_row()
    {
        if ($this->current_row >= $this->rows_number)
        {
            return false;
        }
        $this->current_row ++;
        return $this->get_row($this->current_row);
    }

    /**
     * Return previous row.
     *
     * @return bool|mixed[] Row array if exist, else false.
     */
    function previous_row()
    {
        if ($this->current_row < 0)
        {
            return false;
        }
        $this->current_row --;
        return $this->get_row($this->current_row);
    }
}


/**
 * Adding single quote do string (for use as a SqlCall string arguments.)
 * @param string $text Input text.
 * @return string Output text with quotes.
 */
function quote($text)
{
    return "'".$text."'";
}