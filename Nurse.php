<?php require_once('Connections/medibank.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "NURSE";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "Doctor.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
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

mysql_select_db($database_medibank, $medibank);
$query_NurseHome = "SELECT * FROM users";
$NurseHome = mysql_query($query_NurseHome, $medibank) or die(mysql_error());
$row_NurseHome = mysql_fetch_assoc($NurseHome);
$totalRows_NurseHome = mysql_num_rows($NurseHome);
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
        	<li><a href="Nurse.php">Home</a></li>
            <li><a href="UpdateNurse.php">My Account</a></li>
          <li><a href="https://www.google.com/calendar/render?cid=9sglalebhs7h4hsa1ia3r57850%40group.calendar.google.com#main_7" target="new">Appointments</a></li>
            <li><a href="ViewPatientsNurse.php">Patients</a></li>
          <li><a href="CreateVisit.php">New Visit</a></li>
          <li><a href="Logout.php">Logout</a></li>
        </ul>
    </nav>
</div>
<div id="Content">
	<div id="PageHeading">
	  <h1>Welcome Nurse <?php echo $row_NurseHome['FirstName']; ?> <?php echo $row_NurseHome['LastName']; ?> </h1>
	</div>
	<div id="ContentLeft">
	  <h2>MediBank Nurse Portal</h2>
	  <p>
      <img name="imageField" type="image" id="imageField" src="assets/nurse.gif"></p>
	  <p>Be sure to logged in using your MediBank google account to have proper access to the appointment calendar.
	    <label for="color"><br>
	      Dr. House:</label>
        <input name="color" type="color" id="color" value="#91181A">
	  </p>
	  <p>Dr. Kutner:
        <input name="color3" type="color" id="color3" value="#0773D5">
	  </p>
	  <p>Nurse Joy:
	    <label for="color2"></label>
        <input name="color2" type="color" id="color2" value="#2DA700">
	  </p>
	</div>
    <div id="ContentRight">
    
    <iframe src="https://www.google.com/calendar/embed?showCalendars=0&amp;height=500&amp;wkst=1&amp;bgcolor=%23ccffff&amp;src=9sglalebhs7h4hsa1ia3r57850%40group.calendar.google.com&amp;color=%23711616&amp;ctz=America%2FLos_Angeles" style=" border:solid 1px #777 " width="600" height="500" frameborder="0" scrolling="no"></iframe></div>
</div>
<div id="Footer"></div>
© Franklin Yu | <a href="Admin.php">Admin</a></div>
</body>
</html>
<?php
mysql_free_result($NurseHome);
?>
