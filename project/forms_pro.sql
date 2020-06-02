delimiter $$
create procedure CheckPass(login_ varchar(32), password_ varchar(32))
BEGIN
    select pi_users.id, pi_users.login, pi_users.grants
    from pi_users
    where sha2(concat(password_, "f3%$dg%hdfh55ch;60"), 256) = pi_users.password
      and pi_users.login = login_;
END $$
delimiter ;

delimiter $$
CREATE PROCEDURE Register(login_ varchar(32), password_ varchar(32), email_ varchar(64))
BEGIN
    IF EXISTS(SELECT pi_users.id from pi_users where pi_users.login = login_) THEN
        SELECT -1;
    ELSEIF EXISTS(SELECT pi_users.id from pi_users where pi_users.email = email_) THEN
        SELECT -2;
    ELSE
        INSERT INTO pi_users (login, password, email, grants)
        VALUES (Login_, sha2(concat(password_, "f3%$dg%hdfh55ch;60"), 256), email_, 1);
        SELECT 0;
    END IF;
END $$
delimiter ;

delimiter $$
CREATE PROCEDURE AddAnswer(question_ int, solution_ int, password_ varchar(32), value_ varchar(1024))
BEGIN
    INSERT INTO pi_answers (pi_answers.question, answers, user_link)
    VALUES (question_, value_, sha2(concat(password_, solution_), 256));
    SELECT 1;
END $$
delimiter ;

delimiter $$
CREATE PROCEDURE AddSolution(form_ int, user_ int)
BEGIN
    INSERT INTO pi_solutions (pi_solutions.form, pi_solutions.user) VALUES (form_, user_);
    select pi_solutions.id from pi_solutions where form = form_ and user = user_;
END $$
delimiter ;

delimiter $$
CREATE PROCEDURE GetSolutionsByUser(user_ int)
BEGIN
    SELECT pi_solutions.id FROM pi_solutions WHERE pi_solutions.user=user_;
END $$
delimiter ;

delimiter $$
CREATE PROCEDURE GetAnswersBySolution(solution_ int, password_ varchar(32))
BEGIN
    SELECT pi_answers.answers FROM pi_answers WHERE pi_answers.user_link=sha2(concat(password_, solution_), 256);
END $$
delimiter ;

delimiter $$
CREATE PROCEDURE ValidateSolution(solution_ int, password_ varchar(32), data_ VARCHAR(2048))
BEGIN
    UPDATE pi_solutions SET pi_solutions.validate_key = sha2(concat(password_, data_), 256) WHERE pi_solutions.id = solution_;
    SELECT 1;
END $$
delimiter ;

delimiter $$
CREATE PROCEDURE CheckSolution(solution_ int, password_ varchar(32), data_ VARCHAR(2048))
BEGIN
    DECLARE _validate_key VARCHAR(256);
    SELECT pi_solutions.validate_key INTO _validate_key FROM pi_solutions WHERE pi_solutions.id=solution_;
    IF _validate_key=sha2(concat(password_, data_), 256) THEN
        SELECT 1 AS result;
    ELSE
        SELECT 0 AS result;
    end if;
END $$
delimiter ;

delimiter $$
CREATE PROCEDURE UpdateSolution(form_ int, user_ int)
BEGIN
    UPDATE pi_solutions SET update_count = update_count + 1 where form = form_ and user = user_;
    SELECT 1;
END $$
delimiter ;

delimiter $$
CREATE PROCEDURE GetFormQuestions(form_ int)
BEGIN
    SELECT pi_questions.id FROM pi_questions where pi_questions.form = form_;
END $$
delimiter ;

delimiter $$
CREATE PROCEDURE GetForm(form_ int)
BEGIN
    SELECT pi_forms.id, pi_forms.title, pi_forms.intro, pi_forms.time_limit, pi_users.login
    FROM pi_forms
             JOIN pi_users ON pi_forms.creator = pi_users.id
    where pi_forms.id = form_;
END $$
delimiter ;

delimiter $$
CREATE PROCEDURE GetFormBySolution(solution_ int)
BEGIN
    SELECT pi_forms.id, pi_forms.title, pi_forms.intro
    FROM pi_forms
             JOIN pi_solutions on pi_forms.id = pi_solutions.form
    where pi_solutions.id = solution_;
END $$
delimiter ;

delimiter $$
CREATE PROCEDURE GetQuestion(question_ int)
BEGIN
    SELECT pi_questions.id,
           pi_question_type.type,
           pi_questions.title,
           pi_questions.comment,
           pi_questions.settings,
           pi_questions.answers,
           pi_questions.comment_answer
    FROM pi_questions
             join pi_question_type on pi_questions.type = pi_question_type.id
    where pi_questions.id = question_;
END $$
delimiter ;



delimiter $$
CREATE PROCEDURE GetFormsList(user_ int)
BEGIN
    SELECT pi_forms.id, pi_forms.time_limit, pi_forms.title, pi_users.login
    from pi_forms
             join pi_users on pi_forms.creator = pi_users.id
    where not exists(
            select id, form from pi_solutions where user_ = pi_solutions.user and pi_forms.id = pi_solutions.form);
END $$
delimiter ;

delimiter $$
CREATE PROCEDURE GetFormsResults(user_ int)
BEGIN
    IF user_ = 0 THEN
        SELECT pi_forms.id, pi_forms.time_limit, pi_forms.title, pi_users.login
        from pi_forms
                 join pi_users on pi_forms.creator = pi_users.id;
    ELSE
        SELECT pi_forms.id, pi_forms.time_limit, pi_forms.title, pi_users.login
        from pi_forms
                 join pi_users on pi_forms.creator = pi_users.id
        where pi_forms.creator = user_;
    END IF;
END $$
delimiter ;

delimiter $$
CREATE PROCEDURE GetQuestionResults(question_ int)
BEGIN
    SELECT pi_answers.user_link, pi_answers.answers, pi_answers.comment_answer
    FROM pi_answers
    where pi_answers.question = question_;
END $$
delimiter ;

delimiter $$
CREATE PROCEDURE CheckSolutionExist(user_ int, form_ int)
BEGIN
    select id, form from pi_solutions where user_ = pi_solutions.user and form_ = pi_solutions.form;
END $$
delimiter ;


delimiter $$
CREATE PROCEDURE AddQuestion(form_ int, type_ varchar(32), title_ varchar(64), comment_ varchar(256), answers_ varchar(1024), settings_ varchar(32))
BEGIN
    INSERT INTO pi_questions (pi_questions.form, pi_questions.type, pi_questions.title, pi_questions.comment, pi_questions.answers, pi_questions.settings)
    VALUES (form_, (select pi_question_type.id from pi_question_type where pi_question_type.type = type_), title_, comment_, answers_, settings_);
    SELECT 1;
END $$
delimiter ;

delimiter $$
CREATE PROCEDURE AddForm(user_ int, title_ varchar(64), intro_ varchar(256), time_limit_ TIMESTAMP)
BEGIN
    INSERT INTO pi_forms (creator, time_limit, title, intro) VALUES (user_, time_limit_, title_, intro_);
    SELECT pi_forms.id FROM pi_forms WHERE pi_forms.creator = user_ ORDER BY pi_forms.id DESC LIMIT 1;
END $$
delimiter ;

