$(document).ready(function() { //Document ready start, Only run javascript below once the whole document has finished loading.



    ///////////////////////UserRegister.php///////////////////////////
    //Checks if the email being typed exists in the database.
    $('#txt_email').keyup(function(){
        var email = $('#txt_email').val();

        $.ajax({
            type: "POST",
            url: "JS/Ajax/ajax_check_availability.php",
            data: "email="+email,
            success: function(result){
                $('#email_check_box').html(result);
            }
        });
    });

    //Checks if the username being type exists in the dtaabase.
    $('#txt_username').keyup(function(){
        var username = $('#txt_username').val();

        $.ajax({
            type: "POST",
            url: "JS/Ajax/ajax_check_availability.php",
            data: "username="+username,
            success: function(result){
                $('#username_check_box').html(result);
            }
        });

    });
    //On the key up of either the password or repeat password field, run the passwordMatch function.
    $('#password1, #password2').keyup(passwordMatch);
    ///////////////////////UserRegister.php END///////////////////////////


    ///////////////////////WHSManager.php///////////////////////////
    //Retrieves all the incident records
    $.ajax({
        type: "GET",
        url: 'JS/Ajax/ajax_whs_manager.php',
        success: function(results) {
            $('#incident_results').html(results);
        }
    });
    //When the search button is clicked, retrieve all data in the search fields
    $('#btn_incident_search').click(function(){
        var name_search    = $('#incident_name').val();
        var date_search    = $('#incident_date').val();
        var block_search   = $('#project_block').val();
        var type_search    = $('#incident_type').val();
        var status_search  = $('#incident_status').val();

        var dataString   = "search=" + name_search + "&date=" + date_search + "&block=" + block_search + "&type=" + type_search + "&status=" + status_search;
        //Send an ajax request and return the results
        $.ajax({
            type: "GET",
            url: 'JS/Ajax/ajax_whs_manager.php',
            data: dataString,
            success: function(results) {
                $('#incident_results').html(results);
            }
        });
        return false; //Return false to stop the form from reloading
    });
    ///////////////////////WHSManager.php END///////////////////////////



}); //Document ready end

///////////Misc Library/////////////

//UserRegister.php

    //Checks if both password fields match.
    function passwordMatch() {
        var password1 = $('#password1').val();
        var password2 = $('#password2').val();

        if (password1 != password2) {
            $('#password_match_box').html('<p class="text-danger text-center"><strong>Passwords don\'t match ✘</strong></p>');
        } else {
            $('#password_match_box').html('<p class="text-success text-center"><strong>Passwords match ✔<strong></p>');
        }
    }
//