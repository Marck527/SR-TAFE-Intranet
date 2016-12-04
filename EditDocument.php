<?php
require_once 'Core/start.php';
require_once 'Lib/cDropdown.php';


checkIfHaveAccess('editor');
$html_head = buildHTMLHead('Edit Mode');

$search_bar = buildSearchbar();

//Receives the document's id to be edited.
$document_id = $_GET['document_id'];

$edit_document = <<<SQL
 CALL documentSearchID($document_id);
SQL;

$stmt = $oConn->prepare($edit_document);
$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_OBJ);

//$document_to_update_id = $result->fldDOCID;
$document_name     = $result->fldDocumentName;
$document_location = $result->fldDocumentLocation;
$document_type_id  = $result->fldFKDocumentTypeID;

$stmt->closeCursor(); //Closes current connection.

//Update functionality
if (isset($_POST['btn_update_document'])) {
    $new_document_name = $_POST['new_doc_name'];

    $params = [];
    $params['new_document_name']     = $new_document_name;
    $params['document_to_update_id'] = $document_id;

    $update_document =<<<SQL
      UPDATE 
	    tblDocument D
      INNER JOIN 
	    tblDocumentType DT ON D.fldFKDocumentTypeID = DT.fldID
      LEFT JOIN 
	    tblSustainabilityProjectDocument SPD ON D.fldID = SPD.fldFKSustainabilityProjectDocumentID
      LEFT JOIN 
	    tblSustainabilityProject SP ON fldFKSustainabilityProjectID = SP.fldID
	  LEFT JOIN
	    tblWHSIncidentDocument WID ON D.fldID = WID.fldFKWHSIncidentDocumentID
	  LEFT JOIN
	    tblWHSIncident WI ON WID.fldFKWHSIncidentID = WI.fldID
	  LEFT JOIN
	    tblWHSIncidentType WIT ON WI.fldFKIncidentTypeID = WIT.fldID
      SET
        D.fldDocumentName = :new_document_name
      
SQL;


    if ($document_type_id == 'proj') { //if the document type is a project, append this line.

        $project_date        = $_POST['project_date'];
        $project_description = $_POST['project_description'];
        $project_block_id    = $_POST['project_block'];

        $update_document .=<<<SQL
         ,SP.fldSustainabilityProjectName = :new_project_name, 
          SP.fldSustainabilityProjectDate = :new_project_date,
          SP.fldSustainabilityProjectDescription = :new_project_description,
          SP.fldFKProjectBlockID = :new_project_block
SQL;

        $params['new_project_name']        = $new_document_name;
        $params['new_project_date']        = $project_date;
        $params['new_project_description'] = $project_description;
        $params['new_project_block']       = $project_block_id;

    } else if ($document_type_id == 'rep') {

        $incident_date        = $_POST['incident_date'];
        $incident_date_remind = $_POST['incident_date_remind'];
        $incident_type_id     = $_POST['incident_type'];
        $incident_block_id    = $_POST['incident_block'];

        $current_date =  date("Y-m-d");

        if (empty($incident_date_remind)) {
            $incident_date_remind = null;
        } elseif(strtotime($incident_date_remind) < strtotime($current_date)) {
            echo "<script>alert('Cannot set a date reminder in past.')</script>";
            die();
        } else {
            $incident_date_remind = $_POST['incident_date_remind'];
        }

        $update_document .=<<<SQL
        , WI.fldIncidentName       = :new_incident_name,
          WI.fldIncidentDate       = :new_incident_date,
          WI.fldIncidentDateRemind = :new_incident_remind,
          WI.fldFKIncidentTypeID   = :new_incident_type,
          WI.fldFKIncidentBlockID  = :new_incident_block

SQL;
        $params['new_incident_name']   = $new_document_name;
        $params['new_incident_date']   = $incident_date;
        $params['new_incident_remind'] = $incident_date_remind;
        $params['new_incident_type']   = $incident_type_id;
        $params['new_incident_block']  = $incident_block_id;

    }

    $update_document .=<<<SQL
      WHERE
        D.fldID = :document_to_update_id;
SQL;

    uploadDocument($oConn, 'update_document', $document_id); //handles document upload

    $stmt_update = $oConn->prepare($update_document);
    $stmt_update->execute($params);

    $stmt_update->closeCursor();

//    echo "<pre>", $update_document, "<pre>";
//    echo "<pre>", print_r($params), "<pre>";

}

