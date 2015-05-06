<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_medibank = "localhost";
$database_medibank = "medibank";
$username_medibank = "root";
$password_medibank = "root";
$medibank = mysql_pconnect($hostname_medibank, $username_medibank, $password_medibank) or trigger_error(mysql_error(),E_USER_ERROR); 
?>