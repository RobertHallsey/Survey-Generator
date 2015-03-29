<?php
/**
* Main file of the system:
*   Reads survey description file
*   Presents survey form to user
*   Validates submited survey form
*   Writes survey responses to file
*   Redirects to confirmation page
*
* @author    Robert Hallsey <rhallsey@yahoo.com>
* @copyright 2014
*
* The system consists of six PHP files:
*   survey.php    Main file of the system, described above
*   confirm.php   Shows completed survey form to user
*   form.php      Included by survey.php and confirm.php
*   return.php    Redirects to survey.php for a new survey
*   include.php   Included by other PHP files
*
* The system uses an arbitrarily named text file in *.ini format that describes
* the survey it will process. See documentation for further details.
*
* To install the system, customize include.php appropriately, and if necessary,
* customize the first line of code in each PHP file to include include.php correctly.
*
*/
  include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include.php';
  if (isset($_GET['file']) == FALSE) {
    echo 'No survey file specified';
    exit;
  }
  session_start();
  $_SESSION['survey_file'] = $_GET['file'];
  $survey_file = RH_BASE_PATH . $_GET['file'];
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // The form was posted
    unset($_POST['submit']);
    $survey = $_SESSION['survey'];
    $error = 0;
    $qnum = 0;
    // Make sure all questions were answered
    foreach ($_POST as $section_name => $section_data) {
      $survey[$section_name]['responses'] = $section_data['responses'];
      switch ($survey[$section_name]['type']) {
      case 1:
        foreach ($section_data['responses'] as $response) {
          $qnum++;
          if ($response == 0) {
            $error = $qnum;
            break;
          }
        }
        break;
      case 2:
        $qnum++;
        if ($section_data['responses'][0] == 0) {
          $error = $qnum;
        }
        break;
      case 3:
        $qnum++;
        $array_size = count($section_data['responses']) - 1;
        if (in_array(1, array_slice($section_data['responses'], 0, $array_size)) &&
            $section_data['responses'][$array_size] == 1) {
          $error = -$qnum;
          break;
        }
        if (!in_array(1, $section_data['responses'])) {
          $error = $qnum;
        }
        break;
      }
      if ($error) {
        break;
      }
    }
    // Fork in road: error or no error
    if ($error) {
      if ($error > 0) {
        $error_msg = 'Please answer question #' . $error;
      }
      elseif ($error < 0) {
        $error_msg = 'Question #' . -$error . ', last option is either/or';
      }
      // show form again
      $caller = 'survey';
      include RH_BASE_PATH . 'form.php';
    }
    else {
      // Save responses to file
      $timestamp = time();
      $file_handle = fopen($survey_file . '-resp.txt', 'a');
      $cur_line = '"' . date('Y-m-d', $timestamp) . '","' . date('H:i:s', $timestamp) . '"';
      foreach ($survey as $section_name => $section_data) {
        if ($section_name != 'meta') {
          foreach ($section_data['responses'] as $response) {
            $cur_line .= ',' . $response;
          }
        }
      }
      $cur_line .= EOL;
      fwrite($file_handle, $cur_line);
      fclose($file_handle);
      touch($survey_file . '-resp.txt', $timestamp);
      $_SESSION['timestamp'] = $timestamp;
      $_SESSION['survey'] = $survey;
      $_SESSION['form_status'] = 'closed';
      // Send user to confirmation page
      header('Location: ' . RH_CONFIRM_PAGE_URL);
      exit;
    }
  }
  else {
    // The form was not posted
    if (isset($_SESSION['form_status'])) {
      // ...yet form_status is set, so must be reload
      if ($_SESSION['form_status'] == 'opened') {
        unset($_SESSION['form_status']);
        header('Location: ' . RH_SURVEY_PAGE_URL . '?file=' . $_SESSION['survey_file']);
        exit;
      }
      else {
        unset ($_SESSION['form_status']);
        header('Location: ' . RH_CONFIRM_PAGE_URL);
        exit;
      }
    }
    else {
      // ...and form_status is not set, so must be first time through
      //check the survey file for errors
      if (!file_exists($survey_file)) {
        echo 'Survey file not there';
        exit;
      }
      if (($survey = parse_ini_file($survey_file, TRUE)) == FALSE) {
        echo 'Cannot parse survey file';
        exit;
      }
      if (array_key_exists('meta', $survey) == FALSE) {
        echo 'Survey file missing meta section';
        exit;
      }
      $error = 0;
      foreach ($survey as $section_name => $section_data) {
        if ($section_name != 'meta') {
          if (!array_key_exists('type', $section_data)) {
            echo 'Section ', $section_name, ' missing Type property.';
            exit;
          }
          if (!array_key_exists('questions', $section_data) ||
             (!is_array($section_data['questions']))) {
            echo 'Section ', $section_name, ' questions missing or malformed.';
            exit;
          }
          if (!array_key_exists('answers', $section_data) ||
             (!is_array($section_data['answers']))) {
            echo 'Section ', $section_name, ' answers missing or malformed.';
            exit;
          }
        }
      }
      // pre-fill with blank responses
      foreach ($survey as $section_name => $section_data) {
        if ($section_name != 'meta') {
          switch ($survey[$section_name]['type']) {
          case 1:
          case 2:
            $survey[$section_name]['responses'] =
              array_fill(0, count($survey[$section_name]['questions']), 0);
            break;
          case 3:
            $survey[$section_name]['responses'] =
              array_fill(0, count($survey[$section_name]['answers']), 0);
            break;
          }
        }
      }
      $error_msg = '';
      $_SESSION['survey'] = $survey;
      $_SESSION['form_status'] = 'opened';
      $caller = 'survey';
      include RH_BASE_PATH . 'form.php';
    }
  }
?>
