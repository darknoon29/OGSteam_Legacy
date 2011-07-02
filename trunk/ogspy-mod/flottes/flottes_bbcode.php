<?php
/***************************************************************************
*   filename	: flottes_bbcode.php
*	desc.       : 1.06
*	Author      : 
*	created		: 
*	modified	: AirBAT
*   last modif. : correct Ion and Gauss  
***************************************************************************/

if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}

// def de qq variable
//define("TABLE_MOD_FLOTTES", $table_prefix."mod_flottes");

// insertion des fonctions flotte
require_once("./mod/flottes/function_flottes.php");
require_once("./mod/flottes/flottes_inc.php");
require_once("./mod/flottes/flottes_lang.php");


global $CF,$CFO,$CFU,$CFP,$CFA,$CB1,$CB2,$CB3,$CB4,$CB5,$GA;

buttons_bar($pub_subaction);

global $nbj;

// total points
		$total=0;
		$i=1;
		$request = "SELECT ";
	
		foreach($mod_flottes_lang as $key => $value) {
			if (($key!="planet_name")&($key!="coordinates")&($key!="planet_id")){
				$request .= "(SUM(".$key.") * ".$vaisseaux[$i][1]." / 1000 ) + ";
				$i++;}
		}
		
		$request = substr($request, 0, strlen($request)-2);
		$request .= " as tot FROM ".TABLE_MOD_FLOTTES." WHERE user_id=".$user_data['user_id'];
		
		if ($result = $db->sql_query($request)){
			$total = mysql_fetch_row($result);
			if ($total[0]==''){$total=0;}
			else {$total=$total[0];}}

/////////////////////////////////////////////////////////
// Total flotte joueur

	$request = "SELECT ";
	foreach($mod_flottes_lang as $key => $value) {
		if (($key!="planet_name")&($key!="coordinates")&($key!="planet_id")){
			$request .= "SUM(".$key.") as ".$key.", ";}
	}
	$request = substr($request, 0, strlen($request)-2);
	$request .= " FROM ".TABLE_MOD_FLOTTES." WHERE user_id=".$user_data['user_id'];

