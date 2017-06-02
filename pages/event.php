<?php
//--------------------------
//Filename: events.php
//Creation date: 02.05.2017
//Author: Krisyam Yves BAMOGO$
//Function: Create and manage events
//Last modification: 31.05.2017
//--------------------------
	if(!$connected)
		header('location:'.URL.'/?info=coReq');
// Si la query string info existe
	if(isset($_GET['info'])){
	// Gestion de la variable info passée en query string
		switch($_GET['info']){
		// Suppresion de l'évènement
			case 'invitsDispatched':
				$info[]="Les invitations ont étés correctement distribuées";
			break;
		// Acceptation d'une invitation
			case 'invitA':
				$query='UPDATE user_has_events SET invit=1 WHERE user_iduser='.$_GET['iduser'].' AND events_idevents='.$_GET['idEvent'];
				DBConnect($query, "update");
			break;
		// 	Refus de l'invitation
			case 'invitD':
				$query='UPDATE user_has_events SET invit=0 WHERE user_iduser='.$_GET['iduser'].' AND events-idevents='.$_GET['idEvent'];
				DBConnect($query, "update");
			break;
		// Invitation envoyée
			case 'invitsDispatched':
				$info[]="Les invitations on étés distribuées";
			break;
		// En cas de modification
			case 'modified':
				$invitedTab=array();
				$query='SELECT user_iduser FROM user_has_events WHERE events_idevents='.$_GET['idEvent'].' AND invit=1';
				$res=DBConnect($query, "SELECT");
				while($line=$res->fetch())
				{
					$query='SELECT  Email iduser FROM user WHERE iduser='.$line[0];
					$resu=DBConnect($query, "SELECT");
					$tmp=$resu->fetch();
					$invitedTab[]=$tmp[0];
				}
				$messageText=' Bonjour, un évènement auquel vous participez a été modifier,<br>
				<br>
				cliquez <a href="'.URL.'/?page=myInvits&statut=ME&idEvent='.$_GET['idEvent'].'">ici</a> pour le récaputilatif de l\'évènement.<br>
				<br>
				EventManager team. <br>
				---------------------------------------------------------------------------------------<br>
				Mail automatique, merci de ne pas répondre.<br>';
				foreach($invitedTab as $value){
					sendMessage($messageText, $value);
				}
			break;
			default:
				header('location:'.URL.'/?page=myEvents');
			break;
		}
	}
// Gestion de la variable de switch event
	$events=false;
	if( isset($_GET['idEvent'])){
		$events=true;
		$idEvent=$_GET['idEvent'];
	}
