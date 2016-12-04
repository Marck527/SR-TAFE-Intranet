<?php
/**
 * Created by PhpStorm.
 * User: Marck
 * Date: 12/09/2016
 * Time: 7:19 PM
 */
require_once '../../Lib/Dbconnect.php';

$block_id = $_GET['block_id'];

$sql_block =<<<SQL
SELECT
COUNT(*) AS fldCounter, fldCampusBlockName
FROM
tblSustainabilityProject SP
INNER JOIN
tblCampusBlock CB
ON
SP.fldFKProjectBlockID = CB.fldID
WHERE
SP.fldFKProjectBlockID = :block_id;
SQL;
$stmt_block = $oConn->prepare($sql_block);
$stmt_block->execute([
    'block_id' => $block_id
]);

$results_block = $stmt_block->fetch(PDO::FETCH_OBJ);


$sql_projects =<<<SQL
SELECT
D.fldID AS fldDocumentID,
SP.fldSustainabilityProjectName
FROM
tblSustainabilityProject SP
INNER JOIN
tblSustainabilityProjectDocument SPD
ON
SP.fldID = SPD.fldFKSustainabilityProjectID
INNER JOIN
tblDocument D
ON
SPD.fldFKSustainabilityProjectDocumentID = D.fldID
INNER JOIN
tblCampusBlock CB
ON
SP.fldFKProjectBlockID = CB.fldID
WHERE
SP.fldFKProjectBlockID = :block_id;
SQL;

$stmt_projects = $oConn->prepare($sql_projects);
$stmt_projects->execute([
    'block_id' => $block_id
]);

$results_projects = $stmt_projects->fetchAll(PDO::FETCH_OBJ);
if($stmt_projects->rowCount()) {
    if($results_block->fldCounter > 1) {
        echo "<h3 class='text-center' id='map_result_title'>$results_block->fldCounter Projects in $results_block->fldCampusBlockName</h3>";
    } else {
        echo "<h3 class='text-center' id='map_result_title'>$results_block->fldCounter Project in $results_block->fldCampusBlockName</h3>";
    }

    echo "<div class='list-group'>";
    foreach ($results_projects as $row) {
        $document_id  = $row->fldDocumentID;
        $project_name = $row->fldSustainabilityProjectName;

        echo "<a class='list-group-item text-center' href='SearchResults.php?item_id=$document_id' target='_blank'>$project_name</a>";
    }
    echo "</div>";
} else {
    echo "<h3 class='text-muted text-center'>No project(s) found in this block.</h3>";
}


