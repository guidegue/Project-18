-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 27 avr. 2026 à 21:15
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `orientation_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `competences`
--

CREATE TABLE `competences` (
  `id` int(11) NOT NULL,
  `metier_id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `competences`
--

INSERT INTO `competences` (`id`, `metier_id`, `nom`) VALUES
(1, 1, 'PHP'),
(2, 1, 'JavaScript'),
(3, 1, 'HTML/CSS'),
(4, 1, 'SQL'),
(5, 2, 'Python'),
(6, 2, 'Machine Learning'),
(7, 2, 'Data Visualization'),
(8, 2, 'Statistics'),
(9, 3, 'TCP/IP'),
(10, 3, 'Routage'),
(11, 3, 'Sécurité réseau'),
(12, 3, 'Cisco'),
(13, 4, 'AutoCAD'),
(14, 4, 'Résistance des matériaux'),
(15, 4, 'Topographie'),
(16, 4, 'Béton armé'),
(17, 5, 'Anatomie'),
(18, 5, 'Physiologie'),
(19, 5, 'Pharmacologie'),
(20, 5, 'Pathologie'),
(21, 6, 'Droit civil'),
(22, 6, 'Droit pénal'),
(23, 6, 'Procédure'),
(24, 6, 'Droit des contrats'),
(25, 7, 'Leadership'),
(26, 7, 'Gestion de projet'),
(27, 7, 'Communication'),
(28, 7, 'Prise de décision'),
(29, 8, 'Comptabilité générale'),
(30, 8, 'Comptabilité analytique'),
(31, 8, 'Fiscalité'),
(32, 8, 'Audit'),
(33, 9, 'Pédagogie'),
(34, 9, 'Didactique'),
(35, 9, 'Psychologie éducative'),
(36, 9, 'Communication'),
(37, 10, 'Écriture journalistique'),
(38, 10, 'Reportage'),
(39, 10, 'Montage'),
(40, 10, 'Enquête'),
(41, 11, 'Dessin technique'),
(42, 11, 'Maquette 3D'),
(43, 11, 'Urbanisme'),
(44, 11, 'Matériaux'),
(45, 12, 'Psychologie clinique'),
(46, 12, 'Neuropsychologie'),
(47, 12, 'Psychométrie'),
(48, 12, 'Thérapies'),
(49, 13, 'Génétique'),
(50, 13, 'Microbiologie'),
(51, 13, 'Biochimie'),
(52, 13, 'Écologie'),
(53, 14, 'Chimie organique'),
(54, 14, 'Chimie inorganique'),
(55, 14, 'Chimie analytique'),
(56, 14, 'Chimie physique'),
(57, 15, 'Mécanique'),
(58, 15, 'Électromagnétisme'),
(59, 15, 'Thermodynamique'),
(60, 15, 'Optique'),
(61, 16, 'Agronomie'),
(62, 16, 'Zootechnie'),
(63, 16, 'Phytopathologie'),
(64, 16, 'Irrigation'),
(65, 17, 'Mécanique des fluides'),
(66, 17, 'Résistance des matériaux'),
(67, 17, 'CAO'),
(68, 17, 'Maintenance'),
(69, 18, 'Électricité'),
(70, 18, 'Électronique'),
(71, 18, 'Automatisme'),
(72, 18, 'Électrotechnique'),
(73, 19, 'Pharmacologie'),
(74, 19, 'Chimie pharmaceutique'),
(75, 19, 'Toxicologie'),
(76, 19, 'Droit pharmaceutique'),
(77, 20, 'Microéconomie'),
(78, 20, 'Macroéconomie'),
(79, 20, 'Économétrie'),
(80, 20, 'Finance');

-- --------------------------------------------------------

--
-- Structure de la table `filieres`
--

CREATE TABLE `filieres` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `filieres`
--

INSERT INTO `filieres` (`id`, `nom`, `description`) VALUES
(1, 'Informatique', 'Programmation, développement, IA, cybersécurité'),
(2, 'Réseaux & Télécoms', 'Infrastructure réseau, télécommunications, 5G'),
(3, 'Mathématiques', 'Mathématiques pures et appliquées'),
(4, 'Physique', 'Physique générale, mécanique quantique, électricité'),
(5, 'Chimie', 'Chimie organique, inorganique, analytique'),
(6, 'Biologie', 'Biologie cellulaire, génétique, microbiologie'),
(7, 'Génie Civil', 'Construction, BTP, infrastructures'),
(8, 'Génie Mécanique', 'Mécanique, conception, maintenance industrielle'),
(9, 'Génie Électrique', 'Électricité, électronique, automatisme'),
(10, 'Médecine', 'Diagnostic, traitement des maladies'),
(11, 'Pharmacie', 'Médicaments, préparation, délivrance'),
(12, 'Droit', 'Droit civil, pénal, des affaires'),
(13, 'Sciences Politiques', 'Politique, relations internationales'),
(14, 'Lettres Modernes', 'Littérature, linguistique, français'),
(15, 'Philosophie', 'Philosophie, éthique, logique'),
(16, 'Histoire', 'Histoire générale, archéologie'),
(17, 'Sciences Économiques', 'Microéconomie, macroéconomie'),
(18, 'Gestion', 'Management, marketing, RH'),
(19, 'Journalisme', 'Médias, reportage, investigation'),
(20, 'Architecture', 'Design architectural, urbanisme'),
(21, 'Psychologie', 'Psychologie clinique, cognitive, sociale'),
(22, 'Sociologie', 'Étude des sociétés, enquêtes'),
(23, 'Langues Étrangères', 'Anglais, allemand, espagnol, arabe'),
(24, 'Arts Plastiques', 'Peinture, sculpture, dessin'),
(25, 'Musique', 'Solfège, instruments, composition'),
(26, 'Théâtre', 'Art dramatique, mise en scène'),
(27, 'Agronomie', 'Agriculture, élevage, productions'),
(28, 'Environnement', 'Écologie, développement durable'),
(29, 'Tourisme', 'Gestion des destinations touristiques'),
(30, 'Communication', 'Communication d\'entreprise, publicité');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `expediteur_id` int(11) NOT NULL,
  `destinataire_id` int(11) NOT NULL,
  `sujet` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `lu` tinyint(1) DEFAULT 0,
  `date_envoi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id`, `expediteur_id`, `destinataire_id`, `sujet`, `message`, `lu`, `date_envoi`) VALUES
(1, 2, 1, '📊 Résultats de votre questionnaire - Niveau Bon', 'Bonjour {prenom},\r\n\r\nBon travail ! Vous avez obtenu un score de 73% au questionnaire. Vous avez de bonnes bases dans votre domaine.\r\n\r\n📊 Résultats :\r\n- Score : 73%\r\n- Niveau : Bon\r\n- Bonnes réponses : 11/15\r\n- Filière : Informatique\r\n\r\nJe vous recommande de consulter les ressources pédagogiques pour renforcer vos compétences.\r\n\r\nRestez motivé ! 💪', 1, '2026-04-15 23:34:52'),
(6, 2, 7, '📊 Résultats de votre questionnaire - Niveau Excellent', 'Félicitations ahmat ! 🎉\r\n\r\nVous avez obtenu un excellent score de 100% au questionnaire d\'orientation. Votre profil correspond parfaitement aux métiers recommandés.\r\n\r\n📊 Résultats :\r\n- Score : 100%\r\n- Niveau : Excellent\r\n- Bonnes réponses : 10/10\r\n- Filière : Droit\r\n\r\nJe vous invite à consulter les ressources suggérées pour approfondir vos connaissances.\r\n\r\nN\'hésitez pas à me contacter pour un entretien personnalisé.\r\n\r\nBravo pour ce travail ! 👏', 1, '2026-04-16 13:59:58'),
(7, 2, 8, '📊 Résultats de votre questionnaire - Niveau Bon', 'Bonjour pack,\r\n\r\nBon travail ! Vous avez obtenu un score de 67% au questionnaire. Vous avez de bonnes bases dans votre domaine.\r\n\r\n📊 Résultats :\r\n- Score : 67%\r\n- Niveau : Bon\r\n- Bonnes réponses : 10/15\r\n- Filière : Informatique\r\n\r\nJe vous recommande de consulter les ressources pédagogiques pour renforcer vos compétences.\r\n\r\nRestez motivé ! 💪', 1, '2026-04-16 18:55:27'),
(8, 2, 9, '📊 Résultats de votre questionnaire - Niveau Excellent', 'Félicitations Ali ! 🎉\r\n\r\nVous avez obtenu un excellent score de 90% au questionnaire d\'orientation. Votre profil correspond parfaitement aux métiers recommandés.\r\n\r\n📊 Résultats :\r\n- Score : 90%\r\n- Niveau : Excellent\r\n- Bonnes réponses : 9/10\r\n- Filière : Biologie\r\n\r\nJe vous invite à consulter les ressources suggérées pour approfondir vos connaissances.\r\n\r\nN\'hésitez pas à me contacter pour un entretien personnalisé.\r\n\r\nBravo pour ce travail ! 👏', 1, '2026-04-17 13:16:54'),
(9, 2, 10, '📊 Résultats de votre questionnaire - Niveau Excellent', 'Félicitations DIBRINE ! 🎉\r\n\r\nVous avez obtenu un excellent score de 90% au questionnaire d\'orientation. Votre profil correspond parfaitement aux métiers recommandés.\r\n\r\n📊 Résultats :\r\n- Score : 90%\r\n- Niveau : Excellent\r\n- Bonnes réponses : 9/10\r\n- Filière : Histoire\r\n\r\nJe vous invite à consulter les ressources suggérées pour approfondir vos connaissances.\r\n\r\nN\'hésitez pas à me contacter pour un entretien personnalisé.\r\n\r\nBravo pour ce travail ! 👏', 1, '2026-04-17 19:23:46'),
(10, 2, 11, '📊 Résultats de votre questionnaire - Niveau Excellent', 'Félicitations brahim ! 🎉\r\n\r\nVous avez obtenu un excellent score de 87% au questionnaire d\'orientation. Votre profil correspond parfaitement aux métiers recommandés.\r\n\r\n📊 Résultats :\r\n- Score : 87%\r\n- Niveau : Excellent\r\n- Bonnes réponses : 13/15\r\n- Filière : Informatique\r\n\r\nJe vous invite à consulter les ressources suggérées pour approfondir vos connaissances.\r\n\r\nN\'hésitez pas à me contacter pour un entretien personnalisé.\r\n\r\nBravo pour ce travail ! 👏', 1, '2026-04-18 09:37:21'),
(11, 2, 12, '📊 Résultats de votre questionnaire - Niveau Excellent', 'Félicitations ignebe ! 🎉\r\n\r\nVous avez obtenu un excellent score de 87% au questionnaire d\'orientation. Votre profil correspond parfaitement aux métiers recommandés.\r\n\r\n📊 Résultats :\r\n- Score : 87%\r\n- Niveau : Excellent\r\n- Bonnes réponses : 13/15\r\n- Filière : Informatique\r\n\r\nJe vous invite à consulter les ressources suggérées pour approfondir vos connaissances.\r\n\r\nN\'hésitez pas à me contacter pour un entretien personnalisé.\r\n\r\nBravo pour ce travail ! 👏', 1, '2026-04-21 13:09:28'),
(12, 2, 12, 'selon votre dossier', 'bonjour salomon je vous demande de continue en droit fiscal ,car vous ne pouvez pas cootinuer en droit penal', 0, '2026-04-27 20:30:32'),
(13, 2, 7, 'Suivi personnalisé', 'merci beaucoup', 0, '2026-04-27 20:52:29');

