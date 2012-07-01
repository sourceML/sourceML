<?php

  if($this->out_config("nom_groupe")) require $this->out_file("views/sources/nom_groupe.php");
  if($this->out_config("colonne_logo_groupe")) require $this->out_file("views/sources/logo_groupe.php");
  if(($this->etat("controller") == "groupe") && $this->out_config("groupe_view_albums")) $this->set_config(array("out_albums_menu" => ""));
  if($this->out_config("albums_menu")) require $this->out_file("views/sources/menu_albums.php");
  require $this->out_file("views/sources/lien_contact.php");

?>