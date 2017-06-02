<?php
//--------------------------
//Filename: DBConnection.php
//Creation date: 02.05.2017
//Author: Krisyam Yves BAMOGO
//Function: Fichier contenant les fonctions
//Last modification: 31.05.2017
//--------------------------
	session_start();
// Fonction de connection à la base de données
	function DBConnect($req, $type_req){
		try{
		// Connection à la base de données event manager
			$connection = new PDO('mysql:host=localhost; dbname=eventmanager', 'root', '');

		// Activer l'affichage des erreurs
			$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(Exception $e){
			echo 'Une erreur est survenue! '.$e->getMessage();
			die();
		}

	// Execution de la requête
		if($type_req == 'select' || $type_req == "SELECT")
			$res = $connection->query($req); 
		else
		{
			if (false === $connection->exec($req))
				return false;
			$res = $connection->lastInsertId();
		}
		return ($res);
	}

// Fonction pour l'envoie de mails
	function sendMessage($messageText, $email)
	{
	// Initiations des données de transport
		$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465)
		->setUsername('tpicpnv@gmail.com' )
		->setPassword('CPNV2017')
		->setEncryption('ssl')
		;
	// Logers pour erreurs
		$logger = new Swift_Plugins_Loggers_ArrayLogger();

	// Crer la session de transport
		$mailer = Swift_Mailer::newInstance($transport);
		$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

		$email = strtolower(str_replace(' ', '', $email));
	// Créer le message
		$message = Swift_Message::newInstance('Event Manager')
			->setFrom('tpicpnv@gmail.com')
			->setTo($email)
			->setBody($messageText)
			->addPart($messageText, 'text/html')
		;
	// Envoyer le message
		$result = $mailer->send($message);
	}

// Fonction pour echaper les caractères spéciaux (pour proteger les formulaires des injections)
	function secureArray($array)
	{
		foreach ($array as $key => $value) 
		{
			if(is_array($value)) 
				$array[$key] = secureArray($value);
			else 
				$array[$key] = htmlentities($value, ENT_QUOTES);
		}
		return $array;
	}

// Génerer un nouveau mot de passe
	function generateANewPasswd($nb_caractere = 20)
		{
			// Générer une chaine de 20 caractère et la retourner en output
			$newPasswd = "";
			$string = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ023456789+@!$%?&";
			$chainSize = strlen($string);
			for($i = 1; $i <= $nb_caractere; $i++)
			{
				$random = mt_rand(0,($chainSize-1));
				$newPasswd .= $string[$random];
			}
			return $newPasswd;   
		}

// Fonction pour rénitialiser un mot de passe
	function passwdReset($idUser, $email)
	{
		// Generer un nouveau mot de passe
			$password=generateANewPasswd();
		// Récuperer l'adresse mail de l'utilisateur
			$query='SELECT Email from user WHERE iduser='.$idUser;
			$res=DBConnect($query, "select");
			$line=$res->fetch();
			$emailAdresse=$line['Email'];
		// Modifier le mot de passe de l'utilisateur
			$query='UPDATE User SET Password = "'.sha1(md5($password)).'" WHERE iduser='.$idUser;
			DBConnect($query, 'UPDATE');
		// Envoyer un mail
			$messageText ="
				Votre mot de passe <a href=".URL.">Event manager</a> a été renitialiser !<br> 
				<br>
				Voici votre nouveau: ".$password."<br>
				<br>
				Rendez-vous sur <a href=".URL.">Event manager</a> pour changer de mot de passe !<br>
				<br>
				Event Manager team<br>
				<br>
				----------------------------------------------------<br>
				Mail automatique, merci de ne pas répondre.";
		// Envoyer le message
			sendMessage($messageText, $email);
	}

// Fonction pour supprimer un évènement
	function deleteEvent($idEvent){
	// Récupérer la liste des invités
		$invitedTab=array();
		$query='SELECT user_iduser FROM user_has_events WHERE events_idevents='.$idEvent;
		$res=DBConnect($query, "SELECT");
		while($line=$res->fetch()){
			$query='SELECT  Email iduser FROM user WHERE iduser='.$line[0];
			$resu=DBConnect($query, "SELECT");
			$tmp=$resu->fetch();
			$invitedTab[]=$tmp[0];
		}
	// Envoyer un mail 
		$query='SELECT Name FROM event WHERE idevent='.$idEvent;
		$resu=DBConnect($query, "SELECT");
		$tmp=$resu->fetch();
		$messageText=' Bonjour, l\'évènement '.$tmp['Name'].' a été supprimé,<br>
		<br>
		Merci d\'en prendre bonne note
		<br>
		<br>
		EventManager team. <br>
		---------------------------------------------------------------------------------------<br>
		Mail automatique, merci de ne pas répondre.<br>';
		foreach($invitedTab as $value){
			sendMessage($messageText, $value);
		}
	// Supprimer les invitations
		$query= 'DELETE FROM user_has_events WHERE events_idevents='.$idEvent;
		DBConnect($query, "DELETE");
	// Supprimer l'évènement
		$query= 'DELETE FROM event WHERE idevent='.$idEvent;
		DBConnect($query, "DELETE");
	}