-- --------------------------------------------------------

--
-- Structure de la table `metiers`
--

CREATE TABLE `metiers` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `secteur` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `metiers`
--

INSERT INTO `metiers` (`id`, `nom`, `description`, `secteur`) VALUES
(1, 'Développeur Web', 'Crée et maintient des sites web et applications web', 'Informatique'),
(2, 'Data Scientist', 'Analyse les données et crée des modèles prédictifs', 'Informatique'),
(3, 'Ingénieur Réseau', 'Gère les infrastructures réseau et la sécurité', 'Télécoms'),
(4, 'Ingénieur Génie Civil', 'Conçoit et réalise des ouvrages de construction', 'BTP'),
(5, 'Médecin Généraliste', 'Diagnostique et traite les maladies', 'Santé'),
(6, 'Avocat', 'Défend les clients en justice', 'Droit'),
(7, 'Manager', 'Gère les équipes et les projets', 'Management'),
(8, 'Comptable', 'Gère la comptabilité des entreprises', 'Finance'),
(9, 'Professeur', 'Enseigne dans le secondaire ou supérieur', 'Éducation'),
(10, 'Journaliste', 'Recueille et diffuse l\'information', 'Médias'),
(11, 'Architecte', 'Conçoit des bâtiments et espaces', 'Architecture'),
(12, 'Psychologue', 'Aide les personnes en souffrance psychique', 'Santé'),
(13, 'Biologiste', 'Étudie les organismes vivants', 'Sciences'),
(14, 'Chercheur en Chimie', 'Effectue des recherches en chimie', 'Sciences'),
(15, 'Physicien', 'Étudie les lois de la physique', 'Sciences'),
(16, 'Agronome', 'Optimise les productions agricoles', 'Agriculture'),
(17, 'Ingénieur Mécanique', 'Conçoit des systèmes mécaniques', 'Industrie'),
(18, 'Ingénieur Électrique', 'Conçoit des systèmes électriques', 'Industrie'),
(19, 'Pharmacien', 'Prépare et délivre les médicaments', 'Santé'),
(20, 'Économiste', 'Analyse les phénomènes économiques', 'Finance');

-- --------------------------------------------------------

--
-- Structure de la table `modeles_messages`
--

