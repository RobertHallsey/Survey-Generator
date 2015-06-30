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
<?php $colspan = count($data['answers']) + 1 ?>
<?php if (isset($data['title'])): ?>
<h3><?php echo $data['title'] ?></h3>

<?php endif; ?>
<table class="type1">
  <colgroup>
    <col span="1">
    <col span="<?php echo $colspan ?>">
    <col span="<?php echo $colspan ?>">
  </colgroup>
  <thead>
    <tr>
      <th scope="row" rowspan="2"><?php echo (isset($data['help']) ? $data['help'] : '')?></th>
      <th class="col" scope="col" colspan="<?php echo $colspan ?>">Responses</th>
      <th class="col" scope="col" colspan="<?php echo $colspan ?>">Percentage</th>
    </tr>
    <tr>
<?php foreach ($data['answers'] as $k => $answer): ?>
<?php $class = ($k == 0) ? ' class="col"' : '' ?>
      <th<?php echo $class ?> scope="col"><?php echo $answer ?></th>
<?php endforeach; ?>
      <th scope="col">Tot</th>
<?php foreach ($data['answers'] as $k => $answer): ?>
<?php $class = ($k == 0) ? ' class="col"' : '' ?>
      <th<?php echo $class ?> scope="col"><?php echo $answer ?></th>
<?php endforeach; ?>
      <th scope="col">Tot</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($data['summary'] as $ks => $summary): ?>
    <tr>
      <th scope="row"><?php echo $question_number++ ?>. <?php echo $data['questions'][$ks] ?></th>
<?php $max1 = max(array_slice($summary, 0, $colspan - 1));
      $max2 = max(array_slice($summary, $colspan - 1, $colspan - 1)); ?>
<?php foreach ($summary as $ke => $element): ?>
<?php if (($ke <= $colspan - 2 && $element == $max1) || $ke > $colspan - 2 && $element == $max2):
          $tag1 = '<strong>';
          $tag2 = '</strong>';
      else:
          $tag1 = '';
          $tag2 = '';
      endif; ?>
<?php $class = ($ke == 0 || $ke == $colspan - 1) ? ' class="col"' : '' ?>
      <td<?php echo $class ?>><?php echo $tag1, $element, $tag2 ?></td>
<?php if ($ke == $colspan - 2): // response totals column data ?>
      <td><?php echo $response_count ?></td>
<?php endif; ?>
<?php endforeach; // percentage totals column data ?>
      <td>100</td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>

