<?php
/**
 * Created by PhpStorm.
 * User: Marck
 * Date: 7/09/2016
 * Time: 8:15 PM
 */
require_once 'Lib/Dbconnect.php';
require_once 'Lib/Functions.php';
require_once 'Lib/cUser.php';

session_start();

checkLogout();
$logged_on = returnLogin();
$nav_bar = buildNavBar($logged_on);
$footer = buildFooter();