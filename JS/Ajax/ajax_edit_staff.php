<?php
require_once '../../Lib/Dbconnect.php';

$i_staff_id = $_POST['staff_id'];

$populate_staff =<<<SQL
  SELECT
    fldID,
	fldFirstName,
	fldLastName,
    fldBiography,
    fldPhoto
  FROM
	tblStaff
  WHERE
    fldID = :staff_id;
SQL;

$stmt = $oConn->prepare($populate_staff);
$stmt->execute([
    'staff_id' => $i_staff_id
]);

$result = $stmt->fetchAll(PDO::FETCH_OBJ);

echo json_encode($result);
