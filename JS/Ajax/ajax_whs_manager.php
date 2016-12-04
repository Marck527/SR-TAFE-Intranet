<?php
/**
 * Created by PhpStorm.
 * User: Marck
 * Date: 14/09/2016
 * Time: 8:27 PM
 */

require_once '../../Lib/Dbconnect.php';


$search_query =<<<SQL
SELECT
WI.fldIncidentName,
WI.fldIncidentDate,
WI.fldFKIncidentBlockID,
WI.fldFKIncidentTypeID,
WI.fldIncidentDateRemind,
WIT.fldIncidentTypeName,
CB.fldCampusBlockName,
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
  WI.fldID != ''
SQL;

if (!empty($_GET['search'])) {
    $search_query .= " AND fldIncidentName LIKE ?";
    $params[] = "%" . $_GET['search'] . "%";
}
if (!empty($_GET['date'])) {
    $search_query .= " AND fldIncidentDate = ?";
    $params[] = $_GET['date'];
}
if (!empty($_GET['block'])) {
    $search_query .= " AND fldFKIncidentBlockID = ?";
    $params[] = $_GET['block'];
}
if (!empty($_GET['type'])) {
    $search_query .= " AND fldFKIncidentTypeID = ?";
    $params[] = $_GET['type'];
}


if (!empty($params)) {

    $stmt = $oConn->prepare($search_query);
    $stmt->execute($params);

} else {
    $stmt = $oConn->prepare($search_query);
    $stmt->execute();
}


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
            <th>Date</th>
            <th>Remind</th>
            <th>Block</th>
            <th>Type</th>
            <th>Action</th>
        </tr>
HTML;

    foreach($results as $row ) {
        $document_id       = $row->fldDocumentID;
        $incident_name     = $row->fldIncidentName;
        $incident_date     = $row->fldIncidentDate;
        $incident_remind   = $row->fldIncidentDateRemind;
        $incident_type     = $row->fldIncidentTypeName;
        $incident_block    = $row->fldCampusBlockName;
        $document_location = $row->fldDocumentLocation;


        $sHTML.=<<<HTML
        <tr>
            <td>$incident_name</td>
            <td>$incident_date</td>
            <td>$incident_remind</td>
            <td>$incident_block</td>
            <td>$incident_type</td>
            <td><a href="SearchResults.php?item_id=$document_id">View Incident</a></td>
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