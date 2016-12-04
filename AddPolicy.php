<?php
require_once 'Core/start.php';
require_once 'Lib/cDropdown.php';

checkIfHaveAccess('editor');
$html_head = buildHTMLHead('Admin | Add Policy');

$search_bar = buildSearchbar();

//Functionality
if(isset($_POST['btn_add_policy'])) {
    $policy_title = $_POST['txt_project_title'];
    
    $created_by = $_SESSION['user_id'];

    $insert_policy =<<<SQL
    INSERT INTO
	  tblDocument
      (fldDocumentName, fldFKDocumentTypeID, fldFKUserID)
    VALUES
	(:policy_title, :document_type, :submitted_by);
SQL;

    try {
        $oConn->beginTransaction();

        $stmt1 = $oConn->prepare($insert_policy);
        if($stmt1) {
            $executed =$stmt1->execute([
                'policy_title' => $policy_title,
                'document_type' => 'pol',
                'submitted_by' => $created_by
            ]);
        }
        $last_document_id = $oConn->lastInsertId();

        if(uploadDocument($oConn, 'policy_upload', $last_document_id)) {
            $oConn->commit();
        }

    }catch (PDOException $e) {
        $oConn->rollBack();
    }
}
//

echo HTMLPage($html_head, $nav_bar, $search_bar, $footer);
function HTMLPage($html_head, $nav_bar, $search_bar, $footer)
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
			<h1>Add Policy/Procedure</h1>
		</div>
		<br>
        <form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-4">
                     <div class="form-group">
                        <label for="project_title">Policy Title</label>
                        <input type="text" name="txt_project_title" class="form-control" id="policy_title" placeholder="Policy Title" required>
                    </div>
                </div>
            </div>
	       
            <div class="form-group"> 
                <label for="policy_document">Select Document</label>
                <input type="file" name="policy_upload" id="policy_document" required>
            </div>
            <button type="submit" name="btn_add_policy" class="btn btn-default">Add policy</button>
        </form>
    $footer
	</div>
	
	</body>
    
	</html>
	
HTML;
}
