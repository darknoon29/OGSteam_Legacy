<?php
/***********************************************************************
 * filename	:	Convertisseur.php
 * desc.	:	Fichier principal
 * created	: 	06/11/2006 Mirtador
 *
 * *********************************************************************/
if (!defined('IN_SPYOGAME') && !defined('IN_UNISPY2')) {
	die("Hacking attempt");
}
if(!defined('IN_UNISPY2'))
	require_once("views/page_header.php");
echo "<table width='100%'>";
//D�bug
error_reporting(E_ALL);
//fonction
/**
 * fait les infobulle
 *merci a oXid_FoX pour la fonction
 */
function infobulle($txt_contenu, $titre = 'Aide', $largeur = '200') {
	// remplace ' par \'
	// puis remplace \\' par \'
	// au cas o� le guillemet simple aurait d�j� �t� prot�g� avant l'appel � la fonction
	$txt_contenu = str_replace('\\\\\'','\\\'',str_replace('\'','\\\'',$txt_contenu));
	// remplace le guillemet double par son code HTML
	$txt_contenu = str_replace('"','&quot;',$txt_contenu);

	// pareil avec $titre
	$titre = str_replace('\\\\\'','\\\'',str_replace('\'','\\\'',$titre));
	$titre = str_replace('"','&quot;',$titre);

	// tant qu'on y est, v�rification de $largeur
	if (!is_numeric($largeur))
	  $largeur = 200;

	// affiche l'infobulle
	echo '<img style="cursor: pointer;" src="images/help_2.png" onMouseOver="this.T_WIDTH=210;this.T_TEMP=0;return escape(\'<table width=&quot;',$largeur
	,'&quot;><tr><td align=&quot;center&quot; class=&quot;c&quot;>',$titre,'</td></tr><tr><th align=&quot;center&quot;>',$txt_contenu,'</th></tr></table>\')">';
}
//fonction de s�lection de BBcode
?>
<SCRIPT language="JavaScript">
function selectionner() {
		document.getElementById('bbcode').select();
	}
