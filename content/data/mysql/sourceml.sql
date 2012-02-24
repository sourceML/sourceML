-- phpMyAdmin SQL Dump
-- version 3.2.2.1deb1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Jeu 03 Novembre 2011 à 23:24
-- Version du serveur: 5.1.37
-- Version de PHP: 5.2.10-2ubuntu6.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `sourceml`
--

-- --------------------------------------------------------

--
-- Structure de la table `sml_action_status`
--

CREATE TABLE `sml_action_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(255) NOT NULL,
  `id_status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `sml_action_status`
--

INSERT INTO `sml_action_status` (`id`, `action`, `id_status`) VALUES
(1, 'admin', 1),
(2, 'users', 1),
(3, 'users', 2),
(4, 'users/identification', 0);

-- --------------------------------------------------------

--
-- Structure de la table `sml_config`
--

CREATE TABLE `sml_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Contenu de la table `sml_config`
--

INSERT INTO `sml_config` (`id`, `key`, `value`) VALUES
(1, 'site_name', 'sourceML'),
(2, 'max_list', '10'),
(3, 'description', ''),
(4, 'out', 'dist'),
(5, 'start_action', 'sources/groupe'),
(6, 'contact_form', '0'),
(7, 'out_groupe_view_albums', 'on'),
(8, 'email', ''),
(9, 'captcha', '0'),
(10, 'out_colonne', 'on'),
(11, 'out_navig_menu', ''),
(12, 'out_colonne_logo_groupe', 'on'),
(13, 'out_albums_menu', ''),
(14, 'out_nom_groupe', 'on'),
(15, 'out_navig_menu_top', 'on'),
(16, 'start_action_params', ''),
(17, 'cache_actif', '1'),
(18, 'cache_maj_auto', '1'),
(19, 'cache_time', '72');

-- --------------------------------------------------------

--
-- Structure de la table `sml_groupes`
--

CREATE TABLE `sml_groupes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text,
  `email` varchar(255) NOT NULL,
  `contact_form` tinyint(4) NOT NULL,
  `captcha` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Contenu de la table `sml_groupes`
--


-- --------------------------------------------------------

--
-- Structure de la table `sml_groupe_status`
--

CREATE TABLE `sml_groupe_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(63) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `sml_groupe_status`
--

INSERT INTO `sml_groupe_status` (`id`, `nom`) VALUES
(2, 'editeur'),
(3, 'contributeur'),
(1, 'admin');

-- --------------------------------------------------------

--
-- Structure de la table `sml_licences`
--

CREATE TABLE `sml_licences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Contenu de la table `sml_licences`
--

INSERT INTO `sml_licences` (`id`, `nom`, `url`) VALUES
(1, 'Creative commons by-sa 2.0', 'http://creativecommons.org/licenses/by-sa/2.0/deed.fr'),
(2, 'Creative Commons by-nc-nd 2.5', 'http://creativecommons.org/licenses/by-nc-nd/2.5/'),
(3, 'Creative Commons by-nc-sa 2.5', 'http://creativecommons.org/licenses/by-nc-sa/2.5/'),
(4, 'Creative Commons by-nc 2.5', 'http://creativecommons.org/licenses/by-nc/2.5/'),
(5, 'Creative Commons by-nd 2.5', 'http://creativecommons.org/licenses/by-nd/2.5/'),
(6, 'Creative Commons by-sa 2.5', 'http://creativecommons.org/licenses/by-sa/2.5/'),
(7, 'Creative Commons by 2.5', 'http://creativecommons.org/licenses/by/2.5/'),
(8, 'Licence Art Libre', 'http://artlibre.org/licence/lal/'),
(9, 'Licence C Reaction', 'http://morne.free.fr/Necktar7/creactionfr.htm'),
(10, 'Yellow OpenMusic License', 'http://openmusic.linuxtag.org/yellow.html'),
(11, 'Green OpenMusic License', 'http://openmusic.linuxtag.org/green.html');

-- --------------------------------------------------------

--
-- Structure de la table `sml_sources`
--

CREATE TABLE `sml_sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `licence` int(11) DEFAULT NULL,
  `date_creation` date DEFAULT NULL,
  `date_inscription` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=76 ;

--
-- Contenu de la table `sml_sources`
--


-- --------------------------------------------------------

--
-- Structure de la table `sml_source_cache`
--

CREATE TABLE `sml_source_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `id_source` int(11) NOT NULL,
  `last_update` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=47 ;

--
-- Contenu de la table `sml_source_cache`
--


-- --------------------------------------------------------

--
-- Structure de la table `sml_source_compositions`
--

CREATE TABLE `sml_source_compositions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_source` int(11) NOT NULL,
  `id_composition` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;

--
-- Contenu de la table `sml_source_compositions`
--


-- --------------------------------------------------------

--
-- Structure de la table `sml_source_derivations`
--

CREATE TABLE `sml_source_derivations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_source` int(11) NOT NULL,
  `derivation` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=94 ;

--
-- Contenu de la table `sml_source_derivations`
--


-- --------------------------------------------------------

--
-- Structure de la table `sml_source_documents`
--

CREATE TABLE `sml_source_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_source` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=83 ;

--
-- Contenu de la table `sml_source_documents`
--


-- --------------------------------------------------------

--
-- Structure de la table `sml_source_groupes`
--

CREATE TABLE `sml_source_groupes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_source` int(11) NOT NULL,
  `id_groupe` int(11) NOT NULL,
  `id_groupe_status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

--
-- Contenu de la table `sml_source_groupes`
--


-- --------------------------------------------------------

--
-- Structure de la table `sml_source_infos`
--

CREATE TABLE `sml_source_infos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_source` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `sml_source_infos`
--


-- --------------------------------------------------------

--
-- Structure de la table `sml_source_status`
--

CREATE TABLE `sml_source_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `sml_source_status`
--

INSERT INTO `sml_source_status` (`id`, `nom`) VALUES
(1, 'album'),
(2, 'morceau'),
(3, 'piste');

-- --------------------------------------------------------

--
-- Structure de la table `sml_users`
--

CREATE TABLE `sml_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Contenu de la table `sml_users`
--

INSERT INTO `sml_users` (`id`, `login`, `password`, `email`, `status`) VALUES
(1, 'user', '96ef04740e7c7dd411c04d5102482333', 'contact@sourcetazic.com', 1);

-- --------------------------------------------------------

--
-- Structure de la table `sml_user_status`
--

CREATE TABLE `sml_user_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `creation_default` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `sml_user_status`
--

INSERT INTO `sml_user_status` (`id`, `nom`, `creation_default`) VALUES
(1, 'admin', 0),
(2, 'membre', 1);
