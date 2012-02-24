<?php

  if($this->init_links()) :

  $start_action = $this->config("start_action");

  if($start_action != "sources/groupe/view") $this->set_link("menu_top/groupes", $this->url("sources/groupe"), "groupes", 20);
  if($start_action !== "sources/album/view") $this->set_link("menu_top/albums", $this->url("sources/album"), "albums", 30);
  $this->set_link("menu_top/morceaux", $this->url("sources/morceau"), "morceaux", 40);
  $this->set_link("menu_top/pistes", $this->url("sources/piste"), "sources", 50);

  $this->set_link("admin/config", $this->url("admin/config"), "Configuration", 10);
  $this->set_link("admin/users", $this->url("admin/users"), "Utilisateurs", 20);
  $this->set_link("admin/licences", $this->url("admin/licences"), "Licences", 30);
  $this->set_link("admin/cache", $this->url("admin/cache"), "Cache", 40);
  $this->set_link("admin/maintenance", $this->url("admin/maintenance"), "Maintenance", 50);
  $this->set_link("admin/plugins", $this->url("admin/plugins"), "Plugins", 60);

  else :

  $this->erreur("impossible de charger les liens", true);

  endif;

?>