<?php
//--------------------------
//Filename: user.php
//Creation date: 02.05.2017
//Author: Krisyam Yves BAMOGO$
//Function: Display a userinfos
//Last modification: 31.05.2017
//--------------------------
	if(!$connected)
		header('location:'.URL.'/?info=coReq');
	$query='SELECT  * FROM user WHERE iduser='.$_GET['id'];
	$res=DBConnect($query, "SELECT");
	$line=$res->fetch();

	// var_dump($line);
?>
<div align="center">
	<div class="4u 12u(mobile)">
	<!-- Sidebar -->
		<section class="box">
			<a href="<?php echo $line['imgLink']; ?>" class="image featured" data-fancybox><img src="<?php echo $line['imgLink']; ?>" alt="" /></a>
			<header>
				<h3><?php echo $line['Name'].' '.$line['GName']; ?></h3>
				<h4><?php echo $line['UserName'] ?></h4>
				<p><?php echo $line['Email'] ?></p>
			</header>
			<p><?php if($line['Statut'] != '') echo $line['Statut']; else echo 'Aucun statut'; ?></p>
		</section>
	</div>
</div>