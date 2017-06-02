<?php
//--------------------------
//Filename: registerScript.php
//Creation date: 02.05.2017
//Author: Krisyam Yves BAMOGO$
//Function: Creating an account for new users
//Last modification: 31.05.2017
//--------------------------


// Gestion d'erreurs
	if( isset($_GET['info']) && $_GET['info']=='errorRegister' )
	{
		if($_SESSION['pseudoFree'] == true)
			$info[] = "Ce nom d'utilisateur est déja attribué a un autre utilisateur";
		if($_SESSION['passwdDifferent']== true)
			$info[] = "Les mots de passes sont différents";
		if($_SESSION['emailNotFree'] == true)
			$info[] = "Cette adresse mail à déja été utilisée pour un autre compte";
		if($_SESSION['emptyThings'] == true)
			$info[] = "Le formulaire n'a pas été correctement compléter";
		if($_SESSION['passwdTooShort'] == true)
			$info[] = "Les mots de passent sont trop courts";
	}

// Traiter les données après soumission du formulaire

	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
	// Récuperer e echapper les données du formulaire
		$sPOST=secureArray($_POST);
		extract($sPOST);
	// initialiser les variables de detection d'erreurs
		$_SESSION['passwdDifferent'] = false;
		$_SESSION['passwdTooShort'] = false;
		$_SESSION['pseudoFree'] = false;
		$_SESSION['emailNotFree'] = false;
		$_SESSION['emptyThings'] = false;
		$i=0;
	// Tester que les champs soient remplis et au bon format
		if($fPseudo == '' || $fEmail == '' || $fPassword == '' || $fPasswordRe== '' || $fLastName == '' || $fFirstName == '' || is_numeric($fLastName) || is_numeric($fFirstName))
		{
			$i++;
			$_SESSION['emptyThings'] = true;
		}
	// Tester que le nom d'utilisateur ne soit pas déja dans la bdd
		$querypseudo="SELECT UserName FROM user WHERE UserName='".$fPseudo."'";
		$resPseudotest = DBConnect($querypseudo, 'select');
		$pseudotest = $resPseudotest->fetch();
		if($pseudotest != false)
		{
			$i++;
			$_SESSION['pseudoFree']=true;
		}

	// Tester que les deux mots de passes soient similaire.
		if($fPassword != $fPasswordRe)
		{
			$i++;
			$_SESSION['passwdDifferent']=true;
		}
	// Tester que les mot de passes aient au moins 8 caractères    
		if(strlen($fPassword) < 7 && strlen($fPassword) < 7)
		{
			$i++;
			$_SESSION['passwdTooShort'] = true;
		}

	// Tester que l'adresse mail ne soit pas déja utulisée
		$querymail="SELECT Email FROM user WHERE Email='".$fEmail."'";
		$resEmailtest=DBConnect($querymail, 'select');
		$emailtest=$resEmailtest->fetch();

		if($emailtest != false)
		{
			$i++;
			$_SESSION['emailNotFree']=true;

		}

	// Si il n'y a aucune erreur 
		if($i === 0)
		{
			// Generer un id unique qui servira a activer le compte
			$token = md5(uniqid());
			//Inserer les données dans la DB.
			$queryinsert='INSERT INTO user (Name, GName, Email, Password, UserName, Token, Type) VALUES ("'.$fLastName.'","'.$fFirstName.'", "'.strtolower($fEmail).'","'.sha1(md5($fPassword)).'","'.$fPseudo.'", "'.$token.'", 0)';
			DBConnect($queryinsert, 'INSERT');

			$link = 'http://eventmanager.local/?page=home&info=activation&id='.$token;
			$wrongLink ='http://eventmanager.local/?page=home&info=delete&id='.$token;
			// Message
			$messageText = "
			Bonjour, votre adresse mail a été utilisée pour créer un compte sur http://eventmanager.local !<br>
			<br>
			Vous pouvez activer le compte en cliquant sur le lien suivant: ".$link." !<br>
			<br>
			Si vous n'êtes pas l'auteur de cette inscription, utilisez le lien suivant: ".$wrongLink." pour supprimer votre adresse mail de notre base de données.<br>
			<br>
			EventManager team.<br>
			---------------------------------------------------------------------------------------<br>
			Mail automatique, merci de ne pas répondre.<br>";
			sendMessage($messageText, $fEmail);
			header('location:'.URL.'/?page=home&info=registered');
		}
	// EN cas d'erreur detecté dans le formulaire, renvoie sur la page d'inscription avec les erreurs
		else
			header('location:'.URL.'/?page=register&info=errorRegister');
	}
?>
<div class="row">
    <div class="6u 10u(mobile)">
        <section class="box">    
            <table>
                <tr>
                    <td colspan=2>
                        <h3>Veuillez remplir le formulaire pour céer un compte.</h3>
                        <h3>Veuillez cliquer <font color='red'><a href="<?php echo URL;?>/?page=home">ICI</a></font> si vous possédez déja un compte</h3>
                        <?php
                        if(!empty($info)){
                            echo "<hr><font color='red'><ul>";
                            foreach($info as $msg){
                                echo "<li>".$msg."</li>";
                            }
                        echo "</ul></font>";
                        }?>
                    </td>
                </tr>
            </table>
        </section>
    </div>
    <div class="6u 10u(mobile)">
        <section class="box">
            <form method="post">
                <h3>Formulaire de création de compte</h3>
                <br>
                <table align="center">
                    <tr>
                        <td>
                            <input type="email" name="fEmail" placeholder="Adresse mail">
                        </td>
                    </tr>
                    <tr><td> </td></tr>
                    <tr>
                        <td>
                            <input type="text" name="fPseudo" placeholder="Nom d'utilisateur">
                        </td>
                    </tr>
                    <tr><td> </td></tr>
                    <tr>
                        <td>
                            <input type="text" name="fLastName" placeholder="Prénom">
                        </td>
                    </tr>
                    <tr><td> </td></tr>
                    <tr>
                        <td>
                            <input type="text" name="fFirstName" placeholder="Nom de famille">
                        </td>
                    </tr>
                    <tr><td> </td></tr>
                    <tr>
                        <td>
                            <input type="password" name="fPassword" placeholder="Mot de passe">
                        </td>
                    </tr>
                    <tr><td> </td></tr>
                    <tr>
                        <td>
                            <input type="password" name="fPasswordRe" placeholder="Mot de passe">
                        </td>
                    </tr>
                    <tr><td> </td></tr>
                    <tr>
                        <td colspan="2">
                            <input type="submit" value="Valider !">
                        </td>
                    </tr>
                </table>
            </form>
        </section>
    </div>
</div>