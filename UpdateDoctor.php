<?php @session_start(); ?>
<?php require_once('Connections/medibank.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "PATIENT,NURSE,DOCTOR,ADMIN";
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

$MM_restrictGoTo = "Login.php";
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "updateForm")) {
  $updateSQL = sprintf("UPDATE users SET Email=%s, Password=%s, Address=%s, City=%s, ZipCode=%s, phoneNum=%s WHERE UserID=%s",
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['zipcode'], "int"),
                       GetSQLValueString($_POST['tel'], "int"),
                       GetSQLValueString($_POST['UserIDHiddenField'], "int"));

  mysql_select_db($database_medibank, $medibank);
  $Result1 = mysql_query($updateSQL, $medibank) or die(mysql_error());

  $updateGoTo = "UpdateDoctor.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_User = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_User = $_SESSION['MM_Username'];
}
mysql_select_db($database_medibank, $medibank);
$query_User = sprintf("SELECT * FROM users WHERE Email = %s", GetSQLValueString($colname_User, "text"));
$User = mysql_query($query_User, $medibank) or die(mysql_error());
$row_User = mysql_fetch_assoc($User);
$totalRows_User = mysql_num_rows($User);
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
        	<li><a href="Doctor.php">Home</a></li>
            <li><a href="UpdateDoctor.php">My Account</a></li>
          <li><a href="ViewPatients.php">Patients</a></li>
            <li><a href="CreateVisit.php">New Visit</a></li>
            <li><a href="Logout.php">Logout</a></li>
        </ul>
    </nav>
</div>
<div id="Content">
	<div id="PageHeading">
	  <h1>Update Account</h1>
	</div>
	<div id="ContentLeft">
	  <h2>Update Your Personal Information</h2>
	  <p><img src="assets/updateIcon.gif" width="196" height="175" alt=""/></p>
	  <h6>&nbsp;</h6>
	  <h6>&nbsp;</h6>
	</div>
    <div id="ContentRight">
    <form action="<?php echo $editFormAction; ?>" id="updateForm" name="updateForm" method="POST">
      <table width="600" border="0">
        <tbody>
          <tr>
            <td align="center"><strong>Account:<?php echo $row_User['FirstName']; ?> <?php echo $row_User['LastName']; ?></strong></td>
          </tr>
        </tbody>
      </table>
      <table width="400" border="0" align="center">
        <tbody>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><p>
              <label for="email">Email</label>
            </p>
              <p>
                <input name="email" type="email" class="textFieldStyle" id="email" value="<?php echo $row_User['Email']; ?>">
            </p></td>
          </tr>
          <tr>
            <td><p>
              <label for="password">Password</label>
            </p>
              <p>
                <input name="password" type="password" class="textFieldStyle" id="password" value="<?php echo $row_User['Password']; ?>">
            </p></td>
          </tr>
          <tr>
            <td><p>
              <label for="address">Address</label>
              </p>
              <p>
                <input name="address" type="text" class="textFieldStyle" id="address" value="<?php echo $row_User['Address']; ?>">
            </p></td>
          </tr>
          <tr>
            <td><p>
              <label for="city">City</label>
            </p>
              <p>
                <input name="city" type="text" class="textFieldStyle" id="city" value="<?php echo $row_User['City']; ?>">
            </p></td>
          </tr>
          <tr>
            <td><p>
              <label for="zipcode">Zip Code</label>
              </p>
              <p>
                <input name="zipcode" type="text" class="textFieldStyle" id="zipcode" value="<?php echo $row_User['ZipCode']; ?>">
            </p></td>
          </tr>
          <tr>
            <td><p>
              <label for="tel">Phone</label>
            </p>
              <p>
                <input name="tel" type="tel" class="textFieldStyle" id="tel" value="<?php echo $row_User['phoneNum']; ?>">
            </p></td>
          </tr>
          <tr>
            <td><input name="UserIDHiddenField" type="hidden" id="UserIDHiddenField" value="<?php echo $row_User['UserID']; ?>"></td>
          </tr>
          <tr>
            <td><input name="update" type="submit" class="buttonStyle" id="update" value="Update Account"></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
        </tbody>
      </table>
      <input type="hidden" name="MM_update" value="updateForm">
      </form>
    </div>
    
    
</div>
<div id="Footer"></div>
© Franklin Yu | <a href="Admin.php">Admin</a></div>
</body>
</html>
<?php
mysql_free_result($User);
?>
