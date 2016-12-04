<?php
require_once 'Core/start.php';
require_once 'Lib/cDropdown.php';

checkIfHaveAccess('editor');
$html_head = buildHTMLHead('Admin | Add Sustainability Project');

$search_bar = buildSearchbar();

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
if (isset($_POST['btn_add_project'])) {
    $project_title = $_POST['txt_project_title'];
    $project_date = $_POST['txt_project_date'];
    $project_description = $_POST['txt_project_description'];
    $project_block = $_POST['project_block'];

    $created_by = $_SESSION['user_id'];

    $insert_sus_project =<<<SQL
        INSERT INTO
        tblSustainabilityProject
        (fldSustainabilityProjectName, fldSustainabilityProjectDate, fldSustainabilityProjectDescription, fldFKProjectBlockID)
        VALUES
        (:project_title, :project_date, :project_description, :project_block);
SQL;

    $insert_sus_document =<<<SQL
        INSERT INTO
        tblDocument
        (fldDocumentName, fldFKDocumentTypeID, fldFKUserID)
        VALUES
        (:document_name, :document_type, :document_creator);
        
SQL;
    $insert_to_bridge =<<<SQL
     INSERT INTO tblSustainabilityProjectDocument
     (fldFKSustainabilityProjectDocumentID, fldFKSustainabilityProjectID)
      VALUES(:sus_doc_id, :sus_project_id);
SQL;



    try {
        $oConn->beginTransaction();

        $stmt1 = $oConn->prepare($insert_sus_project);
        if($stmt1) {
            $executed =$stmt1->execute([
                'project_title'       => $project_title,
                'project_date'        => $project_date,
                'project_description' => $project_description,
                'project_block'       => $project_block
            ]);
        }
        $last_project_id = $oConn->lastInsertId();

        $stmt2 = $oConn->prepare($insert_sus_document);
        if($stmt2) {
            $executed = $stmt2->execute([
                'document_name'     =>  $project_title,
                'document_type'     =>  'proj',
                'document_creator'  =>  $created_by
            ]);
        }
        $last_document_id = $oConn->lastInsertId();

        $stmt3 = $oConn->prepare($insert_to_bridge);
        if($stmt3) {
            $stmt3->execute([
                'sus_doc_id'     => $last_document_id,
                'sus_project_id' => $last_project_id
            ]);
        }

        if(uploadDocument($oConn, 'sus_project_upload', $last_document_id)) {
            $oConn->commit();
        }
        
    }catch (PDOException $e) {
        $oConn->rollBack();
    }
}
//

echo HTMLPage($html_head, $nav_bar, $search_bar, $footer, $sDropdownBlock);
function HTMLPage($html_head, $nav_bar, $search_bar, $footer, $sDropdownBlock)
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
			<h1>New Sustainability Project</h1>
		</div>
		<br>
        <form method="post" id="date_check" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="project_title">Project Title</label>
                        <input type="text" name="txt_project_title" class="form-control" id="project_title" placeholder="Add Project Title" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="project_date">Date</label>
                        <input type="date" name="txt_project_date" class="form-control" id="project_date" required>
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
                <label for="description">Description</label>
                <textarea class="form-control" name="txt_project_description" rows="5" id="description" required></textarea>
            </div>
	        
            <div class="form-group"> 
                <label for="sus_project_document">Project Document</label>
                <input type="file" name="sus_project_upload" id="sus_project_document" required>
            </div>
            <button type="submit" name="btn_add_project" class="btn btn-default">Add project</button>
        </form>
    $footer
	</div>
	</body>
	<script>
	$(document).ready(function(){
	    $('#date_check').submit(function(){
	        var project_date = new Date($('#project_date').val());
	            
            var todays_date = new Date();
	            
	        if (todays_date < project_date) {
	            alert("The project date cannot be in the future.");
	            return false;
	        }
	            
	        return true;
        });
	});
    </script>
	</html>
	
HTML;
}
