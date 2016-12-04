<?php
require_once 'Core/start.php';

checkIfHaveAccess('editor');
$html_head = buildHTMLHead('WHS Reports');

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
            'active' => false

        ],
        [
            'title'  => 'Reports',
            'anchor' => 'WHSReports.php',
            'active' => true

        ],
    ]
);


echo HTMLPage($html_head, $nav_bar, $search_bar, $footer, $bread_crumb);
function HTMLPage($html_head, $nav_bar, $search_bar, $footer, $bread_crumb)
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
			<h1>WHS Reports</h1>
		</div>
		$bread_crumb
    <!--Table and divs that hold the pie charts-->
    <div class="table-responsive">
    <table class="table" >
      <tr>
        <td><div id="incident_type_chart_div" ></div></td>
        <td><div id="incident_block_chart_div"></div></td>
      </tr>
    </table>
    </div>
		
    $footer
	</div>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript">

      // Load Charts and the corechart package.
      google.charts.load('current', {'packages':['corechart']});

      // Draw the pie chart for Incident by type Charts is loaded.
      google.charts.setOnLoadCallback(drawIncidentsByType);

      // Draw the pie chart for Incident by block Charts is loaded.
      google.charts.setOnLoadCallback(drawIncidentsByBlock);

      // Callback that draws the pie chart for incident by type.
      function drawIncidentsByType() {
      
        var jsonData = $.ajax({
            url: "JS/Ajax/ajax_chart_data_type.php",
            dataType: "json",
            async: false
        }).responseText;
          
        // Create our data table out of JSON data loaded from server.
        var data = new google.visualization.DataTable(jsonData);
        
        var options = {
            width: 500,
            height: 300,
            title: 'Incidents By Type',
            backgroundColor: 'transparent',
            is3D: false
            };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('incident_type_chart_div'));
        chart.draw(data, options);
      }

      // Callback that draws the pie chart for incident by block.
      function drawIncidentsByBlock() {
        var jsonData = $.ajax({
            url: "JS/Ajax/ajax_chart_data_block.php",
            dataType: "json",
            async: false
        }).responseText;
          
        // Create our data table out of JSON data loaded from server.
        var data = new google.visualization.DataTable(jsonData);
        
        var options = {
            width: 500,
            height: 300,
            title: 'Incidents By Block',
            backgroundColor: 'transparent',
            is3D: false
            };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('incident_block_chart_div'));
        chart.draw(data, options);
      }
    </script>
	</body>
	</html>
	
HTML;
}
