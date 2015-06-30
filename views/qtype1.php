<?php

/**
 * The Survey Generator makes it easy to conduct custom surveys.
 *
 * This file is part of the Survey Generator.
 * @author Robert Hallsey <rhallsey@yahoo.com>
 * @copyright Robert Hallsey, 2015
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 license
 */
?>


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
      <th scope="row"><?php echo $data['help']; ?></th>
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

