<?php if($this->etat("mod") == "sources" && $this->etat("controller") == "groupe" && $this->etat("action") == "view") : ?>

<?php if($this->out_config("nom_groupe")) require $this->out_file("views/sources/nom_groupe.php"); ?>

<?php if($this->out_config("colonne_logo_groupe")) require $this->out_file("views/sources/logo_groupe.php"); ?>

<?php endif; ?>

<?php if($this->out_config("albums_menu")) require $this->out_file("views/sources/current_album.php"); ?>

<?php require $this->out_file("views/sources/lien_contact.php"); ?>
