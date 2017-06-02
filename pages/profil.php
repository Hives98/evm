<?php
//--------------------------
//Filename: profil.php
//Creation date: 02.05.2017
//Author: Krisyam Yves BAMOGO$
//Function: Handling profil management for every users
//Last modification: 31.05.2017
//--------------------------
	if(!$connected)
		header('location:'.URL.'/?info=coReq');
// Récuperer les informations de l'utilisateur
	$query='SELECT * FROM user WHERE idUser='.$idUser.'';
	$res=DBConnect($query, "select");
	$echo=$res->fetch();
	$info=array();
//Gestion d'erreurs
	if( isset($_GET['info']) && $_GET['info']=='error' )
	{
	// Form 1
		if(isset($_SESSION['pseudoExist']) && $_SESSION['pseudoExist'] == true)
			$info[1][] = "Le nom d'utilisateur que vous avez choisit n'est malheuresement pas disponible";
		if(isset($_SESSION['emptyThing']) && $_SESSION['emptyThing']== true)
			$info[1][] = "Le formulaire n'a pas été correctement rempli";
		if(isset($_SESSION['wrongPassword']) && $_SESSION['wrongPassword'] == true)
			$info[0] = "Vous  n'avez pas renter le bon mot de passe";
		if(isset($_SESSION['shortPass']) && $_SESSION['shortPass'] == true)
			$info[3][] = "Le mot de passe choisit est trop court";
		if(isset($_SESSION['same']) && $_SESSION['same'] == true)
			$info[3][] = "Vous avez entrer deux mot de passe différents";
		if(isset($_SESSION['wrongFormat']) && $_SESSION['wrongFormat'] == true)
			$info[2][] = "Vous n'ête pas autorisé a utiliser e format d'image";	
	}
	if( isset($_GET['info']) && $_GET['info']=='none' )
	{
		if(isset($_SESSION['passChanged']) && $_SESSION['passChanged'] == true)
			$info[3][] = "Votre mot de passe à été changé";
		if(isset($_SESSION['infoChanged']) && $_SESSION['infoChanged']== true)
			$info[1][] = "Vos informations ont été mise à jour";
		if(isset($_SESSION['imgChanged']) && $_SESSION['imgChanged']== true)
			$info[2][] = "Votre photo à été mise à jour";
		if(isset($_SESSION['imgDeleted']) && $_SESSION['imgDeleted']== true)
			$info[2][] = "Votre photo à été supprimée";		
	}
