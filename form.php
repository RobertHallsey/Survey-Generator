<?php
/**
* Included by survey.php and confirm.php:
*   Generates the HTML that makes up the form
*
* @author    Robert Hallseey <rhallsey@yahoo.com>
* @copyright 2014
*
* This file needs no customizing to install.
*
*/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
  "http://www.w3.org/TR/html4/strict.dtd">

<html>

<head>
  <title>Survey Form</title>
  <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
  <style>
    h1,
    h2,
    h3 {
      font-family: Verdana, Arial, Helvetica, sans-serif;
    }
    h3 {
      margin-bottom: 0;
      margin-top: 40px;
    }
    p {
      margin-top: 20px;
    }
    form p {
      font-size: 85%;
    }
    table {
      margin-top: 20px;
      font-size: 85%;
      border-collapse:collapse;
    }
    th {
      font-weight: normal;
      vertical-align: bottom;
      border-bottom: 1px solid;
    }
    td {
      text-align: center;
    }
    th:first-child,
    td:first-child {
      text-align: left;
    }
    th:nth-child(n+2),
    td:nth-child(n+2) {
      padding-left: 20px;
    }
  </style>
</head>

<body>
<?php
  echo '<h2>', $survey['meta']['name'], '</h2>', "\n\n";
  if ($caller == 'survey') {
    echo '<p>', $survey['meta']['hello'], '</p>';
    if ($error_msg) {
      echo '<p>', $error_msg, '</p>';
    }
  }
  else {
    echo '<p>', $survey['meta']['goodbye'], '</p>';
  }
  echo "\n\n", '<form name="form" method="post">', "\n\n";
  $qnum = 1;
  foreach ($survey as $section_name => $section_data) {
    if ($section_name != 'meta') {
      if (isset($survey[$section_name]['title'])) {
        echo '<h3>', $survey[$section_name]['title'], '</h3>', "\n\n";
      }
      switch ($section_data['type']) {
      case 1: // 1 = Multiple choice, single answer, panel
        // output any help in table header
        echo '<p><table><thead>', "\n",
          '<tr><th>',
          (isset($section_data['help']) ? $section_data['help'] : ''), 
          '</th>', "\n";
        foreach ($section_data['answers'] as $k => $answer) {
          echo '<th>', $answer, '</th>';
        }
        echo '</tr></thead><tbody>', "\n\n";
        // output each question followed by radio buttons
        foreach ($section_data['questions'] as $sect_qnum => $question) {
          echo '<tr><td>', $qnum, '. ', $question,
            '<input type="hidden" ',
            'name="', $section_name, '[responses][', $sect_qnum, ']" ',
            'value="0"></td>', "\n";
          foreach ($section_data['answers'] as $k => $answer) {
            echo '<td>', '<input id="Q', $qnum, '.', $k, '" type="radio" ', 
              'title="', $question, ': ', $answer, '" ',
              'name="', $section_name, '[responses][', $sect_qnum, ']" ',
              'value="', $k, '"', 
              (($survey[$section_name]['responses'][$sect_qnum] == $k) ? ' checked="checked"' : ''),
              (($caller == 'confirm') ? ' disabled="disabled"' : ''), '>',
              (($k < count($section_data['answers']) - 1) ? '</td>'. "\n" : '</td></tr>'. "\n\n");
          }
          $qnum++;
        }
        echo '</tbody></table></p>', "\n\n";
        break;
      case 2: // 2 = Multiple choice, single answer, stand alone
        // output question
        echo '<p>', $qnum++, '. ', $section_data['questions'][0], '<br />', "\n",
          '<input type="hidden" ', 'name="', $section_name, '[responses][0]" ',
          'value="0">', "\n";
        // output answer choices
        foreach ($section_data['answers'] as $k => $answer) {
          echo '<input type="radio" ',
            'title="', $section_data['questions'][0], ': ', $answer, '" ',
            'name="', $section_name, '[responses][0]" value="', ($k + 1), '"',
            (($survey[$section_name]['responses'][0] == $k + 1) ? ' checked="checked"' : ''),
            (($caller == 'confirm') ? ' disabled="disabled"' : ''), '>', $answer,
            (($k < count($section_data['answers']) - 1) ? '<br />' : '</p>' . "\n"), "\n";
        }
        break;
      case 3: // 3 = Multiple choice, multiple answer, stand alone
        // output question
        echo '<p>', $qnum++, '. ', $section_data['questions'][0], '<br />', "\n";
        // output answer choices
        $num_answers = count($section_data['answers']) - 1;
        foreach ($section_data['answers'] as $k => $answer) {
          echo '<input type="hidden" ', 
            'name="', $section_name, '[responses][', $k, ']" ',
            'value="0">', "\n",
            '<input type="checkbox" ',
            'title="', $section_data['questions'][0], ': ', $answer, '" ',
            'name="', $section_name, '[responses][', $k, ']" value="1"',
            (($survey[$section_name]['responses'][$k] == 1) ? ' checked="checked"' : ''),
            (($caller == 'confirm') ? ' disabled="disabled"' : ''), '>', $answer,
            (($k < count($section_data['answers']) - 1) ? '<br />' : '</p>' . "\n"), "\n";
        }
        break;
      }
    }
  }
  if ($caller == 'survey') {
    echo '<p><input type="reset" value="Clear form and start over" />', "\n",
      '<input type="submit" name="submit" value="Done!" />', "\n";
  }
  else {
    echo '<p>Validation Timestamp: ', date('Y-m-d H:i:s', $timestamp), '<br />',
         '<a href="', RH_BASE_URL, 'return.php', '">', $survey['meta']['exit_msg'], '</a></p>', "\n";
  }
  echo '</form>', "\n";
?>
</body>

</html>
