<?php

// Remember to set SURVEY_BASE_PATH somewhere!

class Survey {

	const SURVEY_VIEWS = SURVEY_BASE_PATH . 'views/';
	const SURVEY_RESET_BUTTON = 'Reset';
	const SURVEY_SUBMIT_BUTTON = 'Submit';
	const SURVEY_RESPONSE_FILE_EXT = 'csv';
	const SURVEY_ERROR_NO_RESPONSE = 'Please answer question #%d';
	const SURVEY_ERROR_EITHER_OR = 'Last option is either/or in question #%d';

	protected $survey_file = '';
	protected $survey_data = array();
	protected $js_function = 'formReset';
	protected $error = 0;
	protected $timestamp = 0;
	protected $response_count = 0;
	
	public function __construct($survey_arg = '') {
		if ($survey_arg == '') {
			exit(__('No survey name specified'));
		}
		$this->survey_file = realpath($survey_arg);
		if ($this->survey_file === FALSE) {
			exit(__('Survey file not found'));
		}
	}

	public function prepareSurvey() {
		$error = $this->loadSurveyFile();
		if ($error) exit($error);
		$this->prefillSurveyResponses();
	}
	
	public function loadSurveyFile() {
		if (!file_exists($this->survey_file)) {
			return __('Survey file not found');
		}
		//check the survey file for errors
		if (($this->survey_data = parse_ini_file($this->survey_file, TRUE)) == FALSE) {
			return __('Cannot parse survey file');
		}
		foreach ($this->survey_data as $section_name => $section_data) {
			if (!array_key_exists('type', $section_data)) {
				return __("Section $section_name has no Type property.");
			}
			if (!array_key_exists('questions', $section_data) ||
				 (!is_array($section_data['questions']))) {
				return __("Section $section_name has missing or malformed questions.");
			}
			if (!array_key_exists('answers', $section_data) ||
				 (!is_array($section_data['answers']))) {
				return __("Section $section_name has missing or malformed answers.");
			}
		}
	}

	public function prefillSurveyResponses() {
		// pre-fill with blank responses
		foreach ($this->survey_data as $section_name => $section_data) {
			switch ($this->survey_data[$section_name]['type']) {
			case 1:
				if ( ! array_key_exists('help', $this->survey_data[$section_name])) {
					$this->survey_data[$section_name]['help'] = '';
				}
				// no break here;
			case 2:
				$this->survey_data[$section_name]['responses'] =
					array_fill(0, count($this->survey_data[$section_name]['questions']), 0);
				break;
			case 3:
				$this->survey_data[$section_name]['responses'] =
					array_fill(0, count($this->survey_data[$section_name]['answers']), 0);
				break;
			}
		}
	}

	public function processSurvey($survey_save, $survey_data) {
		$status = FALSE;
		$this->survey_data =
			array_replace_recursive(
				unserialize(base64_decode($survey_save)),
				$survey_data);
		if ($this->validateErrors() == 0) {
			$this->saveData();
			$status = TRUE;
		}
		return $status;
	}
	
	public function validateErrors() {
		$question_number = 1;
		$this->error = 0;
		$this->js_function = '';
		foreach ($this->survey_data as $section_name => $section_data) {
			$validate_function = 'validateType' . $section_data['type'];
			$this->error = $this->$validate_function($question_number, $section_data['responses']);
			if ($this->error) break;
			$question_number += count($section_data['responses']);
		}
		return $this->error;
	}

	private function validateType1($question_number, $responses) {
		foreach ($responses as $response) {
			if ($response == 0) {
				return $question_number;
			}
			$question_number++;
		}
		return 0;
	}

	private function validateType2($question_number, $responses) {
		return (($responses[0] == 0) ? $question_number : 0);
	}

	private function validateType3($question_number, $responses) {
		$array_size = count($responses) - 1;
		if (in_array(1, array_slice($responses, 0, $array_size)) && $responses[$array_size] == 1) {
			return -$question_number;
		}
		if (!in_array(1, $responses)) {
			return $question_number;
		}
		return 0;
	}

	public function saveData() {
		$this->timestamp = time();
		$cur_line = '"' . date('Y-m-d', $this->timestamp) . '",' .
					'"' . date('H:i:s', $this->timestamp) . '"';
		foreach ($this->survey_data as $section_name => $section_data) {
			foreach ($section_data['responses'] as $response) {
				$cur_line .= ',' . $response;
			}
		}
		$cur_line .= "\r\n";
		$file_name = '';
		if (strpos($this->survey_file, '.') !== FALSE) {
			$file_name = substr($this->survey_file, 0, strripos($this->survey_file, '.') + 1);
		}
		else {
			$file_name = $this->survey_file;
		}
		$file_name .= '.' . Self::SURVEY_RESPONSE_FILE_EXT;
		$file_handle = fopen($file_name, 'a');
		fwrite($file_handle, $cur_line);
		fclose($file_handle);
		touch($file_name, $this->timestamp);
		$this->js_function = 'formDisable';
	}
	
