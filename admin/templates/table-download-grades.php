<div id="tableprueba" style="display: none;">
<table>
  <tr>
    <th>Subject</th>
    <th>Calification</th>
  </tr>
  <?php foreach (json_decode($projection->projection) as $key => $projection_for) { ?>
    <tr>
      <td><?= $projection_for->subject ?></td>
      <td><?= $projection_for->calification ?></td>
    </tr>
  <?php } ?>
</table>
</div>