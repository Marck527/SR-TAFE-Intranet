<?php
require_once 'Core/start.php';

$html_head = buildHTMLHead('Home');
$oUserLogin = new cUser($oConn);

$search_bar = buildSearchbar();

if(isset($_POST['btn_submit_login']) && !empty($_POST['txt_username']) || !empty($_POST['txt_password'])) {
    $username = $_POST['txt_username'];
    $password = $_POST['txt_password'];
    if($oUserLogin->login($username, $password)) {
        header('location: Main.php');
    } else {
        foreach ($oUserLogin->getErrors() as $error) {
            echo "<div class='container'>
                    <div class='alert alert-danger' role='alert'><strong>Oops!</strong> $error</div>
                  </div>";
        }
    }
}

echo HTMLPage($html_head, $nav_bar, $search_bar, $footer);
function HTMLPage($html_head, $nav_bar, $search_bar, $footer)
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
			<h1>SR-TAFE Intranet</h1>
		</div>
		<div class="row">
            <div class="col-md-2">
               <div class="list-group">
                  <a href="#" class="list-group-item disabled">
                    Quick Links
                  </a>
                  <a href="SUSMap.php" class="list-group-item">Sustainability</a>
                  <a href="WHSIncidentMap.php" class="list-group-item">Work Health & Safety</a>
                  <a href="Staff.php" class="list-group-item">Staff</a>
                  <a href="Search.php" class="list-group-item">Document Repository</a>
                </div>
            </div>
            <div class="col-md-10">
                	<div id="myCarousel" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                <li data-target="#myCarousel" data-slide-to="1"></li>
                <li data-target="#myCarousel" data-slide-to="2"></li>
                <li data-target="#myCarousel" data-slide-to="3"></li>
            </ol>
            
            <!-- Wrapper for slides -->
            <div class="carousel-inner" id="slideshow-here" role="listbox">
                <div class="item active">
                    <img src="Images/SUSSlideshow/image1.jpg" alt="Image1">
                </div>
            
                <div class="item">
                    <img src="Images/SUSSlideshow/image2.jpg" alt="Image3">
                </div>
            
                <div class="item">
                    <img src="Images/SUSSlideshow/image3.jpg" alt="Image4">
                </div>
            </div>
            
            <!-- Left and right controls -->
            <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
            </div>
        </div>
	
	$footer
	</div>
	<script>
	
    </script>
	</body>
	</html>
	
HTML;

    return $sHTML;
}
