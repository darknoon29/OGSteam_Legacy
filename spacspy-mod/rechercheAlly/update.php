<?php

/**
* Mise � jour du module
* @package rechercheAlly
* @author Aeris
* @link http://www.ogsteam.fr
 */

if (file_exists('mod/'.$root.'/version.txt')) {
	$file = file('mod/'.$root.'/version.txt');
	
	$db->sql_query('UPDATE '.TABLE_MOD.' SET version = "'.trim($file[1]).'" WHERE id = "'.$pub_mod_id.'"');
}

?>