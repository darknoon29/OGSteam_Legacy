<?php
/***************************************************************************
* block_user_information_lang (Additional variables used by portal)
* @package language français
* @copyright (c) 2007 sjpphpbb http://sjpphpbb.net/phpbb3/portal.php
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
 ***************************************************************************/
 
if (empty($lang) || !is_array($lang)) 
{
	$lang = array();
}

/***************************************************************************
* DEVELOPERS PLEASE NOTE
*
* All language files should use UTF-8 as their encoding and the files must not contain a BOM.
*
* Placeholders can now contain order information, e.g. instead of
* 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
* translators to re-order the output of data while ensuring it remains correct
*
* You do not need this where single placeholders are used, e.g. 'Message %d' is fine
* equally where a string contains only two placeholders which are used to wrap text
* in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
 ***************************************************************************/

$lang = array_merge($lang, array(

		'COOPYRIGHT'				=> 'copyright - By - sjpphppbb http://sjpphpbb.net/phpbb3/',		
		'IMG_SPACER'      			=> '<img src=\'images/spacer.gif\' width=\'100%\' height=\'5\' />',	
		'LOGIN'   					=> 'Se connecter',
		'LOGIN_LOGOUT'   			=> 'Se connecter',
		'LOG_ME_IN_SHORT'   		=> 'Se rappeller de moi',
		'PASSWORD'					=> 'Mot de passe ',
		'REGISTER2'   				=> 'Vous n\avez pas de compte',	
		'REGISTER3'   				=> 'Enregistrez vous',	
		'SEARCH_NEW'				=> 'Voir les nouveaux messages',
		'SEARCH_SELF'				=> 'Voir vos messages',
		'SEARCH_UNANSWERED'			=> 'Voir les messages sans réponse',
		'USERNAME'					=> 'Pseudonyme ',
		'USER_INFO'       			=> 'Infos membre',
));

?>