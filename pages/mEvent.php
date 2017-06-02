<?php
//--------------------------
//Filename: mEvents.php
//Creation date: 02.05.2017
//Author: Krisyam Yves BAMOGO$
//Function: Allows user or admin to modify an existing event
//Last modification: 31.05.2017
//--------------------------
	if(!$connected)
		header('location:'.URL.'/?info=coReq');
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$sPOST=secureArray($_POST);
		extract($sPOST);
		
		$query='UPDATE event SET Name="'.$eventName.'", FDate="'.$fDate.'", LDate="'.$lDate.'", Place="'.$place.'", Details="'.$description.'" WHERE idevent='.$_GET['idEvent'];
		$res=DBConnect($query,"INSERT");
		if(isset($delPic)){
			$query='UPDATE event SET imgLink=" " WHERE idevent='.$_GET['idEvent'];
			DBConnect($query, "UPDATE");
		}
		
		if( isset($_FILES['userFile']['tmp_name']) && $_FILES['userFile']['tmp_name'] != ''){
		// Définir le dossier ou sera stocker l'image et le tableau des extentions autorisées
			$target= ROOT."/images/events"; 
			$tabExt = array('jpg','png','jpeg');
			$fileName=$_FILES['userFile']['name'];
			$tmpName=$_FILES['userFile']['tmp_name'];
		// Récuperer l'extention de l'image
			$extention=$extension=substr(strrchr($fileName,'.'),1);
		// Vérifier que l'extention soit autorisée
			if(in_array(strtolower($extention), $tabExt)){
		// Uploader le fichier / le remplacer si il existe déja
				$fileName= $_GET['idEvent'].".".$extention."";
				$defFile="$target/$fileName";
				move_uploaded_file($tmpName, $defFile);
		// Mettre le lien de l'image dans la DB
				$query='UPDATE event SET imgLink="'.$defFile.'" WHERE idevent='.$_GET['idEvent'];
				DBConnect($query, "UPDATE");
			}	
		}
		if(isset($_GET['info']) && $_GET['info']== 'admin')
			header('location:'.URL.'/?page=eventManagement');
		else
			header('location:'.URL.'/?page=event&idEvent='.$_GET['idEvent'].'&info=modified');
	}
	$query='SELECT * FROM event WHERE idevent='.$_GET['idEvent'];
	$res=DBConnect($query, "SELECT");
	$line=$res->fetch();
?>
<div align="center">
	<div class="6u 10u(mobile)">
		<section class="box">    
			<form method="POST" enctype="multipart/form-data">
				<h3>Modifier l'évènement <?php echo $line['Name']; ?></h3>
				<br>
				<table>
					<tr>
						<td colspan="2">
							<input type="text" name="eventName" value="<?php echo $line['Name']; ?>" required>
						</td>
					</tr>
				</table>
				<table>
						<!--<tr><td> </td></tr>-->
						<tr>
							<td>
								<div class='input-group date' id='datetimepicker1'>
									<input type='text' class="form-control" name="fDate" value="<?php echo $line['FDate']; ?>" required/>
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
								</div>
								<!--<input type="text" name="fDate" placeholder="Date de début" required/>-->
							</td>
							<td> </td>
							<td>
								<div class='input-group date' id='datetimepicker2'>
									<input type='text' class="form-control" name="lDate" value="<?php echo $line['LDate']; ?>"/>
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
								</div>
							</td>
						</tr>
				</table>
				<table>
						<!--<tr><td> </td></tr>-->
						<tr>
							<td>
								<input type="text" name="place" value="<?php echo $line['Place']; ?>" required>
							</td>
						</tr>
				</table>
				<table>
					<tr>
						<td>
							<textarea name="description" rows="10" cols="10" required><?php echo $line['Details']; ?></textarea>
						</td>
					</tr>
				</table>
				<?php if(isset($line['imgLink']) && !empty($line['imgLink'])){ ?>
				<table>
					<tr>
						<td>
							<a href="<?php echo $line['imgLink']; ?>" data-fancybox><img src="<?php echo $line['imgLink']; ?>" width="100%" height="80%" align="center"/></a>
						</td>
					</tr>
				</table>
				<table>
					<tr>
						<td>
							<input type="checkbox" name="delPic">Supprimer la photo
						</td>
					</tr>
				</table>
				<?php } ?>
				<table>
					<tr>
						<td>
							<input type="file" name="userFile"/>
						</td>
					</tr>
					<tr><td> </td></tr>
					<tr>
						<td colspan="2">
							<input type="submit" value="Modifier !"/>
						</td>
					</tr>
				</table>
			</form>
		</section>
	</div>
</div>