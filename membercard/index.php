<?php
session_start();
if(count($_POST)>0) {
	/* Form Required Field Validation */
	foreach($_POST as $key=>$value) {
	if(empty($_POST[$key])) {
	$message = ucwords($key) . " field is required";
	$type = "error";
	break;
	}
	}
	
	
	if(!isset($message)) {
		require_once("class/dbcontroller.php");
		$db_handle = new DBController();
		$query = "SELECT * FROM membercard where mc_id = 'KK".$_POST["mcid"] . "' and mc_birthdate=STR_TO_DATE('".$_POST["mcbirthdate"]."', '%Y-%m-%d') ";
		$result = $db_handle->runQuery($query);
		if($result > 0) {
			$cardtype = $result[0]['mc_cardtype'];
			$Status = $result[0]['mc_activestatus'];
			$DateExpire = date($result[0]['mc_cardexpire']);
			$date_now = date('Y-m-d');
			if(!$cardtype == 'SUBLIME'){
				if ($date_now > $DateExpire) {
					$message = 'Card is expire'." ".$DateExpire;
					$type = "error";
				}
			}
			else if($Status == "active"){
				$message = "Membercard is active";
				$type = "error";
			}
			else if($Status == "inprocess"){
				$message = "Membercard is inprocess";
				$type = "email";
			}
			else{
				$_SESSION['mcid'] = $_POST["mcid"];
				echo "<script>window.open('updatemembercard.php', '_self')</script>";
			}

		} else {
			$message = "Membercard not found";
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
<form name="frmRegistration" method="post" action="">
  <div class="container center" style="height: 100vh;
   display:table; ">
	
   <div class="row justify-content-md-center align-items-center" style="height: 100vh;">
    <div class="col-md-6" >
      <div class="card">
	  <img src="https://img.freepik.com/free-photo/close-up-black-marble-textured-background_53876-63511.jpg?size=626&ext=jpg" class="card-img-top" alt="...">
      <div class="card-body">
	  <h5 class="card-title">Member Card</h5>
		<div class="form-group">
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text" id="inputGroupPrepend">KK</span>
				</div>
				<input type="text" class="form-control " name="mcid" placeholder="Member Card" value="<?php if(isset($_POST['mcid'])) echo $_POST['mcid']; ?>">
			</div>
		</div>
		<div class="form-group">
      <div class="input-group">
        <div class="input-group-prepend">	
          <span class="input-group-text" id="inputGroupPrepend">DD</span>
        </div>
        <input type="date" class="form-control " name="mcbirthdate" value="<?php if(isset($_POST['mcbirthdate'])) echo $_POST['mcbirthdate']; ?>">
	  </div>
	  </div>
	  <div class="form-group ">
       <input type="submit" name="submit" class="btn btn-sm btn-outline-secondary " value="Check Member" class="btnRegister">
			</div>
			<div class="form-group ">
			<?php if(isset($message)) { ?>
             <div class="alert alert-dark text-center"><?php echo $message; ?></div>
			<?php } ?>
            </div>
			</div>
          </div>
    </div>

  </div>
    </div>        
</form>
</body>
</html>