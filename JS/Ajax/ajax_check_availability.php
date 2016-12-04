<?php
require_once '../../Lib/Dbconnect.php';


if (isset($_POST['username']) && strlen($_POST['username']) > 3) {
	$username = $_POST['username'];

	$check_username_availability =<<<SQL
	CALL searchUsername(:username);
SQL;

	$stmt = $oConn->prepare($check_username_availability);
	$stmt->execute([
		'username' => $username
	]);

	$result = $stmt->fetch(PDO::FETCH_OBJ);

	if ($stmt->rowCount()) {
		$username = $result->fldUsername;
		$sHTML =  "<p class='text-danger'><strong>{$username}</strong> has already been taken ✘</p>";
	} else {
		$sHTML =  "<p class='text-success'><strong>That username is available ✔</strong></p>";
	}

	echo $sHTML;

}elseif(isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) { //If the post email is set and the email has a valid regex, check it against the database.
	$email = $_POST['email'];

	$check_email_availability =<<<SQL
	CALL searchEmail(:email);
SQL;

	$stmt_email = $oConn->prepare($check_email_availability);
	$stmt_email->execute([
		'email' => $email
	]);

	$result_email = $stmt_email->fetch(PDO::FETCH_OBJ);

	if ($stmt_email->rowCount()) {
		$email = $result_email->fldEmail;

		$sHTML = "<p class='text-danger'><strong>{$email}</strong> is already being used ✘</p>";
	} else {
		$sHTML = "<p class='text-success'><strong>That email is available ✔</strong></p>";
	}

	echo $sHTML;
}

