<h2>Groupes</h2>

<ul class="admin">
  <li><a class="add" href="<?= $this->url("users/groupes/add") ?>">Nouveau groupe</a></li>
</ul>

<?php if($this->out["groupes"]["list"]) : ?>

<?php $items = "groupes"; $legend = "groupes"; require $this->out_file("views/navig.php"); ?>

<table class="admin">
  <tr>
    <th>nom</th>
    <th align="center" colspan="2">actions</th>
  </tr>
<?php foreach($this->out["groupes"]["list"] as $id_groupe => $groupe) : ?>
  <tr class="hl">
    <td><?= $groupe["nom"] ?></td>
    <td class="action">
    <a href="<?= $this->url("users/groupes/edit", array("id" => $id_groupe)) ?>"
       class="admin_link"
       title="modifier ce groupe"><img src="<?= $this->out_file("icons/edit.gif") ?>" /></a>
    </td>
    <td class="action">
    <a href="<?= $this->url("users/groupes/del", array("id" => $id_groupe)) ?>"
       class="admin_link"
       title="supprimer ce groupe"><img src="<?= $this->out_file("icons/del.gif") ?>"
       onclick="return confirm('Supprimer ce groupe ?')"/></a>
    </td>
  </tr>
<?php endforeach; ?>
</table>

<?php $items = "groupes"; $legend = "groupes"; require $this->out_file("views/navig.php"); ?>

<?php else : ?>
<p>Aucun groupe pour le moment</p>
<?php endif; ?>
