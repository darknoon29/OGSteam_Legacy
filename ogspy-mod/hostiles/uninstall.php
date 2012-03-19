<?php
/**
* uninstall.php
* @package convertisseur
* @author Mirtador
* @link http://www.ogsteam.fr
*/

if (!defined('IN_SPYOGAME')) die('Hacking attempt');

//D�finitions
global $db;
global $table_prefix;


define("TABLE_HOSTILES", $table_prefix."hostiles");
define("TABLE_HOSTILES_ATTACKS", $table_prefix."hostiles_attacks");
define("TABLE_HOSTILES_COMPOSITION", $table_prefix."hostiles_composition");


// On r�cup�re l'id du mod pour xtense...
$query = "SELECT id FROM ".TABLE_MOD." WHERE action='hostiles'";
$result = $db->sql_query($query);
list($mod_id) = $db->sql_fetch_row($result);

$mod_uninstall_name = "hostiles";
$mod_uninstall_table = TABLE_HOSTILES.', '.TABLE_HOSTILES_ATTACKS.', '.TABLE_HOSTILES_COMPOSITION;
uninstall_mod ($mod_uninstall_name, $mod_uninstall_table);

// On regarde si la table xtense_callbacks existe :
$query = 'show tables like "'.TABLE_XTENSE_CALLBACKS.'" ';
$result = $db->sql_query($query);
if($db->sql_numrows($result) != 0)
{
	//Le mod xtense 2 est install� !
	
	//Maintenant on regarde si eXchange est dedans normalement oui mais on est jamais trop prudent...
	$query = 'Select * From '.TABLE_XTENSE_CALLBACKS.' where mod_id = '.$mod_id.' ';
	$result = $db->sql_query($query);
	if($db->sql_numrows($result) != 0)
	{
		// Il est  dedans alors on l'enl�ve :
		$query = 'DELETE FROM '.TABLE_XTENSE_CALLBACKS.' where mod_id = '.$mod_id.' ';
		$db->sql_query($query);
		echo("<script> alert('La compatibilit� du mod Commerce avec le mod Xtense2 a �t� d�sinstall�e !') </script>");
	}
}

?>
