<?php
/*
Autor: Krisyam Yves BAMOGO
last update: 10.05.2017
page title: lougout.php
Fonction : Logout the user
*/

// Si l'utilisateur est connecté, tuer les sessions et le rediriger sur la page d'acceuille
if($connected==true)
{
	session_destroy();
	if(isset($_GET['info']) && $_GET['info']=='passChanged')
        header('location:'.URL.'?info=change');
	else
		header('location:'.URL.'?info=logout');
}
else header('location:'.URL.'?info=notlogged');
?>

