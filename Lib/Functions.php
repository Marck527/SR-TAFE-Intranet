<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Builds the navbar
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function buildNavBar($signed_in_form){

    if(isset($_SESSION['user_permission']) && $_SESSION['user_permission'] == 1 ) {
        $admin_tools = adminDropdown();
    } elseif(isset($_SESSION['user_permission']) && $_SESSION['user_permission'] == 2) {
        $admin_tools = editorDropdown();
    } else {
        $admin_tools = null;
    }

    $banner = buildBanner();

    return <<<HTML
    $banner
    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span> 
            </button>
            <a class="navbar-brand" href="Main.php">SR-TAFE</a>
            </div>
        <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Sustainability
                    <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="SUSProjects.php">Projects</a></li>
                         <li><a href="SUSMap.php">Project Map</a></li>
                    </ul>
                </li>
               
                 <li><a href="WHSIncidentMap.php">WHS<span class="sr-only">(current)</span></a></li>
                <li><a href="Staff.php">Staff <span class="sr-only">(current)</span></a></li>
                <li><a href="Search.php">Repository<span class="sr-only">(current)</span></a></li>
            </ul>
            $signed_in_form
            $admin_tools
        </div> 
        </div>
    </nav>

HTML;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Builds the search bar
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function buildSearchbar($i_default_val = null) {
    return <<<HTML
    <!--Search bar-->
    <form class="navbar-form navbar-right" action="Search.php" method="post" role="search">
        <div class="form-group">
            <input type="text" class="form-control" name="txt_search" placeholder="Search" value="$i_default_val" required>
        </div>
        <button type="submit" class="btn btn-default btn-sm" name="global_search">Search</button>
    </form>
HTML;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Builds the admin dropdown
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function adminDropdown() {
    return <<<HTML
        <!--Admin Only-->
        <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
           <a class="dropdown-toggle" data-toggle="dropdown" href="#">Admin Tools
           <span class="caret"></span></a>
           <ul class="dropdown-menu">
             <li class="dropdown-header">User Manager</li>
             <li><a href="UserManager.php">Manage Users</a></li>
           </ul>
        </li>
        </ul>
        <!--Admin Only-->
HTML;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Builds the editor dropdown
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function editorDropdown() {
    return <<<HTML
     <!--Editors Only-->
        <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
           <a class="dropdown-toggle" data-toggle="dropdown" href="#">Editor Tools
           <span class="caret"></span></a>
            <ul class="dropdown-menu">
             <li class="dropdown-header">Sustainability</li>
             <li><a href="SUSAddProject.php">Add Sustainability Project</a></li>
             <li class="divider"></li>
             <li class="dropdown-header">Work Health & Safety</li>
             <li><a href="WHSManager.php">WHS Manager</a></li> 
             <li><a href="WHSReportIncident.php">Report WHS Incident</a></li>
             <li class="divider"></li>
             <li class="dropdown-header">Other</li>
             <li><a href="AddPolicy.php">Add Policy</a></li>
             <li><a href="StaffAdd.php">Add/Edit Staff</a></li>
           </ul>
        </li>
        </ul>
        <!--Editors Only-->
HTML;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Builds the banner image
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function buildBanner() {
    return <<<HTML
    <div class="container-fluid">
    <div class="row">
        <a href="Main.php">
	        <img src="Images/logo.png" class="img-responsive" alt="banner-logo">
	    </a>
	</div>
    </div>
HTML;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Builds the html head
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function buildHTMLHead($i_title) {
    return <<<HTML
    <head>
		<meta charset="UTF-8">
		<title>{$i_title}</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<script src="Resources/jquery-3.1.1.min.js"></script>
		<link rel="stylesheet" type="text/css" href="Resources/bootstrap-3.3.7-dist/css/bootstrap.min.css">
		<script src="Resources/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
		 <!--<link rel="stylesheet" type="text/css" href="CSS/camstrap.css">-->


        <link href="https://fonts.googleapis.com/css?family=Arimo" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="CSS/my.css">
        <script src="JS/Main.js"></script>
        <script src="Resources/jQuery-rwdImageMaps-master/jquery.rwdImageMaps.min.js"></script>
        
	</head>
HTML;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Builds the footer
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function buildFooter() {
    return <<<HTML
    <br>
    <footer>
      <p class="pull-left text-muted">All contents © Government of Western Australia. All rights reserved.</p>
      <div class="pull-right">
        <ul class="list-inline">
          <li>
            <a href="#">
                <img src="Images/SocialIcons/facebook.png" class="img-responsive" alt="Facebook">
            </a>
          </li>
          <li>
            <a href="#">
                <img src="Images/SocialIcons/twitter.png" class="img-responsive" alt="Twitter">
            </a>
          </li>
          <li>
            <a href="#">
                <img src="Images/SocialIcons/google-plus.png" class="img-responsive" alt="Google Plus">
            </a>
          </li>
        </ul>
      </div>
    </footer>
HTML;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Returns the logged in persons name and their privilege
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function username_logged_in() {
    $user_logged = checkSession('user_logged');
    $user_privilege = checkSession('user_permission');
    $privilege = null;
    switch ($user_privilege) {
        case 1:
            $privilege = 'Admin';
            break;
        case 2:
            $privilege = 'Editor';
            break;
        case 3:
            $privilege = 'Viewer';
            break;
    }
    return <<<HTML
    $user_logged ($privilege)
HTML;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Breadcrumb creator function
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function breadCrumb($links) {
    $sHTML = "<ul class='breadcrumb'>";
    foreach($links as $link) {
        $title = $link['title'];
        $anchor = $link['anchor'];
        $active = $link['active'];
        if($active) {
            $active = "class='active'";
            $sHTML .= "<li $active>$title</li>";
        } else {
            $sHTML .= "<li><a href='$anchor'>$title</a></li>";
        }
    }
    $sHTML .= "</ul>";
    return $sHTML;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Redirect function
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function redirectTo($i_page) {
    return header("location:{$i_page}.php");
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function checkSession($i_session) {
    switch($i_session) {
        case 'user_id':
            return $_SESSION['user_id'];
            break;
        case 'user_logged':
            return $_SESSION['user_logged'];
            break;
        case 'user_permission':
            return $_SESSION['user_permission'];
            break;
    }
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Builds the login form or returns the logged in persons name
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function returnLogin() {
    isset($_SESSION['user_logged']) ? $user_logged = username_logged_in(): false;
    $logout_form = buildLogoutForm();
    if(isset($user_logged)) {
        return <<<HTML
        $logout_form
        <p class="navbar-text navbar-right"><a href="#" class="navbar-link">$user_logged</a></p>
HTML;
    } else {
        return <<<HTML
        <form class="navbar-form navbar-right" method="post">
            <div class="form-group">
                <label for="username"></label>
                <input type="text" name="txt_username" class="form-control" id="username" placeholder="Username">
            </div>
             <div class="form-group">
                <label for="password"></label>
                <input type="password" name="txt_password" class="form-control" id="password" placeholder="Password">
            </div>
            <input class="btn btn-default btn-sm" type="submit" name="btn_submit_login" value="Login">
        </form>
HTML;

    }
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Builds the logout form
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function buildLogoutForm() {
    return <<<HTML
    <form class="navbar-form navbar-right" name="frm_logout" method="POST" onsubmit="return logoutOK()">
		<input class=" btn btn-default btn-sm" type="submit" name="btn_logout" value="Logout"/>
	</form>
HTML;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Access limiter function
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function checkIfHaveAccess($i_permission = null) {
    $logged_in = checkSession('user_id');
    $permission = checkSession('user_permission');

    switch ($i_permission) {
        case 'editor':
            isset($logged_in) && $permission == 2 ? true : redirectTo('main');
            break;
        case 'admin':
            isset($logged_in) && $permission == 1 ? true : redirectTo('main');
            break;
        case 'admin, editor':
            isset($logged_in) && $permission == 1 || isset($logged_in) && $permission == 2  ? true : redirectTo('main');
            break;
        default:
            isset($logged_in) ? true : redirectTo('main');
            break;
    }
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Checks if the user has clicked logged out
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function checkLogout() {
    if(isset($_POST['btn_logout'])) {
        cUser::logout();
    }
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Document uploader function
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function uploadDocument($i_oConn, $i_form_name, $i_last_document_id) {

    $file = $_FILES[$i_form_name];

    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];
    $file_ext = explode('.', $file_name);
    $file_ext = strtolower(end($file_ext));

    $successful_uploads = [];
    $failed_uploads = [];
    $allowedExt = array('png', 'jpg','txt', 'docx', 'pdf');

    if(!$file_tmp) {
        $failed_uploads[] = "No file selected.";
    } elseif(in_array($file_ext, $allowedExt)) {
        if($file_size <= 4194304) {
            if($file_error === 0) {
                $file_new_name = uniqid('', true) . '.' . $file_ext;
                $file_destination = 'Uploads/' . $file_new_name;

                if(move_uploaded_file($file_tmp, $file_destination)) {
                    $sSQLUpload = <<<SQL
                      UPDATE
	                    tblDocument
                      SET 
                        fldDocumentLocation = :file_location
                      WHERE
	                    tblDocument.fldID = :last_document_id;

SQL;
                    $oStmt = $i_oConn->prepare($sSQLUpload);
                    $oStmt->execute([
                        'file_location'    => $file_destination,
                        'last_document_id' => $i_last_document_id
                    ]);
                    $successful_uploads[] = "$file_destination";
                    echo "<script>alert('File successfully uploaded!')</script>";
                    return true;
                }
            }
            else {
                echo "<script>alert('An error has occured trying to upload [{$file_name}]')</script>";
            }
        } else {
            $failed_uploads[] = "[{$file_name}] is too large.";
            echo "<script>alert('The file {$file_name} is too large.')</script>";
        }
    } else {
        $failed_uploads[] = "[{$file_ext}] file extensions are not supported.";
        echo "<script>alert('The file extension .{$file_ext} is not supported.')</script>";
    }
    return false;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Staff photo uploader function
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function uploadStaff($i_oConn, $i_first_name, $i_last_name, $i_biography, $i_staff_id = null) {

    $file = $_FILES['staff_photo'];

    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];
    $file_ext = explode('.', $file_name);
    $file_ext = strtolower(end($file_ext));

    $successful_uploads = [];
    $failed_uploads = [];
    $allowedExt = array('png', 'jpg','txt', 'docx', 'pdf');

    if(!$file_tmp) {
        $failed_uploads[] = "No file selected.";
    } elseif(in_array($file_ext, $allowedExt)) {
        if($file_size <= 2097152) {
            if($file_error === 0) {
                $file_new_name = uniqid('', true) . '.' . $file_ext;
                $file_destination = 'Uploads/' . $file_new_name;

                if(move_uploaded_file($file_tmp, $file_destination)) {

                    $params = [];
                    $params['first_name']     = $i_first_name;
                    $params['last_name']      = $i_last_name;
                    $params['biography']      = $i_biography;
                    $params['photo_location'] = $file_destination;

                    if (empty($i_staff_id)) { //if the incoming staff id is empty, it means it is in add mode.
                        $sSQLUpload = <<<SQL
                          CALL insertStaff(:first_name, :last_name, :biography, :photo_location);
SQL;
                        $oStmt = $i_oConn->prepare($sSQLUpload);
                        $oStmt->execute($params);

                    } else { //else a staff id was provided, there fore run the update sql.

                        $params['staff_id'] = $i_staff_id; //If a staff id is provided, append the id to the params array to be executed.

                        $sSQLUpdate = <<<SQL
                          CALL updateStaff(:first_name, :last_name, :biography, :photo_location, :staff_id);
SQL;
                        $oStmt = $i_oConn->prepare($sSQLUpdate);
                        $oStmt->execute($params);
                    }
                        $successful_uploads[] = "$file_destination";
                        echo "<script>alert('File successfully uploaded!')</script>";
                        return true;
                }
            }
            else {
                echo "<script>alert('An error has occured trying to upload [{$file_name}]')</script>";
            }
        } else {
            $failed_uploads[] = "[{$file_name}] is too large.";
            echo "<script>alert('The file {$file_name} is too large.')</script>";
        }
    } else {
        $failed_uploads[] = "[{$file_ext}] file extensions are not supported.";
        echo "<script>alert('The file extension .{$file_ext} is not supported.')</script>";
    }
    return false;
}

function adminCounter($oConn) {
    //Admin counter
    $stmt_count_admins = $oConn->prepare('CALL countAdmins();');
    $stmt_count_admins->execute();

    $admin_count_res = $stmt_count_admins->fetch(PDO::FETCH_OBJ);
    $admin_count = $admin_count_res->fldAdminCount;
    $stmt_count_admins->closeCursor();
    return $admin_count;
//
}

