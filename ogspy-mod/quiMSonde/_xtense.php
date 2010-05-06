<?php
/**
 * qms_plugin.php 

Script d'interconnexion avec la barre d'outils Xtense v2.

 * @package QuiMSonde
 * @author Sylar
 * @link http://www.ogsteam.fr
 * @version : 1.4b
 * derni�re modification : 08.04.08
 * Largement inspir� du formidable mod QuiMObserve de Santory
 */
if (!defined('IN_SPYOGAME')) die("Hacking attempt");
#
if(class_exists("Callback")){
class QuiMSonde_Callback extends Callback {
        public $version = '2.0b8';
        public function qms_import_enemy_spy($ennemy_spy){
			global $io,$user;
			require_once('lang/lang_french.php');
			$add = 0;
			foreach($ennemy_spy as $spy){
				if(!is_array($spy['from'])){
					$pos_from = explode(':',$spy['from']); 
					$pos_to = explode(':',$spy['to']); 
					$coords_from = $spy['from'];
					$coords_to = $spy['to'];
				}else{
					$pos_from = $spy['from']; 
					$pos_to = $spy['from']; 
					$coords_from = implode(':',$spy['from']);
					$coords_to = implode(':',$spy['to']);
				}
				if(isset($spy['data'])){
					$test = preg_match($lang['regex_xtense2_coord'],$spy['data'],$position);
					$from_name = isset($position[1])?trim($position[1]):"";
					$to_name = isset($position[2])?trim($position[2]):"";
				}else{
					$from_name = $to_name = "";
				}
				$distance = qms_get_distance($pos_from,$pos_to);
				$user_info = qms_get_user_info($pos_from);
				$date = date("m-d H:i:s",$spy['time']);
				$a = qms_add_spy($user['id'], $coords_from, $user_info[0], $user_info[1], $distance,$coords_to, $spy['time'], $spy['proba'], $from_name, $to_name);
				if($a==0)
					$io->append_call_message("L'espionnage de {$coords_to} ({$to_name}) du {$date} existe d�j�.", Io::WARNING);
				else
					$io->append_call_message("L'espionnage de {$coords_to} ({$to_name}) du {$date} a bien �t� enregistr�.", Io::SUCCESS);
				$add += $a;
			}
			$io->append_call_message("Un total de {$add} espionnages ont �t� enregistr�s", Io::SUCCESS);
			return Io::SUCCESS;
		}
        public function getCallbacks() {
                return array(
                        array(
                                'function' => 'qms_import_enemy_spy',
                                'type' => 'ennemy_spy'
                        ),
                );
       }
}
}
// Routine d'Installation
$xtense_version = "2.0b4";

// Pour la future 2.0b5... En attendant de savoir comment ca va tourner...
function QuiMSonde_get_callbacks() {
	return array(array( 'function'=>'qms_import_enemy_spy', 'type'=>'ennemy_spy', 'active'=>1 ));
}

if(defined('INSTALL_MOD_NAME')){
//	if(file_exists($a="mod/Xtense2/includes/functions.php")) {
//		include($a);
		// Sinon, si c'est un module plus ancien, on applique l'ancienne m�thode
		global $db,$table_prefix;
		define("TABLE_XTENSE_CALLBACKS", $table_prefix."xtense_callbacks");
//		if(function_exists('install_callbacks')){
			// Module XTense v2.0b5 d�tect�
//			install_callbacks(QuiMSonde_get_callbacks());
//		}else{
			// Quel est l'ID du mod ?
			$result = $db->sql_query("SELECT id FROM ".TABLE_MOD." WHERE action='".INSTALL_MOD_NAME."'");
			list($mod_id) = $db->sql_fetch_row($result);
			// On regarde si la table xtense_callbacks existe :
			$result = $db->sql_query('show tables from `'.$db->dbname.'` like "'.TABLE_XTENSE_CALLBACKS.'" ');
			if($db->sql_numrows($result) != 0){
				//Maintenant on regarde si QuiMSonde est dedans :
				$result = $db->sql_query("Select * From ".TABLE_XTENSE_CALLBACKS." where mod_id = $mod_id");
				// s'il n'y est pas : alors on l'ajoute!
				if($db->sql_numrows($result) == 0)
					$db->sql_query("INSERT INTO ".TABLE_XTENSE_CALLBACKS." (mod_id, function, type, active) VALUES ('$mod_id', 'qms_import_enemy_spy', 'ennemy_spy', 1)");
			}
//		}
//	}
}

