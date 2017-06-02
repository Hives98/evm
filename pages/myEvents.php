<?php
//--------------------------
//Filename: myEvents.php
//Creation date: 02.05.2017
//Author: Krisyam Yves BAMOGO$
//Function: Display a user events
//Last modification: 31.05.2017
//--------------------------
	if(!$connected)
		header('location:'.URL.'/?info=coReq');

	$query='SELECT * FROM event WHERE OwnerID='.$idUser.' ORDER BY FDate ASC';
	$res=DBConnect($query, "select");
	//$line=$res->fetchALL();
	if(isset($_GET['info'])){
	// Gestion de la variable info passée en query string
		switch($_GET['info']){
		// Suppresion de l'évènement
			case 'deleteEvent':
				deleteEvent($_GET['idEvent']);
				$info[]="L'évènement à été supprimer";
				header('location:'.URL.'/?page=myEvents&info=deletedEvent');
			break;
			default:
				header('location:'.URL.'/?page=myEvents');
			break;
		}
	}
?>
<?php if(isset($info)) { ?>
	<div align="center">
		<?php
			foreach($info as $value){
				echo '<p>'.$value.'</p><br>';
			}		
		?>
	</div>
<?php } ?>
<div align="center">
	<?php while($line=$res->fetch()){ ?>
			<div class="6u 10u(mobile)">
				<section class="box">   
					<header class="major">
						<h2><?php echo $line['Name'] ?></h2>
					</header>
					<table>
						<tr>
							<td>
								 
									<li style="width: 500px; word-wrap: break-word;"><?php echo $line['Details'] ?></li>
									<li><?php echo $line['FDate'] ?> - <?php echo $line['LDate'] ?> </li>
									<li><?php echo $line['Place'] ?></li>
								
							</td>
						</tr>
						<tr><td> </td></tr>
						<?php if(isset($line['imgLink']) && !empty($line['imgLink'])) { ?>
						<tr>
							<td>
								<a  href="<?php echo $line['imgLink']; ?>" data-fancybox><img src="<?php echo $line['imgLink']; ?>" width="100%" height="80%" align="center"/></a>
							</td>
						</tr>
						<tr><td> </td></tr>
						<?php } ?>
						<tr>
							<td>
								<a href="<?php echo URL;?>/?page=event&idEvent=<?php echo $line['idevent']; ?>"><p>Gérer l'évènement</p></a>
							</td>
						</tr>
						<tr>
							<td>
								<a href="<?php echo URL;?>/?page=myEvents&idEvent=<?php echo $line['idevent']; ?>&info=deleteEvent"><p>Supprimer l'évènement</p></a>
							</td>
						</tr>
					</table>
				</section>
			</div>
			<br>
			<br>
	<?php } ?>
</div>