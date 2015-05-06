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

$MM_restrictGoTo = "ViewPatients.php";
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
<?php require_once('Connections/medibank.php'); ?>
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

$colname_currentDoc = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_currentDoc = $_SESSION['MM_Username'];
}
mysql_select_db($database_medibank, $medibank);
$query_currentDoc = sprintf("SELECT * FROM users WHERE Email = %s", GetSQLValueString($colname_currentDoc, "text"));
$currentDoc = mysql_query($query_currentDoc, $medibank) or die(mysql_error());
$row_currentDoc = mysql_fetch_assoc($currentDoc);
$totalRows_currentDoc = mysql_num_rows($currentDoc);

$maxRows_assignedVisits = 10;
$pageNum_assignedVisits = 0;
if (isset($_GET['pageNum_assignedVisits'])) {
  $pageNum_assignedVisits = $_GET['pageNum_assignedVisits'];
}
$startRow_assignedVisits = $pageNum_assignedVisits * $maxRows_assignedVisits;

$colname_assignedVisits = "-1";
if (isset($_POST['docID'])) {
  $colname_assignedVisits = $_POST['docID'];
}
mysql_select_db($database_medibank, $medibank);
$query_assignedVisits = sprintf("SELECT * FROM visits WHERE doctorID = %s ORDER BY `date` DESC", GetSQLValueString($colname_assignedVisits, "int"));
$query_limit_assignedVisits = sprintf("%s LIMIT %d, %d", $query_assignedVisits, $startRow_assignedVisits, $maxRows_assignedVisits);
$assignedVisits = mysql_query($query_limit_assignedVisits, $medibank) or die(mysql_error());
$row_assignedVisits = mysql_fetch_assoc($assignedVisits);

if (isset($_GET['totalRows_assignedVisits'])) {
  $totalRows_assignedVisits = $_GET['totalRows_assignedVisits'];
} else {
  $all_assignedVisits = mysql_query($query_assignedVisits);
  $totalRows_assignedVisits = mysql_num_rows($all_assignedVisits);
}
$totalPages_assignedVisits = ceil($totalRows_assignedVisits/$maxRows_assignedVisits)-1;
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
<title>Patients</title>
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
      <h1>Patient Visit History</h1>
    </div>
    <div id="ContentLeft">
      <h2><img src="assets/visitIcon.gif" width="145" height="217" alt=""/></h2>
    </div>
    <div id="ContentRight">
    <?php $docID = $_POST[$row_currentDoc['UserID']];?>
      <table width="670" border="0" align="center">
        <tbody>
          <tr>
            <td align="center" valign="top"><?php do { ?>
                <form id="updateUserForm" name="updateUserForm" method="POST">
                  <table width="500" border="0" class="tableStyle">
                    <tbody>
                      <tr>
                        <td colspan="6" align="center" style="font-weight: bold">Patient:
                          <input name="textfield" type="text" id="textfield" value="<?php echo $row_assignedVisits['patientID']; ?>">
                        on
                        <input name="textfield2" type="text" id="textfield2" value="<?php echo $row_assignedVisits['date']; ?>"></td>
                      </tr>
                      <tr>
                        <td align="center"><input name="visitID" type="hidden" id="visitID" value="<?php echo $row_assignedVisits['patientID']; ?>">
                          <input type="submit" name="refresh" id="refresh" value="Refresh">
                        <input name="docID" type="hidden" id="docID" value="<?php echo $row_currentDoc['UserID']; ?>"></td>
                        <td align="center"><span style="font-weight: bold">Reason</span>:
<textarea name="reason" readonly id="reason"><?php echo $row_assignedVisits['reason']; ?></textarea></td>
                        <td align="center"><span style="font-weight: bold">Notes</span>:
<textarea name="notes" readonly id="notes"><?php echo $row_assignedVisits['notes']; ?></textarea></td>
                        <td align="center"><span style="font-weight: bold">Diagnosis</span>:
<textarea name="diag" readonly id="daig"><?php echo $row_assignedVisits['diagnosis']; ?></textarea></td>
                        <td align="center"><span style="font-weight: bold">Treatment</span>:
                        <textarea name="treat" readonly id="treat"><?php echo $row_assignedVisits['treatment']; ?></textarea></td>
                        <td align="center">&nbsp;</td>
                      </tr>
                      <tr>
                        <td colspan="6" align="center">&nbsp;</td>
                      </tr>
                      <tr>
                        <td colspan="6" align="center">&nbsp;</td>
                      </tr>
                      <tr>
                        <td colspan="6"></td>
                      </tr>
                    </tbody>
                  </table>
                </form>
                <?php } while ($row_assignedVisits = mysql_fetch_assoc($assignedVisits)); ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div id="Footer"></div>
© Franklin Yu | <a href="Admin.php">Admin</a></div>
</body>
</html>
<?php
mysql_free_result($currentDoc);

mysql_free_result($assignedVisits);
?>
