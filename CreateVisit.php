<?php @session_start(); ?>
<?php require_once('Connections/medibank.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "NURSE,DOCTOR,ADMIN";
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

$MM_restrictGoTo = "Account.php";
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

// *** Redirect if username exists
$MM_flag="MM_insert";
if (isset($_POST[$MM_flag])) {
  $MM_dupKeyRedirect="Register.php";
  $loginUsername = $_POST['email'];
  $LoginRS__query = sprintf("SELECT Email FROM users WHERE Email=%s", GetSQLValueString($loginUsername, "text"));
  mysql_select_db($database_medibank, $medibank);
  $LoginRS=mysql_query($LoginRS__query, $medibank) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);

  //if there is a row in the database, the username was found - can not add the requested username
  if($loginFoundUser){
    $MM_qsChar = "?";
    //append the username to the redirect page
    if (substr_count($MM_dupKeyRedirect,"?") >=1) $MM_qsChar = "&";
    $MM_dupKeyRedirect = $MM_dupKeyRedirect . $MM_qsChar ."requsername=".$loginUsername;
    header ("Location: $MM_dupKeyRedirect");
    exit;
  }
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "regForm")) {
  $insertSQL = sprintf("INSERT INTO visits (patientID, doctorID, reason, notes, diagnosis, treatment) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['select'], "text"),
                       GetSQLValueString($_POST['docID'], "text"),
                       GetSQLValueString($_POST['reason'], "text"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['diagnosis'], "text"),
                       GetSQLValueString($_POST['treatment'], "text"));

  mysql_select_db($database_medibank, $medibank);
  $Result1 = mysql_query($insertSQL, $medibank) or die(mysql_error());

  $insertGoTo = "CreateVisit.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_medibank, $medibank);
$query_CreateVisit = "SELECT * FROM visits";
$CreateVisit = mysql_query($query_CreateVisit, $medibank) or die(mysql_error());
$row_CreateVisit = mysql_fetch_assoc($CreateVisit);
$totalRows_CreateVisit = mysql_num_rows($CreateVisit);

$colname_currentDoc = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_currentDoc = $_SESSION['MM_Username'];
}
mysql_select_db($database_medibank, $medibank);
$query_currentDoc = sprintf("SELECT * FROM users WHERE Email = %s", GetSQLValueString($colname_currentDoc, "text"));
$currentDoc = mysql_query($query_currentDoc, $medibank) or die(mysql_error());
$row_currentDoc = mysql_fetch_assoc($currentDoc);
$totalRows_currentDoc = mysql_num_rows($currentDoc);

mysql_select_db($database_medibank, $medibank);
$query_allPatients = "SELECT * FROM users WHERE AccountType = 'PATIENT'";
$allPatients = mysql_query($query_allPatients, $medibank) or die(mysql_error());
$row_allPatients = mysql_fetch_assoc($allPatients);
$totalRows_allPatients = mysql_num_rows($allPatients);
?>
<!doctype html>
<html>
<head>
<link href="CSS/Layout.css" rel="stylesheet" type="text/css" />
<link href="CSS/Menu.css" rel="stylesheet" type="text/css" />
<style type="text/css">
label {
	font-family: Arial, Helvetica, sans-serif;
}
</style>
<link href="jQueryAssets/jquery.ui.core.min.css" rel="stylesheet" type="text/css">
<link href="jQueryAssets/jquery.ui.theme.min.css" rel="stylesheet" type="text/css">
<meta charset="UTF-8">
<title>CreateVisit</title>
<script src="jQueryAssets/jquery-1.11.1.min.js" type="text/javascript"></script>
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
      <h1>Create a New Patient Visit Form</h1>
    </div>
    <div id="ContentLeft">
      <h2><img src="assets/visitIcon.gif" width="145" height="217" alt=""/></h2>
    </div>
    <div id="ContentRight">
      <form action="<?php echo $editFormAction; ?>" name="regForm" id="visitForm" method="POST">
        <table width="400" border="0">
          <tbody>
            <tr>
              <td height="40" align="center"><span style="align-self: center; font-family: Arial, Helvetica, sans-serif;"><strong>New Visit Form</strong></span></td>
            </tr>
            <tr>
              <td class="textFieldStyle"><p>Patient
                  <select name="select" class="tableStyle" id="select" form="visitForm">
                    <option value="0">Patient Username</option>
                    <?php
do {  
?>
                    <option value="<?php echo $row_allPatients['Email']?>"><?php echo $row_allPatients['Email']?></option>
                    <?php
} while ($row_allPatients = mysql_fetch_assoc($allPatients));
  $rows = mysql_num_rows($allPatients);
  if($rows > 0) {
      mysql_data_seek($allPatients, 0);
	  $row_allPatients = mysql_fetch_assoc($allPatients);
  }
?>
                  </select>
              </p></td>
            </tr>
            <tr>
              <td><p>Reason For Visit</p>
              <p>
                <textarea name="reason" id="reason"></textarea>
              </p></td>
            </tr>
            <tr>
              <td><p>
                <label for="email">Notes</label>
                </p>
                <p>
                  <textarea name="notes" id="notes"></textarea>
                </p></td>
            </tr>
            <tr>
              <td><p>
                <label for="password">Diagnosis</label>
              </p>
                <p>
                  <textarea name="diagnosis" id="diagnosis"></textarea>
              </p></td>
            </tr>
            <tr>
              <td><p>
                <label for="address">Treatment</label>
              </p>
                <p>
                  <textarea name="treatment" id="treatment"></textarea>
              </p></td>
            </tr>
            <tr>
              <td><p>
                <input name="submit" type="submit" class="buttonStyle" id="submit" value="Submit">
              </p></td>
            </tr>
            
          </tbody>
        </table>
        <input name="docID" type="hidden" id="docID" value="<?php echo $row_currentDoc['UserID']; ?>">
<input type="hidden" name="MM_insert" value="regForm">
      </form>
    </div>
  </div>
  <div id="Footer"></div>
© Franklin Yu | <a href="Admin.php">Admin</a></div>
</body>
</html>
<?php
mysql_free_result($CreateVisit);

mysql_free_result($currentDoc);

mysql_free_result($allPatients);
?>
