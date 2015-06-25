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
<h1><?php echo __('Survey'); ?></h1>

<p>The Survey Plugin lets you conduct custom surveys within your Wolf CMS pages. You must first create a Survey Description File and place it in the public/ directory. Suppose you call that file "my_survey." To conduct the survey within a Wolf CMS page, you would place the following code in the page.</p>

<code>
&lt;?php
	if (Plugin::isEnabled('survey_conduct')) survey_conduct('my_survey');
?&gt;
</code>

<p>Survey responses are collected in the file "my_survey.csv," also found in the public/ directory.</p>
<p>Please see the online documentation page for more information.</p>

<hr>

<p>The Survey Plugin for Wolf CMS is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.</p>

<p>The Survey Plugin for Wolf CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.</p>

<p>You should have received a copy of the GNU General Public License along with this program.  If not, see <a href="https://www.gnu.org/licenses/gpl.html">https://www.gnu.org/licenses/gpl.html</a>.</p>

<p>Copyright Robert Hallsey, 2015<br>
  Contact the author at <a href="mailto:rhallsey@yahoo.com?Subject=Survey Plugin for Wolf CMS">rhallsey@yahoo.com</a>
