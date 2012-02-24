
                         sourceML 0.13.1


un CMS en GNU/GPL pour la publication de sources musicales.

sourceML utilise, dans le dossier sourceml/libs :

 - TinyMCE
   http://tinymce.moxiecode.com/

 - flash-mp3-player
   http://flash-mp3-player.net/

 - une classe Php sxml, pour parser les fichiers XML, trouvee ici :
   http://www.shop24-7.info/32-0-simplexml-alternative-php4.html

 - Ptitcaptcha, une classe Php pour un captcha
   http://www.jpfox.fr/?post/2007/10/24/190-un-petit-captcha-en-php

 - InputFilter, une class Php pour filtrer les balises et les attributs
   HTML dangereux dans les description des sources
   http://www.phpclasses.org/inputfilter


installation rapide :
---------------------

 - l'archive sourceml<version>.tar.gz fourni un dossier sourceml
 - uploadez ce dossier a la racine de votre site
 - le site devrait alors etre accessible a l'URL:
   http://votre.site.web/sourceml
 - pour une premiere utilisation, vous pouvez vous identifier avec :
     login: user
     pass: sourcetazic
 - pensez a changer le mot de passe


documentation :
---------------

plus de details sur l'installation, l'utilisation ou le
developpement sur http://www.sourceml.com


historique des modifications :
------------------------------


0.13.1 :
--------

1 Janv 2012

ajout d'un systeme de plugins
ajout d'une arborescence de liens
modification de la methode de surcharge, qui se fait
maintenant avec les plugins


0.13 :
------

26 Nov 2011

changement de structure de la base, pour permettre d'associer
une source a plusieurs groupes, avec des statuts de groupe sur les
sources : "admin", "editeur" ou "contributeur".

pour le moment, ce changement dans la structure de la base n'est pas
visible dans l'utilisation du site. les interfaces sont les memes et
une source continue a n'etre associee qu'a un seul groupe.

cette modification dans la structure de la base a pour but de prevoir
l'evolution des relations entre les groupes et les sources.

un mecanisme d'upgrade peut mettre automatiquement a jour la structure
de la base de données. une variable "UGRADE_DB" dans le fichier config.php
permet d'indiquer si sourceML doit ou non mettre a jour la structure de la
base (le fichier config.php contient lui-meme plus de details sur l'upgrade,
dans ses commentaires).


0.12.4 :
--------

30 Oct 2011

correction de bugs
- encore quelques "nom" au lieu de "titre" dans le traitement des
  champs de la table sources.
- bug logout qui deconnecte pas


0.12.3 :
--------

22 Oct 2011

correction de bugs
- perte d'infos si message pendant edition
- quelques "nom" au lieu de "titre" dans le traitement des
  champs de la table sources

nouveau filtre pour trouver les sources hors composition


0.12.2 :
--------

4 Sept 2011

Correction de bugs
- affichage de la mauvaise licence
- perte de la reference lors de l'edition (pour XML)


0.12.1 :
--------

Cette version detecte l'existance ou non, dans la base de donnees :
 - de la table source_derivations
 - du champ "titre" dans la table sources (anciennement "nom")

! met a jour la base automatiquement pour XML
averti sans mettre a jour pour SQL


0.12 :
------

Une source peut maintenant deriver de plusieurs autres sources
ajout d'une table source_derivations dans la base de donnees
le champ nom de la table sources se nomme maintenant titre

0.11.1.dev :
----------

Une source peut maintenant dériver de plusieurs sources.
Pas mal de changements y compris dans les acces aux donnees
Pour le moment, seul Mysql marche avec cette version


0.11.1 :
------

3 Juin 2011

Mise a jour du module d'acces aux donnees pour Sqlite.

On peut maintenant choisir entre XML (par defaut), Mysql ou Sqlite


0.11 :
------

30 Mai 2011

Mise a jour des acces pour Mysql.

Cette version est la meme que la 0.10.2, avec en plus
la possibilite de choisir entre XML ou Mysql pour le
stockage des donnees.


0.10.2 :
--------

14 Mai 2011 

Utilisation d'un cache en RAM pour les données gerées en XML.


0.10.1 :
--------

5 Mai 2011 

la 0.10 permet d'editer le contenu des fichiers XML, mais le filtrage des
inputs (inputFilter sur POST) bloque l'édition.

ce filtrage a ete desactive par defaut a partir de la 0.10.1
voir le fichier app/core/init/02_inputs.php pour le reactiver


0.10 :
------

2 Mai 2011

une source peut maintenant etre a la fois une reference et une
derivation.

les arborecences de sources sont chargees a la demande, via javascript.

lorsqu'une source fait une reference vers un fichier XML externe, le
membre qui gere la source peut editer directement le code XML (ce qui permet
de charger des XML externes, meme si le serveur bloque les requetes
emises par Php).


0.9 :
-----

21 Nov 2010

corrections de bugs

ajout d'un cache pour les fichiers XML externes


0.8 :
-----

12 Nov 2010

les "pistes" sont maintenant appelees des "sources"

ajout des formulaires de contact :
 - un formulaire général pour le site
 - un formulaire par groupe

chaque formulaire de contact est activable ou pas
avec option "captcha"

une source peut maintenant etre aussi une "derivation"
en plus de "originale" et "reference". une derivation
est une source originale (avec ses propres infos) et
contient en plus une reference a un fichier XML pour la piste
d'origine

pour chaque source, un "fil d'ariane" affiche l'historique
des references et des derivations. la profondeur de ce fil
d'ariane est parametrable en admin


0.7.2 :
-------

6 Nov 2010 

correction du bug des dates qui disparraissent quand on
enregistre l'ordre

modif de la liste des licences

un lien "pistes" apparait en plus quand un morceau a
effectivement des pistes. idem avec un lien "morceaux" pour
les albums.



--------------------------------------------------------------
                     http://www.sourceml.com
