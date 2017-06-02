<?php
//--------------------------
//Filename: home.php
//Creation date: 02.05.2017
//Author: Krisyam Yves BAMOGO$
//Function: Password reset and user connexion
//Last modification: 31.05.2017
//--------------------------
	$reset=0;
	if(isset($_GET['info']))
	{
		switch($_GET['info']){
		// Gestion de l'activation / suppresion du compte
			case 'activation':
				$query='UPDATE user SET Type = 1 where Token="'.$_GET['id'].'"';
            	DBConnect($query, 'update');
            	$info[] = "Votre compte a été activé, vous pouvez vous connecter dès a présent :) !"; 
			break;
			case 'delete':
				$query='DELETE from user where Token="'.$_GET['id'].'"';
            	DBConnect($query, 'delete');
            	$info[] = "Les informations liées a votre compte ont été supprimées :(";
			break;
		// Tester les variables d'erreurs et remplir le tableau d'erreurs
			case 'logout':
				$info[] = "Vous avez été déconnecté";
			break;
			case 'change':
				$info[] = "Votre mot de passe a été changé, merci de vous reconnecter";
			break;
			case 'wrongEmail':
				$info[] = "Cette adresse mail n'est liée a aucun compte.";
			break;
			case 'passwordReseted':
				$info[] = "Votre mot de passe a été modifié.";
			break;
			case 'registered':
				$info[] = "Vote compte a été créer, vous pouvez dès a présent l'activer avec le mail que vous avez recu";
			break;
			case 'wrongData':
				$info[] = "Les informations renseignées ne sont pas correctes";
			break;
			case 'inactiveAccount':
				$info[] = "Merci d'activer votre compe avant de l'utiliser";
			break;
			case 'wrongDatas':
				$info[]="Les informations fournies ne correspondent à aucun compte";
			break;
		// Si une rénitialisation a été demandée
			case 'resetpasswd':
				$reset=1;
			break;
			case 'emailNotExisting':
				$info[]="Cette adresse mail n'est associée à aucun compte";
			break;
			default:
			break;
		}
	}
// En cas de soumission du formulaire	
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		// Extraire et securiser les données transmise via le formulaire
			$sPOST=secureArray($_POST);
			extract($sPOST);
		// Si une rénitialisation de mot de passe a été demandée
			if(isset($_GET['info']) && $_GET['info']=='resetpasswd')
			{
			// Modifier le mot de passe de l'utilisateur
				$query='SELECT * from user WHERE Email="'.$passwordResetEmail.'"';
				$res=DBConnect($query,"select");
				$line=$res->fetch();
				if(!in_array($passwordResetEmail, $line['Email']))
					header('location:'.URL.'?info=emailNotExisting');
				else
				{
				// Récuperer l'Id de l'utilisateur
					$line=$res->fetch();
				// Generer un nouveau mot de passe
					passwdReset($line['iduser'], $line['Email']);
					header('location:'.URL.'/?page=home&info=passReseted');
				}
			}
			else
				$connected == false;
				$query="SELECT * FROM user WHERE Email='".$fEmail."'";
				$res = DBConnect($query, "select");
				$line=$res->fetch();
			// Si l'adresse email n'existe pas dans la bdd rediriger vers le formulaire
				if(!$res)
					header('location:'.URL.'/?page=home&info=wrongData');
			// Si le compte n'est pas actif rediriger vres le formulaire 
				elseif($line['Type'] == '0' || $line['Type'] == '2')
					header('location:'.URL.'/?page=home&info=inactiveAccount');
			// Tester la combinaison mo de passe adresse
				//  || ($line['UserName'] == $fEmail && $line['Password'] == sha1(md5($fPassword)))
				elseif( ($line['Email'] == strtolower($fEmail) && $line['Password'] == sha1(md5($fPassword))))
				{
					// Si la combinaison exite dans la Base de données 
					$_SESSION['connectedUser']=true;
					$_SESSION['name']=$line['Name'];
					$_SESSION['surname']=$line['GName'];
					$_SESSION['username']=$line['username'];
					$_SESSION['mail']=$line['Email'];
					$_SESSION['idUser']=$line['iduser'];
					$_SESSION['Type']=$line['Type'];
					// Redirect to home page with querystring info=loged
					header('location:'.URL.'?info=loged');
				}
				else
					header('location:'.URL.'/?page=home&info=wrongDatas');
	}
