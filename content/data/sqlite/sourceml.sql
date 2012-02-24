CREATE TABLE sml_action_status (
  "id" INTEGER NOT NULL PRIMARY KEY,
  "action" VARCHAR NOT NULL,
  "id_status" INTEGER NOT NULL
);

CREATE TABLE sml_config (
  "id" INTEGER NOT NULL PRIMARY KEY,
  "key" VARCHAR NOT NULL,
  "value" text NOT NULL
);

CREATE TABLE sml_groupe_status (
  "id" INTEGER NOT NULL PRIMARY KEY,
  "nom" VARCHAR NOT NULL
);

CREATE TABLE sml_groupes (
  "id" INTEGER NOT NULL PRIMARY KEY,
  "id_user" INTEGER NOT NULL,
  "nom" VARCHAR NOT NULL,
  "image" VARCHAR DEFAULT NULL,
  "description" text,
  "email" VARCHAR NOT NULL,
  "contact_form" INTEGER NOT NULL,
  "captcha" INTEGER NOT NULL
);

CREATE TABLE sml_licences (
  "id" INTEGER NOT NULL PRIMARY KEY,
  "nom" VARCHAR NOT NULL,
  "url" VARCHAR NOT NULL
);

CREATE TABLE sml_source_cache (
  "id" INTEGER NOT NULL PRIMARY KEY,
  "url" VARCHAR NOT NULL,
  "id_source" INTEGER NOT NULL,
  "last_update" text NOT NULL
);

CREATE TABLE sml_source_compositions (
  "id" INTEGER NOT NULL PRIMARY KEY,
  "id_source" INTEGER NOT NULL,
  "id_composition" INTEGER NOT NULL
);

CREATE TABLE sml_source_derivations (
  "id" INTEGER NOT NULL PRIMARY KEY,
  "id_source" INTEGER NOT NULL,
  "derivation" VARCHAR NOT NULL
);

CREATE TABLE sml_source_documents (
  "id" INTEGER NOT NULL PRIMARY KEY,
  "id_source" INTEGER NOT NULL,
  "nom" VARCHAR NOT NULL,
  "url" VARCHAR NOT NULL
);

CREATE TABLE sml_source_groupes (
  "id" INTEGER NOT NULL PRIMARY KEY,
  "id_source" INTEGER NOT NULL,
  "id_groupe" INTEGER NOT NULL,
  "id_groupe_status" INTEGER NOT NULL
);

CREATE TABLE sml_source_infos (
  "id" INTEGER NOT NULL PRIMARY KEY,
  "id_source" INTEGER NOT NULL,
  "key" VARCHAR NOT NULL,
  "value" text
);

CREATE TABLE sml_source_status (
  "id" INTEGER NOT NULL PRIMARY KEY,
  "nom" VARCHAR NOT NULL
);

CREATE TABLE "sml_sources" (
  "id" INTEGER PRIMARY KEY  NOT NULL,
  "status" INTEGER NOT NULL,
  "reference" VARCHAR DEFAULT (NULL),
  "titre" VARCHAR DEFAULT (NULL),
  "licence" INTEGER DEFAULT (NULL),
  "date_creation" text DEFAULT (NULL),
  "date_inscription" text NOT NULL
);

CREATE TABLE sml_user_status (
  "id" INTEGER NOT NULL PRIMARY KEY,
  "nom" VARCHAR NOT NULL,
  "creation_default" INTEGER NOT NULL
);

CREATE TABLE sml_users (
  "id" INTEGER NOT NULL PRIMARY KEY,
  "login" VARCHAR NOT NULL,
  "password" VARCHAR NOT NULL,
  "email" VARCHAR NOT NULL,
  "status" INTEGER NOT NULL
);

INSERT INTO "sml_action_status" VALUES(1,'admin',1);
INSERT INTO "sml_action_status" VALUES(2,'users',1);
INSERT INTO "sml_action_status" VALUES(3,'users',2);
INSERT INTO "sml_action_status" VALUES(4,'users/identification',0);

