<?php
require_once 'Core/start.php';

checkIfHaveAccess();
$html_head = buildHTMLHead('Staff');

$search_bar = buildSearchbar();

$view_staff =<<<SQL
    CALL viewStaff();
SQL;

$stmt = $oConn->prepare($view_staff);
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_OBJ);


echo HTMLPage($html_head, $footer, $nav_bar, $search_bar, $stmt, $results);
function HTMLPage($html_head, $footer, $nav_bar, $search_bar, $stmt, $results)
{
    $sHTML = "";
    $sHTML .= <<<HTML
	
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
			<h1>Staff</h1>
		</div>
		<div id="staff_bio_div" style="overflow-y: scroll; height:800px;">
HTML;
    if ($stmt->rowCount()) {
        foreach ($results as $result) {
            $staff_full_name = $result->fldStaffName;
            $staff_bio       = $result->fldBiography;
            $staff_photo     = $result->fldPhoto;

            $sHTML.= <<<HTML
           
            <h3 class="text-center">$staff_full_name</h3>
            <div class="text-center">
                <img src="$staff_photo" alt="$staff_full_name" width="200">
            </div>
            <br>
            <div id="staff_bio">
                <p class="text-center">$staff_bio</p>
            </div>
            
            <hr>
            

HTML;
        }
    } else {
        $sHTML .= "<p class='text-muted'>Nothing to see here..</p>";
    }
    $sHTML .= <<<HTML
    </div>
    $footer
	</div>
	</body>
	</html>
HTML;


    return $sHTML;
}
