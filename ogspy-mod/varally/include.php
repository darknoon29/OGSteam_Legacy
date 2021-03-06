<?php
/** $Id$ **/
/**
* display.php
* @package varAlly
* @author Aeris
* @link http://ogsteam.fr
* @version 1.0.0
 */
if (!defined('IN_SPYOGAME')) die('Hacking attempt');
/**
 *
 */
require_once('./parameters/id.php');

$sql = 'SELECT `value` FROM `'.TABLE_MOD_CFG.'` WHERE `config`=\'tblAlly\'';
$result = $db->sql_query($sql);
list($tblSpy)=$db->sql_fetch_row($result);
define('TABLE_VARALLY',$table_prefix.$tblSpy);

$sql = 'SELECT `value` FROM `'.TABLE_MOD_CFG.'` WHERE `config`=\'tblAlly\'';
$result = $db->sql_query($sql);
list($tblSpy)=$db->sql_fetch_row($result);

/**
 * AutoPub
 */ 
function page_footer() {
	
	global $db;

	//R�cup�re le num�ro de version du mod
	$request = 'SELECT `version` from `'.TABLE_MOD.'` WHERE title=\'varAlly\'';
	$result = $db->sql_query($request);
	list($version) = $db->sql_fetch_row($result);
	echo '<div>varAlly (v'.$version.') cr�� par A�ris, l�g�rement modifi� par CyberSpace</div>';
	echo '<div>Remise � jour pour OGSpy 3.0.7 - Shad</div>';
}

/**
 * Barre des boutons
 */
function button_bar() {
	global $user_auth, $user_data, $pub_subaction, $tblSpy;
?>
	<table>
		<tr align='center'>
<?php

		//Bouton Notre alliance
		if ($pub_subaction != 'ally')
		{
			echo '<td class=\'c\' width=\'150\' onclick="window.location =\'?action=varAlly&subaction=ally\'"><a style=\'cursor:pointer\'><font color=\'lime\'>Notre alliance</font></a></td>';
		} else {
			echo '<th width=\'150\'><a>Notre alliance</a></th>';
		}

		//Bouton Stats
		if ($pub_subaction != 'display')
		{
			echo '<td class=\'c\' width=\'150\' onclick="window.location =\'?action=varAlly&subaction=display\'"><a style=\'cursor:pointer\'><font color=\'lime\'>Stats</font></a></td>';
		} else {
			echo '<th width=\'150\'><a>Stats</a></th>';
		}

		//Bouton Administration
		if ($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1) if ($pub_subaction != 'admin')
		{
			echo '<td class=\'c\' width=\'150\' onclick="window.location =\'?action=varAlly&subaction=admin\'"><a style=\'cursor:pointer\'><font color=\'lime\'>Administration</font></a></td>';
		} else {
				echo '<th width=\'150\'><a>Administration</a></th>';
		}
		
?>
		</tr>
	</table>		
<?php
}

/**
 * Verification des tags et suppression des ; excedentaires
 * @param string $tag Le tag a v�rifier
 */
function check_tag ( &$tag ) {
	if (substr($tag,0,1) == ';') $tag = substr($tag,1);
	if (substr($tag,-1) == ';') $tag = substr($tag,0,strlen($tag)-1);
}

/**
 * Affiche les statistiques
 * @param string $fields Type de stats: points,fleet ou research
 * @param string $player Nom du joueur
 */
