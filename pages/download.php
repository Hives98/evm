<?php
//--------------------------
//Filename: download.php
//Creation date: 24.05.2017
//Author: Krisyam Yves BAMOGO
//Function: Download list of users
//Last modification: 31.05.2017
//--------------------------
	include("../assets/fonction.php");
	$query='SELECT * FROM event WHERE idevent='.$_GET['idEvent'];
	$res=DBConnect($query, "SELECT");
	$line=$res->fetch();
	
// Créer le fichier de contact pour l'évènement
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename="'.$line['Name'].'.csv"');
	$fp = fopen('php://output', 'w');
	foreach ($_SESSION['invitedTab'] as $value ){
		$query='SELECT Email FROM user WHERE iduser='.$value;
		$result=DBConnect($query, "SELECT");
		$line=$result->fetch(PDO::FETCH_ASSOC);
		fputcsv($fp, $line);
	}
	fclose($fp);
	
?>