//Delete functionality
if (isset($_POST['btn_delete_document'])) {

    $stmt_get_file_path = $oConn->prepare("CALL masterJoin(:document_id);");
    $stmt_get_file_path->execute([
        'document_id' => $document_id
    ]);

    $result = $stmt_get_file_path->fetch(PDO::FETCH_OBJ);

    $document_type_id_to_delete = $result->fldFKDocumentTypeID;
    $file_location              = $result->fldDocumentLocation; //Puts the file location to be deleted on this variable.

    echo "Document id: ", $document_type_id_to_delete, "<br>";

    if ($document_type_id_to_delete == 'proj') { //if the document on edit mode is a project, collect the project id.

        $sus_project_id = $result->fldSustainabilityProjectID;

    }elseif ($document_type_id_to_delete == 'rep') { //if the document type on edit mode is a report, collect the id.

        $whs_project_id = $result->fldWHSIncidentID;
    }

    $stmt_get_file_path->closeCursor();

    if (isset($sus_project_id)) { //if the document is a project, this variable will be set

        $stmt_delete_sus_project = $oConn->prepare("CALL deleteSusProject(:project_id)"); //delete the project of the document
        $stmt_delete_sus_project->execute([
            'project_id' => $sus_project_id
        ]);

        $stmt_delete_sus_project->closeCursor();

    }elseif (isset($whs_project_id)) {

        $stmt_delete_whs_incident = $oConn->prepare("CALL deleteWHSIncident(:incident_id)");
        $stmt_delete_whs_incident->execute([
            'incident_id' => $whs_project_id
        ]);

        $stmt_delete_whs_incident->closeCursor();
    }


    //Deletes the record of the document from the database.
    $sql_delete =<<<SQL
    CALL deleteDocument(:document_id);
SQL;

    $stmt_delete = $oConn->prepare($sql_delete);
    $deleted = $stmt_delete->execute([ //executes the delete query
        'document_id' => $document_id
    ]);

    $stmt_delete->closeCursor(); //ends the query

    if ($deleted) { //if the document has been deleted from the database, delete the file from the uploads folder as well.
        unlink($file_location);
        redirectTo('Main');
    }
}
//


