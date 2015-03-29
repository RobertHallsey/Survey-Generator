<?php
  
function same_keys ($a1, $a2) {
  $same = FALSE;
  if (!array_diff_key($a1, $a2)) {
    $same = TRUE;
    foreach ($a1 as $k => $v) {
      if (is_array($v) && !same_keys($v, $a2[$k])) {
        $same = FALSE;
        break;
      }
    }
  }
  return $same;
}

  include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include.php';
  if (isset ($_GET['file'])) {
    $survey_file = $_GET['file'];
  }
  else {
    echo 'No survey file specified';
    exit;
  }
  $survey = parse_ini_file($survey_file, TRUE);

  // load CSV file into $responses[]
  $responses = array();
  $file_handle = fopen($survey_file . '-resp.txt', 'r');
  $CSV_count = 0;
  while (($data = fgetcsv($file_handle)) == TRUE) {
    $responses[] = $data;
    // make sure each line has same number of values
    if ($CSV_count == 0) {
      $CSV_count = count(current($responses));
    }
    if ($CSV_count != count(current($responses))) {
      echo 'File has lines of different value counts';
      die;
    }
  }

  // load $responses[] into $survey array
  $response_count = count($responses);
  foreach ($responses as $response) {
    $offset = 2;
    foreach ($survey as $section_name => $section_data) {
      if ($section_name != 'meta') {
        $section_type = (($survey[$section_name]['type'] == 3) ? 'answers' : 'questions');
        foreach ($section_data[$section_type] as $k => $v) {
          $survey[$section_name]['responses'][$k][] = $response[$offset];
          $offset++;
        }
      }
    }
  }

  // summarize responses in $survey array
  foreach ($survey as $section_name => $section_data) {
    if ($section_name != 'meta') {
      switch ($survey[$section_name]['type']) {

      // type 1
      case 1:
        $answer_count = count($survey[$section_name]['answers']);
        foreach ($section_data['questions'] as $kq => $q) {
          $temp_array = array_count_values($survey[$section_name]['responses'][$kq]);
          ksort($temp_array, SORT_NUMERIC);
          $max = max($temp_array);
          $survey[$section_name]['summary'][$kq] = array_fill(0, $answer_count * 2, 0);
          foreach ($temp_array as $kt => $temp_value) {
            $kt--;
            $survey[$section_name]['summary'][$kq][$kt] = 
              (($temp_value == $max) ? '<b><i>' . $temp_value . '</i></b>' : $temp_value);
            $survey[$section_name]['summary'][$kq][$kt + $answer_count] = 
              str_replace(' ', '&nbsp;', 
                (($temp_value == $max) 
                ? '<b><i>' . sprintf("%3.0f", round($temp_value / $response_count * 100, 1)) . '</i></b>'
                : sprintf("%3.0f", round($temp_value / $response_count * 100, 1))));
          }
        }
        break;

      // type 2
      case 2:
        $answer_count = count($section_data['answers']);
        $temp_array = array_count_values($section_data['responses'][0]);
        ksort($temp_array, SORT_NUMERIC);
        $max = max($temp_array);
        $survey[$section_name]['summary'] = array_fill(0, $answer_count, array (0, 0));
        foreach ($temp_array as $kt => $temp_value) {
          $kt--;
          $survey[$section_name]['summary'][$kt] = array (
            0 => (($temp_value == $max) ? '<b><i>' . $temp_value . '<i></b>' : $temp_value),
            1 => str_replace(' ', '&nbsp;',
                 (($temp_value == $max) 
                 ? '<b><i>' . sprintf("%3.0f", round($temp_value / $response_count * 100, 1)) . '<i></b>'
                 : sprintf("%3.0f", round($temp_value / $response_count * 100, 1))))
          );
        }
        break;

      // type 3
      case 3:
        $answer_count = count($section_data['answers']);
        $temp_array = array_fill(0, count($section_data['responses']), 0);
        foreach ($section_data['responses'] as $kr => $response) {
          foreach ($response as $k => $r) {
            $temp_array[$kr] += $r;
          }
        }
        ksort($temp_array, SORT_NUMERIC);
        $max = max($temp_array);
        $survey[$section_name]['summary'] = array_fill(0, $answer_count, array (0, 0));
        foreach ($temp_array as $kt => $temp_value) {
          $survey[$section_name]['summary'][$kt][0] =
            (($temp_value == $max) ? '<b><i>' . $temp_value . '</b></i>' : $temp_value);
          $survey[$section_name]['summary'][$kt][1] =
            str_replace(' ', '&nbsp;', 
              (($temp_value == $max)
              ? '<b><i>' . sprintf("%3.0f", round($temp_value / $response_count * 100, 1)) . '<i></b>'
              : sprintf("%3.0f", round($temp_value / $response_count * 100, 1))));
        }
        break;
      }
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
  "http://www.w3.org/TR/html4/strict.dtd">

<html>

<head>
  <title>survey summary</title>
  <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
  <style>
    h1, h2, h3 {
      font-family: Arial, Helvetica, sans-serif;
    }
    h3 {
      margin-bottom: 0;
      margin-top: 40px;
    }
    table {
      margin-bottom: 20px;
      font-size: 85%;
      border-collapse:collapse;
      border: 2px solid;
    }
    th {
      font-weight: normal;
      vertical-align: bottom;
      border: 1px solid;
      text-align: center;
      padding: 2px 5px 2px 5px;
    }
    td {
      border: 1px solid;
      text-align: right;
      padding: 2px 5px 2px 5px;
    }
    th:first-child,
    td:first-child {
      text-align: left;
    }
    td.total-line {
      text-align: right;
    }
    thead,
    tr:nth-child(2n+2) {
      background-color: #f0f0f0;
    }
    th.not-1st-child {
      text-align: center;
    }
  </style>
</head>

<body>

<h1>Survey Summary</h1>
<h2><?= $survey['meta']['name'] ?></h2>
<h3>Total Responses: <?= $response_count ?></h3>

<?php
  $qnum = 1;
  foreach ($survey as $section_name => $section_data) {
    if ($section_name != 'meta') {
      if (isset($survey[$section_name]['title'])) {
        echo '<h3>', $survey[$section_name]['title'], '</h3>', "\n";
      }
      switch ($survey[$section_name]['type']) {

      // type 1
      case 1:
        $colspan = count($section_data['answers']) + 1;
        echo '<table>', "\n",
          '<colgroup><col span="1" style="border-right: 2px solid;"><col span="', ($colspan * 2), '" style="width: 2.5em;">', "\n",
          '<thead>', "\n",
          '<tr><th rowspan="2">',(isset($section_data['help']) ? $section_data['help'] : ''), '</th>', "\n",
          '<th colspan="', $colspan, '" style="border-right: 2px solid;">Responses</th><th colspan="', $colspan, '">Percentage</th></tr>', "\n",
          '<tr><th class="not-1st-child">';
        $temp_string = '';
        foreach ($section_data['answers'] as $answer) {
          $temp_string .= $answer . '</th><th>';
        }
        $temp_string = substr($temp_string, 0, -4) . '<th style="border-right: 2px solid;">Tot.</th>';
        echo $temp_string;
        foreach ($section_data['answers'] as $answer) {
          echo '<th>', $answer, '</th>';
        }
        echo '<th>Tot.</th></tr>', "\n",
          '</thead><tbody>', "\n";
        foreach ($section_data['summary'] as $ks => $summary) {
          echo '<tr><td>', $qnum++, '. ', $section_data['questions'][$ks], '</td>';
          foreach ($summary as $kse => $summary_element) {
            echo '<td>', $summary_element, '</td>',
              (($kse == $colspan - 2) ? '<td style="border-right: 2px solid;">' . $response_count . '</td>' : '');
          }
          echo '<td>100</td></tr>', "\n";
        }
        echo '</tbody></table>', "\n";
        break;

        // type 2
      case 2:
        echo '<table>', "\n",
          '<colgroup><col span="1"><col span="2" style="width: 2.5em;">',
          '<thead><tr><th>', $qnum++, '. ', $section_data['questions'][0], '</th>',
          '<th>R</th><th>%</th></tr></thead>',
          '<tbody>';
        foreach ($section_data['summary'] as $ks => $summary) {
          echo '<tr><td>', $section_data['answers'][$ks], '</td>',
            '<td>', $summary[0], '</td>',
            '<td>', $summary[1], '</td></tr>';
        }
        echo '<tr><td class="total-line">Total</td><td>', $response_count, '</td><td>100</td></tr>',
          '</tbody></table>', "\n";
        break;

        // type 3
      case 3:
        echo '<table>', "\n",
          '<colgroup><col span="1"><col span="2" style="width: 2.5em;">',
          '<thead>', "\n",
          '<thead><tr><th>', $qnum++, '. ', $section_data['questions'][0], '</th>',
          '<th>R</th><th>%</th></tr></thead>',
          '<tbody>';
        foreach ($section_data['summary'] as $ks => $summary) {
          echo '<tr><td>', $section_data['answers'][$ks], '</td>',
            '<td>', $summary[0], '</td>',
            '<td>', $summary[1], '</td></tr>';
        }
        echo '</tbody></table>', "\n";
        break;
      }
    }
  }
?>

</body>

</html>