</script>
<?php
//Fonction remplasser les virgule par un point
function virguleapoint($unit�e){
if (preg_match("#,#", "$unit�e")){
	$unit�e = preg_replace("#,#", '.', $unit�e);
	return "$unit�e";
	}
return "$unit�e";
}
//fonction remplasser un point par une virgule
function pointavirgule($unit�e){
if (preg_match("#\.#", "$unit�e")){
	$unit�e = preg_replace("#\.#", ',', $unit�e);
	return "$unit�e";
	}
return "$unit�e";
}
//finction de traitement des unit�es
function unit�e($unit�e){
if (preg_match("#k|kilo#i", "$unit�e")){
	$unit�e = preg_replace("#k#i", '', $unit�e);
	$unit�e = $unit�e*1000;
	return "$unit�e";
	}
else if (preg_match("#m|kk|million#i", "$unit�e")){
	$unit�e = preg_replace("#m|kk#i", '*1000000', $unit�e);
	$unit�e = $unit�e*1000000;
	return "$unit�e";
	}
else if (preg_match("#g|mm|giga#i", "$unit�e")){
	$unit�e = preg_replace("#g|mm#i", '*1000000000', $unit�e);
	$unit�e = $unit�e*1000000000;
	return "$unit�e";	
	}
return "$unit�e";
}
//fonction de d�tection d'un nombre
function estunnombre($chainedecaract�re){
	if (preg_match("#[a-z]#i", "$chainedecaract�re")){
		$r�sult= false;
	}
	else{
		$r�sult= true;
	}
	return "$r�sult";
}
// D�finition du ouvert
if(isset($_POST['ouvert'])){
$ouvert=$_POST['ouvert'];
}
else{
$ouvert="0";
}
if ($ouvert=="1"){
	//d�finition des variables
	$titane=$_POST['titane'];
	$carbone=$_POST['carbone'];
	$tritium=$_POST['tritium'];
	$tauxm=$_POST['tauxm'];
	$tauxc=$_POST['tauxc'];
	$tauxd=$_POST['tauxd'];
	$combienm=$_POST['combienm'];
	$combienc=$_POST['combienc'];
	$combiend=$_POST['combiend'];
	$transporteur=$_POST['transporteur'];
	$unit�e=$_POST['unit�e'];
	$couleur=$_POST['couleur'];
	//on remplasse les champ de ressources vide par des 0
	if (!isset($titane)) {$titane="0";}
	if (!isset($carbone)) {$carbone="0";}
	if (!isset($tritium)) {$tritium="0";}
	//On commence pour transformer les virgules en point pour PHP
	$titane=virguleapoint($titane);
	$carbone=virguleapoint($carbone);
	$tritium=virguleapoint($tritium);
	//On v�rifie les unit�es
	$titane=unit�e($titane);
	$carbone=unit�e($carbone);
	$tritium=unit�e($tritium);
	//message d'erreures
	$total_pourc=($combienm)+($combienc)+($combiend);
	//on v�rfi maintenant qu'il ne reste plus de lettre dans tout les variables.
	if(!estunnombre($titane) OR !estunnombre($carbone) OR !estunnombre($tritium) OR !estunnombre($combienm)OR !estunnombre($combienc) OR !estunnombre($combiend) OR !estunnombre($tauxm) OR !estunnombre($tauxc) OR !estunnombre($tauxd)){
		echo "Vous ne devez mettre Uniquement des chiffre et les unit�es!";
		$error="1";
	}
	//on v�rifi ensuite que le total des pourcentages donne 100
	elseif ($total_pourc!="100") {
		echo "Le total des pourcentages doivent donner 100%";
		$error="1";
	}
	//on vrifi qu'il y a au moin une ressource
	elseif ($titane=="0"  &&  $carbone=="0"  &&  $tritium=="0"){
		echo "Vous devez mettre au moins une ressource que vous souhaitez �changer";
		$error="1";
	}
	else{
		//On fait les totaux des taux
		if($tauxm!="0" && $tauxc!="0" && $tauxd!="0")
		$Valleur=($titane)/($tauxm)+($carbone)/($tauxc)+($tritium)/($tauxd);
		else $Valleur=0;
		//on calcule
		if ($tauxm!="0"  &&  $tauxm!=""  &&  $combienm!="0"  &&  $combienm!=""){
			$pourcM=($combienm)/100;
			$TotalM=($Valleur)*($pourcM)*($tauxm);
		}
		else{
			$TotalM="0";
			}
		if ($tauxc!="0"  &&  $tauxc!=""  &&  $combienc!="0"  &&  $combienc!=""){
			$pourcC=($combienc)/100;
			$TotalC=($Valleur)*($pourcC)*($tauxc);
		}
		else{
			$TotalC="0";
			}
		if ($tauxd!="0"  &&  $combiend!="0"  &&  $tauxd!=""  &&  $combiend!=""){
			$pourcD=($combiend)/100;
			$TotalD=($Valleur)*($pourcD)*($tauxd);
		}
		else{
			$TotalD="0";
			}
		//On arrondis tout les nombres, car l ne devrais pas avoirde demis ressources
		$offreM=round($titane);
		$offreC=round($carbone);
		$offreD=round($tritium);
		$TotalM=round($TotalM);
		$TotalC=round($TotalC);
		$TotalD=round($TotalD);
		//On aplique maintenant l'unit�e qu'on veut pour les r�sultats
		$offreM=$offreM/$unit�e;
		$offreC=$offreC/$unit�e;
		$offreD=$offreD/$unit�e;
		$demandeM=$TotalM/$unit�e;
		$demandeC=$TotalC/$unit�e;
		$demandeD=$TotalD/$unit�e;
		//Les op�ration fini on rechange les points par des virgules
		$titane=pointavirgule($titane);
		$carbone=pointavirgule($carbone);
		$tritium=pointavirgule($tritium);
		$offreM=pointavirgule($offreM);
		$offreC=pointavirgule($offreC);
		$offreD=pointavirgule($offreD);
		$demandeM=pointavirgule($demandeM);
		$demandeC=pointavirgule($demandeC);
		$demandeD=pointavirgule($demandeD);
		$TotalM=pointavirgule($TotalM);
		$TotalC=pointavirgule($TotalC);
		$TotalD=pointavirgule($TotalD);
	}
}
?>


		<form action="?action=convertisseur" method="post">
		<!--<input type="hidden" name="action" value="Convertisseur">-->
		<input type="hidden" name="ouvert" value="1"/> 

			<th colspan="4">Convertisseur de ressources</th>
	<tr>
		<td align="center">
		<table>
			<tr>
			<th colspan="4">Options</th>
			</tr>
				
				<tr>
				<?php if (!isset($transporteur)){$transporteur="aucun";}?>
				<td class="c">Calcule des transporteurs <?php infobulle('Ici vous pouvez d�cider si oui ou non il vous met le calcule du nombre de transporteur requis et Quel type de transporteur vous allez utiliser ')?></td>
				<th><input type="radio" name="transporteur" value="aucun" <?php if ($transporteur=='aucun') { ?> checked="checked"<?php } ?> /> Aucun</th>
				<th><input type="radio" name="transporteur" value="pt" <?php if ($transporteur=='pt') { ?> checked="checked"<?php } ?> /> PT-5</th>
				<th><input type="radio" name="transporteur" value="gt" <?php if ($transporteur=='gt') { ?>checked="checked"<?php } ?> /> GT-50</th>
				</tr> 
				
				<tr>
				<?php if (!isset($unit�e)){$unit�e="1000";}?>
				<td class="c">Unit�e pour les r�sultats <?php infobulle('Vous pouvez ici choisir en quel unit� s\'affiche les r�sultats. bien utile pour pas avoir des nom long comme le bras ;\) ')?></td>
				<th><input type="radio" name="unit�e" value="1000" <?php if ($unit�e=='1000') { ?> checked="checked"<?php } ?> /> Kilo</th>
				<th><input type="radio" name="unit�e" value="1000000" <?php if ($unit�e=='1000000') { ?> checked="checked"<?php } ?> /> Million</th>
				<th><input type="radio" name="unit�e" value="1000000000" <?php if ($unit�e=='1000000000') { ?>checked="checked"<?php } ?> />Giga</th>
				</tr>
				
				<tr>
				<?php if (!isset($couleur)){$couleur="clair";}?>
				<td class="c">Couleur du forum pour le BBcode</th>
				<th colspan="3">
				<select name="couleur">
					<option value="clair"	<?php if ($couleur=='clair') {	?> selected="selected"<?php } ?>>Clair</option>
					<option value="foncer"	<?php if ($couleur=='foncer') {	?> selected="selected"<?php } ?>>Foncer</option>
				</select>
				</th>
				</tr>
			</tr>
		</table>
	</td>
	<td align="center">
		<table>
			<th> ressources</th>
			<th> Quantit�s <?php infobulle('Mettez ici, la quantit� de ressource que vous souhaitez �changer, Mettez l\'unit� que vous souhaiter utiliser comme ceci: K,Kilo,M,Million,G,Giga'); ?></th>
			<tr>
				<td class="c">Titane:</td>
					<td>
						<input type="text" name="titane" value="<?php if (isset($titane)) {echo "$titane";} else {echo '0';}?>"/>
					</td>
			</tr>
			<tr>
				<td class="c">Carbone:</td>
					<td>
						<input type="text" name="carbone" value="<?php if (isset($carbone)) {echo "$carbone";} else {echo '0';}?>"/>
					</td>
			</tr>
			<tr>
				<td class="c">Tritium: </td>
					<td>
						<input type="text" name="tritium" value="<?php if (isset($tritium)) {echo "$tritium";} else {echo '0';}?>"/>
					</td>
			</tr>
	</table>
	</td>
	<td>
		<table>
			<tr>
			<th colspan="2">Taux<?php infobulle('Ici vous d�cidez a quel taux vous voulez le vendre, exemple, le taux bien connu, 1 Tritium = 5 Carbone = 6 Titane')?></th>
			</tr>
					<tr>	<td class="c">Titane	</td>	<td>
					<input type="text" name="tauxm" value="<?php if (isset($tauxm)) {echo "$tauxm";} else {echo '6';}?>"/> 	</td>	</tr>
					<tr>	<td class="c">Carbone	</td>	<td>
					<input type="text" name="tauxc" value="<?php if (isset($tauxc)) {echo "$tauxc";} else {echo '5';}?>"/>		</td>	</tr>
					<tr>	<td class="c">Tritium	</td>	<td>
					<input type="text" name="tauxd" value="<?php if (isset($tauxd)) {echo "$tauxd";} else {echo '1';}?>"/>		</td>	</tr>

			</tr>
		</table>
	</td>
	<td align="center">
			<table>
			<tr>
			<th colspan="2">pourcentage (%) <?php infobulle('Choisissez ici quel est le m�lange de chaque ressources que vous d�sirez avoir. Exemple: pour un m�lange titane/carbone on met 50% � carbone et 50% � titane. Attention ne metez pas le % ou le calcule ne marchera pas. Attention! Il faut aussi que la somme de vos pourcentage donne 100%')?></th>
			</tr>
				<td>
					<tr>	<td class="c">Titane</td>	<td>
					<input type="text" name="combienm" value="<?php if (isset($combienm)) {echo "$combienm";} else {echo '0';}?>"/></td>	</tr> 
					<tr>	<td class="c">Carbone</td>	<td>
					<input type="text" name="combienc" value="<?php if (isset($combienc)) {echo "$combienc";} else {echo '0';}?>"/></td>	</tr> 
					<tr>	<td class="c">Tritium</td>	<td>
					<input type="text" name="combiend" value="<?php if (isset($combiend)) {echo "$combiend";} else {echo '0';}?>"/></td>	</tr>
				</td>
			</tr>
		</table>
	</td>
