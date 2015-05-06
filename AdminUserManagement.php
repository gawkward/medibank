<?php require_once('Connections/medibank.php'); ?>
<?php require_once('Connections/medibank.php'); ?>
<?php @session_start(); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "ADMIN";
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

$currentPage = $_SERVER["PHP_SELF"];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "updateUserForm")) {
  $updateSQL = sprintf("UPDATE users SET AccountType=%s, FirstName=%s, LastName=%s, sex=%s, DOB=%s, Email=%s, Password=%s, Address=%s, City=%s, ZipCode=%s, phoneNum=%s, SSN=%s, Provider=%s, PolicyNum=%s, GroupNum=%s WHERE UserID=%s",
                       GetSQLValueString($_POST['accountType'], "text"),
                       GetSQLValueString($_POST['fName'], "text"),
                       GetSQLValueString($_POST['lName'], "text"),
                       GetSQLValueString($_POST['sex'], "text"),
                       GetSQLValueString($_POST['dob'], "date"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['zipcode'], "int"),
                       GetSQLValueString($_POST['phone'], "int"),
                       GetSQLValueString($_POST['ssn'], "int"),
                       GetSQLValueString($_POST['provider'], "text"),
                       GetSQLValueString($_POST['policyNum'], "int"),
                       GetSQLValueString($_POST['groupNum'], "int"),
                       GetSQLValueString($_POST['updateUserHiddenID'], "int"));

  mysql_select_db($database_medibank, $medibank);
  $Result1 = mysql_query($updateSQL, $medibank) or die(mysql_error());

  $updateGoTo = "AdminUserManagement.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$maxRows_ManageUsers = 10;
$pageNum_ManageUsers = 0;
if (isset($_GET['pageNum_ManageUsers'])) {
  $pageNum_ManageUsers = $_GET['pageNum_ManageUsers'];
}
$startRow_ManageUsers = $pageNum_ManageUsers * $maxRows_ManageUsers;

mysql_select_db($database_medibank, $medibank);
$query_ManageUsers = "SELECT * FROM users ORDER BY `TimeStamp` DESC";
$query_limit_ManageUsers = sprintf("%s LIMIT %d, %d", $query_ManageUsers, $startRow_ManageUsers, $maxRows_ManageUsers);
$ManageUsers = mysql_query($query_limit_ManageUsers, $medibank) or die(mysql_error());
$row_ManageUsers = mysql_fetch_assoc($ManageUsers);

if (isset($_GET['totalRows_ManageUsers'])) {
  $totalRows_ManageUsers = $_GET['totalRows_ManageUsers'];
} else {
  $all_ManageUsers = mysql_query($query_ManageUsers);
  $totalRows_ManageUsers = mysql_num_rows($all_ManageUsers);
}
$totalPages_ManageUsers = ceil($totalRows_ManageUsers/$maxRows_ManageUsers)-1;

$queryString_ManageUsers = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_ManageUsers") == false && 
        stristr($param, "totalRows_ManageUsers") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_ManageUsers = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_ManageUsers = sprintf("&totalRows_ManageUsers=%d%s", $totalRows_ManageUsers, $queryString_ManageUsers);
?>
<!doctype html>
<html>
<head>
<link href="CSS/Layout.css" rel="stylesheet" type="text/css" />
<link href="CSS/Menu.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jQuery.Validate/1.6/jQuery.Validate.min.js"></script>
<meta charset="UTF-8">
<title>Medi | Bank</title>
</head>

<body>
<div id="Holder">
  <div id="Header"></div>
  <div id="NavBar">
    <nav>
      <ul>
        	<li><a href="Admin.php">Home</a></li>
            <li><a href="AdminUserManagement.php">User Management</a></li>
            <li><a href="Logout.php">Logout</a></li>
      </ul>
    </nav>
  </div>
  <div id="Content">
    <div id="PageHeading">
      <h1>Admin Control Panel</h1>
    </div>
    <div id="ContentLeft">
      <h2>MediBank Admin Portal</h2>
	  <p>
	    <img name="imageField" type="image" id="imageField" src="assets/adminIcon.gif">
	  </p>
    </div>
    <div id="ContentRight">
      <table width="670" border="0" align="center">
        <tbody>
          <tr>
            <td align="right" valign="top">&nbsp;Showing <?php echo ($startRow_ManageUsers + 1) ?> to <?php echo min($startRow_ManageUsers + $maxRows_ManageUsers, $totalRows_ManageUsers) ?> of <?php echo $totalRows_ManageUsers ?></td>
          </tr>
          <tr>
            <td align="center" valign="top"><?php if ($totalRows_ManageUsers > 0) { // Show if recordset not empty ?>
                <?php do { ?><form action="<?php echo $editFormAction; ?>" id="updateUserForm" name="updateUserForm" method="POST">
                    <table width="500" border="0" class="tableStyle">
                      <tbody>
                        <tr>
                          <td><label for="fName">First Name:</label></td>
                          <td><input name="fName" type="text" id="fName" value="<?php echo $row_ManageUsers['FirstName']; ?>"></td>
                          <td><label for="lName">Last Name:</label></td>
                          <td><input name="lName" type="text" id="lName" value="<?php echo $row_ManageUsers['LastName']; ?>"></td>
                        </tr>
                        <tr>
                          <td><label for="email">Email:</label></td>
                          <td><input name="email" type="email" id="email" value="<?php echo $row_ManageUsers['Email']; ?>"></td>
                          <td><label for="password2">Password:</label></td>
                          <td><input name="password" type="password" id="password2" value="<?php echo $row_ManageUsers['Password']; ?>"></td>
                        </tr>
                        <tr>
                          <td>Sex</td>
                          <td><input name="sex" type="text" id="sex" value="<?php echo $row_ManageUsers['sex']; ?>"></td>
                          <td>DOB</td>
                          <td><input name="dob" type="text" id="dob" value="<?php echo $row_ManageUsers['DOB']; ?>"></td>
                        </tr>
                        <tr>
                          <td><label for="accountType">Account Type:</label></td>
                          <td><input name="accountType" type="text" id="accountType" value="<?php echo $row_ManageUsers['AccountType']; ?>"></td>
                          <td><label for="ssn2">SSN:</label></td>
                          <td><input name="ssn" type="text" id="ssn2" value="<?php echo $row_ManageUsers['SSN']; ?>"></td>
                        </tr>
                        <tr>
                          <td><label for="address">Address:</label></td>
                          <td><input name="address" type="text" id="address" value="<?php echo $row_ManageUsers['Address']; ?>"></td>
                          <td><label for="city2">City:</label></td>
                          <td><input name="city" type="text" id="city2" value="<?php echo $row_ManageUsers['City']; ?>"></td>
                        </tr>
                        <tr>
                          <td><label for="zipcode">Zip Code:</label></td>
                          <td><input name="zipcode" type="number" id="zipcode" value="<?php echo $row_ManageUsers['ZipCode']; ?>"></td>
                          <td><label for="phone2">Phone #:</label></td>
                          <td><input name="phone" type="number" id="phone2" value="<?php echo $row_ManageUsers['phoneNum']; ?>"></td>
                        </tr>
                        <tr>
                          <td><label for="provider2">Provider:</label></td>
                          <td colspan="3"><input name="provider2" type="text" id="provider" value="<?php echo $row_ManageUsers['Provider']; ?>"></td>
                        </tr>
                        <tr>
                          <td><label for="policyNum">Policy #:</label></td>
                          <td><input name="policyNum" type="number" id="policyNum" value="<?php echo $row_ManageUsers['PolicyNum']; ?>"></td>
                          <td><label for="groupNum2">Group #:</label></td>
                          <td><input name="groupNum" type="number" id="groupNum2" value="<?php echo $row_ManageUsers['GroupNum']; ?>"></td>
                        </tr>
                        <tr>
                          <td colspan="4" align="center"><input name="updateUserHiddenID" type="hidden" id="updateUserHiddenID" value="<?php echo $row_ManageUsers['UserID']; ?>">
                          <input name="submit" type="submit" class="buttonStyle" id="submit" value="Update Account"></td>
                        </tr>
                        <tr>
                          <td colspan="4">&nbsp;</td>
                        </tr>
                        <tr>
                          <td colspan="4"></td>
                        </tr>
                      </tbody>
                    </table>
                    <input type="hidden" name="MM_update" value="updateUserForm">
                </form>
                  <?php } while ($row_ManageUsers = mysql_fetch_assoc($ManageUsers)); ?>
            <?php } // Show if recordset not empty ?></td>
          </tr>
          <tr>
            <td align="right" valign="top"><?php if ($pageNum_ManageUsers < $totalPages_ManageUsers) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_ManageUsers=%d%s", $currentPage, min($totalPages_ManageUsers, $pageNum_ManageUsers + 1), $queryString_ManageUsers); ?>">Next</a>
                <?php } // Show if not last page ?>
              <?php if ($pageNum_ManageUsers > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_ManageUsers=%d%s", $currentPage, max(0, $pageNum_ManageUsers - 1), $queryString_ManageUsers); ?>">Previous</a>
                <?php } // Show if not first page ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  © Franklin Yu | <a href="Admin.php">Admin</a>
  <div id="Footer"></div>
</div>
</body>
</html>
<?php
mysql_free_result($ManageUsers);
?>