?>

<!-- Banner -->
<section id="banner">
	<header>
		<h2>Bienvenue sur Event manager.</h2>
		<p>Un puissant outil de gestion d'évènements</p>
	</header>
</section>

<?php if(!$connected && $reset==0){ ?>
<div class="container">
	<header class="major">
		<h2>Connexion / création de compte</h2>
	</header>
	<div class="row">
		<div class="6u 10u(mobile)">
			<section>
				<section class="box">	
					<form method="post">
						<h3>Formulaire de connexion</h3>
							<br>
							<table align="center">
								<tr>
									<td>
										<input type="email" name="fEmail" placeholder="Adresse mail" required>
									</td>
								</tr>
								<tr><td> </td></tr>
								<tr>
									<td>
										<input type="password" name="fPassword" placeholder="********" required>
									</td>
								</tr>
								<tr>
									<td>
										<a href="http://eventmanager.local/?page=home&info=resetpasswd"><p>Rénitialiser le mot de passe</p></a>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<input type="submit" value="connexion">
									</td>
								</tr>
							</table>
					</form>
				</section>
			</section>
		</div>
		<div class="6u 10u(mobile)">
			<section class="box">    
				<table>
					<tr>
						<td>
							<h3>Veuillez remplir le formulaire de connexion si vous possedez un compte</h3>
							<h3>Veuillez cliquer <font color='red'><a href="<?php echo URL;?>/?page=register">ICI</a></font> pour creer un compte</h3>
							<?php
								if(!empty($info)){
									echo "<hr><font color='red'><ul>";
									foreach($info as $msg){
										echo "<li>".$msg."</li>";
									}
								echo "</ul></font>";
                        	}?>
							<br><br><br>
						</td>
					</tr>
				</table>
			</section>
		</div>
	</div>
</div>
<?php } ?>
<?php if(isset($_GET['info']) && $_GET['info']=='resetpasswd') { ?>
	<div align="center">
		<section class="box">
			<form method="POST">
				<table>
					<tr>
						<td>
							<input type="email" name="passwordResetEmail" placeholder="L'adresse mail de votre compte">
						</td>
					</tr>
					<tr><td> </td></tr>
					<tr>
						<td>
							<input type="submit" value="Rénitialiser le mot de passe">
						</td>
					</tr>
				</table>
			</form>
		</section>
	</div>
<?php } ?>
<!-- Intro -->
	<section id="intro" class="container">
		<div class="row">
			<div class="4u 12u(mobile)">
				<section class="first">
					<i class="icon featured fa-cog"></i>
					<header>
						<h2>Gagner du temps</h2>
					</header>
					<p>Nisl amet dolor sit ipsum veroeros sed blandit consequat veroeros et magna tempus.</p>
				</section>
			</div>
			<div class="4u 12u(mobile)">
				<section class="middle">
					<i class="icon featured alt fa-flash"></i>
					<header>
						<h2>Gagnez en efficacité</h2>
					</header>
					<p>Nisl amet dolor sit ipsum veroeros sed blandit consequat veroeros et magna tempus.</p>
				</section>
			</div>
			<div class="4u 12u(mobile)">
				<section class="last">
					<i class="icon featured alt2 fa-star"></i>
					<header>
						<h2>Ganez en ergonomie</h2>
					</header>
					<p>Nisl amet dolor sit ipsum veroeros sed blandit consequat veroeros et magna tempus.</p>
				</section>
			</div>
		</div>
		<!--<footer>
			<ul class="actions">
				<li><a href="#" class="button big">Get Started</a></li>
				<li><a href="#" class="button alt big">Learn More</a></li>
			</ul>
		</footer>-->
	</section>