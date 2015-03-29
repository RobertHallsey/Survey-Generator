<?php
/**
* Redirected to by survey.php:
*   Displays the completed and submitted survey form
*
* @author    Robert Hallseey <rhallsey@yahoo.com>
* @copyright 2014
*
* To install the system, customize include.php appropriately, and if necessary,
* customize the first line of code in each PHP file to include include.php correctly.
*
*/
  include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include.php';
  session_start();
  $survey = $_SESSION['survey'];
  $timestamp = $_SESSION['timestamp'];
  $caller = 'confirm';
  $error_msg = '';
  include RH_BASE_PATH . 'form.php';
 ?>
 