/***************************************************************************************************
Traiter les données après soumission du formulaire
****************************************************************************************************/
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
	// Vérifier le mot de passe de l'utilisateur
		$query='SELECT Password FROM user WHERE iduser='.$idUser;
		$res=DBConnect($query, "select");
		$line=$res->fetch();
		
		$_SESSION['wrongPassword'] = false;
	// traiter le formulaire soumis
		$sPOST=secureArray($_POST);
		extract($sPOST);
	// Traiter le formulaire soumis
		switch ($form){
			case 1:
			// initialisation des variables d'érreurs locales
				$_SESSION['infoChanged'] = false;
				$_SESSION['pseudoExist'] = false;
				$_SESSION['emptyThing'] = false;
			// Si l'utilisateur a renter le bon mot de passe
				if($line['Password']=sha1(md5($fPassword))){
				// Changement de pseudo
					if($fNewPseudo != '' && $fNewName != '' && !(is_numeric($fNewName)) && $fNewGName != '' && !(is_numeric($fNewGName))){
					// Vérifier la disponibilité du nouveau pseudo
						$query='SELECT UserName FROM user WHERE UserName="'.$fNewPseudo.'"' ;
						$res = DBConnect($query, 'select');
						$line = $res->fetch();
					// Si le pseudo n'existe pas dans la base de donnée
						if($line == false){
							if(!empty($fNewPseudo)){
								$query='UPDATE user SET UserName = "'.$fNewPseudo.'" WHERE iduser='.$idUser;
								DBConnect($query, "UPDATE");
							}
							if(!empty($fNewName)){
								$query='UPDATE user SET Name = "'.$fNewName.'" WHERE iduser='.$idUser;
								DBConnect($query, "UPDATE");
							}
							if(!empty($fNewGName)){
								$query='UPDATE user SET GName = "'.$fNewGName.'" WHERE iduser='.$idUser;
								DBConnect($query, "UPDATE");
							}
							if(!empty($fStatut)){
								$query='UPDATE user SET Statut = "'.$fStatut.'" WHERE iduser='.$idUser;
							DBConnect($query, "UPDATE");
							}
							
							$_SESSION['infoChanged'] = true;
							header('location:'.URL.'/?page=profil&info=none');
						}
						else
						{
							if($line['UserName'] == $echo['UserName']){
								if(!empty($fNewName)){
									$query='UPDATE user SET Name = "'.$fNewName.'" WHERE iduser='.$idUser;
									DBConnect($query, "UPDATE");
								}
								if(!empty($fNewGName)){
									$query='UPDATE user SET GName = "'.$fNewGName.'" WHERE iduser='.$idUser;
									DBConnect($query, "UPDATE");
								}
								if(!empty($fStatut)){
									$query='UPDATE user SET Statut = "'.$fStatut.'" WHERE iduser='.$idUser;
									DBConnect($query, "UPDATE");
								}
								$_SESSION['infoChanged'] = true;
								header('location:'.URL.'/?page=profil&info=none');
							}
							else
							$_SESSION['pseudoExist'] = true;
							header('location:'.URL.'/?page=profil&info=error');
						}	
					}
					else{
						$_SESSION['emptyThing'] = true;
						header('location:'.URL.'/?page=profil&info=error');
					}
				}
				else{
					$_SESSION['wrongPassword'] = true;
					header('location:'.URL.'/?page=profil&info=error');
				}					
			break;
				
		// Upuloader ou changer la photo de profile
			case 2:
			// initialisation des variables d'érreurs locales
				$_SESSION['wrongFormat'] = false;
				$_SESSION['imgChanged'] = false;
				$_SESSION['imgDeleted'] = false;
			// Récuperer les données du formulaire et les échapper
				$sPOST=secureArray($_POST);
				extract($sPOST);
				if($line['Password']==sha1(md5($fPassword)))
				{
					if(isset($deletePic)){
						$query='UPDATE user SET imgLink="" WHERE iduser='.$idUser;
						DBConnect($query, "UPDATE");
						$_SESSION['imgDeleted'] = false;
						header('location:'.URL.'/?page=profil&info=none');
					}
				// Si une image à été uploader
					if(file_exists($_FILES['userFile']['tmp_name']) || is_uploaded_file($_FILES['userFile']['tmp_name'])){
					// Définir le dossier ou sera stocker l'image et le tableau des extentions autorisées
						$target= ROOT."/images/users"; 
						$tabExt = array('jpg','png','jpeg');
						$fileName=$_FILES['userFile']['name'];
						$tmpName=$_FILES['userFile']['tmp_name'];
					// Récuperer l'extention de l'image
						$extention=$extension=substr(strrchr($fileName,'.'),1);
					// Vérifier que l'extention soit autorisée
						if(in_array(strtolower($extention), $tabExt)){
						// Uploader le fichier / le remplacer si il existe déja
							$fileName= $idUser.".".$extention."";
							$defFile="$target/$fileName";
							move_uploaded_file($tmpName, $defFile);
						// Mettre le lien de l'image dans la DB
							$query='UPDATE user SET imgLink="'.$defFile.'" WHERE iduser='.$idUser;
							DBConnect($query, "UPDATE");
							$_SESSION['imgChanged'] = true;
							header('location:'.URL.'/?page=profil&info=none');
					}
					else{
						$_SESSION['wrongFormat'] = true;
						header('location:'.URL.'/?page=profil&info=error');
					}	
					}
				}
				else{
					$_SESSION['wrongPassword'] = true;
					header('location:'.URL.'/?page=profil&info=error');
				}
				
			break;
				
		// Changement de mot de passe
			case 3:
			// initialisation des variables d'érreurs locales
				$_SESSION['passChanged'] = false;
				$_SESSION['same'] = false;
				$_SESSION['shortPass'] = false;
			// Vérifier le mot de passe de l'utilisateur
				$query='SELECT Password FROM user WHERE iduser='.$idUser;
				$res=DBConnect($query, "select");
				$line=$res->fetch();
			// Si l'utilisateur a renter le bon mot de passe
				if($line['Password']==sha1(md5($fPassword))){
					if($fNewPassword == $fNewPasswordRe){
						if(strlen($fNewPassword) >= 8 && strlen($fNewPasswordRe) >= 8){
							$query='UPDATE User SET Password = "'.sha1(md5($fNewPassword)).'" WHERE iduser='.$idUser;
                			DBConnect($query, 'UPDATE');
							$_SESSION['passChanged'] = true;
							header('location:'.URL.'/?page=logout&info=passChanged');
						}
						else{
							$_SESSION['shortPass'] = true;
							header('location:'.URL.'/?page=profil&info=error');
						}
							
					}
					else{
						$_SESSION['same'] = true;
						header('location:'.URL.'/?page=profil&info=error');
					}
				}
				else{
					$_SESSION['wrongPassword'] = true;
					header('location:'.URL.'/?page=profil&info=error');
				}
			break;
		}
	}
	
