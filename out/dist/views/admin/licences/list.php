<h2>Les licences</h2>

<ul class="admin">
  <li><a class="add" href="<?= $this->url("admin/licences/add") ?>">Nouvelle licence</a></li>
</ul>

<?php if($this->out["licences"]["list"]) : ?>
<table class="admin">
  <tr>
    <th>nom</th>
    <th align="center" colspan="2">actions</th>
  </tr>
<?php foreach($this->out["licences"]["list"] as $id_licence => $licence) : ?>
  <tr class="hl">
    <td><?= $licence["nom"] ?></td>
    <td class="action">
    <a href="<?= $this->url("admin/licences/edit", array("id" => $id_licence)) ?>"
       class="admin_link"
       title="modifier cette licence"><img src="<?= $this->out_file("icons/edit.gif") ?>" /></a>
    </td>
    <td class="action">
    <a href="<?= $this->url("admin/licences/del", array("id" => $id_licence)) ?>"
       class="admin_link"
       title="supprimer cette licence"><img src="<?= $this->out_file("icons/del.gif") ?>"
       onclick="return confirm('Supprimer cette licence ?')"/></a>
    </td>
  </tr>
<?php endforeach; ?>
</table>
<?php else : ?>
  <p>Aucune licence pour le moment...</p>
<?php endif; ?>