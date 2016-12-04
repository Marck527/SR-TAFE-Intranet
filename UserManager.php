<?php
require_once 'Core/start.php';
require_once 'Lib/cDropdown.php';

checkIfHaveAccess('admin');
$html_head = buildHTMLHead('User Manager');

$search_bar = buildSearchbar();

//User privilege dropdown initialization
$oDropdownPrivilege = new cDropdown($oConn, "SELECT fldID, fldPrivilegeTitle FROM tblUserPrivilege");
$oDropdownPrivilege->setHTMLID('user_privilege');
$oDropdownPrivilege->setSHTMLClass('form-control');
$oDropdownPrivilege->setName('user_privilege');
$oDropdownPrivilege->setIDField('fldID');
$oDropdownPrivilege->setDescriptionField('fldPrivilegeTitle');
$oDropdownPrivilege->setDisplayAll(true);
$oDropdownPrivilege->setDisplayAllText('Any');
$oDropdownPrivilege->setDefaultID(-999);
$sDropdownPrivilege = $oDropdownPrivilege->HTML();
//

echo HTMLPage($html_head, $nav_bar, $search_bar, $footer, $sDropdownPrivilege);
function HTMLPage($html_head, $nav_bar, $search_bar, $footer, $sDropdownPrivilege)
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
	<div class="container">
	    $nav_bar
	    $search_bar
		<div class="page-header">
			<h1>User Manager</h1>
		</div>
    <a href="UserAddEdit.php" class="btn btn-default">New User</a>
    <br>
    <br>
    <form method="post">
        <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="user_privilege">Privilege</label>
                        $sDropdownPrivilege
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="user_search">Username</label>
                        <input type="text" name="user_search" class="form-control" id="user_search" placeholder="Enter username">
                    </div>
                </div>
            </div>
            
        
         
    </form>
    <div id="user_search_results" style="overflow-y: scroll; height:300px;">
        <!--Ajax result(s) here-->
    </div>
HTML;
    $sHTML.=<<<HTML
    $footer
	</div>
	<script>
	    $('#user_search').keyup(function(){
	        var searched_text      = $(this).val();
	        var searched_privilege = $('#user_privilege').val();
	        
	        
	        $.ajax({
	            type: 'POST',
	            url: 'JS/Ajax/ajax_user_manager.php',
	            data: {user_search : searched_text, user_privilege : searched_privilege},
	            success: function(results){
	                $('#user_search_results').html(results);
	            }
	        });
	    });
        
        $('#user_privilege').change(function(){
	        var searched_text      = $('#user_search').val();
	        var searched_privilege = $(this).val();
	        
	        
	        $.ajax({
	            type: 'POST',
	            url: 'JS/Ajax/ajax_user_manager.php',
	            data: {user_search : searched_text, user_privilege : searched_privilege},
	            success: function(results){
	                $('#user_search_results').html(results);
	            }
	        });
	    });
	
    </script>
	</body>
	</html>
HTML;

    return $sHTML;
}
