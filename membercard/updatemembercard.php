<?php
	// Import PHPMailer classes into the global namespace
	// These must be at the top of your script, not inside a function
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require 'src/Exception.php';
	require 'src/PHPMailer.php';
	require 'src/SMTP.php';

	// Load Composer's autoloader
	require 'vendor/autoload.php';

	// Instantiation and passing `true` enables exceptions

	require_once("class/dbcontroller.php");

	require_once("class/encrypt_decrypt.php");

	session_start();
	$type = "";
if(count($_POST)>0) {
	/* Form Required Field Validation */
	foreach($_POST as $key=>$value) {
	if(empty($_POST[$key])) {
		if($key == "mcpassport" || $key == "mcidentification"){

		}
		else  {
			$message = ucwords($key) . " field is required";
			$type = "error";
		}

	break;
	}
	}

	if(!isset($message)) {
	if (!filter_var($_POST["userEmail"], FILTER_VALIDATE_EMAIL)) {
	$message = "Invalid UserEmail";
	$type = "error";
	}
	}

	if(!isset($message)) {
		$mail = new PHPMailer(true);

		//Server settings
		$mail->SMTPDebug = 0;                                       // Enable verbose debug output
		$mail->isSMTP();                                            // Set mailer to use SMTP
		$mail->Host       = 'smtp.gmail.com';  // Specify main and backup SMTP servers
		$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
		$mail->Username   = 'testsendemail.kk@gmail.com';                     // SMTP username
		$mail->Password   = 'sendmailkk19';                               // SMTP password
		$mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
		$mail->Port       = 587;                                    // TCP port to connect to

		//Recipients
		$mail->setFrom('testsendemail.kk@gmail.com', 'Mailer');

		$db_handle = new DBController();
		$query = "SELECT * FROM membercard where mc_email = '" . $_POST["userEmail"] . "' and mc_activestatus ='active'";
		$count = $db_handle->numRows($query);
		if($count==0) {
			$en_decrypt = new EnDecryptController();

			$query = "insert into membercard_updatelog(mc_id,mc_firstname_e,mc_lastname_e,mc_birthdate,mc_mobile,mc_passport,mc_email,mc_identification,update_date,mc_password) 
					values('KK".$_POST["mcid"]."','".$_POST["mcfirstname_e"]."','".$_POST["mcsurename_e"]."',STR_TO_DATE('".$_POST["mcbirthdate"]."', '%Y-%m-%d'),'".$_POST["mcmobile"]."','".$_POST["mcpassport"]."','".$_POST["userEmail"]."','".$_POST["mcidentification"]."',CURDATE(),'".$_POST["mcpassword"]."')";
			$result = $db_handle->insertQuery($query);

			$encrypted_txt = $en_decrypt->encrypt_decrypt('encrypt', $result);
			$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"."?id=" . $encrypted_txt;
			$actual_link =  str_replace('updatemembercard', 'activate', $actual_link);
			$mail->addAddress($_POST["userEmail"], 'User');     // Add a recipient
			try {
			// Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = 'Activation Email';
			$mail->Body    = "<a href='" . $actual_link . "'>Click this link to activate your account. </a>";
			$mail->send();
			/*$message = "Check activation mail is sent to your email.";*/
			} catch (Exception $e) {
			   $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
			}
			if(!empty($result)) {
				$query = "UPDATE membercard set mc_activestatus = 'inprocess' WHERE mc_id='KK" . $_POST["mcid"] . "'";
				$result = $db_handle->updateQuery($query);
				$type = "success";
			} else {
			    $type = "error";
			}
			unset($_POST);
		} else {
			$message = "Email is already in use.";
			$type = "error";
		}
	}
}
?>
<html>
<head>
<title>Member Card</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<link rel="stylesheet" href="css/main.css" type="text/css">
</head>
<body style="
    height: 100vh;
