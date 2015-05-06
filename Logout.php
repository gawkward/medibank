<?php require_once('Connections/medibank.php'); ?>
<?php
// *** Logout the current user.
$logoutGoTo = "Login.php";
if (!isset($_SESSION)) {
  session_start();
}
$_SESSION['MM_Username'] = NULL;
$_SESSION['MM_UserGroup'] = NULL;
unset($_SESSION['MM_Username']);
unset($_SESSION['MM_UserGroup']);
if ($logoutGoTo != "") {header("Location: $logoutGoTo");
exit;
}
?>
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

$colname_Logout = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_Logout = $_SESSION['MM_Username'];
}
mysql_select_db($database_medibank, $medibank);
$query_Logout = sprintf("SELECT * FROM users WHERE Email = %s", GetSQLValueString($colname_Logout, "text"));
$Logout = mysql_query($query_Logout, $medibank) or die(mysql_error());
$row_Logout = mysql_fetch_assoc($Logout);
$totalRows_Logout = mysql_num_rows($Logout);
?>
<!doctype html>
<html>
<head>
<link href="CSS/Layout.css" rel="stylesheet" type="text/css" />
<link href="CSS/Menu.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jQuery.Validate/1.6/jQuery.Validate.min.js"></script>
<meta charset="UTF-8">
<title>Untitled Document</title>
</head>

<body>
<div id="Holder">
<div id="Header"></div>
<div id="NavBar">
	<nav>
    	<ul>
        	<li><a href="#">Login</a></li>
            <li><a href="#">Register</a></li>
            <li><a href="#">Forgot Password</a></li>
        </ul>
    </nav>
</div>
<div id="Content">
	<div id="PageHeading">
	  <h1>You Have Logged Out</h1>
	</div>
	<div id="ContentLeft">
	  <h2>&nbsp;</h2>
	  <h6>&nbsp;</h6>
	  <h6>&nbsp;</h6>
	</div>
    <div id="ContentRight"></div>
</div>
<div id="Footer">© Franklin Yu | <a href="Admin.php">Admin</a></div>
</div>
</body>
</html>
<?php
mysql_free_result($Logout);
?>
