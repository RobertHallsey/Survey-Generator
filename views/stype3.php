<?php

/**
 * This file is a view file from the Survey Generator system.
 *
 * @author Robert Hallsey <rhallsey@yahoo.com>
 * @copyright Robert Hallsey, 2015
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 license
 */
?>
<?php if (isset($data['title'])): ?>

<p><?php echo $data['title'] ?></p>
<?php endif; ?>

<table class="type3">
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
  </tbody>
</table>

