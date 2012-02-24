<?php if($source["status"] != 1 && $source["has_sources"]) : ?>
<h3>
  <a id="toggle_sources_list_<?= $source["id"] ?>"
     href="#"
     class="block_list_toggle"
     onclick="toggle_source_list('<?= $source["id"] ?>'); return false;">[+]</a>
  Sources
</h3>
<div id="source_list_<?= $source["id"] ?>"><div class="pistes"></div></div>
<?php endif; ?>

<?php if($source["status"] != 1 && $source["has_derivations"]) : ?>
<h3>
  <a id="toggle_derivation_list_<?= $source["id"] ?>"
     href="#"
     class="block_list_toggle"
     onclick="toggle_derivation_list('<?= $source["id"] ?>'); return false;">[+]</a>
  D&eacute;rivations
</h3>
<div id="derivation_list_<?= $source["id"] ?>"><div class="derivation"></div></div>
<?php endif; ?>
