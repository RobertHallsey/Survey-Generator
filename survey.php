<?php

define('SURVEY_RESET_BUTTON', 'Reset');
define('SURVEY_SUBMIT_BUTTON', 'Submit');
define('SURVEY_RESPONSE_FILE_EXT', '.csv');
define('SURVEY_ERROR_NO_RESPONSE', 'Please answer question #%d');
define('SURVEY_ERROR_EITHER_OR', 'Last option is either/or in question #%d');

function survey_conduct($given_survey = '') {
	session_start();
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		if ($given_survey == '') {
			exit('No survey name specified');
		}
		$survey = new Survey($given_survey);
		$error = $survey->load_survey_file();
		if ($error) exit($error);
		$survey->prefill_survey_responses();
		$_SESSION['survey'] = $survey;
	}
	else {
		if (!isset($_SESSION['survey'])) {
			exit('Survey is finished');
		}
		$survey = $_SESSION['survey'];
		$survey->update_survey_data($_POST['survey_data']);
		if ($survey->validate_errors() == 0) {
			$survey->save_data();
			unset($_SESSION['survey']);
		}
	}
	$survey->render_form();
}

function survey_summarize($given_survey = '') {
	if ($given_survey == '') {
		exit('No survey name specified');
	}
	$survey = new Survey($given_survey);
	$error = $survey->load_survey_file();
	if ($error) exit($error);
	$error = $survey->load_survey_responses();
	if ($error) exit($error);
	$survey->summarize_responses();
	$html = $survey->build_summary($given_survey);
	echo $html;
}
	
class Survey {
	
	protected $survey_name = '';
	protected $survey_data = array();
	protected $question_number = 1;
	protected $error = 0;
	protected $timestamp = 0;
	protected $js_function = 'formReset';
	
	function __construct($survey_arg = '') {
		if ($survey_arg) {
			$this->survey_name = realpath('public/' . $survey_arg);
			if ($this->survey_name === FALSE) {
				$this->survey_name = realpath($survey_arg);
				if ($this->survey_name === FALSE) {
					exit('Survey file not found');
				}
			}
		}
	}
	
	function load_survey_file() {
		if (!file_exists($this->survey_name)) {
			return 'Survey file not there';
		}
		//check the survey file for errors
		if (($this->survey_data = parse_ini_file($this->survey_name, TRUE)) == FALSE) {
			return 'Cannot parse survey file';
		}
		foreach ($this->survey_data as $section_name => $section_data) {
			if (!array_key_exists('type', $section_data)) {
				return 'Section ' . $section_name . ' missing Type property.';
			}
			if (!array_key_exists('questions', $section_data) ||
				 (!is_array($section_data['questions']))) {
				return 'Section ' . $section_name . ' questions missing or malformed.';
			}
			if (!array_key_exists('answers', $section_data) ||
				 (!is_array($section_data['answers']))) {
				return 'Section ' . $section_name . ' answers missing or malformed.';
			}
		}
	}

