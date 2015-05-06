<?php 
@session_start();
$_SESSION['EMPW'] = $_POST['Email'];
?>
<?php require_once('Connections/medibank.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$colname_EmailPassword = "-1";
if (isset($_SESSION['EMPW'])) {
  $colname_EmailPassword = $_SESSION['EMPW'];
}
mysql_select_db($database_medibank, $medibank);
$query_EmailPassword = sprintf("SELECT * FROM users WHERE Email = %s", GetSQLValueString($colname_EmailPassword, "text"));
$EmailPassword = mysql_query($query_EmailPassword, $medibank) or die(mysql_error());
$row_EmailPassword = mysql_fetch_assoc($EmailPassword);
$totalRows_EmailPassword = mysql_num_rows($EmailPassword);

mysql_free_result($EmailPassword);
?>
<?php

if ($totalRows_EmailPassword > 0) {

$email = $_SESSION['EMPW'];
$subject="Your Medibank Password";
$message="Your password is:".$row_EmailPassword['Password'];
$headers = 'From: noreply@medibank.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
	
mail ( $email, $subject, $message, $headers);
}
	if ($totalRows_EmailPassword > 0) {
		echo "Your password has been sent to your email";
	}
	else {
		echo "Email not found. Please enter an existing email address";
	}

?>