</td>
<td>
</td>
	</tr>
	<tr>

	<th colspan="4">
		<table>
		<input type="submit">
		</table>
	</th>
	</tr>
</table>
	<?php 
//tableaux des r�sultats 
//on doit tout d'abord s'assurer que le formulaire est ouvert
if ($ouvert=="1" and !isset($error)){
	?> 
	<table>
	<td>
	<table>
		<th colspan="3">�change
		<?php
		if ($unit�e=='1000000') echo' (Million)';
		elseif ($unit�e=='1000000000') echo' (Giga)';
		?>
		</th>
		<tr>
		<th></th>
		<th>votre offre</th>
		<th>votre demande </th>
			<?php //Titane?>
		<tr>
			<th>Titane</th>
			<th><?php echo $offreM;?></th>
			<th><?php echo $demandeM;?></th>
		</tr>
			<?php //Carbone?>
		<tr>
			<th>Carbone</th>
			<th><?php echo $offreC;?></th>
			<th><?php echo $demandeC;?></th>
		</tr>
			<?php //Tritium?>
		<tr>
			<th>Tritium</th>
			<th><?php echo $offreD;?></th>
			<th><?php echo $demandeD;?></th>
		</tr>
	<?php if ($transporteur!="aucun") {
		$totalaressevoir=(($TotalM)+($TotalC)+($TotalD));
		$totalaenvoyer=($titane)+($carbone)+($tritium);
		if ($transporteur=="pt"){
			$transporteurenvoyer=$totalaenvoyer/5000;
			$transporteurressus=$totalaressevoir/5000;
			}
		elseif ($transporteur=="gt")
		{
			$transporteurenvoyer=$totalaenvoyer/50000;
			$transporteurressus=$totalaressevoir/50000;
		}
		//on arrondi
		$transporteurenvoyer= ceil($transporteurenvoyer);
		$transporteurressus= ceil($transporteurressus);
	?>	
		<tr><th colspan="3">Transporteur (Vaisseaux)<th></tr>
		<tr>
		<th>Nombre de transporteurs (<?php echo"$transporteur"?>)</th>
		<th><?php echo $transporteurenvoyer;?></th>
		<th><?php echo $transporteurressus;?></th><tr>
			</tr>
		
	<?php
	}
	?>
	</table>
	</td>
	<td>
<?php
// BBCode
require_once("BBcode.php");
?>
<table height="100%">
<td class="c">Offre en BBcode pour les forums</td>
<tr>
<th>
<form method='post'><textarea rows="5" cols="55" id='bbcode'><?php echo $BBcode ?></textarea></form>
</th>
</tr>
<tr>
<th>
<?php echo"<a href='#haut' onclick='selectionner()'>Selectionner</a>"; ?>
</th>
</table>
</td>
</table>

<?php
}
//pied de page
require_once("pieddepage.php");
?>