<?php
/**
 * Created by PhpStorm.
 * User: Marck
 * Date: 13/09/2016
 * Time: 9:04 PM
 */
require_once '../../Lib/Dbconnect.php';

$block_id = $_GET['block_id'];

$sql_block =<<<SQL
SELECT
COUNT(*) AS fldCounter, fldCampusBlockName
FROM
tblWHSIncident WI
INNER JOIN
tblCampusBlock CB
ON
WI.fldFKIncidentBlockID = CB.fldID
WHERE
WI.fldFKIncidentBlockID = :block_id;
SQL;
$stmt_block = $oConn->prepare($sql_block);
$stmt_block->execute([
    'block_id' => $block_id
]);

$results_block = $stmt_block->fetch(PDO::FETCH_OBJ);


$sql_incidents =<<<SQL
SELECT 
WI.fldIncidentName,
WI.fldIncidentDate,
WI.fldFKIncidentBlockID,
WI.fldFKIncidentTypeID,
WI.fldIncidentDateRemind,
WIT.fldIncidentTypeName,
WIT.fldIncidentTypeDescription,
CB.fldCampusBlockName,
WIT.fldIncidentTypeDescription,
D.fldID AS fldDocumentID,
D.fldDocumentLocation
FROM
	tblWHSIncident WI
INNER JOIN
  tblWHSIncidentType WIT
ON 
  WI.fldFKIncidentTypeID = WIT.fldID
INNER JOIN
  tblCampusBlock CB
ON 
  WI.fldFKIncidentBlockID = CB.fldID
INNER JOIN
	tblWHSIncidentDocument WID
ON
	WI.fldID = fldFKWHSIncidentID
INNER JOIN
	tblDocument D
ON
	WID.fldFKWHSIncidentDocumentID = D.fldID
WHERE
  CB.fldID = :block_id;
SQL;

$stmt_incidents = $oConn->prepare($sql_incidents);
$stmt_incidents->execute([
    'block_id' => $block_id
]);

$result_incident = $stmt_incidents->fetchAll(PDO::FETCH_OBJ);
if ($stmt_incidents->rowCount()) {
    if($results_block->fldCounter > 1) {
        echo "<h3 class='text-center' id='map_result_title'>$results_block->fldCounter Incidents in $results_block->fldCampusBlockName</h3>";
    } else {
        echo "<h3 class='text-center' id='map_result_title'>$results_block->fldCounter Incident in $results_block->fldCampusBlockName</h3>";
    }

    echo "<div class='list-group'>";
    foreach ($result_incident as $row) {
        $document_id   = $row->fldDocumentID;
        $incident_name = $row->fldIncidentName;
        $incident_date = $row->fldIncidentDate;
        $incident_doc_location = $row->fldDocumentLocation;

        echo "<a class='list-group-item text-center' href='SearchResults.php?item_id=$document_id' target='_blank'>$incident_name - $incident_date </a>";
    }
    echo "</div>";

} else {
    echo "<h3 class='text-muted text-center'>No incident(s) found in this block.</h3>";
}