function affStats ( $field, $player ) {
	
	global $db;
	
	switch ($field) {
		case 'fleet': $table = TABLE_RANK_PLAYER_FLEET; break;
		case 'research': $table = TABLE_RANK_PLAYER_RESEARCH; break;
		default: $table = TABLE_RANK_PLAYER_POINTS; break;
	}
	
	$query = 'SELECT points FROM '. $table .' WHERE player = \''. $player .'\' ORDER BY datadate DESC LIMIT 2';
	$result = $db->sql_query($query);
	$nb = $db->sql_numrows($result);
	
	switch ($nb) {
		case 0:
			echo '<th colspan=\'2\'> - </th>';
			break;
		
		case 1:
			$val = $db->sql_fetch_assoc($result);
			echo '<th>'. formate_number($val['points']) .'</th><th>-</th>';
			break;
		
		case 2:
			$val = $db->sql_fetch_assoc($result);
			$new = $val['points'];
			$val = $db->sql_fetch_assoc($result);
			$ex = $val['points'];
			$ecart = $new - $ex;
			$pourcent = 100 * $ecart / $ex;
			
			if ($ecart < 0) {
				$color = 'red';
			}
			elseif ($ecart > 0) {
				$color = 'lime';
				$ecart = '+' . $ecart;
				$pourcent = '+'. $pourcent;
			}
			else {
				$color='';
			}
			
			echo '<th>'. formate_number($ex) .' -> '. formate_number($new) .' (<font color=\''. $color .'\'>'. sign($ecart) . formate_number($ecart) .'</font>)</th><th><font color=\''. $color .'\'>'. sign($pourcent) . formate_number($pourcent, 2) .'%</font></th>';
			break;
		
		default:
			echo '<th colspan=\'2\'> - Error - </th>';
			break;
	}
}

function affPoints ( $player, $where ) {
	
	global $db;
	
	if ($where <> '') {
		$query = 'SELECT min(datadate) AS min, max(datadate) as max FROM '. TABLE_VARALLY .' WHERE player = \''. $player .'\''. $where;
		$result = $db->sql_query($query);
		list($min, $max) = $db->sql_fetch_row($result);
		$date = ' AND (datadate=\''. $min .'\' OR datadate=\''. $max .'\')';
		$limit = '';
	}
	else {
		$date = '';
		$limit = ' LIMIT 2';
	}
	
	$query = 'SELECT points FROM '. TABLE_VARALLY .' WHERE player = \''. $player .'\''. $date .' ORDER BY datadate DESC'. $limit;
	$result = $db->sql_query($query);
	$nb = $db->sql_numrows($result);

   	switch ($nb) {
		case 0:
			echo '<th colspan=\'2\'> - </th>';
			break;
		
		case 1:
			$val = $db->sql_fetch_assoc($result);
			echo '<th>'. formate_number($val['points']) .'</th><th>-</th>';
			break;
		
		case 2:
			$val = $db->sql_fetch_assoc($result);
			$new = $val['points'];
			$val = $db->sql_fetch_assoc($result);
			$ex = $val['points'];
		    $ecart = $new - $ex;
		    $pourcent = 100 * $ecart / $ex;
		
		    global $tblecart;
		    $tblecart[] = array(
				'joueur' => $player,
				'ex'     => $ex,
				'new'    => $new,
				'pts'    => $ecart,
				'prc'    => $pourcent
			);

		    if ($ecart < 0) {
				$color = 'red';
			}
			elseif ($ecart > 0) {
				$color = 'lime';
				$ecart = '+'. $ecart;
				$pourcent = '+'. $pourcent;
			}
			else {
				$color = '';
			}
			
			echo '<th>'. formate_number($ex) .' -> '. formate_number($new) .' (<font color=\''. $color .'\'>'. sign($ecart) . formate_number($ecart) .'</font>)</th><th><font color=\''. $color .'\'>'. sign($pourcent) . formate_number($pourcent, 2) .'%</font></th>';
			break;
		
		default:
			echo '<th colspan=\'2\'> - Error - </th>';
			break;
	}
}

function sign ($n) {
	return $n < 0 ? '' : '+';
}

function parseDate ( $date ) {
	preg_match('#(\d{2})/(\d{2})/(\d{4})\s(\d{2}):(\d{2}):(\d{2})#', $date, $reg);
	return(mktime($reg[4], $reg[5], $reg[6], $reg[2], $reg[1], $reg[3]));
}
?>
