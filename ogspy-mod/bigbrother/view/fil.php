<?php
if (!defined('IN_SPYOGAME'))
    die("Hacking attempt");
if (!isset($pub_limit) || !is_numeric($pub_limit)) {
    $pub_limit = 25;
} else {
    $pub_limit = (int)$pub_limit;
}


$requete = "select * from " . TABLE_STORY_PLAYER . " ";
$requete .= " ORDER BY datadate DESC ";
$requete .= "  LIMIT 0," . $pub_limit . " ";


$affichage .= '<br /><table>';
$affichage .= '<tr>';
$affichage .= '<td colspan="4" class="c">Fil d information</td>';
$affichage .= '	</tr>';
$affichage .= '<tr>';
$affichage .= '<td width="150" class="c">date</td>';
$affichage .= '<td width="250" class="c">Nom</td>';
$affichage .= '<td width="250" class="c">status</td>';
$affichage .= '<td width="250" class="c">alliance</td>';

$affichage .= '	</tr>';

$result = $db->sql_query($requete);


while ($row = $db->sql_fetch_assoc($result)) {
    $add = "";
    $affichage .= '<tr>';
    $affichage .= '<td  class="b">' . strftime("%d %b %Y %H:%M:%S", $row['datadate']) .
        '</td>';
    if ($row['name_player'] == $cache_player[$row['id_player']]['name_player']) {
        $add = "";
    } else {
        $add = " (Actuellement : '" . $cache_player[$row['id_player']]['name_player'] .
            "') ";
    }
    $affichage .= '<td  class="b"><a href="index.php?action=bigbrother&subaction=player&id=' .
        $row['id_player'] . '">' . $row['name_player'] . '</a> ' . $add . ' </td>';
    if ($row['status'] == $cache_player[$row['id_player']]['status']) {
        $add = "";
    } else {
        $add = " (Actuellement : '" . $cache_player[$row['id_player']]['status'] . "') ";
    }
    $affichage .= '<td  class="b">' . convert_status($row['status']) . $add .
        '</td>';
    if ($row['id_ally'] == $cache_player[$row['id_player']]['id_ally']) {
        $add = "";
    } else {
        $add = " (Actuellement : '" . convert_ally($cache_player[$row['id_player']]['id_ally']) .
            "') ";
    }
    $affichage .= '<td  class="b">' . convert_ally($row['id_ally']) . $add . '</td>';
    $affichage .= '	</tr>';

}
$affichage .= '</table>';


echo $affichage;

?>
<br />
<table width="25%">
		<form action="index.php?action=bigbrother&subaction=fil" method="POST">
        <tr>
          <td colspan="2" class="c">Rechercher</td>
          </tr>
  		<tr>
			<th>Nombre de r�ponse :</th>
			<th><input type="text" value="<?php echo $pub_limit; ?>" size="5" maxlength="5" name="limit"></th>
                    
		</tr>

		
		<tr>
			<th colspan="2"><input type="submit" value="Chercher"></th>
		</tr>
			</form>
		</tbody></table>
