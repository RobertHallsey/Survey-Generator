<?php

/**
 * This file is a view file from the Survey Generator system.
 *
 * @author Robert Hallsey <rhallsey@yahoo.com>
 * @copyright Robert Hallsey, 2015
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 license
 */
?>

<div id="sf"><!-- sf survey form -->
<?php if ($error_question): ?>
<p><?php echo $error_msg ?></p>

<?php endif; ?>
<form id="form" method="post">
<input type="hidden" name="survey_file" value="<?php echo $survey_file ?>">
<input type="hidden" name="survey_save" value="<?php echo $survey_save ?>">


