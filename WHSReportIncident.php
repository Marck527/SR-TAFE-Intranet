<?php
require_once 'Core/start.php';
require_once 'Lib/cDropdown.php';

checkIfHaveAccess('editor');
$html_head = buildHTMLHead('Admin | Report Incident');

$search_bar = buildSearchbar();

$bread_crumb = breadCrumb([
        [
            'title'  => 'WHS Manager',
            'anchor' => 'WHSManager.php',
            'active' => false

        ],
        [
            'title'  => 'Report incident',
            'anchor' => 'WHSReportIncident.php',
            'active' => true

        ],
        [
            'title'  => 'Reminders',
            'anchor' => 'WHSIncidentReminder.php',
            'active' => false

        ],
        [
            'title'  => 'Reports',
            'anchor' => 'WHSReports.php',
            'active' => false

        ],
    ]
);

//Incident type initialization
$oDropdownType = new cDropdown($oConn, "SELECT fldID, fldIncidentTypeName FROM tblWHSIncidentType");
$oDropdownType->setHTMLID('incident_type');
$oDropdownType->setSHTMLClass('form-control');
$oDropdownType->setName('incident_type');
$oDropdownType->setIDField('fldID');
$oDropdownType->setDescriptionField('fldIncidentTypeName');
$sDropdownType = $oDropdownType->HTML();
//

//Block Dropdown initialization
$oDropdownBlock = new cDropdown($oConn, "SELECT fldID, fldCampusBlockName FROM tblCampusBlock");
$oDropdownBlock->setHTMLID('project_block');
$oDropdownBlock->setSHTMLClass('form-control');
$oDropdownBlock->setName('project_block');
$oDropdownBlock->setIDField('fldID');
$oDropdownBlock->setDescriptionField('fldCampusBlockName');
$oDropdownBlock->setDefaultID('d');
$sDropdownBlock = $oDropdownBlock->HTML();
//

//Functionality
if (isset($_POST['btn_add_incident'])) {
    $incident_name  = $_POST['txt_incident_name'];
    $incident_date  = $_POST['txt_incident_date'];
    $date_remind = $_POST['txt_incident_date_remind'];
    $incident_type  = $_POST['incident_type'];
    $incident_block = $_POST['project_block'];


    $current_date =  date("Y-m-d");

    if (empty($date_remind)) {
        $date_remind = null;
    } elseif(strtotime($date_remind) < strtotime($current_date)) {
        $past = true;
        echo "<script>alert('Cannot set a date reminder in past.')</script>";
        die();
    } else {
        $date_remind = $_POST['txt_incident_date_remind'];
    }

    $created_by = $_SESSION['user_id'];

    $insert_whs_incident =<<<SQL
      INSERT INTO
	    tblWHSIncident
        (fldIncidentName, fldIncidentDate, fldIncidentDateRemind, fldFKIncidentTypeID, fldFKIncidentBlockID)
      VALUES(:incident_name, :incident_date, :date_remind, :incident_type, :incident_block);
SQL;

    $insert_incident_document =<<<SQL
     INSERT INTO
        tblDocument
        (fldDocumentName, fldFKDocumentTypeID, fldFKUserID)
    VALUES
        (:document_name, :document_type, :document_creator);
SQL;

    $insert_to_bridge =<<<SQL
      INSERT INTO
        tblWHSIncidentDocument
        (fldFKWHSIncidentDocumentID, fldFKWHSIncidentID)
      VALUES
      (:incdident_doc_id, :incident_id);
SQL;




    try {
        $oConn->beginTransaction();

        $stmt1 = $oConn->prepare($insert_whs_incident);
        $stmt1->execute([
            'incident_name' => $incident_name,
            'incident_date' => $incident_date,
            'date_remind'   => $date_remind,
            'incident_type' => $incident_type,
            'incident_block'=> $incident_block,
        ]);
        $last_incident_id = $oConn->lastInsertId();

        $stmt2 = $oConn->prepare($insert_incident_document);
        $stmt2->execute([
            'document_name' => $incident_name,
            'document_type' => 'rep',
            'document_creator' => $created_by
        ]);
        $last_document_id = $oConn->lastInsertId();

        $stmt3 = $oConn->prepare($insert_to_bridge);
        $stmt3->execute([
            'incdident_doc_id' => $last_document_id,
            'incident_id'      => $last_incident_id
        ]);

        if(uploadDocument($oConn, 'incident_upload', $last_document_id)) {
            $oConn->commit();
        }
    }catch(PDOException $e) {
        $oConn->rollBack();
    }

}
//

echo HTMLPage($html_head, $nav_bar, $search_bar, $footer, $bread_crumb, $sDropdownType, $sDropdownBlock);
function HTMLPage($html_head, $nav_bar, $search_bar, $footer, $bread_crumb, $sDropdownType, $sDropdownBlock)
{
    return <<<HTML
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
			<h1>Report WHS Incident</h1>
		</div>
        $bread_crumb
        <form id="date_check" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="incident_name">Incident Name</label>
                        <input type="text" name="txt_incident_name" class="form-control" id="incident_name" placeholder="Incident Name" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="incident_date">Date of incident</label>
                        <input type="date" name="txt_incident_date" class="form-control" id="incident_date" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="incident_type">Incident Type</label>
                        $sDropdownType
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="incident_block">Incident Block</label>
                        $sDropdownBlock
                    </div>
                </div>
            </div>
	        
	        
	        
	        
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="reminder_check"> Set a reminder
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group" id="reminder_div">
                        <label for="incident_date_remind">Set reminder</label>
                        <input type="date" name="txt_incident_date_remind" id="date_remind" class="form-control" id="incident_date_remind">
                    </div>
                </div>
            </div>
            
            <div class="form-group"> 
                <label for="policy_document">Incident Document</label>
                <input type="file" name="incident_upload" id="incident_document" required>
            </div>
            <div class="form-group"> 
                <button type="submit" name="btn_add_incident" class="btn btn-default">Add Incident</button>
            </div>
        </form>

    $footer
	</div>
	<script>
	    $(document).ready(function(){
	        
	        $('#reminder_div').hide();
	        
	        $('#reminder_check').click(function(){
	            if($(this).is(":checked")) {
	                $('#reminder_div').show();
	            } else {
	                
	                $('#reminder_div').hide();
	                $('#date_remind').val('');
	            }
	        });
	        
	        $('#date_check').submit(function(){
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
	            
	            return true;
            });
	        
	    });
    </script>
	</body>
	</html>
	
HTML;
}
