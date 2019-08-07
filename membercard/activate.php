<?php
	require_once("class/dbcontroller.php");
	require_once("class/encrypt_decrypt.php");
	$db_handle = new DBController();
	$en_decrypt = new EnDecryptController();
	if(!empty($_GET["id"])) {
	$encrypted_txt = $en_decrypt->encrypt_decrypt('decrypt', $_GET["id"]);

	$query = "SELECT * FROM membercard_updatelog where eventid = '" . $encrypted_txt . "'";

	$row = $db_handle->runQuery($query);
    $mcid = $row[0]["mc_id"];
    $mcid = str_replace('KK', '', $mcid);
	$mcfirstnamee = $row[0]["mc_firstname_e"];
    $mcsurenamee = $row[0]["mc_lastname_e"];
	$mcbirthdate = $row[0]["mc_birthdate"];
	$mcmobile = $row[0]["mc_mobile"];
	$mcpassport = $row[0]["mc_passport"];
	$mcemail = $row[0]["mc_email"];
	$mcidentification = $row[0]["mc_identification"];
	$mcpassword = $row[0]["mc_password"];

	$query = "UPDATE membercard set mc_firstname_e='".$mcfirstnamee."',mc_lastname_e='".$mcsurenamee."',mc_birthdate=STR_TO_DATE('".$mcbirthdate."', '%Y-%m-%d'),mc_mobile='".$mcmobile."',mc_passport='".$mcpassport."',mc_email='".$mcemail."',mc_identification='".$mcidentification."',mc_activestatus = 'active' , mc_password='".$mcpassword."' WHERE mc_id='KK" . $mcid . "'";

	$result = $db_handle->updateQuery($query);
		if(!empty($result)) {
			$message = "Your account is activated.";
			$type = "success";
		} else {
		    $message = "Problem in account activation.";
		    $type = "error";
		}
	}
?>
<html>
<head>
<title>PHP User Activation</title>
<link href="style.css" type="text/css" rel="stylesheet" />
</head>
<body>
<?php if(isset($message)) { ?>
<div class="message <?php echo $type; ?>"><?php echo $message; ?></div>
<?php } ?>
</body></html>
		