<?php
require_once 'Core/start.php';
require_once 'Lib/cDropdown.php';

checkIfHaveAccess('editor');
$html_head = buildHTMLHead('WHS | Reminders');

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
            'active' => false

        ],
        [
            'title'  => 'Reminders',
            'anchor' => 'WHSIncidentReminder.php',
            'active' => true

        ],
        [
            'title'  => 'Reports',
            'anchor' => 'WHSReports.php',
            'active' => false

        ],
    ]
);

//All incidents with reminder
$stmt_all_reminders = $oConn->prepare("CALL allIncidentReminders()");
$stmt_all_reminders->execute();

$result_all_reminders = $stmt_all_reminders->fetchAll(PDO::FETCH_OBJ);
$all_reminders_count = $stmt_all_reminders->rowCount(); //Gets the row count
$stmt_all_reminders->closeCursor();
//

//Incidents due this week
$stmt_due_7 = $oConn->prepare("CALL dueThisWeekIncidentReminders()");
$stmt_due_7->execute();

$result_7 = $stmt_due_7->fetchAll(PDO::FETCH_OBJ);
$result_7_count = $stmt_due_7->rowCount();
$stmt_due_7->closeCursor();
//

//Overdue incidents
$stmt_overdue = $oConn->prepare("CALL overdueIncidentReminders()");
$stmt_overdue->execute();

$result_overdue = $stmt_overdue->fetchAll(PDO::FETCH_OBJ);
$overdue_count = $stmt_overdue->rowCount();
$stmt_overdue->closeCursor();
//




echo HTMLPage($html_head, $nav_bar, $search_bar, $footer, $bread_crumb, $stmt_all_reminders, $result_all_reminders, $all_reminders_count, $stmt_due_7, $result_7, $result_7_count, $stmt_overdue, $result_overdue, $overdue_count);
function HTMLPage($html_head, $nav_bar, $search_bar, $footer, $bread_crumb, $stmt_all_reminders, $result_all_reminders, $all_reminders_count, $stmt_due_7, $result_7, $result_7_count, $stmt_overdue, $result_overdue, $overdue_count)
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
	<div class="container"> <!--Container start-->
	    $nav_bar
	    $search_bar
		<div class="page-header">
			<h1>WHS Incident Reminder </h1>
		</div>
        $bread_crumb
    
    <div> <!--Start of nav tabs div-->
    
      <!-- Nav tabs -->
      <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#all_reminders" aria-controls="all_reminders" role="tab" data-toggle="tab">All reminders <span class="badge">$all_reminders_count</span></a></li>
        <li role="presentation"><a href="#due_this_week" aria-controls="due_this_week" role="tab" data-toggle="tab">Due this week <span class="badge">$result_7_count</span></a></li>
        <li role="presentation"><a href="#overdue" aria-controls="overdue" role="tab" data-toggle="tab">Overdue <span class="badge ">$overdue_count</span></a></li>
      </ul>
    
      <!-- Tab panes -->
      <div class="tab-content"> <!--Start of nav tab-content div-->
      
HTML;

    $sHTML.= <<<HTML
    <div role="tabpanel" class="tab-pane active" id="all_reminders"> <!--Start of all reminders tab panel -->  
HTML;
   /////////////////////////////////////////////////////////////////////////////////////////////
    if ($stmt_all_reminders->rowCount()) {
        $sHTML.=<<<HTML
        <div class="table-responsive" style="overflow-y: scroll; height:300px;">
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

        foreach ($result_all_reminders as $row) {
            $document_id       = $row->fldDocumentID;
            $incident_name     = $row->fldIncidentName;
            $incident_date     = $row->fldIncidentDate;
            $incident_remind   = $row->fldIncidentDateRemind;
            $incident_type     = $row->fldIncidentTypeName;
            $incident_block    = $row->fldCampusBlockName;

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
        $sHTML .= "<p class='text-muted'>Nothing to see here..</p>";
    }
    ///////////////////////////////////////////////////////////////////////////////////////////
    $sHTML.= <<<HTML
    </div> <!--End of all reminder tab panel -->     
HTML;

    $sHTML.= <<<HTML
     <div role="tabpanel" class="tab-pane" id="due_this_week" style="overflow-y: scroll; height:300px;"> <!--Start of due this week tab panel -->  
HTML;
    /////////////////////////////////////////////////////////////////////////////////////////////
    if ($stmt_due_7->rowCount()) {
        $sHTML.=<<<HTML
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

        foreach ($result_7 as $row) {
            $document_id       = $row->fldDocumentID;
            $incident_name     = $row->fldIncidentName;
            $incident_date     = $row->fldIncidentDate;
            $incident_remind   = $row->fldIncidentDateRemind;
            $incident_type     = $row->fldIncidentTypeName;
            $incident_block    = $row->fldCampusBlockName;

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
        $sHTML .= "<p class='text-muted'>Nothing to see here..</p>";
    }
    /////////////////////////////////////////////////////////////////////////////////////////////
    $sHTML.= <<<HTML
    </div> <!--End of due this week tab panel -->   
HTML;


    $sHTML.= <<<HTML
    <div role="tabpanel" class="tab-pane" id="overdue" style="overflow-y: scroll; height:300px;"> <!--Start of overdue tab panel -->   
HTML;
    /////////////////////////////////////////////////////////////////////////////////////////////
    if ($stmt_overdue->rowCount()) {
        $sHTML.=<<<HTML
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

        foreach ($result_overdue as $row) {
            $document_id       = $row->fldDocumentID;
            $incident_name     = $row->fldIncidentName;
            $incident_date     = $row->fldIncidentDate;
            $incident_remind   = $row->fldIncidentDateRemind;
            $incident_type     = $row->fldIncidentTypeName;
            $incident_block    = $row->fldCampusBlockName;

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
        $sHTML .= "<p class='text-muted'>Nothing to see here..</p>";
    }
    /////////////////////////////////////////////////////////////////////////////////////////////
    $sHTML.= <<<HTML
    </div> <!--End of overdue tab panel -->   
HTML;

    $sHTML.= <<<HTML
    </div> <!--End of nav tab-content div-->
    </div> <!--End of nav tabs div-->   
HTML;

    $sHTML.= <<<HTML
        $footer
	</div> <!--Container end-->
	</body>
	</html>
HTML;

    return $sHTML;
}
