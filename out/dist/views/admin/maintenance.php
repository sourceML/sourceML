<h2>Maintenance</h2>

<br />
<h3>Cache</h3>

<ul class="admin">
  <li><a href="<?= $this->url("admin/maintenance/empty_cache") ?>">Vider le cache</a></li>
</ul>

<br />
<h3>Fichiers XML des sources</h3>

<br />
<p>
  Si vous avez migr&eacute; votre site ou que d'une mani&egrave;re ou d'une autre, l'URL de votre installation de
  sourceML a chang&eacute;, vous devriez mettre &agrave; jour les fichiers XML des sources, pour qu'ils prennent
  en compte votre nouvelle URL.
</p>

<ul class="admin">
  <li><a href="<?= $this->url("admin/maintenance/maj_all_xml") ?>">Mettre &agrave; jour les fichiers XML</a></li>
</ul>