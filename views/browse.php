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

<div id="ss">
<div style="float:left; width:25%;">
<h1>Folders</h1>

<p>
<?php if ($up_url[0] === SURVEY_BROWSE && $up_url[1] == 'public'): ?>
  <img src="<?php echo SURVEY_ICONS, 'public.png' ?>" alt="current directory">
  &nbsp;&nbsp;/public
<?php else: ?>
   <a href="<?php echo $up_url[0] ?>">
	<img src="<?php echo SURVEY_ICONS, 'pathup.png' ?>" alt="current directory">
	&nbsp;&nbsp;<?php echo $up_url[1] ?>
  </a>
<?php endif; ?>
</p>

<?php /*
*/ ?>

<?php foreach($dirs as $dir): ?>
<p>
  <a href="<?php echo $dir[0] ?>">
	<img src="<?php echo SURVEY_ICONS, 'folder.png' ?>" alt="folder">
	&nbsp;&nbsp;<?php echo $dir[1] ?>
  </a>
</p>
<?php endforeach; ?>
</div>

<div style="float:right; width: 70%;">
<h1><?php echo __('Survey Summaries') ?></h1>

<?php if ($files): ?>
<?php foreach ($files as $file): ?>
<p>
  <a href="<?php echo $file[0]?>">
	<img src="<?php echo PLUGINS_PATH . 'survey/icons/text.png' ?>" alt="summary">
	&nbsp;&nbsp;<?php echo $file[1] ?>
  </a>
</p>
<?php endforeach; ?>
<?php else: ?>
<p>No survey summaries<br>
	found in this directory</p>
<?php endif; ?>
</div>

</div>
