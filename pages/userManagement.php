<?php
//--------------------------
//Filename: myEvents.php
//Creation date: 02.05.2017
//Author: Krisyam Yves BAMOGO$
//Function: user management for admins
//Last modification: 31.05.2017
//--------------------------
	if(!$connected)
		header('location:'.URL.'/?info=coReq');
	if(!$admin)
		header('location:'.URL.'/?info=adminReq');
// Gestion d'erreur
	if( isset($_GET['info']) && $_GET['info']=='noHandle'){
		$info[] = "Vous n'avez selectionner aucun utilisateur";
	}
	if(isset($_SESSION['passReseted']) && $_SESSION['passReseted']==true)
		$info[]="Le mot de passe de l'utilisateur a été rénitialise";
	if(isset($_SESSION['userDeleted']) && $_SESSION['userDeleted']==true)
		$info[]="L'utilisateur à été supprimer";
	if(isset($_SESSION['blockUser']) && $_SESSION['blockUser']==true)
		$info[]="L'utilisateur à été bloquer";
	if(isset($_SESSION['newAdmin']) && $_SESSION['newAdmin']==true)
		$info[]="Les privillèges administrateurs ont été ajouté à l'utilisateur";
	if(isset($_SESSION['imgDeletedByAdmin']) && $_SESSION['imgDeletedByAdmin']==true)
		$info[]="La photo de profil associée à l'utilisater a été supprimé";
	if(isset($_SESSION['unblockUser']) && $_SESSION['unblockUser']==true)
		$info[]="l'utilisateur à été débloqué !";
// Sélectionner tous les utilisateurs
	$query="SELECT iduser, Name, Type, GName, UserName FROM user ORDER BY UserName DESC";
	$result=DBConnect($query, "select");

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
	// Recuperer les données du formulaire
		$display=array();
		$sPOST=secureArray($_POST);
		$_SESSION['passReseted']=false;
		$_SESSION['userDeleted']=false;
		$_SESSION['blockUser']=false;
		$_SESSION['unblockUser']=false;
		$_SESSION['newAdmin']=false;
		$_SESSION['imgDeletedByAdmin']=false;
		extract($sPOST);
		switch($formType){
			case 1:
			// Récuperer les données de l'utilisateur selectionné
				$query='SELECT * FROM user WHERE iduser='.$select;
				$res=DBConnect($query, "select");
				$_SESSION['handle']=$res->fetch();
			break;
			case 2:
			// Effectuer les actions corespondantes au boutons appuyer
				if(isset($b1)){
					passwdReset($_SESSION['handle']['iduser'], $_SESSION['handle']['Email']);
					$_SESSION['passReseted']=true;
				}
				if(isset($b3)){
					blockAccount($_SESSION['handle']['iduser']);
					$_SESSION['blockUser']=true;
				}
				if(isset($b4)){
					addAdmin($_SESSION['handle']['iduser']);
					$_SESSION['newAdmin']=true;
				}
				if(isset($b5)){
					$query='UPDATE user SET imgLink="" WHERE iduser='.$_SESSION['handle']['iduser'];
					DBConnect($query, "UPDATE");
					$_SESSION['imgDeletedByAdmin']=true;
				}
				if(isset($b6)){
					unblockAccount($_SESSION['handle']['iduser']);
					$_SESSION['unblockUser']=true;
				}
				if(isset($b2)){
					deleteUser($_SESSION['handle']['iduser']);
					$_SESSION['userDeleted']=true;
				}
				unset($_SESSION['handle']);
				header('location:'.URL.'/?page=userManagement');
			break;
			case 3:
				unset($_SESSION['handle']);
			break;
			default:
			break;
		}	
	}
?>
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
	<div class="8u 8u(mobile)">
		<section class="box">
		<?php if (!(isset($_SESSION['handle']))) { ?>
			<form method="POST">
				<input type="hidden" value="1" name="formType"/>
				<table>
					<tr>
						<td>
							<select name="select">
								<?php
									while($line=$result->fetch()){
										echo '<option value="'.$line['iduser'].'">'.$line['Name'].' '.$line['GName'].' ('.$line['UserName'].')</option>';
									}
								?>
							</select>
						</td>
						<td> </td>
						<td>
							<input type="submit" value="Selectionner"/>
						</td>
					</tr>
				</table>
			</form>
		<?php } ?>
		<?php if (isset($_SESSION['handle'])) { ?>
			<form method="POST">
				<input type="hidden" value="3" name="formType"/>
				<table>
					<tr>
						<h4>Vous avec selectionner l'utilisateur <?php echo $_SESSION['handle']['UserName']; ?></h4>
					</tr>
					<tr> 
						<input type="submit" value="Annuler la selection">
					</tr>
				</table>
			</form>
			</br>
			<form method="POST">
				<input type="hidden" value="2" name="formType"/>
					<table>
						<tr>
							<td>
								<input type="checkbox" name="b1">Rénitialiser le mot de passe<br>
							</td>
						</tr>
						<tr>
							<td>
								<input type="checkbox" name="b2">Supprimer le compte<br>
							</td>
						</tr>
						<?php if($_SESSION['handle']['Type']==1 || $_SESSION['handle']['Type']==3) { ?>
						<tr>
							<td>
								<input type="checkbox" name="b3">Bloquer le compte<br>
							</td>
						</tr>
						<?php } ?>
						<?php if($_SESSION['handle']['Type']==2 || $_SESSION['handle']['Type']==0) { ?>
						<tr>
							<td>
								<input type="checkbox" name="b6">Débloquer le compte<br>
							</td>
						</tr>
						<?php } ?>
						<tr>
							<td>
								<input type="checkbox" name="b4">Ajouter les privilèges administrateurs<br>
							</td>
						</tr>
						<tr>
						<?php if(isset($_SESSION['handle']['imgLink'])) { ?>
							<td>
								<input type="checkbox" name="b5">Supprimer la photo<br>
							</td>
						</tr>
						<?php } ?>
						<?php if(isset($_SESSION['handle']['imgLink'])) { ?>
							<tr>
								<td>
									<a href="<?php echo $_SESSION['handle']['imgLink']; ?>" data-fancybox><img src="<?php echo $_SESSION['handle']['imgLink']; ?>" width="250px" height="350px"/></a>
								</td>
							</tr>
						<?php } ?>
						<tr><td> </td></tr>
						<tr>
							<td>
								<input type="submit" value="Valider les changement">
							</td>
						</tr>
					</table>
			</form>
		<?php } ?>
		</section>
	</div>
</div> 