INSERT INTO "sml_config" VALUES(1,'site_name','sourceML');
INSERT INTO "sml_config" VALUES(2,'max_list','10');
INSERT INTO "sml_config" VALUES(3,'description','');
INSERT INTO "sml_config" VALUES(4,'out','dist');
INSERT INTO "sml_config" VALUES(5,'start_action','sources/groupe');
INSERT INTO "sml_config" VALUES(6,'contact_form','0');
INSERT INTO "sml_config" VALUES(7,'out_groupe_view_albums','on');
INSERT INTO "sml_config" VALUES(8,'email','');
INSERT INTO "sml_config" VALUES(9,'captcha','0');
INSERT INTO "sml_config" VALUES(10,'out_colonne','on');
INSERT INTO "sml_config" VALUES(11,'out_navig_menu','');
INSERT INTO "sml_config" VALUES(12,'out_colonne_logo_groupe','on');
INSERT INTO "sml_config" VALUES(13,'out_albums_menu','');
INSERT INTO "sml_config" VALUES(14,'out_nom_groupe','on');
INSERT INTO "sml_config" VALUES(15,'out_navig_menu_top','on');
INSERT INTO "sml_config" VALUES(16,'start_action_params','');
INSERT INTO "sml_config" VALUES(17,'cache_actif','1');
INSERT INTO "sml_config" VALUES(18,'cache_maj_auto','1');
INSERT INTO "sml_config" VALUES(19,'cache_time','72');

INSERT INTO "sml_groupe_status" VALUES(1,'admin');
INSERT INTO "sml_groupe_status" VALUES(2,'editeur');
INSERT INTO "sml_groupe_status" VALUES(3,'contributeur');

INSERT INTO "sml_licences" VALUES(1,'Creative commons by-sa 2.0','http://creativecommons.org/licenses/by-sa/2.0/deed.fr');
INSERT INTO "sml_licences" VALUES(2,'Creative Commons by-nc-nd 2.5','http://creativecommons.org/licenses/by-nc-nd/2.5/');
INSERT INTO "sml_licences" VALUES(3,'Creative Commons by-nc-sa 2.5','http://creativecommons.org/licenses/by-nc-sa/2.5/');
INSERT INTO "sml_licences" VALUES(4,'Creative Commons by-nc 2.5','http://creativecommons.org/licenses/by-nc/2.5/');
INSERT INTO "sml_licences" VALUES(5,'Creative Commons by-nd 2.5','http://creativecommons.org/licenses/by-nd/2.5/');
INSERT INTO "sml_licences" VALUES(6,'Creative Commons by-sa 2.5','http://creativecommons.org/licenses/by-sa/2.5/');
INSERT INTO "sml_licences" VALUES(7,'Creative Commons by 2.5','http://creativecommons.org/licenses/by/2.5/');
INSERT INTO "sml_licences" VALUES(8,'Licence Art Libre','http://artlibre.org/licence/lal/');
INSERT INTO "sml_licences" VALUES(9,'Licence C Reaction','http://morne.free.fr/Necktar7/creactionfr.htm');
INSERT INTO "sml_licences" VALUES(10,'Yellow OpenMusic License','http://openmusic.linuxtag.org/yellow.html');
INSERT INTO "sml_licences" VALUES(11,'Green OpenMusic License','http://openmusic.linuxtag.org/green.html');

INSERT INTO "sml_source_status" VALUES(1,'album');
INSERT INTO "sml_source_status" VALUES(2,'morceau');
INSERT INTO "sml_source_status" VALUES(3,'piste');

INSERT INTO "sml_user_status" VALUES(1,'admin',0);
INSERT INTO "sml_user_status" VALUES(2,'membre',1);

INSERT INTO "sml_users" VALUES(1,'user','96ef04740e7c7dd411c04d5102482333','contact@sourcetazic.com',1);
