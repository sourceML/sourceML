<h2>Les plugins</h2>

<?php if($this->out["plugins"]) : ?>

<form name="plugins_form" action="<?= $this->url("admin/plugins") ?>" method="post">
  <ul class="plugins">
  <?php $data = $this->data(); foreach($this->out["plugins"] as $plugin_name => $plugin) : ?>
    <li class="<?= $plugin["installed"] ? ($plugin["enabled"] ? "enabled" : "disabled") : "uninstalled" ?>">
      <div>
        <p class="folder">dossier <strong><?= $plugin_name ?></strong></p>
        <h3><?= $plugin["title"] ?></h3>
        <div class="clear"><!-- --></div>
        <?= $plugin["description"] ?>
        <ul class="plugin_links">
        <?php if($plugin["installed"]) : ?>
          <li><a href="<?= $this->url("admin/plugins/uninstall", array("id" => $plugin_name)) ?>"
                 onclick="return confirm('si le plugin stocke des donnees, elles seront perdues')">d&eacute;sinstaller</a></li>
          <?php if($plugin["enabled"]) : ?>
          <?php if(($admin_link = $data->get_link("plugins/admin/".$plugin_name)) && $admin_link["url"]) : ?>
          <li><a href="<?= $admin_link["url"] ?>"><?= $admin_link["intitule"] ?></a></li>
          <?php endif; ?>
          <li><a href="<?= $this->url("admin/plugins/disable", array("id" => $plugin_name)) ?>">d&eacute;sactiver</a></li>
          <?php else : ?>
          <li><a href="<?= $this->url("admin/plugins/enable", array("id" => $plugin_name)) ?>">activer</a></li>
          <?php endif; ?>
        <?php else : ?>
          <li><a href="<?= $this->url("admin/plugins/install", array("id" => $plugin_name)) ?>">installer</a></li>
        <?php endif; ?>
          <li>priorit&eacute; : <input type="text" size="4" name="priorite_<?= $plugin_name ?>" value="<?= $plugin["priorite"] ? $plugin["priorite"] : "0" ?>" /></li>
        </ul>
      </div>
    </li>
  <?php endforeach; ?>
    <li class="buttons">
      <input type="submit" value="Enregistrer les priorit&eacute;s" />
    </li>
  </ul>
</form>

<?php else : ?>

<p>Aucun plugin pour le moment.</p>

<?php endif; ?>
