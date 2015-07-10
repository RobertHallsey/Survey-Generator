<?php

include 'View.php';
include 'Survey.php';

function survey_conduct($given_survey) {
	if (session_id() == '') session_start();
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		$survey = new Survey($given_survey);
		$survey->prepareSurvey();
		$_SESSION['survey_running'] = TRUE;
	}
	else { // must be POST
		if (!isset($_SESSION['survey_running'])) exit ('No running survey');
		$survey = new Survey($_POST['survey_file']);
		if ($survey->processSurvey($_POST['survey_save'], $_POST['survey_data'])) {
			unset($_SESSION['survey_running']);
		}
	}
	echo $survey->theForm();
}

function survey_summarize($given_survey = '') {
	$survey = new Survey($given_survey);
	$survey->prepareSummary();
	echo $survey->theSummary();
}
	
function survey_name($given_survey = '') {
	if ($given_survey == '' || file_exists($given_survey) == FALSE) {
		return FALSE;
	}
	$survey = new Survey($given_survey);
	$error = $survey->loadSurveyFile();
	unset($survey);
	if ($error) {
		return FALSE;
	}
	$file_handle = fopen($given_survey, 'r');
	$line = trim(fgets($file_handle), " \t\r\n\0\x0B");
	fclose($file_handle);
	if (substr($line, 0, 1) != ';') {
		return FALSE;
	}
	$line = substr($line, 1);
	return $line;
}

function __($input = '') {
	$output = $input;
	return $output;
}
