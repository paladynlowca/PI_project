# Anonymous survey application

Web application allows to create and solve surveys with anonymization of answers.

##Features

Application allows:

• creating user,

• create surveys with four type of question (single choice, multi choice, text and number) and date of expiration,

• show summary of users answers,

• solving surveys,

• verification of data authenticity.


## Data protection

To make sure that nobody can bind users and their answers, data was protected by user password. On creating survey solution app generate something which I called "user link" and it's inserting to every single question answer. It is simply hashed with SHA-256 algorithm connected user password and solution ID.

Also application can check if someone changed answers; all solutions have hashed string contains all answers and user password, so if data isn't original user can detect it by implemented feature.

## Installation

1. Copy file into server. Be sure, that files from /public_html/project/ is available from outside. If you change data structure, change also in /public_html/project/statments.php variable $root into your project root folder.

2. Run sql script located in /sql/ onto application database and change data in /project/sql_data.php into correct for your server.

And it's all, everything should work properly.

## About structure

All information about application files, classes and functions is available in [https://paladynlowca.github.io/PI_project/documentation/files.html](https://paladynlowca.github.io/PI_project/documentation/files.html) (generated by Doxygen 1.8.18)