// Switch sur la variable event (evenement créer ou pas)
	switch($events){
	// Création de l'évènement insertion dans la BD
		case false:
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
			// Sécurisation et extraction du tableau post
				$sPOST=secureArray($_POST);
				extract($sPOST);
			// Insertion de l'évènement dans la BDD
				$query='INSERT INTO event (Name, FDate, LDate, Place, Details, OwnerId) VALUES ("'.$eventName.'", "'.$fDate.'", "'.$lDate.'", "'.$place.'", "'.$description.'", '.$idUser.')';
				$res=DBConnect($query,"INSERT");
				$lasInsert=$res;
			// Si l'utilisateur a joint une image
				if(file_exists($_FILES['eventImg']['tmp_name']) || is_uploaded_file($_FILES['eventImg']['tmp_name'])){
				// Définir le dossier ou sera stocker l'image et le tableau des extentions autorisées
					$target= ROOT."/images/events"; 
					$tabExt = array('jpg','png','jpeg');
					$fileName=$_FILES['eventImg']['name'];
					$tmpName=$_FILES['eventImg']['tmp_name'];
				// Récuperer l'extention de l'image
					$extention=$extension=substr(strrchr($fileName,'.'),1);
				// Vérifier que l'extention soit autorisée
					if(in_array(strtolower($extention), $tabExt)){
					// Uploader le fichier / le remplacer si il existe déja
						$fileName= $lasInsert.".".$extention."";
						$defFile="$target/$fileName";
						move_uploaded_file($tmpName, $defFile);
					// Mettre le lien de l'image dans la DB
						$query='UPDATE event SET imgLink="'.$defFile.'" WHERE idevent='.$lasInsert;
						DBConnect($query, "UPDATE");
					}	
				}
				header('location:'.URL.'/?page=event&idEvent='.$lasInsert);
			}
		break;
		
		case true:
			$query='SELECT * FROM event WHERE idevent='.$idEvent;
			$res=DBConnect($query, "select");
			$line=$res->fetch();
		// Récuperer les informations de l'utilisateur qui invite
			$query='SELECT * FROM user WHERE iduser='.$idUser;
			$res=DBConnect($query, "select");
			$owner=$res->fetch();
			$mails=array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$sPOST=secureArray($_POST);
				extract($sPOST);
			// Parser la chaine recuperée
				$guests=explode(',', $guests);
			// Si il y a un fichier csv uploader
				if( isset($_FILES['guestsList']['name']) && $_FILES['guestsList']['name'] != ''){
					$storagename = $_FILES['guestsList']['name'];
            		move_uploaded_file($_FILES['guestsList']['tmp_name'], ROOT.'/tmp_files/'.$storagename);
					ini_set('auto_detect_line_endings',TRUE);
					$handle = fopen(ROOT.'/tmp_files/'.$storagename,'r');
					while ( ($data = fgetcsv($handle) ) !== FALSE ) {
						if(!(in_array($data[0], $guests))){
							$guests[]=$data[0];
						}
					}
					fclose($handle);
					ini_set('auto_detect_line_endings',FALSE);
				}
			// Recuperer toutes les adresses mails de la bdd
				$query='SELECT Email FROM user';
				$res=DBConnect($query, "select");
				$handle=$res->fetchAll(PDO::FETCH_ASSOC);
			// Mettre toutes les adresses mails dans un tableau unique
				foreach($handle as $value){
					foreach($value as $element){
						$mails[]=$element;
					}
				}
			
			// Envoyer les invitations pour chaque adresse mails
				foreach($guests as $value){
				// Si la valeur est une adresse mail
					if(filter_var($value, FILTER_VALIDATE_EMAIL)){
					// Si l'adresse mail corestpond a une adresse de la DB
						if(in_array($value, $mails)){
						// Creer un tableau avec les information liées à l'adresse mail
							$query='SELECT iduser FROM user WHERE Email="'.$value.'"';
							$res=DBConnect($query, "select");
							$result=$res->fetch();
						// Inserer une invitation
							$query='SELECT * FROM user_has_events WHERE user_iduser='.$result['iduser'].' AND events_idevents='.$idEvent;
							$res=DBConnect($query, "select");
							$check=$res->fetch();
							if(empty($check)){
								$query='INSERT INTO user_has_events (user_iduser, events_idevents) VALUES ('.$result['iduser'].', '.$idEvent.')';
								DBConnect($query, "INSERT");
							}
							$acceptedLink=URL.'/?page=myInvits&info=invitA&iduser='.$result['iduser'].'&idevent='.$idEvent;
							$deniedLink=URL.'?page=myInvitst&info=invitD&iduser='.$result['iduser'].'&idevent='.$idEvent;

							$messageText='<html>Bonjour, '.$owner['Name'].' '.$owner['GName'].' vous a invité à un évènement.<br>
							<br>
							Pour accepter l\'invitation, cliquez sur le lien suivant : '.$acceptedLink.' <br>
							<br>
							Pour décliner l invitation, cliquez ici : '.$deniedLink.'<br>
							<br>
							EventManager team. <br>
							---------------------------------------------------------------------------------------<br>
							Mail automatique, merci de ne pas répondre.<br> </html>
							';
							sendMessage($messageText, $value);
						}
						if(!in_array($value, $mails)){
							$messageText = 'Bonjour, '.$owner['Name'].' '.$owner['GName'].' vous a invité à un évènement.<br>
							<br>
							Pour rejoindre l\'organisation de cet évènement, créer un compte sur <a href="'.URL.'">eventmanager</a> !<br>
							<br>
							EventManager team.<br>
							---------------------------------------------------------------------------------------<br>
							Mail automatique, merci de ne pas répondre.';
							sendMessage($messageText, $value);
						}
					}
				}
				header('location:'.URL.'/?page=event&idEvent='.$idEvent.'&info=invitsDispatched');
			}
		break;
	}
