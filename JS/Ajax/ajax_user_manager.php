<?php
require_once '../../Lib/Dbconnect.php';

$searched  = $_POST['user_search'];

$search_user =<<<SQL
    SELECT
		U.fldID,
		U.fldFirstName,
        U.fldLastName,
        U.fldUsername,
        U.fldEmail,
        U.fldPassword,
        U.fldFKPrivilegeID,
        UP.fldPrivilegeTitle
	FROM
		tblUser U 
	INNER JOIN
		tblUserPrivilege UP
	ON
		U.fldFKPrivilegeID = UP.fldID
	WHERE
		U.fldUsername LIKE CONCAT('%', :name_search ,'%')
SQL;

$params = [];
$params['name_search'] = $searched;

if (!empty($_POST['user_privilege'])) {
    $search_user.= "AND fldFKPrivilegeID = :user_privilege";
    $params['user_privilege'] = $_POST['user_privilege'];
}

$stmt = $oConn->prepare($search_user);
$stmt->execute($params);

$results = $stmt->fetchAll(PDO::FETCH_OBJ);

$sHTML = "";
if($stmt->rowCount()) {
    $result_count = $stmt->rowCount();

    $sHTML.=<<<HTML
    
    <h4 class="text-muted"><em>$result_count record(s) found.</em></h4>
    <div class="table-responsive">
    <table class="table table-hover">
        <tr>
            <th>Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Privilege</th>
            <th>Action</th>
        </tr>
HTML;

    foreach($results as $row ) {
        $user_id      = $row->fldID;
        $full_name    = $row->fldFirstName . ' ' . $row->fldLastName;
        $username     = $row->fldUsername;
        $email        = $row->fldEmail;
        $privilege    = $row->fldPrivilegeTitle;


        $sHTML.=<<<HTML
        <tr>
            <td>$full_name</td>
            <td>$username</td>
            <td>$email</td>
            <td>$privilege</td>
            <td><a href="UserAddEdit.php?user_id=$user_id">Edit</a></td>
        </tr>
HTML;


    }
    $sHTML.=<<<HTML
        
    </table>
    </div>
HTML;
} else {
    $sHTML.= '<h4 class="text-muted"><em>No records found.</em></h4>';
}

echo $sHTML;