	public function theForm() {
		// build header
		$html = '';
		$error_msg = '';
		if ($this->error) {
			$error_msg = (($this->error > 0)
				? sprintf(Self::SURVEY_ERROR_NO_RESPONSE, $this->error)
				: sprintf(Self::SURVEY_ERROR_EITHER_OR, -$this->error));
		}
		$view_file = Self::SURVEY_VIEWS . 'surveyheader';
		$variables = array(
			'survey_file' => $this->survey_file,
			'survey_save' => base64_encode(serialize($this->survey_data)),
			'error_msg' => $error_msg,
			'error_question' => abs($this->error)
		);
		$html .= new View($view_file, $variables);
		// build body
		$question_number = 1;
		foreach ($this->survey_data as $section_name => $section_data) {
			$view_file = Self::SURVEY_VIEWS . 'qtype' . $this->survey_data[$section_name]['type'];
			$variables = array(
				'heading' => ((isset($this->survey_data[$section_name]['title']))
					 ? $this->survey_data[$section_name]['title'] : ''),
				'number' => $question_number,
				'name' => $section_name,
				'data' => $section_data,
			);
			$html .= new View($view_file, $variables);
			$question_number += count($section_data['questions']);
		}
		// build footer
		$js_code = (($this->js_function == '') ? '' : $this->js_function . '();');
		$view_file = Self::SURVEY_VIEWS . 'surveyfooter';
		$variables = array(
			'js_code' => $js_code,
			'disabled' => ($this->js_function == 'formDisable'),
			'timestamp' => $this->timestamp,
		);
		$html .= new View($view_file, $variables);
		return $html;
	}

	public function prepareSummary() {
		$error = $this->loadSurveyFile();
		if ($error) exit($error);
		//$this->prefillSurveyResponses();
		$error = $this->loadSurveyResponses();
		if ($error) exit($error);
		$this->summarizeResponses();
	}

	public function loadSurveyResponses() {
		// load CSV file into $responses[]
		$response_file = $this->survey_file . '.' . Self::SURVEY_RESPONSE_FILE_EXT;
		if (!file_exists($response_file)) {
			return __('Survey response file not found');
		}
		$CSV_count = 0;
		$responses = array();
		$file_handle = fopen($response_file, 'r');
		while (($data = fgetcsv($file_handle)) == TRUE) {
			$responses[] = $data;
			// make sure each line has same number of values
			if ($CSV_count == 0) {
				$CSV_count = count(current($responses));
			}
			if ($CSV_count != count(current($responses))) {
				return __('File has lines of different value counts');
			}
		}
		$this->response_count = count($responses);
		// load $responses[] into survey array
		foreach ($responses as $response) {
			$offset = 2;
			foreach ($this->survey_data as $section_name => $section_data) {
				$section_type = (($this->survey_data[$section_name]['type'] == 3) ? 'answers' : 'questions');
				foreach ($section_data[$section_type] as $k => $v) {
					$this->survey_data[$section_name]['responses'][$k][] = $response[$offset];
					$offset++;
				}
			}
		}
		return '';
	}

	public function summarizeResponses() {
		// summarize responses in $this->survey_data array
		foreach ($this->survey_data as $section_name => $section_data) {
			$summarize_function = 'summarizeType' . $this->survey_data[$section_name]['type'];
			$this->survey_data[$section_name]['summary'] =
				$this->$summarize_function($section_name, $section_data);
		}
	}

	private function summarizeType1($section_name, $section_data) {
		$answer_count = count($section_data['answers']);
		foreach ($section_data['questions'] as $kq => $q) {
			$temp_array = array_count_values($section_data['responses'][$kq]);
			$section_data['summary'][$kq] = array_fill(0, $answer_count * 2, 0);
			foreach ($temp_array as $kt => $temp_value) {
				$kt--;
				$section_data['summary'][$kq][$kt] = $temp_value;
				$section_data['summary'][$kq][$kt + $answer_count] =
					round($temp_value / $this->response_count * 100, 0);
			}
		}
		return $section_data['summary'];
	}

	private function summarizeType2($section_name, $section_data) {
		$answer_count = count($section_data['answers']);
		$temp_array = array_count_values($section_data['responses'][0]);
		$section_data['summary'] = array_fill(0, $answer_count, array (0, 0));
		foreach ($temp_array as $kt => $temp_value) {
			$kt--;
			$section_data['summary'][$kt] = array (
				0 => $temp_value,
				1 => round($temp_value / $this->response_count * 100, 0)
			);
		}
		return $section_data['summary'];
	}

	private function summarizeType3($section_name, $section_data) {
		$answer_count = count($section_data['answers']);
		$temp_array = array_fill(0, count($section_data['responses']), 0);
		foreach ($section_data['responses'] as $kr => $response) {
			foreach ($response as $r) {
				$temp_array[$kr] += $r;
			}
		}
		$section_data['summary'] = array_fill(0, $answer_count, array (0, 0));
		foreach ($temp_array as $kt => $temp_value) {
			$section_data['summary'][$kt][0] = $temp_value;
			$section_data['summary'][$kt][1] =
				round($temp_value / $this->response_count * 100, 0);
		}
		return $section_data['summary'];
	}

	function theSummary() {
		$html = '';
		$view_file = Self::SURVEY_VIEWS . 'summaryheader';
		$variables = array(
			'response_count' => $this->response_count
		);
		$html .= new View($view_file, $variables);
		$question_number = 1;
		foreach ($this->survey_data as $section_name => $section_data) {
			$view_file = Self::SURVEY_VIEWS . 'stype' . $this->survey_data[$section_name]['type'];
			$variables = array(
				'question_number' => $question_number,
				'data' => $section_data,
				'response_count' => $this->response_count,
			);
			$html .= new View($view_file, $variables);
			$question_number += count($section_data['questions']);
		}
		$variables = array();
		$view_file = Self::SURVEY_VIEWS . 'summaryfooter';
    	$html .= new View($view_file, $variables);
		return $html;
	}

}