?>
<?php if ($events==false) {?>
<div align="center">
<div class="6u 10u(mobile)">
	<section class="box">    
		<form method="POST" enctype="multipart/form-data">
			<h3>Créer un évènement</h3>
			<br>
			<table>
				<tr>
					<td colspan="2">
						<input type="text" name="eventName" placeholder="Nom de l'évènement" required>
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>
						<div class='input-group date' id='datetimepicker1'>
							<input type='text' class="form-control" name="fDate" placeholder="Date/heure de début" required/>
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</td>
					<td> </td>
					<td>
						<div class='input-group date' id='datetimepicker2'>
							<input type='text' class="form-control" name="lDate" placeholder="Date/ heure de fin (facultatif)"/>
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>
						<input type="text" name="place" placeholder="Lieu" required>
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>
						<textarea name="description" rows="10" cols="10" placeholder="Description de l'évènement" required></textarea>
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>
						<input type="file" name="eventImg"/>
					</td>
				</tr>
				<tr><td> </td></tr>
				<tr>
					<td colspan="2">
						<input type="submit" value="Créer mon évènement !">
					</td>
				</tr>
			</table>
		</form>
	</section>
</div>
</div>
<?php } ?>
<?php if ($events==true) {?>
<?php if(isset($info) && !empty($info)) { ?>
<div align="center">
	<section class="box">
		<?php
			foreach($info as $value){
				echo'<li>'.$value.'</li>';
			}
			
		?>
	</section>
</div>
<br>
<br>
<?php } ?>
<div class="row">
	<div class="6u 10u(mobile)">
		<section class="box">    
			<table>
				<tr>
					<td>
						<header class="major">
							<h2>Votre évènement</h2>
						</header>
						<li><?php echo $line['Name'] ?></li>
						<li><?php echo $line['FDate'] ?></li>
						<li><?php echo $line['Place'] ?></li>
					</td>
				</tr>
				<tr><td> </td></tr>
				<?php if( isset($line['imgLink']) && !empty($line['imgLink']) ) { ?>
				<tr>
					<td>
						<a  href="<?php echo $line['imgLink']; ?>" data-fancybox><img src="<?php echo $line['imgLink']; ?>" width="100%" height="80%" align="center"/></a>
					</td>
				</tr>
				<?php } ?>
				<tr><td> </td></tr>
				<tr>
					<td>
						<a href="<?php echo URL; ?>/?page=mEvent&idEvent=<?php echo $_GET['idEvent']; ?>"><p>Modifier ces informations</p></a>
					</td>
				</tr>
			</table>
		</section>
	</div>
	<div class="6u 10u(mobile)">
		<section class="box">
			<form method="POST" enctype="multipart/form-data">
				<table>
					<tr>
						<td>
							<label> Ajouter des invités (utilisé le séparateur , pour entrer plusieurs adresses (sans espace après la virgule)) </label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="email" name="guests" multiple>
						</td>
					</tr>
					<tr><td> </td></tr>
					<tr>
						<td>
							<label> Importer une liste d'invités </label> 		
						</td>
					</tr>
					<tr>
						<td><input type="file" name="guestsList"></td>
					</tr>
					<tr><td> </td></tr>
					<tr>
						<td><button type="submit">Valider!</button></td>
					</tr>
				</table>
			</form>
		</section>
	</div>
</div>
	<?php
		$invitedTab=array();
		$query='SELECT user_iduser FROM user_has_events WHERE events_idevents='.$_GET['idEvent'].' AND invit=1';
		$res=DBConnect($query, "SELECT");
		while($line=$res->fetch())
		{
			$query='SELECT  Name, GName, iduser FROM user WHERE iduser='.$line[0];
			$resu=DBConnect($query, "SELECT");
			$invitedTab[]=$resu->fetch();
		}
	?>
<div class="row">
	<div class="6u 10u(mobile)">
		<section class="box">    
			<table>
				<tr>
					<td>
						<header class="major">
							<h2>Liste des invités participants à l'évènement</h2>
						</header>
					</td>
				</tr>
				<tr>
					<td>
						<?php if(isset($invitedTab)){ ?>
							<?php $_SESSION['invitedTab']=array(); ?>
							<?php foreach($invitedTab as $value) { ?>
								<h4><?php echo '<a href="'.URL.'/?page=user&id='.$value['iduser'].'">'.$value['Name'].' '.$value['GName'].'</a>'; ?></h4>
								<?php $_SESSION['invitedTab'][]=$value['iduser']; ?>
							<?php } ?>
						<?php } ?>
					</td>
				</tr>
				<?php if(!empty($invitedTab)) { ?>
				<tr><td> </td></tr>
				<tr>
					<div align="center">
						<td><a href="<?php echo ROOT; ?>/pages/download.php?idEvent=<?php echo $_GET['idEvent'] ?>"><img src="<?php echo ROOT.'/images/download.png'; ?>"></a></td>
					</div>
				</tr>
				<?php } ?>
			</table>
		</section>
	</div>
</div>
<?php } ?>