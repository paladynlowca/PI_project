<?php
include_once 'config.php';
/**
 * @file
 * Header class file.
 */

/**
 * Class Header
 *
 * @see Footer
 */
class Header
{
    /**
     * Creating main menu left bar HTML code (options of surveys).
     *
     * @param string $rel_path Related path to "project" folder.
     * @param Session $session Session class.
     * @return string Html code.
     */
    function insert_left_menu($rel_path, $session)
    {
        $html = "";
        if ($session->check_login()) {
            $html .= "
                <div class='main_menu_element' onclick='chceck_solutions()'>
                    <a href='{$rel_path}forms_list.php'>Ankiety</a>
                </div>
                <div class='main_menu_element' onclick='check_solutions_show()'>
                    Weryfikacja rozwiązań
                </div>
                <div class=\"popup_form\" id=\"popup_check_solutions\" onclick=\"login_hide()\">
                    <div class=\"popup_center_box\" onclick=\"event.stopPropagation()\">
                        <form id=\"check_solutions_form\" method=\"post\" action=\"check_solutions.php\">
                            <label for=\"password\">Hasło:</label>
                            <input type=\"password\" id=\"password\" name=\"password\"><br><br>
                            <button form=\"check_solutions_form\">Weryfikuj rozwiązania</button>
                        </form>
                    </div>
                </div>
    ";
        }
        if (array_key_exists('usergrant', $_SESSION) and $_SESSION['usergrant'] >= 50) {
            $html .= "
                <div class='main_menu_element'>
                    <a href='{$rel_path}result_list.php'>Rozwiązania</a>
                </div>
                <div class='main_menu_element'>
                    <a href='{$rel_path}create_form.php'>Nowa ankieta</a>
                </div>
    ";
        }
        return $html;
    }

    /**
     *
     * Creating main menu right bar HTML code (options of user and logging).
     *
     * @param Session $session Session class.
     * @return string Html code.
     */
    function insert_user_bar($session)
    {
        if ($session->check_login()) {
            $data = "
                <div id='header_username' class = 'main_menu_element'>
                    {$_SESSION['username']}
                </div>
                <div id='logout_button' class = 'main_menu_element' onclick='logout()' href='confirm_logout.php'>
                    Wylogowanie
                </div>";
        } else {
            $data = "
                <div id='login_button' class = 'main_menu_element' onclick='login_show()'>
                    Logowanie
                </div>
                <div id='register_button' class = 'main_menu_element' onclick='register_show()'>
                    Rejestracja
                </div>";
        }
        return $data;
    }

    /**
     *
     * Inserting overall header HTML code into page.
     *
     * @param string $rel_path Related path to "project" folder.
     */
    function insert_header($rel_path)
    {
        $session = new Session();
        $html = "
<!doctype html>
<html lang='pl'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport'
          content='width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0'>
    <meta http-equiv='X-UA-Compatible' content='ie=edge'>
    <title>Ankieter</title>
    <link rel='stylesheet' type='text/css' href='{$rel_path}styles/style.css'>
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js'></script>
    <script type='text/javascript' src='{$rel_path}scripts/script.js'></script>
</head>
<body>";
        include_once 'login_form.html';
        $menu_left = $this->insert_left_menu($rel_path, $session);
        $menu_right = $this->insert_user_bar($session);
        $html .= "
    <section id = 'header'>
        <div id = 'main_menu'>
            <div class='main_menu_left'>
                {$menu_left}
            </div>
            <div class='main_menu_right'>
                {$menu_right}
            </div>
        </div>
        <hr>
    </section>";
        echo $html;
    }
}


