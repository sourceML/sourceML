<h2>Gestion du cache</h2>

<p class="info">
  Pour les fichiers XML externes (r&eacute;f&eacute;rences et d&eacute;rivations).
</p>

<form name="config_form" action="<?= $this->url("admin/cache") ?>" method="post">
  <ul class="form">
    <li>
      <label for="cache_actif">utiliser le cache</label>
      <div><p>
              <input type="radio" name="cache_actif" id="cache_actif_oui" value="1"<?= $this->out["config"]["cache_actif"] ? " checked=\"checked\"" : "" ?> /> oui
        <br /><input type="radio" name="cache_actif" id="cache_actif_non" value="0"<?= $this->out["config"]["cache_actif"] ? "" : " checked=\"checked\"" ?> /> non
      </p></div>
    </li>
    <li id="li_cache_maj_auto" <?= $this->out["config"]["cache_actif"] ? "" : " style=\"display: none\"" ?>>
      <label for="cache_maj_auto">mise &agrave; jour du cache</label>
      <p>
              <input type="radio" name="cache_maj_auto" id="cache_maj_auto_oui" value="1"<?= $this->out["config"]["cache_maj_auto"] ? " checked=\"checked\"" : "" ?> /> automatique
        <br /><input type="radio" name="cache_maj_auto" id="cache_maj_auto_non" value="0"<?= $this->out["config"]["cache_maj_auto"] ? "" : " checked=\"checked\"" ?> /> manuelle
      </p>
    </li>
    <li id="li_cache_time" <?= $this->out["config"]["cache_actif"] && $this->out["config"]["cache_maj_auto"] ? "" : " style=\"display: none\"" ?>>
      <label for="cache_time">dur&eacute;e de validit&eacute; du cache</label>
      <p>
        <input type="text" size="5" name="cache_time" id="cache_time" value="<?= $this->out["config"]["cache_time"] ?>" /> heures
      </p>
    </li>
    <li class="buttons">
      <input type="submit" value="Enregistrer" />
    </li>
  </ul>
</form>