	function prefill_survey_responses() {
		// pre-fill with blank responses
		foreach ($this->survey_data as $section_name => $section_data) {
			switch ($this->survey_data[$section_name]['type']) {
			case 1:
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

	function load_survey_responses() {
		// load CSV file into $responses[]
		$response_file = $this->survey_name . SURVEY_RESPONSE_FILE_EXT;
		if (!file_exists($response_file)) {
			return 'Survey response file not found';
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
				return 'File has lines of different value counts';
			}
		}
		$this->response_count = count($responses);
		// load $responses[] into $survey array
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
	
	function render_form() {
		$view = new View;
//		echo $this->build_header(),
		$error_msg = '';
		if ($this->error) {
			$error_msg = (($this->error > 0)
				? sprintf(SURVEY_ERROR_NO_RESPONSE, $this->error)
				: sprintf(SURVEY_ERROR_EITHER_OR, -$this->error));
		}
		$error_question = abs($this->error);
		$variables = array(
			'error_msg' => $error_msg,
			'error_question' => $error_question
		);
		$html = $view->create_html('surv_head', $variables);
//		$this->build_body(),
		$this->question_number = 1;
		foreach ($this->survey_data as $section_name => $section_data) {
			$variables = array(
				'heading' => ((isset($this->survey_data[$section_name]['title']))
					 ? $this->survey_data[$section_name]['title'] : ''),
				'number' => $this->question_number,
				'name' => $section_name,
				'data' => $section_data,
			);
			$question_type = 'question_type' . $section_data['type'];
			$html .= $view->create_html($question_type, $variables);
			$this->question_number += count($section_data['questions']);
		}
//		$this->build_footer();
		$execute = (($this->js_function == '') ? '' : $this->js_function . '();');
		$variables = array(
			'execute' => $execute,
			'disabled' => ($this->js_function == 'formDisable'),
			'timestamp' => $this->timestamp,
		);
		$html .= $view->create_html('surv_foot', $variables);
		echo $html;
	}
	
	function update_survey_data($data) {
		foreach ($data as $section_name => $section_data) {
			$this->survey_data[$section_name]['responses'] = $section_data['responses'];
		}
	}

	function validate_errors() {
		$this->question_number = 1;
		$this->error = 0;
		$this->js_function = '';
		foreach ($this->survey_data as $section_name => $section_data) {
			$validate_function = 'validate_type' . $section_data['type'];
			$this->error = $this->$validate_function($this->question_number, $section_data['responses']);
			if ($this->error) break;
			$this->question_number += count($section_data['responses']);
		}
		return $this->error;
	}

	function validate_type1($question_number, $responses) {
		foreach ($responses as $response) {
			if ($response == 0) {
				return $question_number;
			}
			$question_number++;
		}
		return 0;
	}

	function validate_type2($question_number, $responses) {
		return (($responses[0] == 0) ? $question_number : 0);
	}

	function validate_type3($question_number, $responses) {
		$array_size = count($responses) - 1;
		if (in_array(1, array_slice($responses, 0, $array_size)) && $responses[$array_size] == 1) {
			return -$question_number;
		}
		if (!in_array(1, $responses)) {
			return $question_number;
		}
		return 0;
	}

	function save_data() {
		$this->timestamp = time();
		$cur_line = '"' . date('Y-m-d', $this->timestamp) . '",' .
					'"' . date('H:i:s', $this->timestamp) . '"';
		foreach ($this->survey_data as $section_name => $section_data) {
			foreach ($section_data['responses'] as $response) {
				$cur_line .= ',' . $response;
			}
		}
		$cur_line .= "\r\n";
		$file_handle = fopen($this->survey_name . SURVEY_RESPONSE_FILE_EXT, 'a');
		fwrite($file_handle, $cur_line);
		fclose($file_handle);
		touch($this->survey_name . SURVEY_RESPONSE_FILE_EXT, $this->timestamp);
		$this->js_function = 'formDisable';
	}

	function summarize_responses() {
		// summarize responses in $this->survey_data array
		foreach ($this->survey_data as $section_name => $section_data) {
			$summarize_function = 'summarize_type' . $this->survey_data[$section_name]['type'];
			$this->survey_data[$section_name]['summary'] =
				$this->$summarize_function($section_name, $section_data);
		}
	}

	function summarize_type1($section_name, $section_data) {
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

	function summarize_type2($section_name, $section_data) {
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

	function summarize_type3($section_name, $section_data) {
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

	function build_summary($survey_file) {
		$html = '';
		$view = new View;
		$variables = array(
			'response_count' => $this->response_count,
		);
		$html .= $view->create_html('summary_head', $variables);
		$this->question_number = 1;
		foreach ($this->survey_data as $section_name => $section_data) {
			$summary_type = 'summary_type' . $this->survey_data[$section_name]['type'];
			$variables = array(
				'question_number' => $this->question_number,
				'data' => $section_data,
				'response_count' => $this->response_count,
			);
			$html .= $view->create_html($summary_type, $variables);
			$this->question_number += count($section_data['questions']);
		}
		$variables = array();
		$html .= $view->create_html('summary_foot', $variables);
		return $html;
	}

}

class View {

	public $template = array();

	function __construct() {
$this->template['surv_head'] = <<<'SURV_HEAD'
<div id="sf"><!-- sf survey form -->

<?php if ($error_question): ?>
<p><?php echo $error_msg ?></p>

<?php endif; ?>
<form id="survey" method="post">
SURV_HEAD;

$this->template['surv_foot'] = <<<'SURV_FOOT'
<?php if (!$disabled): ?>
<p><input type="reset" value="<?php echo SURVEY_RESET_BUTTON ?>"><input type="submit" name="submit" value="<?php echo SURVEY_SUBMIT_BUTTON ?>"></p>

<?php endif; ?>
</form>

<?php if ($execute): ?>

<script type="text/javascript">
	function formDisable() {
		var form = document.getElementById("survey");
		var elements = form.elements;
		for (var i = 0, len = elements.length; i < len; i++) {
			elements[i].disabled = true;
		}
	}
	function formReset() {
		this.form.reset()
	}
	<?php echo $execute ?>

</script>

<?php endif; ?>

</div><!-- sf survey form -->

SURV_FOOT;

$this->template['question_type1'] = <<<'QUESTION_TYPE1'


<?php if ($heading): ?>

<p><?php echo $heading ?></p>
<?php endif; ?>			
<table class="type1">
  <colgroup>
  	<col span="1">
    <col span="<?php echo count($data['answers']) ?>">
  </colgroup>
  <thead>
    <tr>
      <th scope="row"><?php echo $data['help'] ?></th>
<?php foreach ($data['answers'] as $answer): ?>
      <th scope="col"><?php echo $answer ?></th>
<?php endforeach; ?>
    </tr>
  </thead>
  <tbody>
<?php foreach ($data['questions'] as $q_index => $question): ?>
    <tr>
      <th scope="row"><?php echo $number ?>. <?php echo $question ?><input type="hidden" name="survey_data[<?php echo$name ?>][responses][<?php echo $q_index ?>]" value="0"></th>
<?php foreach ($data['answers'] as $a_index => $answer): ?>
      <td><input type="radio" aria-label="<?php echo $question ?>: <?php echo $answer ?>" name="survey_data[<?php echo $name ?>][responses][<?php echo $q_index ?>]" value="<?php echo $a_index + 1?>"<?php echo (($data['responses'][$q_index] == $a_index + 1) ? ' checked' : '') ?>></td>
<?php endforeach; ?>
    </tr>
<?php $number++; ?>
<?php endforeach; ?>
  </tbody>
</table>
QUESTION_TYPE1;

$this->template['question_type2'] = <<<'QUESTION_TYPE2'


<?php if ($heading): ?>

<p><?php echo $heading ?></p>
<?php endif; ?>			
<fieldset class="type2">
  <legend><?php echo $number ?>. <?php echo $data['questions'][0]?></legend>
  <input type="hidden" name="survey_data[<?php echo $name ?>][responses][0]" value="0">
<?php foreach ($data['answers'] as $a_index => $answer): ?>
  <input type="radio" id="Q<?php echo $number ?><?php echo $a_index ?>" name="survey_data[<?php echo $name ?>][responses][0]" value="<?php echo ($a_index + 1)?>"<?php echo (($data['responses'][0] == $a_index + 1) ? ' checked' : '')?>>
  <label for="Q<?php echo $number ?><?php echo $a_index ?>"><?php echo $answer ?></label><?php echo (($a_index + 1 < count($data['answers'])) ? '<br>' : '')?>

<?php endforeach; ?>
</fieldset>
QUESTION_TYPE2;

$this->template['question_type3'] = <<<'QUESTION_TYPE3'


<?php if ($heading): ?>

<p><?php echo $heading ?></p>
<?php endif; ?>			
<fieldset class="type3">
  <legend><?php echo $number ?>. <?php echo $data['questions'][0] ?></legend>
<?php foreach ($data['answers'] as $a_index => $answer): ?>
  <input type="hidden" name="survey_data[<?php echo $name ?>][responses][<?php echo $a_index ?>]" value="0">
  <input type="checkbox" id="Q<?php echo $number ?><?php echo $a_index ?>" name="survey_data[<?php echo $name ?>][responses][<?php echo $a_index ?>]" value="1"<?php echo (($data['responses'][$a_index] == 1) ? ' checked' : '') ?>>
  <label for="Q<?php echo $number ?><?php echo $a_index ?>"><?php echo $answer ?></label><?php echo (($a_index + 1 < count($data['answers'])) ? '<br>' : '') ?>

<?php endforeach; ?>
</fieldset>
QUESTION_TYPE3;

$this->template['summary_head'] = <<<'SUMMARY_HEAD'

<div id="ss"><!-- ss survey summary -->

<p><?php echo 'Total Responses:'?> <?php echo $response_count ?></p>

SUMMARY_HEAD;

$this->template['summary_type1'] = <<<'SUMMARY_TYPE1'

<?php $colspan = count($data['answers']) + 1 ?>
<?php if (isset($data['title'])): ?>

<p><?php echo $data['title'] ?></p>
<?php endif; ?>

<table class="type1">
  <colgroup>
    <col span="1">
    <col span="<?php echo $colspan ?>">
    <col span="<?php echo $colspan ?>">
  </colgroup>
  <thead>
    <tr>
      <th scope="row" rowspan="2"><?php echo (isset($data['help']) ? $data['help'] : '')?></th>
      <th class="col" scope="col" colspan="<?php echo $colspan ?>">Responses</th>
      <th class="col" scope="col" colspan="<?php echo $colspan ?>">Percentage</th>
    </tr>
    <tr>
<?php foreach ($data['answers'] as $k => $answer): ?>
<?php $class = ($k == 0) ? ' class="col"' : '' ?>
      <th<?php echo $class ?> scope="col"><?php echo $answer ?></th>
<?php endforeach; ?>
      <th scope="col">Tot</th>
<?php foreach ($data['answers'] as $k => $answer): ?>
<?php $class = ($k == 0) ? ' class="col"' : '' ?>
      <th<?php echo $class ?> scope="col"><?php echo $answer ?></th>
<?php endforeach; ?>
      <th scope="col">Tot</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($data['summary'] as $ks => $summary): ?>
    <tr>
      <th scope="row"><?php echo $question_number++ ?>. <?php echo $data['questions'][$ks] ?></th>
<?php $max1 = max(array_slice($summary, 0, $colspan - 1));
      $max2 = max(array_slice($summary, $colspan - 1, $colspan - 1)); ?>
<?php foreach ($summary as $ke => $element): ?>
<?php if (($ke <= $colspan - 2 && $element == $max1) || $ke > $colspan - 2 && $element == $max2):
          $tag1 = '<strong>';
          $tag2 = '</strong>';
      else:
          $tag1 = '';
          $tag2 = '';
      endif; ?>
<?php $class = ($ke == 0 || $ke == $colspan - 1) ? ' class="col"' : '' ?>
      <td<?php echo $class ?>><?php echo $tag1, $element, $tag2 ?></td>
<?php if ($ke == $colspan - 2): // response totals column data ?>
      <td><?php echo $response_count ?></td>
<?php endif; ?>
<?php endforeach; // percentage totals column data ?>
      <td>100</td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>

SUMMARY_TYPE1;

$this->template['summary_type2'] = <<<'SUMMARY_TYPE2'

<?php if (isset($data['title'])): ?>

<p><?php echo $data['title'] ?></p>
<?php endif; ?>

<table class="type2">
  <colgroup>
    <col span="1">
    <col span="2">
  </colgroup>
  <thead>
    <tr>
      <th scope="col"><?php echo $question_number++ ?>. <?php echo $data['questions'][0] ?></th>
      <th scope="col">R</th>
      <th scope="col">%</th>
    </tr>
  </thead>
  <tbody>
<?php $index = count($data['summary']) - 1;
      $max = 0;
      foreach ($data['summary'] as $summary):
          $max = (($summary[0] > $max) ? $summary[0] : $max);
      endforeach; ?>
<?php foreach ($data['summary'] as $ks => $summary): ?>
    <tr<?php echo ($ks == $index) ? '' : '' ?>>
      <th scope="row"><?php echo $data['answers'][$ks] ?></th>
<?php if ($summary[0] == $max):
          $tag1 = '<strong>';
          $tag2 = '</strong>';
      else:
          $tag1 = '';
          $tag2 = '';
      endif; ?>
      <td><?php echo $tag1, $summary[0], $tag2 ?></td>
      <td><?php echo $tag1, $summary[1], $tag2 ?></td>
    </tr>
<?php endforeach; ?>
    <tr>
      <th scope="row">Total</th>
      <td><?php echo $response_count ?></td>
      <td>100</td>
    </tr>
  </tbody>
</table>

SUMMARY_TYPE2;

$this->template['summary_type3'] = <<<'SUMMARY_TYPE3'

<?php if (isset($data['title'])): ?>

<p><?php echo $data['title'] ?></p>
<?php endif; ?>

<table class="type3">
  <colgroup>
    <col span="1">
    <col span="2">
  </colgroup>
  <thead>
    <tr>
      <th scope="col"><?php echo $question_number++ ?>. <?php echo $data['questions'][0] ?></th>
      <th scope="col">R</th>
      <th scope="col">%</th>
    </tr>
  </thead>
  <tbody>
<?php $index = count($data['summary']) - 1;
      $max = 0;
      foreach ($data['summary'] as $summary):
          $max = (($summary[0] > $max) ? $summary[0] : $max);
      endforeach; ?>
<?php foreach ($data['summary'] as $ks => $summary): ?>
    <tr<?php echo ($ks == $index) ? '' : '' ?>>
      <th scope="row"><?php echo $data['answers'][$ks] ?></th>
<?php if ($summary[0] == $max):
          $tag1 = '<strong>';
          $tag2 = '</strong>';
      else:
          $tag1 = '';
          $tag2 = '';
      endif; ?>
      <td><?php echo $tag1, $summary[0], $tag2 ?></td>
      <td><?php echo $tag1, $summary[1], $tag2 ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>

SUMMARY_TYPE3;

$this->template['summary_foot'] = <<<'SUMMARY_FOOT'


</div><!-- ss:survey summary -->

SUMMARY_FOOT;

	}
	
	function create_html($tname, $vlist) {
		extract($vlist);
		ob_start();
		eval('?>' . $this->template[$tname]);
        return ob_get_clean();
	}
	
}