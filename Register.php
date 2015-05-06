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
  $insertSQL = sprintf("INSERT INTO users (FirstName, LastName, DOB, Email, Password, Address, City, ZipCode, phoneNum, SSN, Provider, PolicyNum, GroupNum) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['firstName'], "text"),
                       GetSQLValueString($_POST['lastName'], "text"),
                       GetSQLValueString($_POST['dob'], "date"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['zipCode'], "int"),
                       GetSQLValueString($_POST['phoneNum'], "int"),
                       GetSQLValueString($_POST['ssn'], "int"),
                       GetSQLValueString($_POST['provider'], "text"),
                       GetSQLValueString($_POST['policyNum'], "int"),
                       GetSQLValueString($_POST['groupNum'], "int"));

  mysql_select_db($database_medibank, $medibank);
  $Result1 = mysql_query($insertSQL, $medibank) or die(mysql_error());

  $insertGoTo = "Login.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_medibank, $medibank);
$query_Register = "SELECT * FROM users";
$Register = mysql_query($query_Register, $medibank) or die(mysql_error());
$row_Register = mysql_fetch_assoc($Register);
$totalRows_Register = mysql_num_rows($Register);
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
<meta charset="UTF-8">
<title>Register</title>
</head>

<body>
<div id="Holder">
  <div id="Header"></div>
  <div id="NavBar">
    <nav>
      <ul>
        <li><a href="Login.php">Login</a></li>
        <li><a href="Register.php">Register</a></li>
      </ul>
    </nav>
  </div>
  <div id="Content">
    <div id="PageHeading">
      <h1>Account Registration</h1>
    </div>
    <div id="ContentLeft">
      <h2>Create an Account</h2>
      <h6>&nbsp;</h6>
      <h6><img src="assets/updateIcon.gif" width="196" height="175" alt=""/></h6>
    </div>
    <div id="ContentRight">
      <form action="<?php echo $editFormAction; ?>" name="regForm" id="regForm" method="POST">
        <table width="400" border="0">
          <tbody>
            <tr>
              <td height="40" align="center"><span style="align-self:center"><strong>Personal Information</strong></span></td>
            </tr>
            <tr>
              <td align="left"><table border="0">
                  <tbody>
                    <tr>
                      <td align="left" class="StyleTxtField"><label for="firstName">First Name</label>
                        <input name="firstName" type="text" class="textFieldStyle" id="firstName" style="width:250px" /></td>
                      <td class="StyleTxtField"><label for="lastName">Last Name</label>
                        <input name="lastName" type="text" required class="textFieldStyle" id="lastName" style="width:250px" /></td>
                    </tr>
                  </tbody>
                </table></td>
            </tr>
            <tr>
              <td><p>
                <label for="email">Email</label>
                </p>
                <p>
                  <input name="email" type="email" required class="textFieldStyle" id="email" style="width:250px">
                </p></td>
            </tr>
            <tr>
              <td><p>
                <label for="password">Password</label>
              </p>
                <p>
                  <input name="password" type="password" class="textFieldStyle" id="password" style="width:250px">
                </p>                <p>&nbsp;</p></td>
            </tr>
            <tr>
              <td><label for="sex">Sex:</label>
                <select name="sex" required id="sex">
                  <option value="M">Male</option>
                  <option value="F">Female</option>
              </select></td>
            </tr>
            <tr>
              <td><p>Date of Birth</p>
              <p>
                <input name="dob" type="date" required id="dob">
              </p></td>
            </tr>
            <tr>
              <td><p>
                  <label for="address">Address</label>
                </p>
                <p>
                  <input name="address" type="text" required class="textFieldStyle" id="address" style="width:250px" />
                </p></td>
            </tr>
            <tr>
              <td><p>
                  <label for="city">City</label>
                </p>
                <p>
                  <input class="textFieldStyle" name="city" type="text" id="city" style="width:250px" />
                </p></td>
            </tr>
            <tr>
              <td colspan="2"><p>
                  <label for="zipCode">Zip Code</label>
                </p>
                <p>
                  <input class="textFieldStyle" name="zipCode" type="text" id="zipCode" style="width:250px" />
                </p></td>
            </tr>
            <tr>
              <td><p>
                <label for="phoneNum">Phone </label>
                </p>
                <p>
                  <input name="phoneNum" type="tel" class="textFieldStyle" id="phoneNum" style="width:250px">
              </p></td>
            </tr>
            <tr>
              <td height="40" align="center"><span style="align-self:center"><strong>Insurance Information</strong></span></td>
            </tr>
            <tr>
              <td><p>
                  <label for="ssn">Social Security No.</label>
                </p>
                <p>
                  <input name="ssn" type="text" class="textFieldStyle" id="ssn" style="width:250px" />
                </p></td>
            </tr>
            <tr>
              <td><p>
                  <label for="provider">Provider</label>
                </p>
                <p>
                  <input name="provider" type="text" class="textFieldStyle" id="provider" style="width:250px" />
                </p></td>
            </tr>
            <tr>
              <td><p>
                  <label for="policyNum">Policy No.</label>
                </p>
                <p>
                  <input name="policyNum" type="text" class="textFieldStyle" id="policyNum" style="width:250px" />
                </p></td>
            </tr>
            <tr>
              <td><p>
                  <label for="groupNum">Group No.</label>
                </p>
                <p>
                  <input name="groupNum" type="text" class="textFieldStyle" id="groupNum" style="width:250px" />
                </p></td>
            </tr>
            <tr>
              <td height="35" align="center"><p>
                  <input name="submit" type="submit" class="textFieldStyle" id="submit" value="Register">
                </p></td>
            </tr>
          </tbody>
        </table>
        <input type="hidden" name="MM_insert" value="regForm">
      </form>
    </div>
  </div>
  <div id="Footer"></div>
© Franklin Yu | <a href="Admin.php">Admin</a></div>
</body>
</html>
<?php
mysql_free_result($Register);
?>
