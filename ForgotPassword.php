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
if (isset($_SESSION['EmailPassword'])) {
  $colname_EmailPassword = $_SESSION['EmailPassword'];
}
mysql_select_db($database_medibank, $medibank);
$query_EmailPassword = sprintf("SELECT * FROM users WHERE Email = %s", GetSQLValueString($colname_EmailPassword, "text"));
$EmailPassword = mysql_query($query_EmailPassword, $medibank) or die(mysql_error());
$row_EmailPassword = mysql_fetch_assoc($EmailPassword);
$totalRows_EmailPassword = mysql_num_rows($EmailPassword);
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
        	<li><a href="Login.php">Login</a></li>
            <li><a href="Register.php">Register</a></li>
            <li><a href="ForgotPassword.php">Forgot Password</a></li>
        </ul>
    </nav>
</div>
<div id="Content">
	<div id="PageHeading">
	  <h1>Page Heading</h1>
	</div>
	<div id="ContentLeft">
	  <h2>Your Message Here</h2>
	  <h6>&nbsp;</h6>
	  <h6>Your text here</h6>
	</div>
    <div id="ContentRight">
      <form action="EmailPasswordScript.php" method="post" name="emailPasswordForm" id="emailPasswordForm">
        <p>
          <label for="Email">Email:</label>
          <input type="email" name="Email" id="Email">
        </p>
        <p>
          <input name="emailPW" type="submit" class="buttonStyle" id="emailPW" value="Email Password">
        </p>
      </form>
    </div>
</div>
© Franklin Yu | <a href="Admin.php">Admin</a>
<div id="Footer"></div>
</div>
</body>
</html>
<?php
mysql_free_result($EmailPassword);
?>
