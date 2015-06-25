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

<div id="sf"><!-- sf survey form -->
<?php if ($error_question): ?>
<p><?php echo $error_msg ?></p>

<?php endif; ?>
<form id="form" method="post">
<input type="hidden" name="survey_file" value="<?php echo $survey_file ?>">
<input type="hidden" name="survey_save" value="<?php echo $survey_save ?>">


