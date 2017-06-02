-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  127.0.0.1
-- Généré le :  Ven 02 Juin 2017 à 01:30
-- Version du serveur :  5.7.14
-- Version de PHP :  7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `eventmanager`
--

-- --------------------------------------------------------

--
-- Structure de la table `event`
--

CREATE TABLE `event` (
  `idevent` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `FDate` varchar(45) NOT NULL,
  `LDate` varchar(45) DEFAULT NULL,
  `Place` varchar(255) NOT NULL,
  `Details` mediumtext NOT NULL,
  `imgLink` varchar(100) DEFAULT NULL,
  `OwnerId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `event`
--

INSERT INTO `event` (`idevent`, `Name`, `FDate`, `LDate`, `Place`, `Details`, `imgLink`, `OwnerId`) VALUES
(1, 'Finale ligue des champions', '2017-06-03 20:20', '2017-06-04 00:30', 'Bulldog bar, Lausanne', 'Hello tout le monde !\r\nJe vous invite &agrave; me rejoindre samedie soir pour regarder le match,\r\nJ&#039;ai fait une r&eacute;servation, il faut me r&eacute;pondre au plus tard demain !\r\n\r\nJ&#039;&egrave;sp&egrave;re &agrave; samedie ', './images/events/1.jpg', 1),
(2, 'Ap&eacute;ro fin de TPI', '2017-06-02 08:50', '', 'Sainte-Croix', 'Hola !\r\nLes TPI&#039;s seront rendu, que de mieux que de d&eacute;compresser au tour d&#039;un verre ?\r\n\r\n', './images/events/2.jpg', 3),
(3, 'Frauenfeld Festival', '2017-07-06 19:00', '2017-07-09 09:30', 'Frauenfeld', 'Festival de musique', './images/events/3.jpg', 3);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `iduser` int(11) NOT NULL,
  `Name` varchar(45) NOT NULL,
  `GName` varchar(45) NOT NULL,
  `Password` longtext NOT NULL,
  `Email` varchar(255) NOT NULL,
  `UserName` varchar(45) NOT NULL,
  `Statut` text,
  `imgLink` varchar(100) DEFAULT NULL,
  `Token` text,
  `Type` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `user`
--

INSERT INTO `user` (`iduser`, `Name`, `GName`, `Password`, `Email`, `UserName`, `Statut`, `imgLink`, `Token`, `Type`) VALUES
(1, 'Krisyam Yves', 'Bamogo', 'a9f5eecf34fbf29dc7645837ee63a8df9acb7d50', 'bamogo.yves@yahoo.fr', 'Hives98', 'Hello, je suis le premier compte d&#039;utilisateur sur ce site !', './images/users/1.jpg', 'cef1cb2fbf42c013c7b97cb640a584bb', '3'),
(2, 'Yves', 'Bamogo', 'a9f5eecf34fbf29dc7645837ee63a8df9acb7d50', 'krisyam-yves.bamogo@cpnv.ch', 'TheLegend27', 'Hey Everyone', NULL, 'd2a97733fb548e34e08fb669c33caae8', '1'),
(3, 'Martin', 'Eden', 'a9f5eecf34fbf29dc7645837ee63a8df9acb7d50', 'krisyam@bamogo.ovh', 'Outkast', NULL, './images/users/3.jpg', 'f7420689c7712a72e9493f4a90c36bf3', '1');

-- --------------------------------------------------------

--
-- Structure de la table `user_has_events`
--

CREATE TABLE `user_has_events` (
  `user_iduser` int(11) NOT NULL,
  `events_idevents` int(11) NOT NULL,
  `invit` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `user_has_events`
--

INSERT INTO `user_has_events` (`user_iduser`, `events_idevents`, `invit`) VALUES
(1, 1, '1'),
(1, 2, '1'),
(1, 3, '1'),
(2, 1, NULL),
(2, 2, '1'),
(2, 3, '1');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`idevent`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`iduser`);

--
-- Index pour la table `user_has_events`
--
ALTER TABLE `user_has_events`
  ADD PRIMARY KEY (`user_iduser`,`events_idevents`),
  ADD KEY `fk_user_has_events_events1_idx` (`events_idevents`),
  ADD KEY `fk_user_has_events_user_idx` (`user_iduser`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `event`
--
ALTER TABLE `event`
  MODIFY `idevent` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `iduser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `user_has_events`
--
ALTER TABLE `user_has_events`
  ADD CONSTRAINT `fk_user_has_events_events1` FOREIGN KEY (`events_idevents`) REFERENCES `event` (`idevent`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_has_events_user` FOREIGN KEY (`user_iduser`) REFERENCES `user` (`iduser`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
