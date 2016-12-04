<?php

require_once '../../Lib/Dbconnect.php';
require_once '../../Lib/cDropdown.php';


$advanced_search_sql =<<<SQL
    SELECT
		D.fldID AS fldDOCID,
        D.fldDocumentName,
        D.fldDocumentLocation,
        D.fldFKDocumentTypeID,
        DT.fldDocumentTypeName,
        SP.fldFKProjectBlockID,
        WI.fldFKIncidentBlockID
	
	FROM
		tblDocument D
	INNER JOIN
		tblDocumentType DT
	ON
		D.fldFKDocumentTypeID = DT.fldID
	LEFT JOIN
		tblSustainabilityProjectDocument SPD
	ON
		D.fldID = SPD.fldFKSustainabilityProjectDocumentID
	LEFT JOIN
		tblSustainabilityProject SP
	ON
		fldFKSustainabilityProjectID = SP.fldID
	LEFT JOIN
		tblWHSIncidentDocument WID
	ON
		D.fldID = WID.fldFKWHSIncidentDocumentID
	LEFT JOIN
		tblWHSIncident WI
	ON
		WID.fldFKWHSIncidentID = WI.fldID
	WHERE
		D.fldID != ''

SQL;

    if (!empty($_POST['name_to_search'])) {
        $advanced_search_sql.= "AND D.fldDocumentName LIKE ?";
        $params[] = "%" . $_POST['name_to_search'] . "%";
    }

    if (!empty($_POST['document_type_to_search'])) {
        $advanced_search_sql.= "AND D.fldFKDocumentTypeID = ?";
        $params[] = $_POST['document_type_to_search'];
    }

    if (isset($_POST['document_type_to_search']) && $_POST['document_type_to_search'] == 'rep') {
        if (!empty($_POST['document_block'])) {
            $advanced_search_sql.= "AND WI.fldFKIncidentBlockID = ?";
            $params[] = $_POST['document_block'];
        }
    } else {
        if (!empty($_POST['document_block'])) {
            $advanced_search_sql.= "AND SP.fldFKProjectBlockID = ?";
            $params[] = $_POST['document_block'];
        }
    }


    if (!empty($params)) {
        $stmt_adv_search = $oConn->prepare($advanced_search_sql);
        $stmt_adv_search->execute($params);

    } else {
        $stmt_adv_search = $oConn->prepare($advanced_search_sql);
        $stmt_adv_search->execute();
    }

    $results = $stmt_adv_search->fetchAll(PDO::FETCH_OBJ);

$sHTML = "";
if($stmt_adv_search->rowCount()) {
    $result_count = $stmt_adv_search->rowCount();

    $sHTML.=<<<HTML
    
    <h4 class="text-muted"><em>$result_count record(s) found.</em></h4>
    <div class="table-responsive">
    <table class="table table-hover">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Type</th>
            <th>Action</th>
        </tr>
HTML;

    foreach($results as $row ) {
        $document_id       = $row->fldDOCID;
        $document_name     = $row->fldDocumentName;
        $document_type     = $row->fldDocumentTypeName;

        $sHTML.=<<<HTML
        <tr>
            <td>$document_id</td>
            <td>$document_name</td>
            <td>$document_type</td>
            <td><a href="SearchResults.php?item_id=$document_id">View</a></td>
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