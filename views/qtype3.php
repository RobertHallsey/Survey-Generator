<?php

/**
 * This file is a view file from the Survey Generator system.
 *
 * @author Robert Hallsey <rhallsey@yahoo.com>
 * @copyright Robert Hallsey, 2015
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 license
 */
?>


<?php if ($heading): ?>

<p><?php echo $heading ?></p>
<?php endif; ?>			
<fieldset class="type3">
  <legend><?php echo $number ?>. <?php echo $data['questions'][0]?></legend>
<?php foreach ($data['answers'] as $a_index => $answer): ?>
  <input type="hidden" name="survey_data[<?php echo $name ?>][responses][<?php echo $a_index ?>]" value="0">
  <input type="checkbox" id="Q<?php echo $number ?><?php echo $a_index ?>" name="survey_data[<?php echo $name ?>][responses][<?php echo $a_index ?>]" value="1"<?php echo (($data['responses'][$a_index] == 1) ? ' checked' : '') ?>>
  <label for="Q<?php echo $number ?><?php echo $a_index ?>"><?php echo $answer ?></label><?php echo (($a_index + 1 < count($data['answers'])) ? '<br>' : '') ?>

<?php endforeach; ?>
</fieldset>

