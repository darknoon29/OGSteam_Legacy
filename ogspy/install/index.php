<?php
/**
 * Fichier d'installation d'ogspy : ROOT/install/index.php 
 * @package OGSpy
 * @subpackage install
 * @author Kyser
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @version 3.04
 * @since 3.04 - 26 sept. 07
 */

define("IN_SPYOGAME", true);
define("INSTALL_IN_PROGRESS", true);

require_once("../common.php");
require_once("version.php");
if (isset($pub_redirection)) {
	switch ($pub_redirection) {
		case "install";
		redirection("install.php");
		break;
		
		case "upgrade";
		redirection("upgrade_to_latest.php");
		break;
	}
}

?>
<html>
<head>
<title>OGSpy</title>
<link rel="stylesheet" type="text/css" href="../skin/OGSpy_skin/formate.css" />
</head>
<body>

<table width="100%" align="center" cellpadding="20">
<tr>
	<td height="70"><div align="center"><img src="../images/OgameSpy2.jpg"></div></td>
</tr>
<tr>
	<td align="center">
		<table>
		<tr>
			<td align="center"><font size="3"><b>Bienvenue sur le projet OGSpy</b></font></td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>	
			<td>
<font size="2">
<ul><li type="square">OGSPy est un projet qui a pour but d'enregistrer dans une base de donn�es les coordonn�es de tous les joueurs d'un univers.
<li type="square">Disposer d'un tel outil offre de multiples avantages pour une alliance ou un collectif :
<ul><li type="disc">Recensement de toutes les coordonn�es libres selon plusieurs crit�res (galaxie, syst�me solaire et rang).
<li type="disc">Recensement de toutes les plan�tes d'un joueur ou d'une ally. Information vitale durant les p�riodes de guerre.
<li type="disc">Possibilit�s d'extensions quasi illimit�es gr�ce aux mods.
<li type="disc">Etc ...
</ul></ul>
<center>Si vous souhaitez plus d'informations, rendez-vous sur ce forum : <a href="http://www.ogsteam.fr/" target="_blank">http://www.ogsteam.fr/</a></center>
</font>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
<?php 	
if(!(version_compare(PHP_VERSION, "5.0.0") >= 0)){
    echo "<tr><td style='font-size: 24px;'><font color='red'>Version de PHP insuffisante</font></td></tr>";
    echo "<tr><td><font color='red'>Pour pouvoir effectuer une installation compl�te d'OGSpy, votre h�bergement doit �tre dot� au minimum de la version 5 de PHP.";
    echo "<br/><br/>Vous disposez actuellement de la version : " . PHP_VERSION;
    echo "</font></td></tr>";
}else{
?>
		<tr><td>&nbsp;</td></tr>
		<form action="index.php" method="POST">
		<tr>
			<td align="center"><font color="orange"><b>Choisissez quelle action vous d�sirez effectuer : </b></font>
				<select name="redirection" onchange="this.form.submit();" onkeyup="this.form.submit();">
					<option></option>
					<option value="install">Installation compl�te</option>
					<option value="upgrade">Mise � jour</option>
				</select>
			</td>
		</tr>
		</form>
		</table>
	</td>
</tr>
<?php
} // Fin Version compare
?>
<tr align="center">
	<td>
		<center><font size="2"><i><b>OGSpy</b> is an <b>OGSteam Software</b> (c) 2005-2012</i><br />v <?php echo $install_version ;?></font></center>
	</td>
</tr>
</table>
</body>
</html>