">
<?php
		$db_handle = new DBController();
		$query = "SELECT * FROM membercard where mc_id = 'KK" . ($_SESSION['mcid']) . "'";
		$con = $db_handle->connectDB();
        $result = mysqli_query($con, $query);
		while ($row=mysqli_fetch_array($result)) {

        $mcid = $row[0];
        $mcid = str_replace('KK', '', $mcid);
		$mcfirstnamee = trim($row[3]);
        $mcsurenamee = trim($row[4]);
		$mcbirthdate = trim($row[5]);
		$mcmobile = trim($row[6]);
		$mcpassport = trim($row[7]);
		$mcemail = trim($row[8]);
		$mcidentification = trim($row[9]);

?>

<form name="frmUpdate" method="post" action="">
<?php } ?>

  <div class="container center" style="height: 100vh;
   display:table; ">

   <div class="row justify-content-md-center align-items-center" style="height: 100vh;">
    <div class="col-md-6" >
      <div class="card">
        <div class="card-body">
			<?php  if($type == "success")  { ?>
				<blockquote class="blockquote mb-0">
				<p>Check activation mail is sent to your email..</p>
				<small>You will be redirected in <span id="counter">10</span> second(s).</small>
				<footer class="blockquote-footer">Thank You.</footer>
				</blockquote>
				<script type="text/javascript">
				function countdown() {
				var i = document.getElementById('counter');
				if (parseInt(i.innerHTML)<=1) {
				    location.href = 'index.php';
				}
				i.innerHTML = parseInt(i.innerHTML)-1;
				}
				setInterval(function(){ countdown(); },1000);
				</script>
            <?php } ?>

			<?php if($type == "" || $type == "error" ) { ?>
			<h5 class="card-title">Member Card Update</h5>
			<div class="form-group">
			  <div class="input-group">
				<div class="input-group-prepend">
				  <span class="input-group-text" id="inputGroupPrepend">KK</span>
				</div>
				<input type="text" class="form-control" readonly="true" name="mcid" placeholder="Member Card" value="<?php echo $mcid;?>">

			  </div>
			</div>

			<div class="form-group">
			<label for="exampleFormControlInput1">Firstname <span class="text-danger">*</span></label>
			<input type="text" class="form-control" name="mcfirstname_e" placeholder="Firstname Eng" value="<?php if(isset($mcfirstnamee)) echo $mcfirstnamee;?>" >
			</div>
			<div class="form-group">
			<label for="exampleFormControlInput1">Surnname <span class="text-danger">*</span></label>
			<input type="text" class="form-control" name="mcsurename_e" placeholder="Surnname Eng" value="<?php if(isset($mcsurenamee)) echo $mcsurenamee;?>">
			</div>
			<div class="form-group">
			<label for="exampleFormControlInput1">Birthdate <span class="text-danger">*</span></label>
			<input type="date" class="form-control" name="mcbirthdate"  value="<?php echo $mcbirthdate ?>">
			</div>
			
			<div class="form-group">
			<label for="exampleFormControlInput1">Passport</label>
			<input type="text" class="form-control" name="mcpassport" placeholder="Passport" value="<?php echo $mcpassport?>" >
			</div>
			<div class="form-group">
			<label for="exampleFormControlInput1">Identification</label>
			<input type="text" class="form-control" name="mcidentification" placeholder="Identification" value="<?php if(isset($mcidentification)) { echo $mcidentification;}?>" >
			</div>
			<hr>
			<div class="form-group">
			<label for="exampleFormControlInput1">Mobile <span class="text-danger">*</span></label>
			<input type="text" class="form-control" name="mcmobile" placeholder="Mobile" value="<?php echo $mcmobile?>">
			</div>
			<div class="form-group">
			<label for="exampleFormControlInput1">Email <span class="text-danger">*</span></label>
			<input type="text" class="form-control" name="userEmail" placeholder="Email" value="<?php echo $mcemail?>" >
			</div>
			<hr>
			<div class="form-group">
			<label for="exampleFormControlInput1">Password <span class="text-danger">*</span></label>
			<input type="password" class="form-control" name="mcpassword" placeholder="Password" value="" >
			</div>
			 <div class="form-group">
			<div class="form-check">
			  <input class="form-check-input" type="checkbox" id="gridCheck">
			  <label class="form-check-label" for="gridCheck">
			    Check me out
			  </label>
			</div>
			</div>
			<div class="form-group">
			<div class="form-check">
			  <input class="form-check-input" type="checkbox" id="gridCheck">
			  <label class="form-check-label" for="gridCheck">
			    Check me out
			  </label>
			</div>
			</div>
			<div class="form-group ">
       			<input type="submit" name="submit" class="btn btn-sm btn-outline-secondary " value="Update" class="btnUpdate">
			</div>
			<div class="form-group ">
			<?php if(isset($message)) { ?>
             <div class="alert alert-dark text-center"><?php echo $message; ?></div>
             </div>
        <?php } ?>

			</div>

			<?php } ?>

          </div>
    </div>

  </div>
    </div>
</form>
</body>
</html>