// Definition
global $table_prefix;
if(!defined('TABLE_QMS')){
	define("TABLE_QMS", $table_prefix."QuiMeSonde");
	define("TABLE_QMS_config", $table_prefix."QuiMeSonde_config");
}

// Importation d'un espionnage depuis xtense2
function qms_import_enemy_spy($enemy_spy){
	global $user;
	// Nombre d'espionnage renvoy� par la barre
	$nb_spy = count($enemy_spy);
	$add=0;
	for($i=0;$i<$nb_spy;$i++){
		if(!is_array($enemy_spy[$i]['from']))
		{
	preg_match("#(.*):(.*):(.*)#",$enemy_spy[$i]['from'],$from);
	preg_match("#(.*):(.*):(.*)#",$enemy_spy[$i]['to'],$to);
	$pos_from = array($from[1],$from[2],$from[3]); 
	$pos_to = array($to[1],$to[2],$to[3]); 
			$distance = qms_get_distance(array($from[1],$from[2],$from[3]),array($to[1],$to[2],$to[3]));
			$user_info = qms_get_user_info(array($from[1],$from[2],$from[3]));
			$add += qms_add_spy(
				$user['id'], $enemy_spy[$i]['from'], $user_info[0], $user_info[1], $distance, 
				$enemy_spy[$i]['to'], $enemy_spy[$i]['time'], $enemy_spy[$i]['proba']
				);
		}
		else
		{
			$distance = qms_get_distance($enemy_spy[$i]['from'],$enemy_spy[$i]['to']);
			$user_info = qms_get_user_info($enemy_spy[$i]['from']);
			$from_name = isset($enemy_spy[$i]['from_name'])?$enemy_spy[$i]['from_name']:"";
			$to_name = isset($enemy_spy[$i]['to_name'])?$enemy_spy[$i]['to_name']:"";
			$add += qms_add_spy(
				$user['id'], $enemy_spy[$i]['from'][0].":".$enemy_spy[$i]['from'][1].":".$enemy_spy[$i]['from'][2], 
				$user_info[0], $user_info[1], $distance, $enemy_spy[$i]['to'][0].":".$enemy_spy[$i]['to'][1].":".$enemy_spy[$i]['to'][2],
				$enemy_spy[$i]['time'], $enemy_spy[$i]['proba'], $from_name, $to_name
				);
		}
	}
	if($add==count($enemy_spy))
		$return = (bool) True;
	else
		$return = (bool) False;
	return ($return);
}

function qms_add_spy($sender,$from,$name,$alliance,$distance,$to,$time,$proba,$from_name="",$to_name=""){
	global $sql;
	if (!$sql->check("SELECT id FROM ".TABLE_QMS." WHERE sender_id='$sender' AND datadate='$time' AND position='$from'")) 
	{
		$query = "INSERT INTO ".TABLE_QMS." ( `id`, `sender_id`, `position`, `position_name`, `joueur`, `alliance`, `distance`, `cible`, `cible_name`, `datadate`,  `pourcentage` ) ";
		$query .= "VALUES ( NULL, '$sender', '$from', '$from_name', '$name', '$alliance', '$distance', '$to', '$to_name', '$time', '$proba' )";
		$sql->query($query);
	} else return 0;
	return 1;
}

function qms_get_distance($c_dep,$c_arr){			// Renvoi la distance entre 2 plan�tes
	if($c_dep[0]==$c_arr[0])							// De la meme galaxie
		if($c_dep[1]==$c_arr[1])						// Du meme syst�me
			$dist=1000+abs($c_dep[2]-$c_arr[2])*5;		// Distance entre 2 plan�tes d'un meme syst�mes.
		else											// Pas du m�me syst�me
			$dist=2700+abs($c_dep[1]-$c_arr[1])*95;		// Distance entre 2 syst�mes.
	else												// Pas la m�me galaxie.
		$dist=abs($c_dep[0]-$c_arr[0])*20000;			// Distance entre 2 Galaxie.
	return $dist;
}
function qms_get_user_info($coord){	// recup�re le nom du joueur et l'alliance d'une certaine position
	global $sql;	
	list($player,$ally) = Array ("?","");	
	$query = "SELECT  `player` , `ally`  FROM `".TABLE_UNIVERSE."` WHERE `galaxy` = ".$coord[0]." and `system` = ".$coord[1]." and `row` = ".$coord[2];
	if($coord[0]&&$coord[1]&&$coord[2]&&$sql->check($query)){
		$result=$sql->query($query);		
		$out = $sql->assoc();
		$player = $out[0]['player'];
		$ally = $out[0]['ally'];
	}
	return array($player,$ally);
}

?>