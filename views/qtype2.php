<?php

/**
 * The Survey Plugin for Wolf CMS makes it easy to conduct custom surveys.
 *
 * This file is part of the Survey Plugin for Wolf CMS.
 *
 * @author Robert Hallsey <rhallsey@yahoo.com>
 * @copyright Robert Hallsey, 2015
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 license
 */
?>


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

