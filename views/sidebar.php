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
<?php

/**
 * The Survey plugin makes it easy to conduct custom surveys.
 *
 * @author Robert Hallsey <rhallsey@yahoo.com>
 * @copyright Robert Hallsey, 2008
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 license
 */
?>
<p class="button">
  <a href="<?php echo get_url('plugin/survey/index/'); ?>">
	<img src="<?php echo PLUGINS_PATH . 'survey/icons/home.png' ?>" align="middle" alt="home">
    <?php echo __('Home'); ?>
  </a>
</p>

<p class="button">
  <a href="<?php echo get_url('plugin/survey/browse/'); ?>">
    <img src="<?php echo PLUGINS_PATH . 'survey/icons/summaries.png' ?>" align="middle" alt="summaries">
    <?php echo __('Summaries'); ?>
  </a>
</p>

<p class="button">
  <a href="<?php echo get_url('plugin/survey/documentation/'); ?>">
	<img src="<?php echo PLUGINS_PATH . 'survey/icons/documentation.png' ?>" align="middle" alt="documentation">
    <?php echo __('Documentation'); ?>
  </a>
</p>
