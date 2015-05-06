<?php @session_start(); ?>
<?php require_once('Connections/medibank.php'); ?>
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

$colname_currentUser = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_currentUser = $_SESSION['MM_Username'];
}
mysql_select_db($database_medibank, $medibank);
$query_currentUser = sprintf("SELECT * FROM users WHERE Email = %s", GetSQLValueString($colname_currentUser, "text"));
$currentUser = mysql_query($query_currentUser, $medibank) or die(mysql_error());
$row_currentUser = mysql_fetch_assoc($currentUser);
$totalRows_currentUser = mysql_num_rows($currentUser);

$maxRows_myVisits = 10;
$pageNum_myVisits = 0;
if (isset($_GET['pageNum_myVisits'])) {
  $pageNum_myVisits = $_GET['pageNum_myVisits'];
}
$startRow_myVisits = $pageNum_myVisits * $maxRows_myVisits;

$colname_myVisits = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_myVisits = $_SESSION['MM_Username'];
}
mysql_select_db($database_medibank, $medibank);
$query_myVisits = sprintf("SELECT * FROM visits WHERE patientID = %s ORDER BY `date` DESC", GetSQLValueString($colname_myVisits, "text"));
$query_limit_myVisits = sprintf("%s LIMIT %d, %d", $query_myVisits, $startRow_myVisits, $maxRows_myVisits);
$myVisits = mysql_query($query_limit_myVisits, $medibank) or die(mysql_error());
$row_myVisits = mysql_fetch_assoc($myVisits);

if (isset($_GET['totalRows_myVisits'])) {
  $totalRows_myVisits = $_GET['totalRows_myVisits'];
} else {
  $all_myVisits = mysql_query($query_myVisits);
  $totalRows_myVisits = mysql_num_rows($all_myVisits);
}
$totalPages_myVisits = ceil($totalRows_myVisits/$maxRows_myVisits)-1;
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
<title>Patients Visit History</title>
<script src="jQueryAssets/jquery-1.11.1.min.js" type="text/javascript"></script>
</head>

<body>
<div id="Holder">
  <div id="Header"></div>
  <div id="NavBar">
    <nav>
      <ul>
        	<li><a href="Account.php">Home</a></li>
            <li><a href="Update.php">My Account</a></li>
            <li><a href="PatientHistory.php">Visits</a></li>
            <li><a href="Contacts.php">Contacts</a></li>
            <li><a href="Logout.php">Logout</a></li>
      </ul>
    </nav>
  </div>
  <div id="Content">
    <div id="PageHeading">
      <h1>Patient Visit History for <?php echo $row_currentUser['FirstName']; ?> <?php echo $row_currentUser['LastName']; ?></h1>
    </div>
    <div id="ContentLeft">
      <h2><img src="assets/visitIcon.gif" width="145" height="217" alt=""/></h2>
    </div>
    <div id="ContentRight">
      <table width="670" border="0" align="center">
        <tbody>
          <tr>
            <td align="center" valign="top"><?php do { ?>
                <form id="updateUserForm" name="updateUserForm" method="POST">
                  <table width="500" border="0" class="tableStyle">
                    <tbody>
                      <tr>
                        <td colspan="6" align="center" style="font-weight: bold">Visit on
                        <input name="date" type="text" id="date" value="<?php echo $row_myVisits['date']; ?>" readonly></td>
                      </tr>
                      <tr>
                        <td align="center"><input name="refresh" type="submit" id="refresh" value="Refresh"></td>
                        <td align="center"><span style="font-weight: bold">Reason</span>:
<textarea name="reason" readonly id="reason"><?php echo $row_myVisits['reason']; ?></textarea></td>
                        <td align="center"><span style="font-weight: bold">Notes</span>:
<textarea name="notes" readonly id="notes"><?php echo $row_myVisits['notes']; ?></textarea></td>
                        <td align="center"><span style="font-weight: bold">Diagnosis</span>:
<textarea name="diag" readonly id="daig"><?php echo $row_myVisits['diagnosis']; ?></textarea></td>
                        <td align="center"><span style="font-weight: bold">Treatment</span>:
                        <textarea name="treat" readonly id="treat"><?php echo $row_myVisits['treatment']; ?></textarea></td>
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
                <?php } while ($row_myVisits = mysql_fetch_assoc($myVisits)); ?></td>
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
mysql_free_result($currentUser);

mysql_free_result($myVisits);
?>
