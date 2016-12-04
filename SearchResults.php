<?php
require_once 'Core/start.php';

checkIfHaveAccess();
$html_head = buildHTMLHead('Results');

isset($_GET['item_id']) ? $item_id = $_GET['item_id'] : $item_id = null;

if (!empty($item_id)) {
    $get_sql =<<<SQL
    CALL documentSearchID($item_id);
SQL;

    $stmt_get = $oConn->prepare($get_sql);
    $stmt_get->execute();
    $result = $stmt_get->fetch(PDO::FETCH_OBJ);

}

echo HTMLPage($html_head, $footer, $nav_bar, $stmt_get, $result);
function HTMLPage($html_head, $footer, $nav_bar, $stmt_get, $result)
{
    $document_id       = $result->fldDOCID;
    $document_type_id  = $result->fldFKDocumentTypeID;
    $document_name     = $result->fldDocumentName;
    $document_location = $result->fldDocumentLocation;
    $document_type     = $result->fldDocumentTypeName;

    //Displays the edit button when an administrator is logged in.
    isset($_SESSION['user_permission']) && $_SESSION['user_permission'] == 2 ? $edit_button = "<a href='EditDocument.php?document_id=$document_id'><span class=\"glyphicon glyphicon-edit\" aria-hidden=\"true\"></span> Edit</a>" : $edit_button = null;
    
    $sHTML = "";
    $sHTML.= <<<HTML
	
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
			<h1>$document_name |<small>$document_type</small></h1>
		</div>
		<p class="text-left">$edit_button</p>

HTML;

    if ($document_type_id == 'proj') {
        $project_date        = $result->fldSustainabilityProjectDate;
        $project_description = $result->fldSustainabilityProjectDescription;
        $project_block       = $result->fldCampusProjectBlock;

        $sHTML.= <<<HTML
        <h4>Project date: $project_date</h4>
        <h4>Project block: $project_block</h4>
        <p class="small">$project_description</p>
HTML;
    } elseif ($document_type_id == 'rep') {
        $incident_date      = $result->fldIncidentDate;
        $incident_type_name = $result->fldIncidentTypeName;
        $incident_block     = $result->fldCampusIncidentBlock;

        $sHTML.= <<<HTML
        <h4>Incident date: $incident_date</h4>
        <h4>Incident block: $incident_block</h4> 
        <h4>Incident type: $incident_type_name</h4>
HTML;
    }

    $sHTML.= <<<HTML
    <a href="$document_location" target="_blank" class="btn btn-info" role="button"><span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Open File</a>
    <br>
HTML;

    $sHTML.= <<<HTML
    $footer
	</div>
	</body>
	</html>
HTML;


    return $sHTML;
}
