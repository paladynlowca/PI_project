<?php
include_once 'config.php';
/**
 * @file
 * Session class file.
 */

/**
 * Class contains all user session handling methods.
 */
class Session
{
    /**
     *
     * Checking if user is logged in.
     *
     * @return bool Login status.
     */
    function check_login()
    {
        if (!array_key_exists('islogged', $_SESSION))
        {
            $_SESSION['islogged'] = false;
        }
        return $_SESSION['islogged'];
    }

    /**
     *
     * Checking if user have level of grants.
     *
     * @param int $grants Expected grants level.
     * @return bool User grants status.
     */
    function check_grants($grants)
    {
        if($this->check_login())
        {
            if ($_SESSION['usergrant'] >= $grants)
            {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * Login user into services
     *
     * @param string $login User login.
     * @param string $password User password.
     * @return string Status code - log_of for success.
     */
    function login($login, $password)
    {
        if ($this->check_login())
        {
            return 'log_previously_logged';
        }

        $quarry = new SqlCall('CheckPass', quote($login), quote($password));
        $quarry->do_quarry();
        if($quarry->get_length() === 0)
        {
            return 'log_invalid_data';
        }
        $row = $quarry->next_row();
        $_SESSION['username'] = $row["login"];
        $_SESSION['userid'] = $row["id"];
        $_SESSION['usergrant'] = $row["grants"];
        $_SESSION['islogged'] = true;
        return 'log_ok';
    }

    /**
     *
     * Logout user.
     *
     * @return string Status code - logout_ok for success.
     * @see login
     */
    function logout()
    {
        if($this->check_login())
        {
            $_SESSION['islogged'] = false;
            $_SESSION['username'] = '';
            $_SESSION['userid'] = null;
            $_SESSION['usergrant'] = 0;
            return 'logout_ok';
        }
        return 'logout_not_logged';
    }

    /**
     *
     * Creating user account and logging into it.
     *
     * @param string $login User login.
     * @param string $password User password.
     * @param string $repeat_password Repeat user password.
     * @param string $email User email address.
     * @return string Status code - reg_ok for success.
     */
    function register($login, $password, $repeat_password, $email)
    {
        if(!$this->check_login())
        {
            if ($password !== $repeat_password)
            {
                return 'reg_different_passwords';
            }
            $quarry = new SqlCall('Register', quote($login), quote($password), quote($email));
            $quarry->do_quarry();

            if ($quarry->get_length())
            {
                $result = $quarry->next_row()['result'];
                if ($result == -1)
                {
                    return 'reg_login_exist';
                }
                elseif ($result == -2)
                {
                    return 'reg_email_exist';
                }
                else
                {
                    $this->login($login, $password);
                    return 'reg_ok';
                }
            }
            return 'reg_general_error';
        }
        return 'reg_previously_logged';
    }
}