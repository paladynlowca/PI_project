$(document).ready(function(){

    $("#login_form").submit(login_form_submit);

    $("#register_form").submit(register_form_submit);

    $("#main_form").submit(main_form_submit);
});

/**
 * @file
 * Various scripts.
 */


/**
 * Send login form data, run after submit login form.
 *
 * @param e Event data.
 */
function login_form_submit(e) {

    e.preventDefault();

    //do your own request an handle the results
    $.ajax({
        url: e.currentTarget.action,
        type: 'post',
        dataType: 'text',
        data: $("#login_form").serialize(),
        success: function(data){
            if (data.toString().endsWith("ok")) {
                login_hide();
                window.location.href = "index.php";
            }
            else
            {
                alert("Błędne dane logowania")
            }
        },
    });

}

/**
 * Send register form data, run after submit register form.
 *
 * @param e Event data.
 */
function register_form_submit(e) {

    e.preventDefault();

    $.ajax({
        url: e.currentTarget.action,
        type: 'post',
        dataType: 'text',
        data: $("#register_form").serialize(),
        success: function(data){
            if (data.toString().endsWith("ok")) {
                login_hide();
                window.location.href = "index.php";
            }
            else if (!data.localeCompare("reg_login_exist"))
            {
                alert("Podany login jest w uzyciu.")
            }
            else if (!data.localeCompare("reg_email_exist"))
            {
                alert("Podany e-mail jest w uzyciu.")
            }
            else if (!data.localeCompare("reg_different_passwords"))
            {
                alert("Hasła nie sa identyczne.")
            }
            else
            {
                alert("Błąd rejestracji.")
            }
        },
    });

}

/**
 * Send main form data, run after submit main form.
 *
 * @param e Event data.
 */
function main_form_submit(e) {
    e.preventDefault();
    //do your own request an handle the results
    let form = $("#main_form");
    $.ajax({
        url: e.currentTarget.action,
        type: 'post',
        dataType: 'text',
        data: form.serialize() +
            "&form_id=" + form.attr("data-formid"),
        success: function(data){
            alert("Formularz poprawnie dodany");
            window.location.href = "forms_list.php";
        },
        error: function (data) {
            alert('Błąd wysyłania');
        }
    });

}

/**
 * Hiding all popup form windows.
 *
 */
function login_hide() {
    $('#popup_login').css('display', 'none');
    $('#popup_register').css('display', 'none');
    $('#popup_check_solutions').css('display', 'none');
}

/**
 * Show login popup window.
 */
function login_show() {
    $('#popup_login').css('display', 'flex');
}

/**
 * Show register popup window.
 */
function register_show() {
    $('#popup_register').css('display', 'flex');
}

/**
 * Show confirm check solution popup window.
 */
function check_solutions_show() {
    $('#popup_check_solutions').css('display', 'flex');
}

/**
 * Logout user.
 */
function logout() {
    $.ajax({
        url: $("#logout_button").attr('href'),
        type: 'post',
        dataType: 'text',
        data: '',
        success: function(data) {
            if (data.toString().endsWith("ok")) {
                window.location.href = "index.php";
            }
            else
            {
                alert("Błąd wyglogowania")
            }
        }
        ,
    });
}
