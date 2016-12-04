<?php
/**
 * Created by PhpStorm.
 * User: Student
 * Date: 13/09/2016
 * Time: 12:58 PM
 */
require_once '../../Lib/Dbconnect.php';

$last_incident =<<<SQL
    SELECT 
	  datediff(Now(), MAX(fldIncidentDate)) AS fldDaysSinceLastIncident
    FROM
	  tblWHSIncident;

SQL;

$stmt_last_incident = $oConn->prepare($last_incident);
$stmt_last_incident->execute();

$result = $stmt_last_incident->fetch(PDO::FETCH_OBJ);

$days_since_incident = $result->fldDaysSinceLastIncident;

if ($days_since_incident > 0) {
    echo "<h4 class='text-center'>Days since the last report: ", "<span class=\"label label-danger\">$days_since_incident</span></h3>";
}


