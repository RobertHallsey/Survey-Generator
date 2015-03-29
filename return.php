<?php
/**
* Redirects to confirm.php:
*   Unsets session variable for clean slate
*   Redirects after survey is submitted
*
* @author    Robert Hallsey <rhallsey@yahoo.com>
* @copyright 2014
*
* To install the system, customize include.php appropriately, and if necessary,
* customize the first line of code in each PHP file to include include.php correctly.
*
*/
  session_start();
  $location = $_SESSION['survey']['meta']['exit_url'];
  unset($_SESSION['survey']);
  unset($_SESSION['form_status']);
  unset($_SESSION['timestamp']);
  unset($_SESSION['survey_file']);
  header('Location: ' . $location);
  exit;
?>