// Fonction pour supprimer un utilisateur
	function deleteUser($id){
	// Sélectionner les informations de l'utilisateur à supprimer
		$query='SELECT Email FROM USER WHERE iduser='.$id;
		$res=DBConnect($query, "select");
		$line=$res->fetch();
	// Envoie d'un mail de notification
		$messageText='Bonjour, <br>
		votre compte EventManager à été supprimer par un administrateur pour non respect de la charte.<br>
		Vous pouvez récréer un compte avec cette adresse, mais veuillez à  respecter la charte.<br>
		<br>
		EventManager team. <br>
		---------------------------------------------------------------------------------------<br>
		Mail automatique, merci de ne pas répondre.<br>
		';
		sendMessage($messageText, $line['Email']);
	// Supprimer tous les évènements de l'utilisateur
		$query='SELECT idevent FROM event WHERE OwnerId='.$id;
		$res=DBConnect($query, "SELECT");
		while($line=$res->fetch()){
			deleteEvent($line['idevent']);
		}
	// Supprimer toutes les invitations de l'utilisateur
		$query= 'DELETE FROM user_has_events WHERE user_iduser='.$id;
		DBConnect($query, "DELETE");
	// Supprimer l'utilisateur 
		$query='DELETE from user where iduser='.$id;
		DBConnect($query, 'delete');
	}

// Fonction pour bloquer un compte
	function blockAccount($id){
	// Bloquer l'utilisateur
		$query='UPDATE user SET Type = 2 where iduser='.$id;
		DBConnect($query, 'update');
	// Avertir l'utilisateur que son compte à été bloqué
		$query='SELECT Email FROM user WHERE iduser='.$id;
		$res=DBConnect($query, "SELECT");
		$line=$res->fetch();
		$messageText='
		Bonjour '.$line['UserName'].', <br>
		<br>
		Votre compte à été bloqué par un administrateur,<br>
		Merci de bien vouloir contacter un administrateur pour en discuter.<br>
		Vous pouvez le faire en répondant à cet email.<br>
		<br>
		EventManager team. <br>
		---------------------------------------------------------------------
		';
		sendMessage($messageText,$line['Email']);
	}

// Débloquer un utilisateur
	function unblockAccount($id){
	// Débloquer l'utilisateur
		$query='UPDATE user SET Type = 1 where iduser='.$id;
		DBConnect($query, 'update');
	// Avertir l'utilisateur
		$query='SELECT Email FROM user WHERE iduser='.$id;
		$res=DBConnect($query, "SELECT");
		$line=$res->fetch();
		$messageText='
		Bonjour '.$line['UserName'].', <br>
		<br>
		Votre compte à été déblqué par un administrateur,<br>
		Vous pouvez maintenant vous reconnecter !<br>
		<br>
		EventManager team. <br>
		---------------------------------------------------------------------<<br>
		Mail automatique, merci de ne pas répondre.<br>
		';
		sendMessage($messageText,$line['Email']);
	}

// Ajouter un utilisateur dans le groupe administrateur
	function addAdmin($id){
	// Passer l'utilisateur en administrateur
		$query='UPDATE user SET Type = 3 where iduser='.$id;
		DBConnect($query, 'update');
	// Avertir l'utilisateur
		$query='SELECT Email FROM user WHERE iduser='.$id;
		$res=DBConnect($query, "SELECT");
		$line=$res->fetch();
		$messageText='
		Bonjour '.$line['UserName'].', <br>
		<br>
		Votre compte est désormais un compte administrateur,<br>
		A votre prochiane connexion, vous verrez apparaite un onglet management.<br>
		Vous êtes autoriser à utiliser cet onglet pour modifier des comptes d\'utilisateur ou des évènements.<br>
		Merci de n\'utiliser ces capacités qu\'a des fins de modération.<br>
		<br>
		With great power comes great responsibility.
		<br>
		EventManager team. <br>
		---------------------------------------------------------------------<<br>
		Mail automatique, merci de ne pas répondre.<br>
		';
		sendMessage($messageText,$line['Email']);
	}