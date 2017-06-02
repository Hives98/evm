<?php
//--------------------------
//Filename: management.php
//Creation date: 02.05.2017
//Author: Krisyam Yves BAMOGO$
//Function: admins management interface
//Last modification: 31.05.2017
//--------------------------
if(!$connected)
	header('location:'.URL.'/?info=coReq');
if(!$admin)
	header('location:'.URL.'/?info=adminReq');
?>
<div align="center">
	<section class="box">
		<table>
			<tr>
				<td>
					<a href="<?php echo URL; ?>/?page=userManagement"><button>Gérer un utilisateur</button></a>
				</td>
			</tr>
		</table>
		<table>
			<tr>
				<td>
					<a href="<?php echo URL; ?>/?page=eventManagement"><button>Gérer un évènement</button></a>
				</td>
			</tr>
		</table>
	</section>
</div>