<?php
require_once '../../Lib/Dbconnect.php';

//Incidents by type query
$stmt_incidents_type = $oConn->prepare('CALL incidentByBlock();');
$stmt_incidents_type->execute();

$incident_type_result = $stmt_incidents_type->fetchAll();



$rows = array();
$table = array();

$table['cols'] = array(

    array('label' => 'Block', 'type' => 'string'),
    array('label' => 'Number', 'type' => 'number')


);
//

/* Extract the information from $result */
foreach($incident_type_result as $r) {

    $temp = array();

    // the following line will be used to slice the Pie chart

    $temp[] = array('v' => (string) $r['fldCampusBlockName']);

    // Values of each slice

    $temp[] = array('v' => (int) $r['fldBlockCount']);
    $rows[] = array('c' => $temp);
}

$table['rows'] = $rows;

// convert data into JSON format
$jsonTable = json_encode($table);

echo $jsonTable;