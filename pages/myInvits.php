<?php
//--------------------------
//Filename: myEvents.php
//Creation date: 02.05.2017
//Author: Krisyam Yves BAMOGO$
//Function: Display user invits
//Last modification: 02.05.2017
//--------------------------
	if(!$connected)
		header('location:'.URL.'/?info=coReq');

	if(isset($_GET['info'])){
	// Gestion de la variable info passée en query string
		switch($_GET['info']){
		// Suppresion de l'évènement
			case 'deleteEvent':
				$query= 'DELETE FROM user_has_events WHERE events_idevents='.$_GET['idEvent'];
				DBConnect($query, "DELETE");
				$query= 'DELETE FROM event WHERE idevent='.$_GET['idEvent'];
				DBConnect($query, "DELETE");
				header('location:'.URL.'/?page=myEvents');
			break;
		// Acceptation d'une invitation
			case 'invitA':
				$query='UPDATE user_has_events SET invit=1 WHERE user_iduser='.$_GET['iduser'].' AND events_idevents='.$_GET['idevent'];
				DBConnect($query, "update");
			break;
		// 	Refus de l'invitation
			case 'invitD':
				$query='UPDATE user_has_events SET invit=NULL WHERE user_iduser='.$_GET['iduser'].' AND events_idevents='.$_GET['idevent'];
				DBConnect($query, "update");
			break;
		// Invitation envoyée
			case 'invitsDispatched':
				$info[]="Les invitations on étés distribuées";
			break;
			default:
				header('location:'.URL.'/?page=myEvents');
			break;
		}
	}
	if(isset($_GET['statut'])){
		switch($_GET['statut']){
			case 'N':
				$query='SELECT * FROM user_has_events WHERE user_iduser='.$idUser;
			break;
			case 'A':
				$query='SELECT events_idevents FROM user_has_events WHERE user_iduser='.$idUser.' AND invit=1';
			break;

			case 'P':
				$query='SELECT events_idevents FROM user_has_events WHERE user_iduser='.$idUser.' AND invit IS NULL';
			break;
			case 'ME':
				$query='SELECT events_idevents FROM user_has_events WHERE user_iduser='.$idUser.' AND events_idevents='.$_GET['idEvent'];
			break;
			default:
				$query='SELECT * FROM user_has_events WHERE user_iduser='.$idUser;
			break;
		}
	}
	else
	$query='SELECT * FROM user_has_events WHERE user_iduser='.$idUser;
	$res=DBConnect($query, "select");
?>
<div align="center">
	<table>
		<?php while($handle=$res->fetch()){
			$query='SELECT * FROM event WHERE idevent='.$handle['events_idevents'];
			$result=DBConnect($query, "select");
			while($line=$result->fetch()){ 
			?>
			<tr>
				<div class="8u 12u(mobile) important(mobile)">
					<!-- Content -->
					<article class="box post">
						<a href="<?php echo $line['imgLink']; ?>" class="image featured" data-fancybox><img src="<?php echo $line['imgLink']; ?>" alt="" width="100%" height="80%"/></a>
						<header>
							<h2><?php echo $line['Name'] ?></h2>
							<p><?php echo $line['FDate'] ?> - <?php echo $line['LDate'] ?></p>
							<p><?php echo $line['Place'] ?></p>
						</header>
						<p><?php echo $line['Details'] ?></p>
						<?php
							$query='SELECT * FROM user WHERE iduser='.$line['OwnerId'];
							$resu=DBConnect($query, "select");
							$resa=$resu->fetch();
						?>
						<p><a href="<?php echo URL;?>/?page=user&id=<?php echo $resa['iduser'] ?>"><?php echo $resa['Name'].' '.$resa['GName'] ?></a></p>
						<?php
							if(isset($_GET['statut'])){
								switch($_GET['statut']){
									case 'A':
										echo '<a href="'.URL.'/?page=myInvits&info=invitD&iduser='.$idUser.'&idevent='.$handle['events_idevents'].'"><button>Décliner l\'invitation</button></a>';
									break;
									case 'P':
										echo '<a href="'.URL.'/?page=myInvits&info=invitA&iduser='.$idUser.'&idevent='.$handle['events_idevents'].'"><button>Accepter l\'invitation</button></a>';
									break;
									default:
									break;
								}
							}	
						?>
					</article>
				</div>
			</tr>
			<br>
			<br>
			<br>
			<?php } ?>
		<?php } ?>
	</table>
</div>