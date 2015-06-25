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
<?php if (isset($data['title'])): ?>

<h3><?php echo $data['title'] ?></h3>
<?php endif; ?>
<table class="type2">
  <colgroup>
    <col span="1">
    <col span="2">
  </colgroup>
  <thead>
    <tr>
      <th scope="col"><?php echo $question_number++ ?>. <?php echo $data['questions'][0] ?></th>
      <th scope="col">R</th>
      <th scope="col">%</th>
    </tr>
  </thead>
  <tbody>
<?php $index = count($data['summary']) - 1;
      $max = 0;
      foreach ($data['summary'] as $summary):
          $max = (($summary[0] > $max) ? $summary[0] : $max);
      endforeach; ?>
<?php foreach ($data['summary'] as $ks => $summary): ?>
    <tr<?php echo ($ks == $index) ? '' : '' ?>>
      <th scope="row"><?php echo $data['answers'][$ks] ?></th>
<?php if ($summary[0] == $max):
          $tag1 = '<strong>';
          $tag2 = '</strong>';
      else:
          $tag1 = '';
          $tag2 = '';
      endif; ?>
      <td><?php echo $tag1, $summary[0], $tag2 ?></td>
      <td><?php echo $tag1, $summary[1], $tag2 ?></td>
    </tr>
<?php endforeach; ?>
    <tr>
      <th scope="row">Total</th>
      <td><?php echo $response_count ?></td>
      <td>100</td>
    </tr>
  </tbody>
</table>

