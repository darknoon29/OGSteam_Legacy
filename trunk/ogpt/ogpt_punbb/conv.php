<?php
/***********************************************************************
MACHINE

************************************************************************/


define('PUN_ROOT', './');
require PUN_ROOT.'include/common.php';
/// parser pour le texte des tutos...
require PUN_ROOT.'include/parser.php';
// Load the index.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/index.php';


///ogpt
$lien="conv.php";
$page_title = "convertiseur re et rc ogame";
require PUN_ROOT.'ogpt/include/ogpt.php';
/// fin ogpt


define('PUN_ALLOW_INDEX', 1);
require PUN_ROOT.'header.php';

?>


  <div class="blockform">
	<h2><span>En developpement</a></span></h2>

    </div>




  <div class="blockform">
	<h2><span>Propuls� par ogspy/<a href="http://www.ogsteam.fr">ogsteam</a></span></h2>

    </div>




<?php

$footer_style = 'index';
require PUN_ROOT.'footer.php';