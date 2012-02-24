<?php

  # FICHIER A ADAPTER EN FONCTION DE VOTRE INSTALLATION

  # si vous avez mis tel quel sur votre serveur, a la racine, le dossier "sourceml"
  # et que vous voulez utiliser le stockage des donnees en XML (comportement par defaut)
  # alors vous ne devriez pas avoir a modifier ce fichier.


  # si vous faites l'installation dans un autre dossier que "sourceml",
  # (par exemple directement a la racine ou dans un dossier avec un autre nom)
  # indiquez le nom du dossier dans la configuration suivante :

  # chemin web du site (dossier d'execution du script d'appel)
  # absolu a partir de la racine du site

  # installation a la racine du site :
  # $PATHES["web"] = "/";

  $PATHES["web"] = "/sourceml/";



  # les chemins suivant sont des chemins internes a sourceML

  # chemin de l'application
  # relatif au dossier d'execution du script
  $PATHES["app"] = "app";

  # dossier des plugins
  # relatif au dossier d'execution du script
  $PATHES["plugins"] = "plugins";

  # dossier des librairies
  # relatif au dossier d'execution du script
  $PATHES["libs"] = "libs";

  # dossier des contenus editables par l'application
  # relatif au dossier d'execution du script
  $PATHES["content"] = "content";

  # dossier des templates
  # relatif au dossier d'execution du script
  $PATHES["out"] = "out";

  # nom du dossier (sans chemin) du template par defaut
  $PATHES["dist_out"] = "dist";

  # --------------------------------------------------------------
  #                                                base de donnees
  # "mysql", "sqlite" ou "xml"

  # --------------------------- MYSQL
/*
  $bdd["sgbd"] = "mysql";
  $bdd["host"] = "localhost";
  $bdd["base"] = "sourceml";
  $bdd["table_prefix"] = "sml_";
  $bdd["user"] = "mysql_user";
  $bdd["password"] = "mysql_password";
*/
  # --------------------------- SQLITE
/*
  $bdd["sgbd"] = "sqlite";
  $bdd["host"] = "content/data/sqlite";
  $bdd["base"] = "sourceml.sqlite";
  $bdd["table_prefix"] = "sml_";
*/
  # --------------------------- XML

  $bdd["sgbd"] = "xml";
  $bdd["host"] = "content/data/xml";
  $bdd["base"] = "sourceml";
  $bdd["table_prefix"] = "sml_";

  # --------------------------------------------------------------
  #                                     upgrade de la base donnees
  #

  # si cette installation est un upgrade et que la base de donnees doit etre
  # modifiee, afficher un message d'erreur, sans modifier la base :

  $CONFIG["UPGRADE_DB"] = false;

  # si vous voulez que sourceML upgrade la base de donnees,
  # decommentez cette ligne :

//  $CONFIG["UPGRADE_DB"] = true;

  # et rechargez la page d'accueil de votre installation.
  # l'upgrade devrait se derouler de maniere transparente
  # si tout se passe bien, vous devriez a nouveau pouvoir utiliser
  # le site. vous pourrez alors remettre en commentaire la ligne ci-dessus.

  # !!! ATTENTION !!!
  # faites une sauvegarde de vos donnees avant de lancer l'upgrade
  # (dossier content et, si vous utilisez Mysql, un export de la base)

  # --------------------------------------------------------------
  #                                                  lien sourceML
  #

  $CONFIG["version"] = "<a href=\"http://www.sourceml.com/\">sourceML</a>";

?>