-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  127.0.0.1
-- Généré le :  Jeu 01 Juin 2017 à 15:03
-- Version du serveur :  5.7.14
-- Version de PHP :  7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données :  `eventmanager`
--
CREATE DATABASE IF NOT EXISTS `eventmanager` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `eventmanager`;

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
-- Structure de la table `user_has_events`
--

CREATE TABLE `user_has_events` (
  `user_iduser` int(11) NOT NULL,
  `events_idevents` int(11) NOT NULL,
  `invit` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



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
  MODIFY `idevent` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `iduser` int(11) NOT NULL AUTO_INCREMENT;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `user_has_events`
--
ALTER TABLE `user_has_events`
  ADD CONSTRAINT `fk_user_has_events_events1` FOREIGN KEY (`events_idevents`) REFERENCES `event` (`idevent`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_has_events_user` FOREIGN KEY (`user_iduser`) REFERENCES `user` (`iduser`) ON DELETE NO ACTION ON UPDATE NO ACTION;