CREATE TABLE `modeles_messages` (
  `id` int(11) NOT NULL,
  `titre` varchar(200) NOT NULL,
  `contenu` text NOT NULL,
  `actif` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `modeles_messages`
--

INSERT INTO `modeles_messages` (`id`, `titre`, `contenu`, `actif`) VALUES
(1, 'Félicitations - Excellent score', 'Félicitations {prenom} ! 🎉\r\n\r\nVous avez obtenu un excellent score de {score}% au questionnaire d\'orientation. Votre profil correspond parfaitement aux métiers recommandés.\r\n\r\n📊 Résultats :\r\n- Score : {score}%\r\n- Niveau : {niveau}\r\n- Bonnes réponses : {bonnes}/{total}\r\n- Filière : {filiere}\r\n\r\nJe vous invite à consulter les ressources suggérées pour approfondir vos connaissances.\r\n\r\nN\'hésitez pas à me contacter pour un entretien personnalisé.\r\n\r\nBravo pour ce travail ! 👏', 1),
(2, 'Bon score - À renforcer', 'Bonjour {prenom},\r\n\r\nBon travail ! Vous avez obtenu un score de {score}% au questionnaire. Vous avez de bonnes bases dans votre domaine.\r\n\r\n📊 Résultats :\r\n- Score : {score}%\r\n- Niveau : {niveau}\r\n- Bonnes réponses : {bonnes}/{total}\r\n- Filière : {filiere}\r\n\r\nJe vous recommande de consulter les ressources pédagogiques pour renforcer vos compétences.\r\n\r\nRestez motivé ! 💪', 1),
(3, 'Score moyen - Besoin de travail', 'Bonjour {prenom},\r\n\r\nJ\'ai pris connaissance des résultats de votre questionnaire. Votre score de {score}% montre que vous avez besoin de consolider certaines compétences.\r\n\r\n📊 Résultats :\r\n- Score : {score}%\r\n- Niveau : {niveau}\r\n- Bonnes réponses : {bonnes}/{total}\r\n- Filière : {filiere}\r\n\r\nNe vous découragez pas ! Je vous conseille vivement de consulter les ressources recommandées.\r\n\r\nJe reste disponible pour vous aider. 📚', 1),
(4, 'Score faible - Accompagnement renforcé', 'Bonjour {prenom},\r\n\r\nJ\'ai pris connaissance des résultats de votre questionnaire. Votre score de {score}% indique que vous auriez besoin d\'un accompagnement plus personnalisé.\r\n\r\n📊 Résultats :\r\n- Score : {score}%\r\n- Niveau : {niveau}\r\n- Bonnes réponses : {bonnes}/{total}\r\n- Filière : {filiere}\r\n\r\nJe vous propose de prendre rendez-vous avec moi pour discuter de votre parcours et des axes d\'amélioration.\r\n\r\nEnsemble, nous allons construire votre plan de progression. 🤝', 1);

-- --------------------------------------------------------

--
-- Structure de la table `orientations`
--

CREATE TABLE `orientations` (
  `id` int(11) NOT NULL,
  `etudiant_id` int(11) NOT NULL,
  `conseiller_id` int(11) NOT NULL,
  `metier_propose` varchar(150) NOT NULL,
  `metier_alternatif` varchar(150) DEFAULT NULL,
  `message_orientation` text DEFAULT NULL,
  `statut` enum('propose','accepte','refuse','en_attente') DEFAULT 'propose',
  `date_proposition` datetime DEFAULT current_timestamp(),
  `date_reponse` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `profils_etudiants`
--

CREATE TABLE `profils_etudiants` (
  `id` int(11) NOT NULL,
  `etudiant_id` int(11) NOT NULL,
  `filiere_id` int(11) DEFAULT NULL,
  `niveau` enum('L1','L2','L3','M1','M2') DEFAULT 'L1',
  `moyenne_generale` decimal(4,2) DEFAULT 0.00,
  `interets` text DEFAULT NULL,
  `biographie` text DEFAULT NULL,
  `date_maj` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `profils_etudiants`
--

INSERT INTO `profils_etudiants` (`id`, `etudiant_id`, `filiere_id`, `niveau`, `moyenne_generale`, `interets`, `biographie`, `date_maj`) VALUES
(4, 1, 1, 'L3', 19.00, 'IA', NULL, '2026-04-15 22:39:14'),
(5, 6, 5, 'L3', 15.00, 'chimie', NULL, '2026-04-16 00:22:05'),
(6, 7, 12, 'L1', 12.00, 'droit penal', NULL, '2026-04-16 10:28:41'),
(7, 8, 1, 'L3', 12.00, 'deployement web', NULL, '2026-04-16 18:53:07'),
(8, 9, 6, 'L1', 17.00, 'biologie organique', NULL, '2026-04-17 13:12:13'),
(9, 10, 16, 'L1', 15.00, 'histoire', NULL, '2026-04-17 19:19:32'),
(10, 11, 1, 'L1', 0.00, '', NULL, '2026-04-18 09:34:22'),
(11, 12, 1, 'L3', 17.00, 'IA', NULL, '2026-04-21 13:08:03'),
(12, 13, 12, 'M1', 13.00, 'droit penal', NULL, '2026-04-21 13:14:15');

-- --------------------------------------------------------

--
-- Structure de la table `questionnaires`
--

CREATE TABLE `questionnaires` (
  `id` int(11) NOT NULL,
  `filiere_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `option_a` varchar(255) DEFAULT NULL,
  `option_b` varchar(255) DEFAULT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `option_d` varchar(255) DEFAULT NULL,
  `bonne_reponse` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `questionnaires`
--

INSERT INTO `questionnaires` (`id`, `filiere_id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `bonne_reponse`) VALUES
(1, 1, 'Que signifie PHP ?', 'Hypertext Preprocessor', 'Personal Home Page', 'Pre Hypertext Processor', 'Public Home Page', 'a'),
(2, 1, 'Que signifie SQL ?', 'Structured Query Language', 'Simple Query Language', 'Standard Query Language', 'System Query Language', 'a'),
(3, 1, 'Que signifie HTML ?', 'HyperText Markup Language', 'HighText Machine Language', 'HyperText Markdown Language', 'HighText Markup Language', 'a'),
(4, 1, 'Que signifie CSS ?', 'Cascading Style Sheets', 'Creative Style Sheets', 'Computer Style Sheets', 'Colorful Style Sheets', 'a'),
(5, 1, 'JavaScript est un langage...', 'Côté client', 'Côté serveur uniquement', 'Compilé', 'Assembleur', 'a'),
(6, 1, 'Qu\'est-ce que Git ?', 'Logiciel de versionnement', 'Un langage', 'Une base de données', 'Un serveur web', 'a'),
(7, 1, 'Que signifie API ?', 'Application Programming Interface', 'Application Program Interface', 'Application Programming Internet', 'Application Program Internet', 'a'),
(8, 1, 'Qu\'est-ce que Docker ?', 'Un conteneur d\'applications', 'Un langage', 'Une base de données', 'Un navigateur', 'a'),
(9, 1, 'Que signifie HTTPS ?', 'HTTP sécurisé', 'High Transfer Protocol', 'Hyper Text Transfer Protocol', 'HTTP Standard', 'a'),
(10, 1, 'Qu\'est-ce qu\'un algorithme ?', 'Une suite d\'instructions', 'Un langage', 'Une base de données', 'Un serveur', 'a'),
(11, 1, 'Que signifie MVC ?', 'Modèle-Vue-Contrôleur', 'Modèle-Vue-Client', 'Modèle-Vue-Contrôle', 'Modèle-Vue-Contrôleur', 'a'),
(12, 1, 'Qu\'est-ce que le Machine Learning ?', 'Apprentissage automatique', 'Des robots', 'Des jeux vidéo', 'Des bases de données', 'a'),
(13, 1, 'Que signifie Big Data ?', 'Grandes masses de données', 'Grandes bases de données', 'Grands serveurs', 'Grands réseaux', 'a'),
(14, 1, 'Qu\'est-ce que la cybersécurité ?', 'Protection des systèmes informatiques', 'Création de virus', 'Hacking', 'Réseaux sociaux', 'a'),
(15, 1, 'Que signifie NoSQL ?', 'Not Only SQL', 'No SQL', 'Non SQL', 'New SQL', 'a'),
(16, 2, 'Que signifie TCP/IP ?', 'Transmission Control Protocol/Internet Protocol', 'Transfer Control Program/Internet Program', 'Transmission Control Program/Internet Protocol', 'Transfer Control Protocol/Internet Program', 'a'),
(17, 2, 'Quel est le rôle d\'un routeur ?', 'Connecter plusieurs réseaux', 'Sécuriser les données', 'Stocker les fichiers', 'Afficher des pages web', 'a'),
(18, 2, 'Qu\'est-ce qu\'une adresse IP ?', 'Une adresse unique pour chaque appareil', 'Un mot de passe', 'Un nom de domaine', 'Un type de câble', 'a'),
(19, 2, 'Que signifie DNS ?', 'Domain Name System', 'Data Network System', 'Digital Name Service', 'Domain Network Service', 'a'),
(20, 2, 'Qu\'est-ce qu\'un pare-feu ?', 'Système de sécurité réseau', 'Un navigateur', 'Un antivirus', 'Un câble réseau', 'a'),
(21, 2, 'Que signifie VPN ?', 'Virtual Private Network', 'Very Private Network', 'Virtual Public Network', 'Visual Private Network', 'a'),
(22, 2, 'Que signifie 5G ?', '5ème génération de réseau mobile', '5 Gigabits', '5 GHz', '5 Gigahertz', 'a'),
(23, 2, 'Qu\'est-ce que la fibre optique ?', 'Câble qui transmet la lumière', 'Antenne', 'Satellite', 'Routeur', 'a'),
(24, 2, 'Que signifie IoT ?', 'Internet of Things', 'Internet of Technology', 'Input Output Transfer', 'Internet Over Time', 'a'),
(25, 2, 'Que signifie LAN ?', 'Local Area Network', 'Large Area Network', 'Long Area Network', 'Low Area Network', 'a'),
(26, 3, 'Qu\'est-ce qu\'une dérivée ?', 'Mesure de variation d\'une fonction', 'Valeur constante', 'Équation', 'Inégalité', 'a'),
(27, 3, 'Que permet de calculer une intégrale ?', 'L\'aire sous une courbe', 'La pente', 'La moyenne', 'La médiane', 'a'),
(28, 3, 'Qu\'est-ce qu\'une matrice ?', 'Un tableau de nombres', 'Une équation', 'Une fonction', 'Un vecteur', 'a'),
(29, 3, 'Qu\'est-ce que la moyenne ?', 'Somme divisée par l\'effectif', 'Valeur du milieu', 'Valeur la plus fréquente', 'Différence max-min', 'a'),
(30, 3, 'Qu\'est-ce que l\'écart-type ?', 'Une mesure de dispersion', 'La moyenne', 'La médiane', 'Le mode', 'a'),
(31, 3, 'Que signifie \"théorème de Pythagore\" ?', 'a² + b² = c²', 'a + b = c', 'a × b = c', 'a / b = c', 'a'),
(32, 3, 'Qu\'est-ce que le nombre π ?', '3.14159...', '2.71828...', '1.61803...', '0.57721...', 'a'),
(33, 3, 'Qu\'est-ce qu\'une probabilité ?', 'Une mesure de chance', 'Une certitude', 'Une impossibilité', 'Une équation', 'a'),
(34, 3, 'Que signifie \"factorielle\" ?', 'n! = n × (n-1) × ... × 1', 'n! = n + (n-1) + ... + 1', 'n! = n × n', 'n! = n + n', 'a'),
(35, 3, 'Qu\'est-ce qu\'une fonction continue ?', 'Dessinable sans lever le crayon', 'Toujours croissante', 'Toujours décroissante', 'Toujours paire', 'a'),
(36, 10, 'Où se trouve le cœur ?', 'Cage thoracique à gauche', 'Abdomen', 'Crâne', 'Bassin', 'a'),
(37, 10, 'Combien d\'os compte le corps humain ?', '206', '150', '300', '250', 'a'),
(38, 10, 'Quel est le plus grand organe ?', 'La peau', 'Le foie', 'Les poumons', 'Le cœur', 'a'),
(39, 10, 'Quel est le rôle des reins ?', 'Filtrer le sang', 'Digérer', 'Pomper le sang', 'Respirer', 'a'),
(40, 10, 'Qu\'est-ce que l\'hypertension ?', 'Pression artérielle élevée', 'Fièvre', 'Infection', 'Allergie', 'a'),
(41, 10, 'Que signifie \"diabète\" ?', 'Excès de sucre dans le sang', 'Manque de fer', 'Infection', 'Fracture', 'a'),
(42, 10, 'Qu\'est-ce qu\'un virus ?', 'Agent infectieux microscopique', 'Bactérie', 'Champignon', 'Parasite', 'a'),
(43, 10, 'Qu\'est-ce qu\'un antibiotique ?', 'Tue les bactéries', 'Tue les virus', 'Tue les champignons', 'Tue les parasites', 'a'),
(44, 10, 'Que signifie \"diagnostic\" ?', 'Identification de la maladie', 'Traitement', 'Prévention', 'Rééducation', 'a'),
(45, 10, 'Quelle est la température normale ?', '37°C', '36°C', '38°C', '39°C', 'a'),
(46, 12, 'Qu\'est-ce que la Constitution ?', 'Loi fondamentale du pays', 'Un contrat', 'Un jugement', 'Un décret', 'a'),
(47, 12, 'Quels sont les trois pouvoirs ?', 'Exécutif, législatif, judiciaire', 'Politique, économique, social', 'Fédéral, régional, local', 'Primaire, secondaire, tertiaire', 'a'),
(48, 12, 'Qui détient le pouvoir législatif ?', 'Le Parlement', 'Le Président', 'Le Premier ministre', 'Les juges', 'a'),
(49, 12, 'Qu\'est-ce qu\'un contrat ?', 'Un accord de volontés', 'Une loi', 'Un jugement', 'Une amende', 'a'),
(50, 12, 'Que signifie \"responsabilité civile\" ?', 'Obligation de réparer un dommage', 'Peine de prison', 'Amende', 'Impôt', 'a'),
(51, 12, 'Qu\'est-ce qu\'un divorce ?', 'Dissolution du mariage', 'Un contrat', 'Séparation de biens', 'Adoption', 'a'),
(52, 12, 'Que signifie \"présomption d\'innocence\" ?', 'Accusé innocent jusqu\'à preuve du contraire', 'Accusé toujours coupable', 'Accusé doit prouver son innocence', 'Juge décide sans preuve', 'a'),
(53, 12, 'Qu\'est-ce que le droit civil ?', 'Relations entre personnes privées', 'Relations avec l\'État', 'Droit pénal', 'Droit constitutionnel', 'a'),
(54, 12, 'Qu\'est-ce qu\'un avocat ?', 'Défend les clients en justice', 'Juge les affaires', 'Fait les lois', 'Exécute les jugements', 'a'),
(55, 12, 'Que signifie \"tribunal\" ?', 'Juridiction qui juge les affaires', 'Police', 'Prison', 'Mairie', 'a'),
(56, 18, 'Que signifie marketing ?', 'Promotion d\'un produit', 'Gestion financière', 'Production', 'Ressources humaines', 'a'),
(57, 18, 'Qu\'est-ce que le SWOT ?', 'Forces, Faiblesses, Opportunités, Menaces', 'Stratégie, Web, Organisation, Temps', 'Suivi, Warrant, Objectif, Travail', 'Salaire, Week-end, Offre, Travail', 'a'),
(58, 18, 'Qu\'est-ce qu\'un bilan comptable ?', 'Document actifs/passifs', 'Relevé bancaire', 'Facture', 'Devis', 'a'),
(59, 18, 'Qu\'est-ce que la TVA ?', 'Taxe sur la Valeur Ajoutée', 'Taxe sur les Ventes', 'Taxe sur les Achats', 'Taxe sur les Apports', 'a'),
(60, 18, 'Qu\'est-ce que le PIB ?', 'Produit Intérieur Brut', 'Prix Intérieur de Base', 'Produit International Brut', 'Prix International de Base', 'a'),
(61, 18, 'Que signifie \"inflation\" ?', 'Hausse générale des prix', 'Baisse des prix', 'Stagnation', 'Croissance', 'a'),
(62, 18, 'Qu\'est-ce que le management ?', 'Gestion d\'une équipe/projet', 'Comptabilité', 'Marketing', 'Finance', 'a'),
(63, 18, 'Que signifie \"leadership\" ?', 'Capacité à diriger', 'Gestion financière', 'Analyse marketing', 'Production', 'a'),
(64, 18, 'Que signifie \"ROI\" ?', 'Return On Investment', 'Retour sur Investissement', 'Revenu sur Investissement', 'Rentabilité sur Investissement', 'a'),
(65, 18, 'Que signifie \"KPI\" ?', 'Key Performance Indicator', 'Indicateur Clé', 'Performance', 'Objectif', 'a'),
(66, 4, 'Quelle est la formule de la vitesse ?', 'v = d/t', 'v = d × t', 'v = t/d', 'v = d + t', 'a'),
(67, 4, 'Quelle est l\'unité de la force ?', 'Le Newton (N)', 'Le Joule (J)', 'Le Watt (W)', 'Le Pascal (Pa)', 'a'),
(68, 4, 'Que signifie \"énergie cinétique\" ?', 'Énergie liée au mouvement', 'Énergie stockée', 'Énergie thermique', 'Énergie lumineuse', 'a'),
(69, 4, 'Quelle est la loi d\'Ohm ?', 'U = R × I', 'U = I/R', 'I = U × R', 'R = U × I', 'a'),
(70, 4, 'Que signifie \"gravité\" ?', 'Force d\'attraction entre masses', 'Force magnétique', 'Force électrique', 'Force nucléaire', 'a'),
(71, 4, 'Quelle est la vitesse de la lumière ?', '300 000 km/s', '300 km/s', '3000 km/s', '30 000 km/s', 'a'),
(72, 4, 'Que signifie \"thermodynamique\" ?', 'Étude de la chaleur', 'Étude de l\'électricité', 'Étude du mouvement', 'Étude de la lumière', 'a'),
(73, 4, 'Qu\'est-ce qu\'un atome ?', 'La plus petite unité de matière', 'Une molécule', 'Un électron', 'Un proton', 'a'),
(74, 4, 'Que signifie \"électromagnétisme\" ?', 'Interaction entre électricité et magnétisme', 'Étude de la lumière', 'Étude du son', 'Étude de la chaleur', 'a'),
(75, 4, 'Qu\'est-ce que la mécanique quantique ?', 'Étude des particules subatomiques', 'Étude des planètes', 'Étude des fluides', 'Étude des solides', 'a'),
(76, 5, 'Que signifie H2O ?', 'L\'eau', 'L\'oxygène', 'L\'hydrogène', 'Le dioxyde de carbone', 'a'),
(77, 5, 'Que signifie pH ?', 'Potentiel hydrogène', 'Poids hydrogène', 'Pouvoir hydrogène', 'Point hydrogène', 'a'),
(78, 5, 'Quelle est la formule du dioxyde de carbone ?', 'CO2', 'CO', 'C2O', 'OC2', 'a'),
(79, 5, 'Qu\'est-ce qu\'un acide ?', 'Substance qui libère H+', 'Substance qui libère OH-', 'Substance neutre', 'Substance basique', 'a'),
(80, 5, 'Qu\'est-ce qu\'une base ?', 'Substance qui libère OH-', 'Substance qui libère H+', 'Substance neutre', 'Substance acide', 'a'),
(81, 5, 'Que signifie \"chimie organique\" ?', 'Chimie du carbone', 'Chimie des métaux', 'Chimie des gaz', 'Chimie de l\'eau', 'a'),
(82, 5, 'Qu\'est-ce qu\'une molécule ?', 'Association d\'atomes', 'Un atome seul', 'Un électron', 'Un proton', 'a'),
(83, 5, 'Que signifie \"oxydation\" ?', 'Perte d\'électrons', 'Gain d\'électrons', 'Perte de protons', 'Gain de protons', 'a'),
(84, 5, 'Qu\'est-ce qu\'un catalyseur ?', 'Accélère une réaction chimique', 'Ralentit une réaction', 'Arrête une réaction', 'Ne change rien', 'a'),
(85, 5, 'Que signifie \"réaction exothermique\" ?', 'Dégage de la chaleur', 'Absorbe de la chaleur', 'Ne change pas', 'Produit de la lumière', 'a'),
(86, 6, 'Qu\'est-ce que l\'ADN ?', 'Acide désoxyribonucléique', 'Acide ribonucléique', 'Protéine', 'Glucide', 'a'),
(87, 6, 'Quelle est l\'unité de base du vivant ?', 'La cellule', 'La molécule', 'L\'atome', 'L\'organe', 'a'),
(88, 6, 'Qu\'est-ce que la photosynthèse ?', 'Production de sucre par les plantes', 'Respiration des plantes', 'Digestion des plantes', 'Reproduction des plantes', 'a'),
(89, 6, 'Que signifie \"mitochondrie\" ?', 'Centrale énergétique de la cellule', 'Noyau de la cellule', 'Membrane cellulaire', 'Cytoplasme', 'a'),
(90, 6, 'Qu\'est-ce qu\'un gène ?', 'Segment d\'ADN', 'Protéine', 'Cellule', 'Organe', 'a'),
(91, 6, 'Que signifie \"évolution\" ?', 'Transformation des espèces', 'Création des espèces', 'Disparition des espèces', 'Fixité des espèces', 'a'),
(92, 6, 'Qu\'est-ce que l\'ARN ?', 'Acide ribonucléique', 'Acide désoxyribonucléique', 'Protéine', 'Glucide', 'a'),
(93, 6, 'Que signifie \"métabolisme\" ?', 'Ensemble des réactions chimiques', 'Structure de la cellule', 'Fonction des organes', 'Réproduction', 'a'),
(94, 6, 'Qu\'est-ce qu\'un écosystème ?', 'Interaction entre êtres vivants et environnement', 'Un groupe d\'animaux', 'Une forêt', 'Une rivière', 'a'),
(95, 6, 'Que signifie \"biodiversité\" ?', 'Variété des espèces vivantes', 'Nombre d\'animaux', 'Nombre de plantes', 'Nombre de bactéries', 'a'),
(96, 7, 'Qu\'est-ce que la résistance des matériaux ?', 'Capacité à supporter des forces', 'La couleur', 'Le prix', 'Le poids', 'a'),
(97, 7, 'Que signifie \"béton armé\" ?', 'Béton renforcé avec acier', 'Béton coloré', 'Béton léger', 'Béton isolant', 'a'),
(98, 7, 'Quel est le rôle d\'une poutre ?', 'Porter des charges en flexion', 'Décorer', 'Isoler', 'Éclairer', 'a'),
(99, 7, 'Qu\'est-ce qu\'une fondation ?', 'Partie en contact avec le sol', 'Le toit', 'Les murs', 'Les fenêtres', 'a'),
(100, 7, 'Qu\'est-ce qu\'un théodolite ?', 'Instrument de mesure des angles', 'Mètre ruban', 'Niveau à bulle', 'GPS', 'a'),
(101, 7, 'Qu\'est-ce que la topographie ?', 'Mesure du terrain', 'Mesure des bâtiments', 'Mesure des routes', 'Mesure des ponts', 'a'),
(102, 7, 'Que signifie \"BTP\" ?', 'Bâtiment et Travaux Publics', 'Bureau Technique Public', 'Bâtiment Travaux Privés', 'Bureau Technique Privé', 'a'),
(103, 7, 'Qu\'est-ce qu\'un ciment ?', 'Liant hydraulique', 'Matériau isolant', 'Matériau décoratif', 'Matériau conducteur', 'a'),
(104, 7, 'Que signifie \"permis de construire\" ?', 'Autorisation de construire', 'Déclaration de travaux', 'Certificat de conformité', 'Assurance dommage', 'a'),
(105, 7, 'Qu\'est-ce que le nivellement ?', 'Mesure des différences d\'altitude', 'Mesure des distances', 'Mesure des angles', 'Calcul des surfaces', 'a'),
(106, 8, 'Qu\'est-ce que la mécanique des fluides ?', 'L\'étude des liquides et gaz en mouvement', 'L\'étude des solides', 'L\'étude des matériaux', 'L\'étude des structures', 'a'),
(107, 8, 'Que signifie \"CAD\" ?', 'Conception Assistée par Ordinateur', 'Computer Aided Design', 'Calcul Assisté', 'Dessin Assisté', 'a'),
(108, 8, 'Qu\'est-ce qu\'un engrenage ?', 'Un système de transmission de mouvement', 'Un moteur', 'Une courroie', 'Un piston', 'a'),
(109, 8, 'Que signifie \"thermique\" ?', 'Étude de la chaleur', 'Étude de l\'électricité', 'Étude du mouvement', 'Étude de la lumière', 'a'),
(110, 8, 'Qu\'est-ce qu\'un piston ?', 'Un composant de moteur', 'Une roue', 'Un arbre', 'Un ressort', 'a'),
(111, 8, 'Que signifie \"maintenance\" ?', 'Entretien des machines', 'Fabrication', 'Conception', 'Vente', 'a'),
(112, 8, 'Qu\'est-ce que la résistance des matériaux ?', 'Capacité à supporter des forces', 'La couleur', 'Le prix', 'Le poids', 'a'),
(113, 8, 'Que signifie \"vibration\" ?', 'Oscillation mécanique', 'Bruit', 'Chaleur', 'Lumière', 'a'),
(114, 8, 'Qu\'est-ce qu\'un moteur électrique ?', 'Convertit l\'électricité en mouvement', 'Convertit le mouvement en électricité', 'Produit de la chaleur', 'Produit de la lumière', 'a'),
(115, 8, 'Que signifie \"CAO\" ?', 'Conception Assistée par Ordinateur', 'Calcul Assisté', 'Dessin Assisté', 'Fabrication Assistée', 'a'),
(116, 9, 'Quelle est l\'unité de la tension électrique ?', 'Le Volt (V)', 'L\'Ampère (A)', 'L\'Ohm (Ω)', 'Le Watt (W)', 'a'),
(117, 9, 'Quelle est l\'unité du courant électrique ?', 'L\'Ampère (A)', 'Le Volt (V)', 'L\'Ohm (Ω)', 'Le Watt (W)', 'a'),
(118, 9, 'Quelle est la loi d\'Ohm ?', 'U = R × I', 'U = I/R', 'I = U × R', 'R = U × I', 'a'),
(119, 9, 'Que signifie \"courant alternatif\" ?', 'Le courant change de sens périodiquement', 'Le courant est constant', 'Le courant est nul', 'Le courant est continu', 'a'),
(120, 9, 'Qu\'est-ce qu\'un transformateur ?', 'Change la tension électrique', 'Change la fréquence', 'Change l\'intensité', 'Change la puissance', 'a'),
(121, 9, 'Que signifie \"électronique\" ?', 'Étude des composants électroniques', 'Étude des moteurs', 'Étude des câbles', 'Étude des batteries', 'a'),
(122, 9, 'Qu\'est-ce qu\'un circuit électrique ?', 'Un chemin fermé pour le courant', 'Un interrupteur', 'Une lampe', 'Une pile', 'a'),
(123, 9, 'Que signifie \"puissance électrique\" ?', 'P = U × I', 'P = U/I', 'P = I/U', 'P = U + I', 'a'),
(124, 9, 'Qu\'est-ce qu\'un générateur ?', 'Produit de l\'électricité', 'Consomme de l\'électricité', 'Stocke l\'électricité', 'Transporte l\'électricité', 'a'),
(125, 9, 'Que signifie \"automatisme\" ?', 'Système qui fonctionne automatiquement', 'Système manuel', 'Système mécanique', 'Système hydraulique', 'a'),
(126, 11, 'Qu\'est-ce qu\'un médicament générique ?', 'Copie d\'un médicament breveté', 'Un médicament cher', 'Un médicament dangereux', 'Un médicament naturel', 'a'),
(127, 11, 'Que signifie \"posologie\" ?', 'Dose et fréquence du médicament', 'Prix du médicament', 'Forme du médicament', 'Couleur du médicament', 'a'),
(128, 11, 'Qu\'est-ce qu\'une contre-indication ?', 'Situation où le médicament est déconseillé', 'Effet secondaire', 'Surdosage', 'Allergie', 'a'),
(129, 11, 'Que signifie \"effet secondaire\" ?', 'Effet indésirable du médicament', 'Effet principal', 'Guérison', 'Prévention', 'a'),
(130, 11, 'Qu\'est-ce qu\'un excipient ?', 'Substance inactive dans un médicament', 'Principe actif', 'Médicament', 'Poison', 'a'),
(131, 11, 'Que signifie \"ordonnance\" ?', 'Prescription médicale', 'Médicament', 'Pharmacie', 'Traitement', 'a'),
(132, 11, 'Qu\'est-ce que la pharmacovigilance ?', 'Surveillance des effets des médicaments', 'Fabrication des médicaments', 'Vente des médicaments', 'Recherche des médicaments', 'a'),
(133, 11, 'Que signifie \"principe actif\" ?', 'Substance qui a l\'effet thérapeutique', 'Sucre', 'Colorant', 'Conservateur', 'a'),
(134, 11, 'Qu\'est-ce qu\'un antibiotique ?', 'Tue les bactéries', 'Tue les virus', 'Tue les champignons', 'Tue les parasites', 'a'),
(135, 11, 'Que signifie \"automedication\" ?', 'Se soigner sans médecin', 'Traitement par médecin', 'Hospitalisation', 'Urgence', 'a'),
(136, 13, 'Qu\'est-ce que la démocratie ?', 'Le pouvoir du peuple', 'Le pouvoir d\'un seul', 'Le pouvoir d\'une élite', 'L\'absence de pouvoir', 'a'),
(137, 13, 'Que signifie \"suffrage universel\" ?', 'Droit de vote pour tous', 'Vote pour les hommes', 'Vote pour les riches', 'Vote obligatoire', 'a'),
(138, 13, 'Qu\'est-ce que la République ?', 'Un système où le chef de l\'État est élu', 'Une monarchie', 'Une dictature', 'Une théocratie', 'a'),
(139, 13, 'Que signifie \"séparation des pouvoirs\" ?', 'Pouvoirs exécutif, législatif, judiciaire séparés', 'Un seul pouvoir', 'Deux pouvoirs', 'Pouvoir absolu', 'a'),
(140, 13, 'Qu\'est-ce qu\'une constitution ?', 'Loi fondamentale d\'un pays', 'Un traité', 'Un décret', 'Une loi ordinaire', 'a'),
(141, 13, 'Que signifie \"gauche politique\" ?', 'Courant politique progressiste', 'Courant conservateur', 'Courant extrémiste', 'Courant religieux', 'a'),
(142, 13, 'Qu\'est-ce que l\'abstention ?', 'Ne pas voter', 'Voter blanc', 'Voter nul', 'Voter pour tous', 'a'),
(143, 13, 'Que signifie \"référendum\" ?', 'Vote direct des citoyens', 'Vote des députés', 'Vote des sénateurs', 'Vote du président', 'a'),
(144, 13, 'Qu\'est-ce qu\'un parti politique ?', 'Organisation qui défend des idées', 'Le gouvernement', 'Le parlement', 'La justice', 'a'),
(145, 13, 'Que signifie \"coalition\" ?', 'Alliance de partis', 'Opposition', 'Majorité', 'Minorité', 'a'),
(146, 14, 'Qu\'est-ce qu\'un roman ?', 'Un récit fictif long', 'Un poème', 'Une pièce de théâtre', 'Un essai', 'a'),
(147, 14, 'Que signifie \"littérature\" ?', 'Ensemble des œuvres écrites', 'La grammaire', 'La conjugaison', 'L\'orthographe', 'a'),
(148, 14, 'Qu\'est-ce qu\'une métaphore ?', 'Comparaison sans outil de comparaison', 'Comparaison avec \"comme\"', 'Exagération', 'Personnification', 'a'),
(149, 14, 'Que signifie \"poésie\" ?', 'Art du langage en vers', 'Art du roman', 'Art du théâtre', 'Art de l\'essai', 'a'),
(150, 14, 'Qu\'est-ce qu\'une fable ?', 'Un court récit avec une morale', 'Un long roman', 'Une pièce de théâtre', 'Un poème', 'a'),
(151, 14, 'Que signifie \"grammaire\" ?', 'Ensemble des règles d\'une langue', 'Le vocabulaire', 'La conjugaison', 'L\'orthographe', 'a'),
(152, 14, 'Qu\'est-ce qu\'un auteur ?', 'Celui qui écrit une œuvre', 'Celui qui lit', 'Celui qui publie', 'Celui qui vend', 'a'),
(153, 14, 'Que signifie \"style littéraire\" ?', 'La manière d\'écrire d\'un auteur', 'Le sujet du livre', 'La longueur du livre', 'La date de publication', 'a'),
(154, 14, 'Qu\'est-ce qu\'un classique ?', 'Une œuvre reconnue par le temps', 'Un livre récent', 'Un livre pour enfants', 'Un livre scientifique', 'a'),
(155, 14, 'Que signifie \"genre littéraire\" ?', 'Catégorie d\'œuvre (roman, poésie, théâtre)', 'Le style', 'La longueur', 'La date', 'a'),
(156, 15, 'Que signifie \"philosophie\" ?', 'Amour de la sagesse', 'Science de la nature', 'Étude des dieux', 'Art de la parole', 'a'),
(157, 15, 'Qu\'est-ce que la métaphysique ?', 'Étude de l\'être et de la réalité', 'Étude de la nature', 'Étude de l\'homme', 'Étude de la société', 'a'),
(158, 15, 'Que signifie \"éthique\" ?', 'Étude des valeurs morales', 'Étude de la logique', 'Étude de la connaissance', 'Étude de l\'art', 'a'),
(159, 15, 'Qu\'est-ce que la logique ?', 'Étude du raisonnement valide', 'Étude des émotions', 'Étude de la nature', 'Étude de l\'homme', 'a'),
(160, 15, 'Que signifie \"épistémologie\" ?', 'Étude de la connaissance', 'Étude de l\'être', 'Étude de la morale', 'Étude de l\'art', 'a'),
(161, 15, 'Qu\'est-ce que le rationalisme ?', 'La raison comme source de connaissance', 'Les sens comme source de connaissance', 'La foi comme source', 'L\'intuition', 'a'),
(162, 15, 'Que signifie \"existentialisme\" ?', 'Courant philosophique sur l\'existence', 'Étude de l\'essence', 'Étude de Dieu', 'Étude de la nature', 'a'),
(163, 15, 'Qu\'est-ce que la dialectique ?', 'Art du dialogue et de la contradiction', 'Art du discours', 'Art de l\'écriture', 'Art de la lecture', 'a'),
(164, 15, 'Que signifie \"sophisme\" ?', 'Raisonnement fallacieux', 'Bonne démonstration', 'Preuve', 'Argument', 'a'),
(165, 15, 'Qu\'est-ce que le scepticisme ?', 'Doute méthodique', 'Croyance absolue', 'Certitude', 'Foi', 'a'),
(166, 16, 'Qu\'est-ce que l\'histoire ?', 'L\'étude du passé', 'L\'étude du présent', 'L\'étude du futur', 'L\'étude de l\'espace', 'a'),
(167, 16, 'Que signifie \"chronologie\" ?', 'Ordre des événements dans le temps', 'Carte géographique', 'Arbre généalogique', 'Liste des rois', 'a'),
(168, 16, 'Qu\'est-ce qu\'une source historique ?', 'Document du passé', 'Livre d\'histoire', 'Professeur d\'histoire', 'Musée', 'a'),
(169, 16, 'Que signifie \"archéologie\" ?', 'Étude des vestiges du passé', 'Étude des textes', 'Étude des langues', 'Étude des religions', 'a'),
(170, 16, 'Qu\'est-ce que la Révolution française ?', '1789', '1917', '1848', '1830', 'a'),
(171, 16, 'Que signifie \"Moyen Âge\" ?', 'Période de 476 à 1492', 'Période antique', 'Période moderne', 'Période contemporaine', 'a'),
(172, 16, 'Qu\'est-ce qu\'une guerre mondiale ?', 'Guerre impliquant plusieurs continents', 'Guerre entre deux pays', 'Guerre civile', 'Guerre religieuse', 'a'),
(173, 16, 'Que signifie \"colonisation\" ?', 'Occupation d\'un territoire par une puissance étrangère', 'Indépendance', 'Décolonisation', 'Migration', 'a'),
(174, 16, 'Qu\'est-ce que la Renaissance ?', 'Période de renouveau artistique et scientifique', 'Guerre', 'Peste', 'Famine', 'a'),
(175, 16, 'Que signifie \"historiographie\" ?', 'Étude de l\'écriture de l\'histoire', 'Étude des dates', 'Étude des cartes', 'Étude des monuments', 'a'),
(176, 17, 'Qu\'est-ce que l\'économie ?', 'Gestion des ressources rares', 'Étude de la nature', 'Étude de l\'homme', 'Étude de la société', 'a'),
(177, 17, 'Que signifie \"offre et demande\" ?', 'Loi du marché', 'Loi de la nature', 'Loi de l\'État', 'Loi morale', 'a'),
(178, 17, 'Qu\'est-ce que le PIB ?', 'Produit Intérieur Brut', 'Prix Intérieur de Base', 'Produit International Brut', 'Prix International de Base', 'a'),
(179, 17, 'Que signifie \"inflation\" ?', 'Hausse générale des prix', 'Baisse des prix', 'Stagnation', 'Croissance', 'a'),
(180, 17, 'Qu\'est-ce que le chômage ?', 'Situation sans emploi', 'Emploi à temps partiel', 'Emploi précaire', 'Retraite', 'a'),
(181, 17, 'Que signifie \"microéconomie\" ?', 'Comportement des agents économiques', 'Économie nationale', 'Économie mondiale', 'Finances publiques', 'a'),
(182, 17, 'Qu\'est-ce que la macroéconomie ?', 'Économie dans son ensemble', 'Économie d\'une entreprise', 'Économie d\'une famille', 'Économie locale', 'a'),
(183, 17, 'Que signifie \"budget de l\'État\" ?', 'Dépenses et recettes publiques', 'Budget des entreprises', 'Budget des ménages', 'Budget des banques', 'a'),
(184, 17, 'Qu\'est-ce qu\'une récession ?', 'Baisse de l\'activité économique', 'Croissance économique', 'Stabilité', 'Inflation', 'a'),
(185, 17, 'Que signifie \"libre-échange\" ?', 'Suppression des barrières commerciales', 'Protectionnisme', 'Autarcie', 'Colonialisme', 'a'),
(186, 19, 'Qu\'est-ce que le journalisme ?', 'Recherche et diffusion de l\'information', 'Création de publicité', 'Écriture de romans', 'Enseignement', 'a'),
(187, 19, 'Que signifie \"une\" dans un journal ?', 'La première page', 'Le titre', 'L\'éditorial', 'La rubrique sport', 'a'),
(188, 19, 'Qu\'est-ce qu\'un reportage ?', 'Enquête sur le terrain', 'Interview', 'Chronique', 'Éditorial', 'a'),
(189, 19, 'Que signifie \"déontologie\" ?', 'Code d\'éthique professionnelle', 'Technique d\'écriture', 'Loi', 'Règlement', 'a'),
(190, 19, 'Qu\'est-ce qu\'une interview ?', 'Entretien avec une personne', 'Enquête', 'Reportage', 'Chronique', 'a'),
(191, 19, 'Que signifie \"source\" ?', 'Origine de l\'information', 'Journal', 'Témoin', 'Document', 'a'),
(192, 19, 'Qu\'est-ce que la une ?', 'Première page du journal', 'Dernière page', 'Page centrale', 'Page sport', 'a'),
(193, 19, 'Que signifie \"dépêche\" ?', 'Information courte et rapide', 'Grand article', 'Enquête', 'Interview', 'a'),
(194, 19, 'Qu\'est-ce qu\'un éditorial ?', 'Article d\'opinion du directeur', 'Information neutre', 'Reportage', 'Interview', 'a'),
(195, 19, 'Que signifie \"photographie de presse\" ?', 'Image illustrant un article', 'Photo d\'art', 'Portrait', 'Paysage', 'a'),
(196, 20, 'Qu\'est-ce que l\'architecture ?', 'Art de concevoir des bâtiments', 'Art de peindre', 'Art de sculpter', 'Art de dessiner', 'a'),
(197, 20, 'Que signifie \"plan\" ?', 'Dessin technique d\'un bâtiment', 'Carte géographique', 'Schéma électrique', 'Maquette', 'a'),
(198, 20, 'Qu\'est-ce qu\'une maquette ?', 'Modèle réduit d\'un bâtiment', 'Dessin', 'Plan', 'Photo', 'a'),
(199, 20, 'Que signifie \"urbanisme\" ?', 'Aménagement des villes', 'Construction des bâtiments', 'Décoration intérieure', 'Paysagisme', 'a'),
(200, 20, 'Qu\'est-ce qu\'un permis de construire ?', 'Autorisation de construire', 'Plan de construction', 'Devis', 'Contrat', 'a'),
(201, 20, 'Que signifie \"façade\" ?', 'Face avant d\'un bâtiment', 'Toit', 'Mur', 'Fenêtre', 'a'),
(202, 20, 'Qu\'est-ce qu\'un architecte ?', 'Professionnel qui conçoit des bâtiments', 'Maçon', 'Ingénieur', 'Designer', 'a'),
(203, 20, 'Que signifie \"matériau\" ?', 'Élément utilisé pour construire', 'Outil', 'Machine', 'Équipement', 'a'),
(204, 20, 'Qu\'est-ce que le développement durable ?', 'Construction respectueuse de l\'environnement', 'Construction rapide', 'Construction économique', 'Construction esthétique', 'a'),
(205, 20, 'Que signifie \"habitat\" ?', 'Lieu de vie', 'Bureau', 'Usine', 'École', 'a');

-- --------------------------------------------------------

--
-- Structure de la table `reponses_etudiants`
--

CREATE TABLE `reponses_etudiants` (
  `id` int(11) NOT NULL,
  `etudiant_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `reponse` char(1) DEFAULT NULL,
  `date_reponse` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reponses_etudiants`
--

INSERT INTO `reponses_etudiants` (`id`, `etudiant_id`, `question_id`, `reponse`, `date_reponse`) VALUES
(1, 1, 1, 'b', '2026-04-15 22:49:20'),
(2, 1, 2, 'a', '2026-04-15 22:49:20'),
(3, 1, 3, 'a', '2026-04-15 22:49:21'),
(4, 1, 4, 'a', '2026-04-15 22:49:21'),
(5, 1, 5, 'a', '2026-04-15 22:49:21'),
(6, 1, 6, 'a', '2026-04-15 22:49:21'),
(7, 1, 7, 'a', '2026-04-15 22:49:21'),
(8, 1, 8, 'a', '2026-04-15 22:49:21'),
(9, 1, 9, 'c', '2026-04-15 22:49:21'),
(10, 1, 10, 'a', '2026-04-15 22:49:21'),
(11, 1, 11, 'a', '2026-04-15 22:49:21'),
(12, 1, 12, 'a', '2026-04-15 22:49:21'),
(13, 1, 13, 'b', '2026-04-15 22:49:21'),
(14, 1, 14, 'a', '2026-04-15 22:49:21'),
(15, 1, 15, 'c', '2026-04-15 22:49:21'),
(46, 6, 76, 'a', '2026-04-16 00:32:17'),
(47, 6, 77, 'a', '2026-04-16 00:32:17'),
(48, 6, 78, 'a', '2026-04-16 00:32:17'),
(49, 6, 80, 'a', '2026-04-16 00:32:17'),
(50, 6, 81, 'a', '2026-04-16 00:32:17'),
(51, 6, 82, 'a', '2026-04-16 00:32:17'),
(52, 6, 83, 'a', '2026-04-16 00:32:17'),
(53, 6, 84, 'a', '2026-04-16 00:32:17'),
(54, 6, 85, 'a', '2026-04-16 00:32:17'),
(58, 6, 79, 'a', '2026-04-16 00:32:51'),
(65, 7, 46, 'a', '2026-04-16 10:31:35'),
(66, 7, 47, 'a', '2026-04-16 10:31:35'),
(67, 7, 48, 'a', '2026-04-16 10:31:35'),
(68, 7, 49, 'a', '2026-04-16 10:31:35'),
(69, 7, 50, 'a', '2026-04-16 10:31:35'),
(70, 7, 51, 'a', '2026-04-16 10:31:35'),
(71, 7, 52, 'a', '2026-04-16 10:31:35'),
(72, 7, 53, 'a', '2026-04-16 10:31:35'),
(73, 7, 54, 'a', '2026-04-16 10:31:36'),
(74, 7, 55, 'a', '2026-04-16 10:31:36'),
(115, 8, 1, 'b', '2026-04-16 18:55:19'),
(116, 8, 2, 'a', '2026-04-16 18:55:19'),
(117, 8, 3, 'a', '2026-04-16 18:55:19'),
(118, 8, 4, 'a', '2026-04-16 18:55:19'),
(119, 8, 5, 'b', '2026-04-16 18:55:19'),
(120, 8, 6, 'a', '2026-04-16 18:55:19'),
(121, 8, 7, 'a', '2026-04-16 18:55:19'),
(122, 8, 8, 'd', '2026-04-16 18:55:20'),
(123, 8, 9, 'c', '2026-04-16 18:55:20'),
(124, 8, 10, 'a', '2026-04-16 18:55:20'),
(125, 8, 11, 'a', '2026-04-16 18:55:20'),
(126, 8, 12, 'a', '2026-04-16 18:55:20'),
(127, 8, 13, 'a', '2026-04-16 18:55:20'),
(128, 8, 14, 'a', '2026-04-16 18:55:20'),
(129, 8, 15, 'c', '2026-04-16 18:55:20'),
(160, 9, 86, 'c', '2026-04-17 13:16:47'),
(161, 9, 87, 'd', '2026-04-17 13:16:47'),
(162, 9, 88, 'b', '2026-04-17 13:16:47'),
(163, 9, 89, 'c', '2026-04-17 13:16:47'),
(164, 9, 90, 'c', '2026-04-17 13:16:48'),
(165, 9, 91, 'c', '2026-04-17 13:16:48'),
(166, 9, 92, 'c', '2026-04-17 13:16:48'),
(167, 9, 93, 'a', '2026-04-17 13:16:48'),
(168, 9, 94, 'a', '2026-04-17 13:16:48'),
(169, 9, 95, 'a', '2026-04-17 13:16:49'),
(190, 10, 166, 'a', '2026-04-17 19:23:37'),
(191, 10, 167, 'a', '2026-04-17 19:23:37'),
(192, 10, 168, 'a', '2026-04-17 19:23:37'),
(193, 10, 169, 'a', '2026-04-17 19:23:38'),
(194, 10, 170, 'a', '2026-04-17 19:23:38'),
(195, 10, 171, 'd', '2026-04-17 19:23:38'),
(196, 10, 172, 'a', '2026-04-17 19:23:38'),
(197, 10, 173, 'a', '2026-04-17 19:23:39'),
(198, 10, 174, 'a', '2026-04-17 19:23:39'),
(199, 10, 175, 'a', '2026-04-17 19:23:39'),
(200, 11, 1, 'a', '2026-04-18 09:37:17'),
(201, 11, 2, 'a', '2026-04-18 09:37:17'),
(202, 11, 3, 'a', '2026-04-18 09:37:17'),
(203, 11, 4, 'a', '2026-04-18 09:37:17'),
(204, 11, 5, 'a', '2026-04-18 09:37:17'),
(205, 11, 6, 'a', '2026-04-18 09:37:17'),
(206, 11, 7, 'a', '2026-04-18 09:37:17'),
(207, 11, 8, 'a', '2026-04-18 09:37:17'),
(208, 11, 10, 'a', '2026-04-18 09:37:17'),
(209, 11, 11, 'a', '2026-04-18 09:37:17'),
(210, 11, 12, 'a', '2026-04-18 09:37:17'),
(211, 11, 13, 'a', '2026-04-18 09:37:18'),
(212, 11, 14, 'a', '2026-04-18 09:37:18'),
(213, 11, 15, 'c', '2026-04-18 09:37:18'),
(214, 12, 1, 'a', '2026-04-21 13:09:16'),
(215, 12, 2, 'a', '2026-04-21 13:09:17'),
(216, 12, 3, 'a', '2026-04-21 13:09:17'),
(217, 12, 4, 'a', '2026-04-21 13:09:17'),
(218, 12, 5, 'a', '2026-04-21 13:09:17'),
(219, 12, 6, 'a', '2026-04-21 13:09:17'),
(220, 12, 7, 'a', '2026-04-21 13:09:17'),
(221, 12, 8, 'a', '2026-04-21 13:09:18'),
(222, 12, 9, 'a', '2026-04-21 13:09:19'),
(223, 12, 10, 'a', '2026-04-21 13:09:19'),
(224, 12, 11, 'a', '2026-04-21 13:09:19'),
(225, 12, 12, 'b', '2026-04-21 13:09:19'),
(226, 12, 13, 'a', '2026-04-21 13:09:19'),
(227, 12, 14, 'a', '2026-04-21 13:09:20'),
(228, 12, 15, 'c', '2026-04-21 13:09:20');

-- --------------------------------------------------------

--
-- Structure de la table `ressources`
--

CREATE TABLE `ressources` (
  `id` int(11) NOT NULL,
  `titre` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `lien` varchar(500) DEFAULT NULL,
  `type` enum('article','video','cours','livre') DEFAULT 'article',
  `filiere_id` int(11) DEFAULT NULL,
  `ajoutee_par` int(11) DEFAULT NULL,
  `date_ajout` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ressources`
--

INSERT INTO `ressources` (`id`, `titre`, `description`, `lien`, `type`, `filiere_id`, `ajoutee_par`, `date_ajout`) VALUES
(16, 'Cours complet PHP', 'Apprenez PHP de zéro à expert - Langage de programmation pour le web', 'https://www.php.net/manual/fr/', 'cours', NULL, 1, '2026-04-16 00:37:29'),
(17, 'Introduction à Python', 'Débuter avec Python - Langage polyvalent pour la data science', 'https://docs.python.org/fr/3/tutorial/', 'cours', NULL, 1, '2026-04-16 00:37:29'),
(18, 'HTML5 et CSS3', 'Créer des sites web modernes et responsives', 'https://developer.mozilla.org/fr/docs/Web', 'cours', NULL, 1, '2026-04-16 00:37:29'),
(19, 'Base de données SQL', 'Maîtriser SQL pour gérer vos données', 'https://www.w3schools.com/sql/', 'cours', NULL, 1, '2026-04-16 00:37:29'),
(20, 'JavaScript moderne', 'Apprenez ES6, React, Vue.js', 'https://javascript.info/', 'cours', NULL, 1, '2026-04-16 00:37:29'),
(21, 'Git et GitHub', 'Gestion de versions et collaboration', 'https://git-scm.com/doc', 'cours', NULL, 1, '2026-04-16 00:37:29'),
(22, 'Cybersécurité', 'Protégez vos systèmes informatiques', 'https://www.cyber.gouv.fr/', 'article', NULL, 1, '2026-04-16 00:37:29'),
(23, 'Intelligence Artificielle', 'Introduction au Machine Learning', 'https://developers.google.com/machine-learning', 'cours', NULL, 1, '2026-04-16 00:37:29'),
(24, 'Développement Web Full Stack', 'Devenez développeur complet', 'https://openclassrooms.com/fr/courses', 'cours', NULL, 1, '2026-04-16 00:37:29'),
(25, 'Algorithmique', 'Les bases de la programmation', 'https://www.coursera.org/learn/algorithms', 'cours', NULL, 1, '2026-04-16 00:37:29'),
(26, 'Réseaux TCP/IP', 'Comprendre les protocoles réseau', 'https://www.cisco.com/c/fr_fr/solutions/ip-networking.html', 'cours', 2, 1, '2026-04-16 00:37:29'),
(27, 'Sécurité réseau', 'Protégez votre infrastructure', 'https://www.cisco.com/c/fr_fr/products/security/index.html', 'cours', NULL, 1, '2026-04-16 00:37:29'),
(28, 'Administration réseau', 'Gérez les serveurs et équipements', 'https://openclassrooms.com/fr/courses/7168871-administrez-les-reseaux-et-securisez-les-donnees', 'cours', NULL, 1, '2026-04-16 00:37:29'),
(29, 'Fibre optique', 'Technologie et déploiement', 'https://www.arcep.fr/la-fibre-optique.html', 'article', NULL, 1, '2026-04-16 00:37:29'),
(30, '5G et télécoms', 'La nouvelle génération mobile', 'https://www.5gobservatory.eu/', 'article', NULL, 1, '2026-04-16 00:37:29'),
(31, 'Cisco CCNA', 'Certification réseau', 'https://www.cisco.com/c/fr_fr/training-events/training-certifications/certifications/associate/ccna.html', 'cours', NULL, 1, '2026-04-16 00:37:29'),
(32, 'Virtualisation réseau', 'SDN, NFV, Cloud', 'https://www.vmware.com/fr/solutions/networking.html', 'cours', NULL, 1, '2026-04-16 00:37:29'),
(33, 'Wireshark', 'Analyse de trafic réseau', 'https://www.wireshark.org/', 'video', NULL, 1, '2026-04-16 00:37:29'),
(34, 'Linux pour réseaux', 'Administration système', 'https://linuxjourney.com/', 'cours', 2, 1, '2026-04-16 00:37:29'),
(35, 'IoT et objets connectés', 'Internet des objets', 'https://www.iot.fr/', 'article', NULL, 1, '2026-04-16 00:37:29'),
(36, 'Cours de mathématiques', 'Tous les niveaux', 'https://www.maths-france.fr/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(37, 'Khan Academy Maths', 'Vidéos et exercices', 'https://fr.khanacademy.org/math', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(38, 'Fonctions et équations', 'Révisions complètes', 'https://www.fonctions-equations.fr/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(39, 'Statistiques et probabilités', 'Cours et exercices', 'https://www.statistiques.fr/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(40, 'Algèbre linéaire', 'Matrices, vecteurs', 'https://www.bibmath.net/ressources/algebrelineaire.php', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(41, 'Analyse mathématique', 'Dérivées, intégrales', 'https://www.analyse-math.fr/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(42, 'Géométrie', 'Cours et problèmes', 'https://www.geometrie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(43, 'Probabilités', 'Introduction aux probas', 'https://www.probabilites.fr/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(44, 'Mathématiques financières', 'Applications pratiques', 'https://www.math-finance.fr/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(45, 'Olympiades maths', 'Préparation concours', 'https://www.maths-olympiades.fr/', 'article', NULL, 1, '2026-04-16 00:37:30'),
(46, 'Cours de physique', 'Mécanique, électricité, thermodynamique', 'https://www.physique.fr/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(47, 'Mécanique quantique', 'Introduction', 'https://www.quantum-physics.fr/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(48, 'Électromagnétisme', 'Cours et exercices', 'https://www.electromagnetisme.fr/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(49, 'Thermodynamique', 'Principes fondamentaux', 'https://www.thermodynamique.fr/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(50, 'Optique', 'Lentilles, miroirs, lasers', 'https://www.optique.fr/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(51, 'Physique nucléaire', 'Atomes et radioactivité', 'https://www.nucleaire.fr/', 'article', NULL, 1, '2026-04-16 00:37:30'),
(52, 'Astrophysique', 'Étoiles, planètes, univers', 'https://www.astrophysique.fr/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(53, 'Laboratoire virtuel', 'Simulations physiques', 'https://phet.colorado.edu/fr/', 'video', NULL, 1, '2026-04-16 00:37:30'),
(54, 'Relativité', 'Einstein et la relativité', 'https://www.relativite.fr/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(55, 'Physique des particules', 'Modèle standard', 'https://www.particules.fr/', 'article', NULL, 1, '2026-04-16 00:37:30'),
(56, 'Cours de chimie', 'Organique, inorganique, analytique', 'https://www.chimie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(57, 'Chimie organique', 'Molécules, réactions', 'https://www.chimie-organique.net/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(58, 'Tableau périodique', 'Éléments chimiques', 'https://www.periodictable.com/', 'video', NULL, 1, '2026-04-16 00:37:30'),
(59, 'Chimie analytique', 'Méthodes d\'analyse', 'https://www.chimie-analytique.fr/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(60, 'Laboratoire virtuel', 'Expériences en ligne', 'https://www.labster.com/fr/', 'video', NULL, 1, '2026-04-16 00:37:30'),
(61, 'Biochimie', 'Chimie du vivant', 'https://www.biochimie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(62, 'Chimie des solutions', 'pH, concentration', 'https://www.solutions-chimie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(63, 'Thermochimie', 'Réactions et énergie', 'https://www.thermochimie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:30'),
(64, 'Spectroscopie', 'Analyse spectrale', 'https://www.spectroscopie.fr/', 'article', NULL, 1, '2026-04-16 00:37:30'),
(65, 'Green Chemistry', 'Chimie durable', 'https://www.greenchemistry.com/', 'article', NULL, 1, '2026-04-16 00:37:30'),
(66, 'Cours de biologie', 'Cellule, génétique, évolution', 'https://www.biologie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(67, 'Génétique', 'ADN, ARN, hérédité', 'https://www.genetique.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(68, 'Biologie cellulaire', 'Structure de la cellule', 'https://www.biologie-cellulaire.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(69, 'Microbiologie', 'Bactéries, virus, champignons', 'https://www.microbiologie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(70, 'Écologie', 'Écosystèmes, biodiversité', 'https://www.ecologie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(71, 'Anatomie', 'Systèmes du corps humain', 'https://www.anatomie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(72, 'Biologie marine', 'Océans et vie marine', 'https://www.biologie-marine.fr/', 'article', NULL, 1, '2026-04-16 00:37:31'),
(73, 'Botanique', 'Plantes et végétaux', 'https://www.botanique.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(74, 'Zoologie', 'Étude des animaux', 'https://www.zoologie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(75, 'Évolution', 'Théorie de Darwin', 'https://www.evolution.fr/', 'article', NULL, 1, '2026-04-16 00:37:31'),
(76, 'Anatomie humaine', 'Systèmes et organes', 'https://www.msdmanuals.com/fr/professional', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(77, 'Pharmacologie', 'Médicaments et thérapies', 'https://www.pharmacologie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(78, 'Pathologies', 'Maladies et diagnostics', 'https://www.pathologies.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(79, 'Urgences médicales', 'Conduite à tenir', 'https://www.urgence-medicale.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(80, 'Pédiatrie', 'Médecine des enfants', 'https://www.pediatrie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(81, 'Cardiologie', 'Cœur et vaisseaux', 'https://www.cardiologie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(82, 'Neurologie', 'Système nerveux', 'https://www.neurologie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(83, 'Chirurgie', 'Techniques opératoires', 'https://www.chirurgie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(84, 'Radiologie', 'Imagerie médicale', 'https://www.radiologie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(85, 'Santé publique', 'Prévention et épidémiologie', 'https://www.sante-publique.fr/', 'article', NULL, 1, '2026-04-16 00:37:31'),
(86, 'Code civil', 'Loi fondamentale', 'https://www.legifrance.gouv.fr/code/civil', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(87, 'Code pénal', 'Droit des infractions', 'https://www.legifrance.gouv.fr/code/penal', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(88, 'Droit des affaires', 'Entreprises et commerce', 'https://www.droit-des-affaires.fr/', 'cours', 12, 1, '2026-04-16 00:37:31'),
(89, 'Droit du travail', 'Relations professionnelles', 'https://www.droit-travail.fr/', 'cours', 12, 1, '2026-04-16 00:37:31'),
(90, 'Droit constitutionnel', 'Fondamentaux', 'https://www.droit-constitutionnel.fr/', 'cours', 12, 1, '2026-04-16 00:37:31'),
(91, 'Procédure civile', 'Règles de procédure', 'https://www.procedure-civile.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(92, 'Droit administratif', 'Relations avec l\'État', 'https://www.droit-administratif.fr/', 'cours', 12, 1, '2026-04-16 00:37:31'),
(93, 'Droit européen', 'Institutions et lois', 'https://www.droit-europeen.fr/', 'cours', 12, 1, '2026-04-16 00:37:31'),
(94, 'Jurisprudence', 'Décisions de justice', 'https://www.jurisprudence.fr/', 'article', NULL, 1, '2026-04-16 00:37:31'),
(95, 'Préparation CRFPA', 'Devenir avocat', 'https://www.crfpa.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(96, 'Management', 'Leadership et gestion d\'équipe', 'https://www.manager-go.com/', 'cours', 18, 1, '2026-04-16 00:37:31'),
(97, 'Comptabilité', 'Principes fondamentaux', 'https://www.comptabilite.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(98, 'Marketing digital', 'Stratégies en ligne', 'https://www.marketing-digital.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(99, 'Finance d\'entreprise', 'Analyse financière', 'https://www.finance-entreprise.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(100, 'Ressources humaines', 'Gestion du personnel', 'https://www.rh.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(101, 'Gestion de projet', 'Méthodes agiles', 'https://www.gestion-projet.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(102, 'Entrepreneuriat', 'Créer son entreprise', 'https://www.entrepreneuriat.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(103, 'Logistique', 'Supply chain', 'https://www.logistique.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(104, 'Négociation', 'Techniques commerciales', 'https://www.negociation.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(105, 'Business Plan', 'Préparer son projet', 'https://www.business-plan.fr/', 'article', NULL, 1, '2026-04-16 00:37:31'),
(106, 'Dessin technique', 'Plans et coupes', 'https://www.dessin-technique.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(107, 'AutoCAD', 'Logiciel de CAO', 'https://www.autodesk.fr/products/autocad/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(108, 'Urbanisme', 'Aménagement du territoire', 'https://www.urbanisme.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(109, 'Histoire de l\'architecture', 'Styles et mouvements', 'https://www.histoire-architecture.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(110, 'BIM', 'Modélisation 3D', 'https://www.bim.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(111, 'Matériaux de construction', 'Propriétés et usages', 'https://www.materiaux-construction.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(112, 'Architecture durable', 'Éco-construction', 'https://www.architecture-durable.fr/', 'article', NULL, 1, '2026-04-16 00:37:31'),
(113, 'SketchUp', 'Modélisation 3D', 'https://www.sketchup.com/fr', 'video', NULL, 1, '2026-04-16 00:37:31'),
(114, 'Rénovation', 'Réhabilitation', 'https://www.renovation.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(115, 'Design d\'intérieur', 'Aménagement', 'https://www.design-interieur.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(116, 'Écriture journalistique', 'Techniques d\'écriture', 'https://www.ecriture-journalistique.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(117, 'Déontologie', 'Éthique du journalisme', 'https://www.deontologie-journalisme.fr/', 'article', NULL, 1, '2026-04-16 00:37:31'),
(118, 'Reportage', 'Enquête sur le terrain', 'https://www.reportage.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(119, 'Montage vidéo', 'Adobe Premiere, Final Cut', 'https://www.montage-video.fr/', 'video', NULL, 1, '2026-04-16 00:37:31'),
(120, 'Radio', 'Techniques audio', 'https://www.radio-journalisme.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(121, 'Photojournalisme', 'Image et information', 'https://www.photojournalisme.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(122, 'Médias sociaux', 'Communication digitale', 'https://www.medias-sociaux.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(123, 'Enquête', 'Journalisme d\'investigation', 'https://www.enquete-journalistique.fr/', 'article', NULL, 1, '2026-04-16 00:37:31'),
(124, 'Presse écrite', 'Rédaction magazine', 'https://www.presse-ecrite.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(125, 'Webjournalisme', 'Journalisme en ligne', 'https://www.webjournalisme.fr/', 'cours', NULL, 1, '2026-04-16 00:37:31'),
(126, 'Introduction à la psychologie', 'Bases fondamentales', 'https://www.psychologie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(127, 'Psychologie clinique', 'Troubles et thérapies', 'https://www.psychologie-clinique.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(128, 'Psychologie cognitive', 'Mémoire, attention', 'https://www.psychologie-cognitive.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(129, 'Psychologie sociale', 'Comportement en groupe', 'https://www.psychologie-sociale.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(130, 'Neuropsychologie', 'Cerveau et comportement', 'https://www.neuropsychologie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(131, 'Psychométrie', 'Tests et évaluations', 'https://www.psychometrie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(132, 'Psychopathologie', 'Maladies mentales', 'https://www.psychopathologie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(133, 'Thérapies', 'CBT, EMDR, psychanalyse', 'https://www.therapies.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(134, 'Psychologie du développement', 'Enfance et vieillissement', 'https://www.developpement-psycho.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(135, 'Psychologie du travail', 'RH et organisation', 'https://www.psychologie-travail.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(136, 'Introduction à la philosophie', 'Les grands penseurs', 'https://www.philosophie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(137, 'Métaphysique', 'L\'être et la réalité', 'https://www.metaphysique.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(138, 'Éthique', 'Valeurs morales', 'https://www.ethique.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(139, 'Logique', 'Raisonnement et arguments', 'https://www.logique-philo.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(140, 'Épistémologie', 'Théorie de la connaissance', 'https://www.epistemologie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(141, 'Platon', 'Œuvres complètes', 'https://www.platon.fr/', 'article', NULL, 1, '2026-04-16 00:37:32'),
(142, 'Aristote', 'Philosophie grecque', 'https://www.aristote.fr/', 'article', NULL, 1, '2026-04-16 00:37:32'),
(143, 'Philosophie politique', 'État et société', 'https://www.philosophie-politique.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(144, 'Existentialisme', 'Sartre, Camus', 'https://www.existentialisme.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(145, 'Philosophie des sciences', 'Science et vérité', 'https://www.philosophie-sciences.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(146, 'Introduction à l\'économie', 'Bases et concepts', 'https://www.economie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(147, 'Microéconomie', 'Comportement des agents', 'https://www.microeconomie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(148, 'Macroéconomie', 'Économie globale', 'https://www.macroeconomie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(149, 'Économétrie', 'Statistiques économiques', 'https://www.econometrie.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(150, 'Finance internationale', 'Marchés et devises', 'https://www.finance-internationale.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(151, 'Politique économique', 'Rôle de l\'État', 'https://www.politique-economique.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(152, 'Développement économique', 'Croissance et inégalités', 'https://www.developpement-economique.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(153, 'Commerce international', 'Mondialisation', 'https://www.commerce-international.fr/', 'cours', NULL, 1, '2026-04-16 00:37:32'),
(154, 'Histoire de la pensée économique', 'Grands économistes', 'https://www.histoire-pensee-economique.fr/', 'article', NULL, 1, '2026-04-16 00:37:32'),
(155, 'Économie circulaire', 'Développement durable', 'https://www.economie-circulaire.fr/', 'article', NULL, 1, '2026-04-16 00:37:32');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('etudiant','conseiller','admin') DEFAULT 'etudiant',
  `date_inscription` datetime DEFAULT current_timestamp(),
  `actif` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `role`, `date_inscription`, `actif`) VALUES
(1, 'admin', 'Système', 'admin@orientation.cm', '$2y$10$OJ/AMg/89HnH3egZaLgX0ub4FyMecTMkiBhy9Qtx2HpQGOmCcPvcq', 'admin', '2026-04-15 22:35:49', 1),
(2, 'Nguemoo', 'Paul', 'conseiller@orientation.com', '$2y$10$VwlyFcuWJqtJUy6rRAZ/ZOVXlm8cOgVNkVmbPqGjTpOXUCcSjpHwO', 'conseiller', '2026-04-15 22:35:49', 1),
(6, 'DOUMBA', 'JEAN', 'doumbajean@gmail.com', '$2y$10$d9SGrL8IbNMwwyn.NP/kEeK8dclUiSBGOm5g4mg0nq4NNpIMafpiu', 'etudiant', '2026-04-16 00:21:12', 1),
(7, 'ali', 'ahmat', 'aliahmat@gmail.com', '$2y$10$3i4Qm7rkUo6N7JODDpJGHuOSQnni6AM1l.2Pks1PYreKQ14BwI0OO', 'etudiant', '2026-04-16 10:27:49', 1),
(8, 'valentin', 'pack', 'pack@gmail.com', '$2y$10$aG2/VgK/5F4OBUbupLQwA.ZK/Tq/UX5Vye5xXh6OPLZ4lAMZAVKrO', 'etudiant', '2026-04-16 18:52:03', 1),
(9, 'haroun', 'Ali', 'harounali@gmail.com', '$2y$10$tlZhVDqBp6B7.y9tIV0pg.OmGdxH6DkJb5yFApCyks.h22W2WuNZe', 'etudiant', '2026-04-17 13:11:22', 1),
(10, 'ALLAH', 'DIBRINE', 'dibrineallah@gmail.com', '$2y$10$avpZG4opLhy7CqGX4X5mIuxT.3TRW6GOvaWFz4UH1S/S7ewtPgeYi', 'etudiant', '2026-04-17 19:16:29', 1),
(11, 'djamoss', 'brahim', 'djamossbrahim@gmail.com', '$2y$10$R1mngf0Zb1g1jNeE23x2kuaWvkG5WgL2xspOa9LJyNCcuQeHr.muW', 'etudiant', '2026-04-18 09:33:36', 1),
(12, 'salomon', 'ignebe', 'salomonignebe@gmail.com', '$2y$10$YDlgo703N4PHXUrZbspoue8AXVk0tEmFQkE2JF/IaK9WYrisLeJG2', 'etudiant', '2026-04-21 13:05:59', 1),
(13, 'donald', 'jean', 'donaldjean@gmail.com', '$2y$10$XQO1SeOdSPckuj3.4rjA.eqYb.Z9Xz3oMptGLJr9atvTLEohu2ML.', 'etudiant', '2026-04-21 13:12:43', 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `competences`
--
ALTER TABLE `competences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `metier_id` (`metier_id`);

--
-- Index pour la table `filieres`
--
ALTER TABLE `filieres`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expediteur_id` (`expediteur_id`),
  ADD KEY `destinataire_id` (`destinataire_id`);

--
-- Index pour la table `metiers`
--
ALTER TABLE `metiers`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `modeles_messages`
--
ALTER TABLE `modeles_messages`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `orientations`
--
ALTER TABLE `orientations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `etudiant_id` (`etudiant_id`),
  ADD KEY `conseiller_id` (`conseiller_id`);

--
-- Index pour la table `profils_etudiants`
--
ALTER TABLE `profils_etudiants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `etudiant_id` (`etudiant_id`),
  ADD KEY `filiere_id` (`filiere_id`);

--
-- Index pour la table `questionnaires`
--
ALTER TABLE `questionnaires`
  ADD PRIMARY KEY (`id`),
  ADD KEY `filiere_id` (`filiere_id`);

--
-- Index pour la table `reponses_etudiants`
--
ALTER TABLE `reponses_etudiants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_reponse` (`etudiant_id`,`question_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Index pour la table `ressources`
--
ALTER TABLE `ressources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ajoutee_par` (`ajoutee_par`),
  ADD KEY `filiere_id` (`filiere_id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `competences`
--
ALTER TABLE `competences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT pour la table `filieres`
--
ALTER TABLE `filieres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `metiers`
--
ALTER TABLE `metiers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `modeles_messages`
--
ALTER TABLE `modeles_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `orientations`
--
ALTER TABLE `orientations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `profils_etudiants`
--
ALTER TABLE `profils_etudiants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `questionnaires`
--
ALTER TABLE `questionnaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;

--
-- AUTO_INCREMENT pour la table `reponses_etudiants`
--
ALTER TABLE `reponses_etudiants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- AUTO_INCREMENT pour la table `ressources`
--
ALTER TABLE `ressources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `competences`
--
ALTER TABLE `competences`
  ADD CONSTRAINT `competences_ibfk_1` FOREIGN KEY (`metier_id`) REFERENCES `metiers` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`expediteur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`destinataire_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `orientations`
--
ALTER TABLE `orientations`
  ADD CONSTRAINT `orientations_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orientations_ibfk_2` FOREIGN KEY (`conseiller_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `profils_etudiants`
--
ALTER TABLE `profils_etudiants`
  ADD CONSTRAINT `profils_etudiants_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `profils_etudiants_ibfk_2` FOREIGN KEY (`filiere_id`) REFERENCES `filieres` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `questionnaires`
--
ALTER TABLE `questionnaires`
  ADD CONSTRAINT `questionnaires_ibfk_1` FOREIGN KEY (`filiere_id`) REFERENCES `filieres` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reponses_etudiants`
--
ALTER TABLE `reponses_etudiants`
  ADD CONSTRAINT `reponses_etudiants_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reponses_etudiants_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questionnaires` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `ressources`
--
ALTER TABLE `ressources`
  ADD CONSTRAINT `ressources_ibfk_1` FOREIGN KEY (`ajoutee_par`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ressources_ibfk_2` FOREIGN KEY (`filiere_id`) REFERENCES `filieres` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
