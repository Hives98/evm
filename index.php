<?php
//--------------------------
//Filename: index.php
//Creation date: 02.05.2017
//Author: Krisyam Yves Bamogo
//Function: Main page, includes scripts and secondary pages
//Last modification: 01.06.2017
//--------------------------
// Défnition du chemin racine
	define('ROOT', dirname('index.php'));
// Définition de la constante URL
	//define('URL', $_SERVER['SERVER_NAME']);
	define('URL', "http://eventmanager.local");
// Inclure les fichiers permetant d'utiliser les librairies php
	include(ROOT."/vendor/autoload.php");
// Inclure le fichier de fonctions
	include(ROOT."/assets/fonction.php");

// Création des variables qui permettrons d'utiliser plus facilement les données de l'utilisateur connecté
	$connected = false;
	$admin = false;
	if(isset($_SESSION['connectedUser'])){
		$connected = true;
		$name=$_SESSION['name'];
		$gName=$_SESSION['surname'];
		$userName=$_SESSION['username'];
		$email=$_SESSION['mail'];
		$idUser=$_SESSION['idUser'];
		$type=$_SESSION['Type'];
	}
	if(isset($_SESSION['connectedUser']) && $_SESSION['Type'] == 3)
		$admin = true;
	// Définition de la page dar défaut 
	$page = 'home.php';
	//Définition de la page si la querry string est définie
	if(isset($_GET['page']) && $_GET['page'] != ''){
		$page = htmlspecialchars($_GET['page']);
		$page = $page.".php";
	}

?>
<html>
	<head>
		<title>Event Manager</title>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="src/bootstrap/css/bootstrap.css" />
		<link rel="stylesheet" href="src/bootstrap/css/bootstrap-datetimepicker.css" />
		<link rel="stylesheet" type="text/css" href="src/fancybox-3.0/dist/jquery.fancybox.min.css">
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<!--[if lte IE 8]><script src="assets/js/ie/html5shiv.js"></script><![endif]-->
		<link rel="stylesheet" href="assets/css/main.css" />
		<!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie8.css" /><![endif]-->
		
	</head>
	<body class="homepage">
		<!-- Header -->
		<div id="header-wrapper">
			<div id="header">
			<!-- Logo -->
				<h1><a href="<?php echo URL;?>">Event Manager</a></h1>
				<!-- Nav -->
					<nav id="nav">
						<ul>
							<li <?php if($page=="home.php") echo 'class="current"'?>><a href="<?php echo URL;?>">Accueil</a></li>
						<?php if($connected){ ?>
							<li <?php if($page=="event.php") echo 'class="current"'?>><a href="<?php echo URL;?>/?page=event">Evènements</a>
								<ul>
									<li><a href="<?php echo URL;?>/?page=myEvents">Mes évènements</a></li>
									<li>
										<a href="<?php echo URL;?>/?page=myInvits&statut=N">Mes invitations</a>
										<ul>
											<li><a href="<?php echo URL;?>/?page=myInvits&statut=A">Acceptées</a></li>
											<li><a href="<?php echo URL;?>/?page=myInvits&statut=P">Invitations en attente</a></li>
										</ul>
									</li>
								</ul>
							</li>
							<li <?php if($page=="profil.php") echo 'class="current"'?>><a href="<?php echo URL;?>/?page=profil">Profil</a>
								<ul>
									<li><a href="<?php echo URL;?>/?page=logout">Déconnexion</a></li>
								</ul>
							</li>
						<?php if($admin){ ?>
							<li <?php if($page=="management.php") echo 'class="current"'?>><a href="<?php echo URL;?>/?page=management">Management</a></li>
						<?php } ?>
						<?php } ?>
						</ul>
					</nav>
			</div>
		</div>
	<!-- Main -->
		<div id="main-wrapper">
			<?php include(ROOT."/pages/".$page); ?>
		</div>
	<!-- Footer -->
		<div id="footer-wrapper">
			<section id="footer" class="container">
			<!-- Copyright -->
				<div id="copyright">
					<ul class="links">
						<li>&copy; Untitled. All rights reserved.</li><li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
						<li>Krisyam Yves Bamogo TPI <a href="http://www.cpnv.ch">CPNV</a></li>
					</ul>
				</div>
			</section>
		</div>
	</body>
	<!-- Scripts -->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/jquery.dropotron.min.js"></script>
	<script src="assets/js/skel.min.js"></script>
	<script src="assets/js/skel-viewport.min.js"></script>
	<script src="assets/js/util.js"></script>
	<!--[if lte IE 8]><script src="assets/js/ie/respond.min.js"></script><![endif]-->
	<script src="assets/js/main.js"></script>
	<!--mines-->
	<script type="text/javascript" src="src/jquery-3.2.0.js"></script>
	<script type="text/javascript" src="src/bootstrap/js/moment.js"></script>
	<script type="text/javascript" src="src/bootstrap/js/bootstrap.js"></script>
	<script type="text/javascript" src="src/bootstrap/js/bootstrap-datetimepicker.js"></script>
	<script type="text/javascript">
		$(function () {
               $('#datetimepicker1').datetimepicker();
		});
	</script>
	<script type="text/javascript">
		$(function () {
			$('#datetimepicker2').datetimepicker();
		});
	</script>
	<script type="text/javascript" src="src/fancybox-3.0/dist/jquery.fancybox.min.js"></script>
	<script src="https://www.gstatic.com/firebasejs/4.0.0/firebase-app.js"></script>
	<script src="https://www.gstatic.com/firebasejs/4.0.0/firebase-auth.js"></script>
	<script src="https://www.gstatic.com/firebasejs/4.0.0/firebase-database.js"></script>
	<script src="https://www.gstatic.com/firebasejs/4.0.0/firebase-messaging.js"></script>
	<script src="https://www.gstatic.com/firebasejs/4.0.0/firebase.js"></script>
	<script>
	  // Initialize Firebase
	  var config = {
		apiKey: "AIzaSyBnL74k5hM997J2-UyJKUIQWt3Em6Z2zy8",
		authDomain: "eventmanager-166007.firebaseapp.com",
		databaseURL: "https://eventmanager-166007.firebaseio.com",
		projectId: "eventmanager-166007",
		storageBucket: "eventmanager-166007.appspot.com",
		messagingSenderId: "665789700379"
	  };
	  firebase.initializeApp(config);
	</script>
	<script>
		function faceLog(){
			var provider = new firebase.auth.FacebookAuthProvider();
			provider.addScope('email');
			provider.addScope('public_profile');
			firebase.auth().signInWithPopup(provider).then(function(result){
				console.log(result);
				$.ajax({                                      
    				url: 'assets/treatmentAjax.php',       
					type: "POST",
					data: { res: result } 
				}).done(function( msg ) {
    				alert(msg);
				});
			})
		}
	</script>
	<script>
		function Glog(){
			var provider = new firebase.auth.GoogleAuthProvider();
			provider.addScope('https://www.googleapis.com/auth/contacts.readonly');
		}
	</script>
</html>