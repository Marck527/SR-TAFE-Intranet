<?php
require_once 'Core/start.php';

$html_head = buildHTMLHead('Register');

if (isset($_POST['btn_register'])) { //if the register button is clicked, collect all data.
	
	$firstname = $_POST['txt_first_name'];
	$lastname  = $_POST['txt_last_name'];
	$email     = $_POST['txt_email'];
	$username  = $_POST['txt_username'];
	$password  = $_POST['txt_password'];

	$privilege = 3;

	$oUserRegister = new cUser($oConn);
	$oUserRegister->register($firstname, $lastname, $email, $username, $password, $privilege);
	$errors = $oUserRegister->getErrors();
	if (!empty($errors)) {
		foreach ($errors as $index => $error) {
			echo <<<HTML
			<div class='container'>
				<div class='alert alert-danger'>
  					<strong>Sorry!</strong> $error 
				</div>
			</div> 
HTML;
			
				
		}
	} else {
		echo "<script>alert('Registered Successfully!')</script>";
	}
}


echo HTMLPage($html_head, $nav_bar, $footer);
function HTMLPage($html_head, $nav_bar, $footer)
{
    return <<<HTML
	
	<!DOCTYPE html>
	
	<!--
	Author: Marck Munoz
	Date: 2016
	-->

	<html lang="en">
    $html_head
	<body>
	<div class="container">
        $nav_bar
		<div class="page-header">
			<h1>Register</h1>
		</div>
		<form method="post">
			<div class="row">
				<div class="col-md-4">	
					<div class="form-group">
						<label for="txt_first_name">First Name*</label>
						<input type="text" class="form-control" name="txt_first_name" id="txt_first_name" placeholder="First Name" required>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="txt_last_name">Last Name*</label>
						<input type="text" class="form-control" name="txt_last_name" id="txt_last_name" placeholder="Last Name" required>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">	
					<div class="form-group">
						<label for="txt_email">Email*</label>
						<input type="email" class="form-control" name="txt_email" id="txt_email" placeholder="Example@example.com" autocomplete="off" required>
					</div>
				</div>
				<div class="col-md-4">
					<div id="email_check_box">
					</div>
				</div>
				
			</div>
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label for="txt_username">Username*</label>
						<input type="text" class="form-control" name="txt_username" id="txt_username" placeholder="Choose Username" autocomplete="off" required>
					</div>	
				</div>
				<div class="col-md-4">
					<div id="username_check_box">
					</div>
				</div>
				
			</div>	
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label for="password1">Password*</label>
						<input type="password" class="form-control" name="txt_password" id="password1" placeholder="Choose Password" required>
					</div>	
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="password2">Repeat Password*</label>
						<input type="password" class="form-control" name="txt_rpt_password" id="password2" placeholder="Repeat Password" required>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<div id="password_match_box"></div>
				</div>
			</div>	
			<button type="submit" class="btn btn-default" name="btn_register" id="btn_register">Register</button>
		</form>
	$footer
	</div>
	</body>
	
	</html>
	
HTML;

}