/////////////////////////////////////////////////
if($result = $db->sql_query($request)) {
	while($ligne = mysql_fetch_row($result)) {
		$PT = $ligne[0]; 	
		$GT = $ligne[1]; 
		$CLE = $ligne[2];
		$CLO = $ligne[3]; 	
		$CR = $ligne[4]; 
		$VB = $ligne[5];
		$VC = $ligne[6]; 	
		$REC = $ligne[7]; 
		$SE = $ligne[8];
		$BMD = $ligne[9]; 	
		$DST = $ligne[10]; 
		$EDLM = $ligne[11];
		$TRA = $ligne[12];
		$sat = $ligne[13];
	}
/*	$sat=0;
	for ($i=1 ; $i<=18 ; $i++) {
	$sat += $user_building[$i]["Sat"];
	}*/
	$mic=0;
	for ($i=1 ; $i<=18 ; $i++) {
	$mic += $user_defence[$i][$mic_lang];
	}
	$mip=0;
	for ($i=1 ; $i<=18 ; $i++) {
	$mip += $user_defence[$i][$mip_lang];
	}
	
	$pb=0;
	for ($i=1 ; $i<=18 ; $i++) {
	$pb += $user_defence[$i][$pb_lang];
	}
	$gb=0;
	for ($i=1 ; $i<=18 ; $i++) {
	$gb += $user_defence[$i][$gb_lang];
	}
	$lm=0;
	for ($i=1 ; $i<=18 ; $i++) {
	$lm += $user_defence[$i][$lm_lang];
	}
	$lle=0;
	for ($i=1 ; $i<=18 ; $i++) {
	$lle += $user_defence[$i][$lle_lang];
	}
	$llo=0;
	for ($i=1 ; $i<=18 ; $i++) {
	$llo += $user_defence[$i][$llo_lang];
	}
	$cg=0;
	for ($i=1 ; $i<=18 ; $i++) {
	$cg += $user_defence[$i][$cg_lang];
	}
	$ai=0;
	for ($i=1 ; $i<=18 ; $i++) {
	$ai += $user_defence[$i][$ai_lang];
	}
	$lp=0;
	for ($i=1 ; $i<=18 ; $i++) {
	$lp += $user_defence[$i][$lp_lang];
	}
	 
////// Nom du joueur In Game
	$joueur=$user_data['user_stat_name'];
//// heure locale
	setlocale(LC_TIME, 'fr', 'fr_FR', 'fr_FR.ISO8859-1');
	$date=strftime("%A %d %B %Y.");

/////// si le nom du joueur est pas null alors on va chercher ses statistiques flottes	
	if ($joueur!=''){
////// derni�re date de Stat
		$out="";
		$dated=1;
		$fp=0;
		$fr=0;
		$query = "SELECT max(datadate) FROM ".TABLE_RANK_PLAYER_FLEET." WHERE player='".$joueur."'";
		if ($result = $db->sql_query($query)){
			if ( $val = mysql_fetch_row($result) ){
				$dated=$val[0];
			}
			else {
				$out="Pas de lecture date";
			}
		}
		else {
			$out="erreur base date";
		}
		if ($dated==''){
			$dated=$date+1;
		}
		$lastmodified = date('d/M/Y H:i',$dated);
		

//	echo "dated :".$dated." - ".$lastmodified;

		if (isset($dated)){
			$query  = "SELECT rank, points  FROM ".TABLE_RANK_PLAYER_FLEET." WHERE datadate=".$dated." AND player='".$joueur."'";
			if ($result = $db->sql_query($query)){	
				if ( $val = mysql_fetch_row($result) ){
					$fr = $val[0];  // le classement flotte
//					$fp = $val[1];   // les points flotte
				}
				else {
					$out.=" - Pas de lecture stat ".$dated;
					$fr=0;
				}
			}
			else {
				$out=" - erreur base stat";
				$fr=0;
			}
		}
		else {
			$out.=" - Pas de lecture stat dated";
			$fr=0;
		}
//		echo $out;
	}
	else {
		$joueur=$user_data['user_name'];
		$lastmodified=$date;
		$dated=0;
		$fr=0;
	}

// fin total joueur
//////////////////////////////////////////////////
		

 		$header = '[center][color='.$CB1.'][b][i][u]Flotte:[/color][/u][/i][/b]';
		$footer = '[color='.$CB3.']Statut de la flotte de [/color][color='.$CB2.']'.$joueur.'[/color][color='.$CB3.'] r�alis� par le Mod Flottes d\''.$GA.' le '.$date.'[/color][/i][/center]';
		
		$conv  = $header."\n";
		$conv .= "\n";
		if ($fr!=0){
		$conv .= '[i][color='.$CB1.']Classement par Vaisseaux: [/color][color='.$CB2.']'.$fr.' [/color][color='.$CB1.']�me au [/color][color='.$CB2.']'.$lastmodified.' [/color][color='.$CB1.']heure[/color][color='.$CB3.'] [/color][/i]'."\n";
		$conv .= "\n";}
		else {
		$conv .= '[i][color='.$CB1.']Joueur non class� ou classement par Vaisseaux inexistant[/color][color='.$CB3.'] [/color][/i]'."\n";
		$conv .= "\n";}
		$conv .= '[b][color='.$CB4.']Vaisseaux de Guerre[/color][/b]'."\n";
		$conv .= "\n";
		$conv .= '[i][color='.$CB2.']'.$CLE.'[/color] [color='.$CB5.']'.$mod_flottes_lang["CLE"].'[/color]'."\n";
		$conv .= '[color='.$CB2.']'.$CLO.'[/color] [color='.$CB5.']'.$mod_flottes_lang["CLO"].'[/color]'."\n";
		$conv .= '[color='.$CB2.']'.$CR.'[/color] [color='.$CB5.']'.$mod_flottes_lang["CR"].'[/color]'."\n";
		$conv .= '[color='.$CB2.']'.$VB.'[/color] [color='.$CB5.']'.$mod_flottes_lang["VB"].'[/color]'."\n";
		$conv .= '[color='.$CB2.']'.$BMD.'[/color] [color='.$CB5.']'.$mod_flottes_lang["BMD"].'[/color]'."\n";
		$conv .= '[color='.$CB2.']'.$DST.'[/color] [color='.$CB5.']'.$mod_flottes_lang["DST"].'[/color]'."\n";
		$conv .= '[color='.$CB2.']'.$EDLM.'[/color] [color='.$CB5.']'.$mod_flottes_lang["EDLM"].'[/color]'."\n";
		if ($GA=='OGAME') {
		$conv .= '[color='.$CB2.']'.$TRA.'[/color] [color='.$CB5.']'.$mod_flottes_lang["TRA"].'[/color][/i]'."\n";
		}
		$conv .= "\n";
		$conv .= '[b][color='.$CB4.']Vaisseaux de Transport[/color][/b]'."\n";
		$conv .= "\n";
		$conv .= '[i][color='.$CB2.']'.$PT.'[/color] [color='.$CB5.']'.$mod_flottes_lang["PT"].'[/color]'."\n";
		$conv .= '[color='.$CB2.']'.$GT.'[/color] [color='.$CB5.']'.$mod_flottes_lang["GT"].'[/color]'."\n";
		$conv .= '[color='.$CB2.']'.$REC.'[/color] [color='.$CB5.']'.$mod_flottes_lang["REC"].'[/color][/i]'."\n";
		$conv .= "\n";
		$conv .= '[b][color='.$CB4.']Vaisseaux divers[/color][/b]'."\n";
		$conv .= "\n";
		$conv .= '[i][color='.$CB2.']'.$VC.'[/color] [color='.$CB5.']'.$mod_flottes_lang["VC"].'[/color]'."\n";
		$conv .= '[color='.$CB2.']'.$SE.'[/color] [color='.$CB5.']'.$mod_flottes_lang["SE"].'[/color]'."\n";
		$conv .= '[color='.$CB2.']'.$sat.'[/color] [color='.$CB5.']'.$mod_flottes_lang["SAT"].'[/color][/i]'."\n";
		if ($GA=='UNIVERS') {
		$conv .= '[color='.$CB2.']'.$TRA.'[/color] [color='.$CB5.']'.$mod_flottes_lang["TRA"].'[/color][/i]'."\n";
		}
		$conv .= "\n";
		$conv .= '[b][color='.$CB4.']Missiles[/color][/b]'."\n";
		$conv .= "\n";
		$conv .= '[i][color='.$CB2.']'.$mic.'[/color] [color='.$CB5.']'.$LANG["ogame_AntiBallisticMissiles"].'[/color]'."\n";
		$conv .= '[color='.$CB2.']'.$mip.'[/color] [color='.$CB5.']'.$LANG["ogame_InterplanetaryMissiles"].'[/color][/i]'."\n";
		$conv .= "\n";
		$conv .= '[b][color='.$CB4.']D�fenses[/color][/b]'."\n";
		$conv .= "\n";
		$conv .= '[i][color='.$CB2.']'.$lm.'[/color] [color='.$CB5.']'.$LANG["ogame_RocketLauncher"].'[/color]'."\n";
		$conv .= '[color='.$CB2.']'.$lle.'[/color] [color='.$CB5.']'.$LANG["ogame_LightLaser"].'[/color]'."\n";
		$conv .= '[color='.$CB2.']'.$llo.'[/color] [color='.$CB5.']'.$LANG["ogame_HeavyLaser"].'[/color]'."\n";
		$conv .= '[color='.$CB2.']'.$ai.'[/color] [color='.$CB5.']'.$LANG["ogame_IonCannon"].'[/color]'."\n";
		$conv .= '[color='.$CB2.']'.$cg.'[/color] [color='.$CB5.']'.$LANG["ogame_GaussCannon"].'[/color]'."\n";
		$conv .= '[color='.$CB2.']'.$lp.'[/color] [color='.$CB5.']'.$LANG["ogame_PlasmaTuret"].'[/color]'."\n";
		$conv .= '[color='.$CB2.']'.$pb.'[/color] [color='.$CB5.']'.$LANG["ogame_SmallShield"].'[/color]'."\n";
		$conv .= '[color='.$CB2.']'.$gb.'[/color] [color='.$CB5.']'.$LANG["ogame_LargeShield"].'[/color][/i]'."\n";
		$conv .= "\n";
		$conv .= '[color='.$CB1.']Soit un total de [/color][color='.$CB2.']'.number_format($total, 0, ',', ' ').'[/color][color='.$CB1.'] points ou [/color][color='.$CB2.']'.number_format($total*1000, 0, ',', ' ').'[/color][color='.$CB1.'] ressources investies dans les vaisseaux[/color].'."\n";
		$conv .= "\n";
     	$conv .= $footer;

//		echo 'Rapport converti<br><textarea rows=\'10\' cols=\'10\'>'.$conv.'</textarea>';
?>
	<form action='' method='POST' name='flottes'>
	<input type='hidden' name='action' value='flottes' >
	<input type='hidden' name='permit' value='change' >
		<table width="100%">
			<tr align="center">
<!--				<td colspan='18'></td>   -->


				
				<td class='c' colspan='18'><b>Export BBCode</b></td><br>
			</tr>

			<tr align="center">
				<th colspan='18'>
<?php
					echo "<textarea name='flottes_conv' rows='30' id='flottes_conv' >".$conv."</textarea><br>";
?>

					<div style="background-color : transparent; width: 49%; text-align : center; float: left;">
					<input type="button" name="apercu" onClick="preview()" title="Aper&ccedil;u (ne fonctionne pas bien avec LDU)" value="Aper&ccedil;u"></div>
					<td></td>

				</th>
			</tr>

<!-- fin BBcode  -->

		</table>
	</form>


<div style="border: 1px ridge white; padding: 5px; overflow: auto; visibility: hidden; position: absolute; width: 470px; height: 960px; top: 10px; left: 50%; margin-left: -235px; background-color: #000000;" id="message">
	<table width="100%">
	<tbody>
		<tr>
		<td><b id="note0">Aper&ccedil;u</b></td>
		<td align="right"><input type="button" name="fermer" onClick="closeMessage()" value="Fermer"></td>
		</tr>
	</tbody>
	</table>
	<div id="preview" style="background-color: #2e2e2e; padding: 10px; font-size : 12px; text-align: left;"> </div>
	<table width="100%">
	<tbody>
		<tr>
		<td align="left">Thanks to <a href="http://www.takanacity.com/" title="website of Takana's OGame Tools">Takana's Team</a> for this preview !</td>
		<td align="right"><input type="button" name="fermer" onClick="closeMessage()" value="Fermer"></td>
		</tr>
	</tbody>
	</table>
</div>

<?php
}	
?>
