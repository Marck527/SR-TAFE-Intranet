<?php
require_once 'Core/start.php';
require_once 'Lib/cDropdown.php';

checkIfHaveAccess('editor');
$html_head = buildHTMLHead('Add Staff');

$search_bar = buildSearchbar();


//Functionality
if (isset($_POST['btn_add_staff']) && $_POST['select_staff'] == 0) { //if the dropdown selector is on add mode, add the entered details.
    $staff_first_name = $_POST['staff_first_name'];
    $staff_last_name  = $_POST['staff_last_name'];
    $staff_biography  = $_POST['staff_biography'];

    uploadStaff($oConn, $staff_first_name, $staff_last_name, $staff_biography);

} elseif (isset($_POST['btn_add_staff']) && $_POST['select_staff'] != 0) { //else if the selector has chosen a name, update their details.
    $staff_to_update_id = $_POST['select_staff'];
    $staff_first_name = $_POST['staff_first_name'];
    $staff_last_name  = $_POST['staff_last_name'];
    $staff_biography  = $_POST['staff_biography'];

    uploadStaff($oConn, $staff_first_name, $staff_last_name, $staff_biography, $staff_to_update_id);
}
//

//Staff Dropdown initialization
$oDropdownStaff = new cDropdown($oConn, "CALL viewStaff()");
$oDropdownStaff->setHTMLID('select_staff');
$oDropdownStaff->setSHTMLClass('form-control');
$oDropdownStaff->setName('select_staff');
$oDropdownStaff->setIDField('fldID');
$oDropdownStaff->setDescriptionField('fldStaffName');
$oDropdownStaff->setDisplayAll(true);
$oDropdownStaff->setDisplayAllText('Create New');
$oDropdownStaff->setDefaultID(-999);
$sDropdownStaff = $oDropdownStaff->HTML();
//


echo HTMLPage($html_head, $footer, $nav_bar, $search_bar, $sDropdownStaff);
function HTMLPage($html_head, $footer, $nav_bar, $search_bar, $sDropdownStaff)
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
			<h1>Add/Edit Staff</h1>
		</div>
		<div id="div_test">
		
        </div>
		<form method="post" enctype="multipart/form-data">
		    <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="select_staff">New / Edit existing</label>
                        $sDropdownStaff
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="staff_first_name">Staff First Name</label>
                        <input type="text" name="staff_first_name" class="form-control" id="staff_first_name" required>
                    </div>
                </div>
                 <div class="col-md-4">
                     <div class="form-group">
                        <label for="staff_last_name">Staff Last Name</label>
                        <input type="text" name="staff_last_name" class="form-control" id="staff_last_name" required>
                    </div>
                 </div>
            </div>
	        
           
            <div class="form-group">
                <label for="staff_biography">Staff Bio</label>
                <textarea class="form-control" rows="5" name="staff_biography" id="staff_biography"></textarea>
            </div>
            <div id="existing_picture"></div>
            <div class="form-group"> 
                <label for="staff_photo">Upload/Change Photo</label>
                <input type="file" name="staff_photo" id="staff_photo" required>
            </div>
            <button type="submit" name="btn_add_staff" class="btn btn-default">Add / Update</button>
        </form>
    $footer
	</div>
    <script>
	    $('#select_staff').on('change', function(){
	        var staffID = $(this).val();
	        var result_div = $('#div_test');
	        
	        if(staffID == 0) {
	            $('#staff_first_name').val('');
	            $('#staff_last_name').val('');
	            $('#staff_biography').val('');
	            $('#existing_picture').html("");
	        } else {
	            $.ajax({
	                type: 'POST',
	                url:  'JS/Ajax/ajax_edit_staff.php',
	                data: 'staff_id='+ staffID,
	                success: function(result){
	                    var data = $.parseJSON(result);
	                    
	                    $.each(data, function(i, item){
	                        
	                        $('#staff_first_name').val(item.fldFirstName);
	                        $('#staff_last_name').val(item.fldLastName);
	                        $('#staff_biography').val(item.fldBiography);
	                        $('#existing_picture').html('<img src="' + item.fldPhoto + '" width="200" />');
	                    });
	                }
	            
	            });
	        }
	    });
    </script>
	</body>
	</html>
	
HTML;
}