echo HTMLPage($html_head, $footer, $nav_bar, $search_bar, $oConn, $result, $document_type_id, $document_name, $document_location);
function HTMLPage($html_head, $footer, $nav_bar, $search_bar, $oConn, $result, $document_type_id, $document_name, $document_location)
{
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
	    $search_bar
		<div class="page-header">
			<h1>Edit Mode</h1>
		</div>
		
	<form id="date_check"  method="post" enctype="multipart/form-data">
	    <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="documentname">Document Name</label>
                        <input type="text" class="form-control" id="documentname" name="new_doc_name" value="$document_name" required>
                    </div>
                </div>
            </div>
	    
        
HTML;
    //If the document type is a project, include the following extra fields
    if ($document_type_id == 'proj') {

        $project_date        = $result->fldSustainabilityProjectDate;
        $project_description = $result->fldSustainabilityProjectDescription;
        $project_block_id    = $result->fldCampusBlockProjectID;

        //Block Dropdown initialization
        $oDropdownBlock = new cDropdown($oConn, "SELECT fldID, fldCampusBlockName FROM tblCampusBlock");
        $oDropdownBlock->setHTMLID('project_block');
        $oDropdownBlock->setSHTMLClass('form-control');
        $oDropdownBlock->setName('project_block');
        $oDropdownBlock->setIDField('fldID');
        $oDropdownBlock->setDescriptionField('fldCampusBlockName');
        $oDropdownBlock->setDefaultID($project_block_id);
        $sDropdownBlock = $oDropdownBlock->HTML();
        //

        $sHTML.= <<<HTML
        <div class="row">
            <div class="col-md-2">
                <div  class="form-group">
                    <label for="projectdate">Project Date</label>
                    <input type="date" class="form-control" id="projectdate" name="project_date" value="$project_date" required>
                </div>
            </div>
             <div class="col-md-2">
                <div class="form-group">
                    <label for="project_block">Project Block</label>
                    $sDropdownBlock
                </div>
            </div>
        </div>  
        <div class="form-group">
            <label for="projectdescription">Description</label>
            <textarea class="form-control" id="projectdescription" name="project_description" rows="5"  required>$project_description</textarea>
        </div>
HTML;
    } else if ($document_type_id == 'rep') { //else if the type is report, then add the following extras.

        $incident_date        = $result->fldIncidentDate;
        $incident_date_remind = $result->fldIncidentDateRemind;
        $incident_type        = $result->fldFKIncidentTypeID;
        $incident_block_id    = $result->fldCampusBlockReportID;

        //Incident type initialization
        $oDropdownType = new cDropdown($oConn, "SELECT fldID, fldIncidentTypeName FROM tblWHSIncidentType");
        $oDropdownType->setHTMLID('incident_type');
        $oDropdownType->setSHTMLClass('form-control');
        $oDropdownType->setName('incident_type');
        $oDropdownType->setIDField('fldID');
        $oDropdownType->setDescriptionField('fldIncidentTypeName');
        $oDropdownType->setDefaultID($incident_type);
        $sDropdownType = $oDropdownType->HTML();
        //

        //Block Dropdown initialization
        $oDropdownBlock = new cDropdown($oConn, "SELECT fldID, fldCampusBlockName FROM tblCampusBlock");
        $oDropdownBlock->setHTMLID('incident_block');
        $oDropdownBlock->setSHTMLClass('form-control');
        $oDropdownBlock->setName('incident_block');
        $oDropdownBlock->setIDField('fldID');
        $oDropdownBlock->setDescriptionField('fldCampusBlockName');
        $oDropdownBlock->setDefaultID($incident_block_id);
        $sDropdownBlock = $oDropdownBlock->HTML();
        //

        $sHTML.= <<<HTML
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="incident_date">Incident Date</label>
                    <input type="date" class="form-control" id="incident_date" name="incident_date" value="$incident_date" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="date_remind">Incident Date Remind</label>
                    <input type="date" class="form-control" id="date_remind" name="incident_date_remind" value="$incident_date_remind">
                </div>
            </div>
        </div>    
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="incident_type">Incident Type</label>
                    $sDropdownType
                </div>    
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="incident_block">Incident Block</label>
                    $sDropdownBlock
                </div>
            </div>
        </div>

        
HTML;
    }

    $sHTML.= <<<HTML
        <a href="$document_location" target="_blank">Current Document</a>
        <br>
        <div class="form-group"> 
            <label for="policy_document">Update Document</label>
            <input type="file" name="update_document" id="update_document">
        </div>
        
	    <input type="submit" class="btn btn-default" name="btn_update_document" value="Update">
    </form>
    
    <br>
    <!--Delete form-->
    <form method="post" class="are_you_sure">
         <input type="submit" class="btn btn-danger" name="btn_delete_document" value="Delete">
    </form>
    
HTML;

    $sHTML.= <<<HTML
    $footer
	</div>
	<script>
	$(document).ready(function(){
	    
	    $('.are_you_sure').submit(function(){
            var c = confirm("Are you sure you want to perform this action?");
            return c;
        });
        
        $('#date_check').submit(function(){
                var project_date = new Date($('#projectdate').val());
                
	            var incident_date = new Date($('#incident_date').val());
	            var remind_date   = new Date($('#date_remind').val());
	            
	            var todays_date = new Date();
	            
	            if (todays_date < incident_date) {
	                alert("The incident date cannot be in the future.");
	                return false;
	            }
	            if (todays_date > remind_date) {
	                alert("The reminder date cannot be in the past");
	                return false;
	            }
	            if (todays_date < project_date) {
	                alert("The project date cannot be in the future.");
	                return false;
	            }
	            
	            return true;
        });
      
	});
    </script>
	</body>
	</html>
	
HTML;

    return $sHTML;
}
