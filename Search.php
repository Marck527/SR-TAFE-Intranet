<?php
require_once 'Core/start.php';
require_once 'Lib/cDropdown.php';

checkIfHaveAccess();
$html_head = buildHTMLHead('Search');

isset($_POST['global_search']) ? $initial_search = $_POST['txt_search'] : $initial_search = null;


//Document type dropdown initialization
$oDropdownDocType = new cDropdown($oConn, "SELECT fldID, fldDocumentTypeName FROM tblDocumentType");
$oDropdownDocType->setHTMLID('document_type');
$oDropdownDocType->setSHTMLClass('form-control');
$oDropdownDocType->setName('document_type');
$oDropdownDocType->setIDField('fldID');
$oDropdownDocType->setDescriptionField('fldDocumentTypeName');
$oDropdownDocType->setDefaultID(-99);
$oDropdownDocType->setDisplayAll(true);
$oDropdownDocType->setDisplayAllText('Any');
$sDropdownDocType = $oDropdownDocType->HTML();
//

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


//
echo HTMLPage($html_head, $footer, $nav_bar, $sDropdownDocType, $sDropdownBlock, $initial_search);
function HTMLPage($html_head, $footer, $nav_bar, $sDropdownDocType, $sDropdownBlock, $initial_search)
{
    $sHTML = "";
    $sHTML.=<<<HTML
	
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
		<div class="page-header">
			<h1>Repository</h1>
		</div>
		<form class="form-inline" >
            <div class="form-group">
                <label for="document_name">Name:</label>
                <input type="search" class="form-control" id="document_name" name="document_name" placeholder="Document name" value="$initial_search">
            </div>
            <div class="form-group" id="document_type_dropdown">
                <label for="document_type">Type:</label>
                $sDropdownDocType
            </div>
            <div class="form-group" id="hide_me_away">
                <label for="document_block">Block:</label>
                $sDropdownBlock
            </div>
           
            <input class="btn btn-default" type="submit" name="document_search" id="btn_document_search" value="Search">
        </form>
        <hr>
        
        <div id="global_search_result" style="overflow-y: scroll; height:400px;">
        <!--Search results here -->
        </div>
HTML;

    $sHTML.=<<<HTML
     $footer
	</div>
	<script>
	$(document).ready(function(){
	    //The best javascript you'll ever see
	    
	    
	    $('#hide_me_away').hide();

	    $('#document_type_dropdown').change(function(){
	    
	        var document_type  = $('#document_type').val();
	    
	        if (document_type == 'proj' || document_type == 'rep') {
	        $('#hide_me_away').show();
	        } else {
	        $('#hide_me_away').hide();
            $('#project_block').val(0);
	        
	        }
	        
	    });

        $('#btn_document_search').click(function(){
            var name_search    = $('#document_name').val();
            var document_type  = $('#document_type').val();
            var document_block = $('#project_block').val();
    
            var dataString     = {name_to_search: name_search, document_type_to_search: document_type, document_block: document_block} ;
    
            $.ajax({
                type: "POST",
                url: 'JS/Ajax/ajax_global_search.php',
                data: dataString,
                success: function(results) {
                    $('#global_search_result').html(results);
                }
            });
            return false; 
        });
	    
	    $('#btn_document_search').click();
	    
	});
    </script>
	</body>
	</html>
HTML;

    return $sHTML;
}


