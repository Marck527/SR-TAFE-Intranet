<?php
require_once 'Core/start.php';
require_once 'Lib/cDropdown.php';

checkIfHaveAccess('admin');
$html_head = buildHTMLHead('Register');

isset($_GET['user_id']) ? $user_id = $_GET['user_id'] : $user_id = null; //Ternary if statement

$oUserAddUpdate = new cUser($oConn); //



if (isset($_POST['btn_add_update']) && empty($user_id)) { //Add mode

    $firstname = $_POST['txt_first_name'];
    $lastname = $_POST['txt_last_name'];
    $email = $_POST['txt_email'];
    $username = $_POST['txt_username'];
    $password = $_POST['txt_password'];
    $privilege = $_POST['user_privilege'];

    if(adminCounter($oConn) == 2 && $privilege == '1') {
        echo <<<HTML
        <div class="alert alert-danger">
            <strong>Error!</strong> You cannot add more than 2 admins
        </div>
HTML;

    } else {
        $oUserAddUpdate->register($firstname, $lastname, $email, $username, $password, $privilege);
        $errors = $oUserAddUpdate->getErrors();
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
        }
    }

}

if(isset($_POST['btn_add_update']) && !empty($user_id)) { //Update mode
    $firstname = $_POST['txt_first_name'];
    $lastname  = $_POST['txt_last_name'];
    $email     = $_POST['txt_email'];
    $username  = $_POST['txt_username'];
    $password  = $_POST['txt_password'];
    $privilege = $_POST['user_privilege'];

    if (adminCounter($oConn) == 2 && $privilege == '1'){ //If there are 2 admins already and the selected value in the privilege dropdown is admin
        echo <<<HTML
        <div class="alert alert-danger">
            <strong>Error!</strong> You cannot add more than 2 admins
        </div>
HTML;
    } else {
        if (!empty($password)) {
            $oUserAddUpdate->update($firstname, $lastname, $email, $username, $password, $privilege, $user_id);
        } else {
            $oUserAddUpdate->update2($firstname, $lastname, $email, $username, $privilege, $user_id);
        }

    }


}

//Delete functionality
if (isset($_POST['btn_delete'])) {
    $oUserAddUpdate->deleteUser($user_id);

    redirectTo('UserManager'); //Redirect to user manager page after user deletion.
}
//

echo HTMLPage($html_head, $nav_bar, $footer, $user_id, $oConn);
function HTMLPage($html_head, $nav_bar, $footer, $user_id, $oConn)
{
    if (!empty($user_id)) {
        $get_user =<<<SQL
        CALL searchUserID(:user_id);
SQL;
        $stmt = $oConn->prepare($get_user);
        $stmt->execute([
            'user_id' => $user_id
        ]);

        $result = $stmt->fetch(PDO::FETCH_OBJ);

        $first_name   = $result->fldFirstName;
        $last_name    = $result->fldLastName;
        $email        = $result->fldEmail;
        $username     = $result->fldUsername;
        $privilege_id = $result->fldFKPrivilegeID;

        $stmt->closeCursor();
    }

    isset($privilege_id) ? $default_id = $privilege_id : $default_id = 2;

    isset($first_name) ? $first_name : $first_name = null;
    isset($last_name) ? $last_name : $last_name = null;
    isset($email) ? $email : $email = null;
    isset($username) ? $username : $username = null;

    //User privilege dropdown initialization
    $oDropdownPrivilege = new cDropdown($oConn, "SELECT fldID, fldPrivilegeTitle FROM tblUserPrivilege");
    $oDropdownPrivilege->setHTMLID('user_privilege');
    $oDropdownPrivilege->setSHTMLClass('form-control');
    $oDropdownPrivilege->setName('user_privilege');
    $oDropdownPrivilege->setIDField('fldID');
    $oDropdownPrivilege->setDescriptionField('fldPrivilegeTitle');
    $oDropdownPrivilege->setDefaultID($default_id);
    $sDropdownPrivilege = $oDropdownPrivilege->HTML();
//

    if (!$user_id) {
        $req = 'required';
    } else {
        $req = null;
    }

    $sHTML = "";
    $sHTML.=<<<HTML
	
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
			<h1>Add / Update</h1>
		</div>
		<form  method="post">
			<div class="row">
				<div class="col-md-4">	
					<div class="form-group">
						<label for="txt_first_name">First Name*</label>
						<input type="text" class="form-control" name="txt_first_name" id="txt_first_name" placeholder="First Name" value="$first_name" required>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="txt_last_name">Last Name*</label>
						<input type="text" class="form-control" name="txt_last_name" id="txt_last_name" placeholder="Last Name" value="$last_name" required>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">	
					<div class="form-group">
						<label for="txt_email">Email*</label>
						<input type="email" class="form-control" name="txt_email" id="txt_email" placeholder="Example@example.com" autocomplete="off" value="$email" required>
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
						<input type="text" class="form-control" name="txt_username" id="txt_username" placeholder="Choose Username" autocomplete="off" value="$username" required>
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
						<input type="password" class="form-control" name="txt_password" id="password1" placeholder="Choose Password" $req>
					</div>	
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="password2">Repeat Password*</label>
						<input type="password" class="form-control" name="txt_rpt_password" id="password2" placeholder="Repeat Password" $req>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<div id="password_match_box"></div>
				</div>
			</div>
HTML;
if ($_SESSION['user_id'] != $user_id) { //If the admin logged in viewing their profile, don't show the privilge dropdown.
    $sHTML .=<<<HTML
    <div class="row">
	    <div class="col-md-2">	
		    <div class="form-group">
				<label for="txt_email">Privilege*</label>
				$sDropdownPrivilege
			</div>
		</div>
	</div>
HTML;
}

$sHTML .=<<<HTML
	<button type="submit" class="btn btn-default" name="btn_add_update" id="btn_add_update">Add / Update</button>
    </form>
HTML;

    if ($user_id && $_SESSION['user_id'] != $user_id) { //If there is a user id, it means it's in edit mode, add the delete button. Don't show if the currently logged in user is viewing their profile.
        $sHTML .=<<<HTML
        <br>
        <form id="delete_user_confirm" method="post">
            <button type="submit" class="btn btn-danger" name="btn_delete" id="btn_delete">Delete User</button>
        </form>
HTML;
    }
    $sHTML .=<<<HTML
    $footer
	</div>
	<script>
	$(document).ready(function(){
	    
	    $('#delete_user_confirm').submit(function(){
            var c = confirm("Deleting this user will also delete all documents created by this user, are you sure you want to continue? This cannot be undone.");
            return c;
        });
        
	});
    </script>
	</body>
	</html>
HTML;

    return $sHTML;
}