?>
<div class="row">
	<div class="6u 10u(mobile)">
		<section class="box">    
			<table>
				<tr>
					<td>
						<header class="major">
							<h2>Vos informations</h2>
						</header>
						<li>Nom : <?php echo $echo['Name']; ?></li>
						<li>Prénom : <?php echo $echo['GName']; ?></li>
						<li>Adresse mail : <?php echo $echo['Email']; ?></li>
						<li>Nom d'utilisateur : <?php echo $echo['UserName']; ?></li>
					</td>
				</tr>
			</table>
		</section>
	</div>
	<div class="6u 10u(mobile)">
		<section class="box">
			<?php
				if(isset($info[1])){
					foreach($info[1] as $value){
						echo "<h4>".$value."</h4>";
					}
				}
				if(isset($info[0]))
					echo "<h4>".$info[0]."</h4>";
			?>
			<form method="POST">
				<table>
					<tr>
						<td>
							<header class="major">
								<h2>Changer vos informations</h2>
							</header>
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" name="fNewPseudo" value="<?php echo $echo['UserName'] ?>" value="">
						</td>
					</tr>
					<tr><td> </td></tr>
					<tr>
						<td>
							<input type="text" name="fNewName" value="<?php echo $echo['Name'] ?>">
						</td>
					</tr>
					<tr><td> </td></tr>
					<tr>
						<td>
							<input type="text" name="fNewGName" value="<?php echo $echo['GName'] ?>">
						</td>
					</tr>
					<tr><td> </td></tr>
					<tr>
						<td>
							<input type="text" name="fStatut" maxlength="60" value="<?php if($echo['Statut'] != '') echo $echo['Statut']; else echo 'Statut non renseigné'; ?>">
						</td>
					</tr>
					<tr><td> </td></tr>
					<tr>
						<td>
							<input type="password" name="fPassword" placeholder="********">
						</td>
					</tr>
					<tr><td> </td></tr>
					<tr>
						<td colspan="2">
							<input type="hidden" name="form" value="1">
							<input type="submit" value="Changer">
						</td>
					</tr>
				</table>
			</form>
		</section>
	</div>
</div>

<div class="row">
	<div class="6u 10u(mobile)">
		<section class="box">
			<?php
				if(isset($info[2])){
					foreach($info[2] as $value){
						echo "<h4>".$value."</h4>";
					}
				}
				if(isset($info[0]))
					echo "<h4>".$info[0]."</h4>";
			?>
			<form method="POST" enctype="multipart/form-data">
				<table>
					<tr>
						<td>
							<header class="major">
								<h2>Photo</h2>
							</header>
						</td>
					</tr>
					<?php if(isset($echo['imgLink']) && !empty($echo['imgLink'])) { ?>
					<tr>
						<td>
							<div align="center">
								<a href="<?php echo $echo['imgLink']; ?>" data-fancybox><img src="<?php echo $echo['imgLink'] ?>" width="80%" height="100%" align="center"/></a>
							</div>
						</td>
					</tr>
					<tr><td> </td></tr>
					<tr>
						<td>
							<input type="checkbox" name="deletePic"> Supprimer la photo <br>
						</td>
					</tr>
					<tr><td> </td></tr>
					<?php } ?>
					<tr>
							<td>
								<input type="file" name="userFile" accept=".png, .jpg, .jpeg" <?php if(!isset($echo['imgLink']) || empty($echo['imgLink'])) echo 'required'; ?>>
							</td>
						</tr>
						<tr><td> </td></tr>
						<tr>
							<td>
								<input type="password" name="fPassword" placeholder="*****">
							</td>
						</tr>
						<tr><td> </td></tr>
						<tr>
							<td>
								<input type="hidden" name="form" value="2">
								<input type="submit" Value="Valider" >
							</td>
						</tr>
				</table>
			</form>
		</section>
	</div>
	<div class="6u 10u(mobile)">
		<section class="box">
			<?php
				if(isset($info[3])){
					foreach($info[3] as $value){
						echo "<h4>".$value."</h4>";
					}
				}
				if(isset($info[0]))
					echo "<h4>".$info[0]."</h4>";
			?>
			<form method="POST">
				<table>
					<tr>
						<td>
							<header class="major">
								<h2>Mot de passe</h2>
							</header>
						</td>
					</tr>
					<tr>
						<td>
							<input type="password" name="fNewPassword" placeholder="Nouveau mot de passe">
						</td>
					</tr>
					<tr><td> </td></tr>
					<tr>
						<td>
							<input type="password" name="fNewPasswordRe" placeholder="Nouveau mot de passe">
						</td>
					</tr>
					<tr><td> </td></tr>
					<tr>
						<td>
							<input type="password" name="fPassword" placeholder="**********">
						</td>
					</tr>
					<tr><td> </td></tr>
					<tr>
						<td>
							<input type="hidden" name="form" value="3">
							<input type="submit" Value="Valider" >
						</td>
					</tr>
				</table>
			</form>
		</section>
	</div>
</div>