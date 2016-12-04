<?php
require_once 'Core/start.php';

checkIfHaveAccess();
$html_head = buildHTMLHead('Policies | Sustainability');

$search_bar = buildSearchbar();

$bread_crumb = breadCrumb([
        [
            'title'  => 'Projects',
            'anchor' => 'SUSProjects.php',
            'active' => true

        ],
        [
            'title'  => 'Project Map',
            'anchor' => 'SUSMap.php',
            'active' => false

        ],
    ]
);

$sus_projects =<<<SQL
  SELECT
	SP.fldSustainabilityProjectName,
    SP.fldSustainabilityProjectDate,
    SP.fldSustainabilityProjectDescription,
	CB.fldCampusBlockName,
	D.fldID AS fldDocumentID,
    D.fldDocumentName,
    D.fldDocumentLocation,
    U.fldFirstName,
    U.fldLastName
    
  FROM 
	tblSustainabilityProject SP 
  INNER JOIN
	tblCampusBlock CB
  ON
	SP.fldFKProjectBlockID = CB.fldID
  INNER JOIN
	tblSustainabilityProjectDocument SPD
  ON 
	SP.fldID = SPD.fldFKSustainabilityProjectID
  INNER JOIN
	tblDocument D
  ON
	SPD.fldFKSustainabilityProjectDocumentID = D.fldID
  INNER JOIN
	tblUser U
  ON
	D.fldFKUserID = U.fldID;
SQL;

$stmt = $oConn->prepare($sus_projects);
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_OBJ);

echo HTMLPage($html_head, $nav_bar, $search_bar, $footer, $bread_crumb, $stmt, $results);
function HTMLPage($html_head, $nav_bar, $search_bar, $footer, $bread_crumb, $stmt, $results)
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
	<div class="sus_bg">
	<div class="container">
	    $nav_bar
	    $search_bar
		<div class="page-header">
			<h1>Sustainability Projects</h1>
		</div>
		$bread_crumb
        <br>
HTML;

   if($stmt->rowCount()) {
       $sHTML.=<<<HTML
    <div id="projects_accordion" style="overflow-y: scroll; height:600px;">   
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
HTML;

       foreach($results as $row ) {
           $document_id = $row->fldDocumentID;
           $project_name = $row->fldSustainabilityProjectName;
           $project_date = $row->fldSustainabilityProjectDate;
           $project_description =$row->fldSustainabilityProjectDescription;
           $project_block = $row->fldCampusBlockName;
           $project_submitted_by = $row->fldFirstName . ' ' . $row->fldLastName;

           $html_heading_id = str_replace(' ', '', $project_name . $document_id);
           $html_collapse_id = $project_block . $document_id;


           $sHTML.= <<<HTML
            <div class="panel panel-default"> <!--Panel start-->
                <div class="panel-heading" role="tab" id="$html_heading_id">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#$html_collapse_id" aria-expanded="true" aria-controls="$html_collapse_id">
                            $project_name
                        </a>
                    </h4>
                </div>
                <div id="$html_collapse_id" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="$html_heading_id">
                    <div class="panel-body">
                        <div class="row">
                             <div class="col-md-12">
                                <h3 class="text-center">$project_name</h3>
                             </div>
                        </div>
                        <div class="row">
                             <div class="col-md-12">
                                <p class="text-muted text-center">$project_block  |  Date: $project_date</p>
                             </div>  
                        </div>
                        <hr>
                        <div class="row">
                            
                             <div class="col-md-12">
                                $project_description
                            </div>
                        </div>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-md-3">
                               <a href="SearchResults.php?item_id=$document_id" class="btn btn-info" role="button">View Project
                                <span class="glyphicon glyphicon-chevron-right"></span>
                               </a> <br>
                            </div>
                             <div class="col-md-9">
                               <p class="text-right text-muted">Submitted by: $project_submitted_by</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!--Panel end-->
HTML;


       }
       $sHTML.=<<<HTML
    </div> <!--End of panel-group accordion-->
HTML;
   } else {
       $sHTML.= '<p class="text-muted">Nothing to see here..</p>';
   }

    $sHTML.=<<<HTML
    </div>
     $footer
	</div>
	</div>
	</body>
	</html>
HTML;

    return $sHTML;
}
