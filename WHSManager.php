<?php
require_once 'Core/start.php';
require_once 'Lib/cDropdown.php';

checkIfHaveAccess('editor');
$html_head = buildHTMLHead('Admin | WHS Incident Manger');

$search_bar = buildSearchbar();

$bread_crumb = breadCrumb([
        [
            'title'  => 'WHS Manager',
            'anchor' => 'WHSManager.php',
            'active' => true

        ],
        [
            'title'  => 'Report incident',
            'anchor' => 'WHSReportIncident.php',
            'active' => false

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

//Block Dropdown initialization
$oDropdownBlock = new cDropdown($oConn, "SELECT fldID, fldCampusBlockName FROM tblCampusBlock");
$oDropdownBlock->setHTMLID('project_block');
$oDropdownBlock->setSHTMLClass('form-control');
$oDropdownBlock->setName('project_block');
$oDropdownBlock->setIDField('fldID');
$oDropdownBlock->setDescriptionField('fldCampusBlockName');
$oDropdownBlock->setDefaultID(-99);
$oDropdownBlock->setDisplayAll(true);
$oDropdownBlock->setDisplayAllText('Any');
$sDropdownBlock = $oDropdownBlock->HTML();
//

//Incident type initialization
$oDropdownType = new cDropdown($oConn, "SELECT fldID, fldIncidentTypeName FROM tblWHSIncidentType");
$oDropdownType->setHTMLID('incident_type');
$oDropdownType->setSHTMLClass('form-control');
$oDropdownType->setName('incident_type');
$oDropdownType->setIDField('fldID');
$oDropdownType->setDescriptionField('fldIncidentTypeName');
$oDropdownType->setDefaultID(-99);
$oDropdownType->setDisplayAll(true);
$oDropdownType->setDisplayAllText('Any');
$sDropdownType = $oDropdownType->HTML();
//

//Incident reminder counter
    $stmt_count_reminders = $oConn->prepare("CALL countIncidentReminders()");
    $stmt_count_reminders->execute();

    $result = $stmt_count_reminders->fetch(PDO::FETCH_OBJ);
    $reminder_count = $result->fldIncidentReminderCount;
//


echo HTMLPage($html_head, $nav_bar, $search_bar, $footer, $bread_crumb, $sDropdownBlock, $sDropdownType, $reminder_count);
function HTMLPage($html_head, $nav_bar, $search_bar, $footer, $bread_crumb, $sDropdownBlock, $sDropdownType, $reminder_count)
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
			<h1>WHS Manager</h1>
		</div>
		$bread_crumb
        <br>
         <a href="WHSReportIncident.php" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-exclamation-sign"></span> Report Incident</a>
         <a href="WHSIncidentReminder.php" class="btn btn-sm btn-warning"><span class="glyphicon glyphicon-flag"></span> Reminders <span class="badge">$reminder_count</span></a>
         <a href="WHSReports.php" class="btn btn-sm btn-info"><span class="glyphicon glyphicon-info-sign"></span> Reports</a>
        <h4 class="text-muted text-center">Filter results</h4>
        <form class="form-inline"  id="incident_search_form">
            <div class="form-group">
                <label for="incident_name">Name:</label>
                <input type="search" class="form-control" id="incident_name" name="incident_name" placeholder="[Enter incident name]">
            </div>
             <div class="form-group">
                <label for="incident_date">Date:</label>
                <input type="date" class="form-control" id="incident_date">
            </div>
            <div class="form-group">
                <label for="project_block">Block:</label>
                $sDropdownBlock
            </div>
            <div class="form-group">
                <label for="incident_type">Type:</label>
                $sDropdownType
            </div>
            <input class="btn btn-default" type="submit" name="incident_search" id="btn_incident_search" value="Search">
        </form>
        <hr>
        <br>
        <div id="incident_results" style="overflow-y: scroll; height:300px;">
            <!--Ajax results here.-->
        </div>
    $footer
	</div>
	</body>
	</html>
HTML;

}
