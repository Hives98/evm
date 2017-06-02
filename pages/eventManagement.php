<?php
//--------------------------
//Filename: eventManagement.php
//Creation date: 02.05.2017
//Author: Krisyam Yves BAMOGO$
//Function: User event administration interface
//Last modification: 31.05.2017
//--------------------------
	if(!$connected)
		header('location:'.URL.'/?info=coReq');
	if(!$admin)
		header('location:'.URL.'/?info=adminReq');
// Gestion d'erreur
	if( isset($_GET['info']) && $_GET['info']=='deleted'){
		$info[] = "L'évènement à été supprimer";
	}
// Sélectionner tous les utilisateurs
	$query="SELECT * FROM event";
	$result=DBConnect($query, "select");

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		extract($_POST);
		if(isset($check)){
			switch($check){
				case 'delete_event':
					deleteEvent($select);
					header('location:'.URL.'?page=eventManagement&info=deleted');
				break;
				case 'manage_event':
					header('location:'.URL.'?page=mEvent&idEvent='.$select.'&info=admin');
				break;
			}
		}else{
			
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
			<form method="POST">
				<input type="hidden" value="1" name="formType"/>
				<table>
					<tr>
						<td>
							<select name="select" required>
								<?php
									while($line=$result->fetch()){
										$query='SELECT Name, GName FROM user WHERE iduser='.$line['OwnerId'];
										$res=DBConnect($query, "SELECT");
										$handle=$res->fetch();
										$details = $line['Name'].' de '.$handle['Name'].' '.$handle['GName'];
										echo '<option value="'.$line['idevent'].'">'.$details.'</option>';
									}
								?>
							</select>
						</td>
					</tr>
					<tr><td> </td></tr>
					<tr>
						<td>
							<input type="checkbox" value="delete_event" name="check"> Supprimer l'évènement
						</td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" value="manage_event" name="check"> Gérer l'évènement
						</td>
					</tr>
					<tr><td> </td></tr>
					<tr>
						<td>
							<input type="submit" value="Valider"/>
						</td>
					</tr>
				</table>
			</form>
		