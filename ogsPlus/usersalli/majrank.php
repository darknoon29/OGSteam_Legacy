<?php

  require ('../admin/connexion.php'); 
  $quet=mysql_query("UPDATE alli SET ppts='".htmlentities($_POST['a'])."', progpts='".htmlentities($_POST['b'])."', tpts='".htmlentities($_POST['c'])."', pvaiss='".htmlentities($_POST['d'])."', progvaiss='".htmlentities($_POST['e'])."', tvaiss='".htmlentities($_POST['f'])."', prech='".htmlentities($_POST['g'])."', progrech='".htmlentities($_POST['h'])."', trech='".htmlentities($_POST['i'])."', tmpts='".htmlentities($_POST['j'])."', tmvaiss='".htmlentities($_POST['k'])."', tmrech='".htmlentities($_POST['l'])."', mbrs='".htmlentities($_POST['m'])."', uptime='".time()."' WHERE id ='$id'");
  {  
  } mysql_close();

?>
<?php echo $lng_be_aa ?>

<META http-EQUIV="Refresh" CONTENT="1; url=index.php?lng=<?php echo $lng ?>&page=galerie.php">
