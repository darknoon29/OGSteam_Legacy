<?php
/**
 * ogs.php 
 * Fonctions concernant le protocole de communication ogstratege / ogspy
 * 
 * @author Kyser
 * @package OGSpy
 * @subpackage includes
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @modified $Date: 2012-07-09 16:44:26 +0200 (lun., 09 juil. 2012) $
 * @author Kyser
 * @link $HeadURL: http://svn.ogsteam.fr/trunk/ogspy/includes/ogs.php $
 * @version 3.1.0 ( $Rev: 7665 $ ) 
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
    
    }
/**
 * Envoi des données vers OGS
 */
function galaxy_ExportPlanets()
{
    global $db, $user_data, $server_config, $user_auth;
    global $pub_galnum, $pub_sincedate;

    galaxy_check_auth("export_planet");

    $request_galaxy = "";
    $request_date = "";
    if (isset($pub_galnum)) {
        if (!check_var($pub_galnum, "Num")) {
            die("<!-- [ErrorFatal=01] Données transmises incorrectes  -->");
        }
        if ($pub_galnum != "") {
            log_("get_system_OGS", intval($pub_galnum));
            $request_galaxy = " where galaxy = " . intval($pub_galnum);
        } else
            log_("get_system_OGS");
    } else
        log_("get_system_OGS");


    if (isset($pub_sincedate)) {
        if (!check_var($pub_sincedate, "Special", "#^(\d){4}-(\d){2}-(\d){2}\s(\d){2}:(\d){2}:(\d){2}$#")) {
            die("<!-- [ErrorFatal=02] Données transmises incorrectes  -->");
        }
        if ($pub_sincedate != "") {
            list($day, $hour) = explode(" ", $pub_sincedate);
            list($year, $month, $day) = explode("-", $day);
            list($hour, $minute, $seconde) = explode(":", $hour);
            $timestamp = mktime($hour, $minute, $seconde, $month, $day, $year);
            $request_date = isset($request_galaxy) ? " and " : " where ";
            $request_date .= "last_update >= " . $timestamp;
        }
    }

    $ally_protection = array();
    if ($server_config["ally_protection"] != "")
        $ally_protection = explode(",", $server_config["ally_protection"]);

    $request = "select galaxy, system, row, moon, name, ally, player, status, user_name, last_update" .
        " from " . TABLE_UNIVERSE . " left join " . TABLE_USER .
        " on last_update_user_id = user_id" . $request_galaxy . $request_date .
        " order by galaxy, system, row";

    $result = $db->sql_query($request);

    $i = 0;
    echo "galaxy=1,system=2,row=3,moon=4,planetname=5,playername=6,allytag=7,status=8,datetime=9,sendername=10<->";
    while (list($galaxy, $system, $row, $moon, $name, $ally, $player, $status, $user_name,
        $last_update) = $db->sql_fetch_row($result)) {
        if ($name == "")
            $name = " ";
        if ($ally == "")
            $ally = " ";
        if ($player == "")
            $player = " ";
        if (is_null($user_name))
            $user_name = "Inconnu";

        if (!in_array($ally, $ally_protection) || $ally == "" || $user_auth["server_show_positionhided"] ==
            1 || $user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
            $texte = $galaxy . "<||>";
            $texte .= $system . "<||>";
            $texte .= $row . "<||>";
            $texte .= ($moon == 1) ? "M" : "";
            $texte .= "<||>";

            $texte .= utf8_decode($name) . "<||>";
            $texte .= utf8_decode($player) . "<||>";
            $texte .= utf8_decode($ally) . "<||>";
            $texte .= $status . "<||>";
            $texte .= date("Y-m-d H:i:s", $last_update) . "<||>";
            $texte .= utf8_decode($user_name);
            $texte .= "<->";

            echo $texte;
            $i++;
        }
    }

    user_set_stat(null, null, null, null, null, null, null, $i);

    //Statistiques serveur
    $db->sql_query($request);
    /*//Incompatible MySQL 4.0
    $request = "insert into ".TABLE_STATISTIC." values ('planetexport_ogs', '".$i."')";
    $request .= " on duplicate key update statistic_value = statistic_value + ".$i."";*/
    $request = "update " . TABLE_STATISTIC .
        " set statistic_value = statistic_value + " . $i;
    $request .= " where statistic_name = 'planetexport_ogs'";
    $db->sql_query($request);
    if ($db->sql_affectedrows() == 0) {
        $request = "insert ignore into " . TABLE_STATISTIC .
            " values ('planetexport_ogs', '" . $i . "')";
        $db->sql_query($request);
    }

    //Statistiques joueur

    exit();
}
    
    
    
/**
 * Envoi du classement alliance vers OGS
 */
function galaxy_ExportRanking_ally()
{
    global $db, $user_data;
    global $pub_date, $pub_type;

    galaxy_check_auth("export_ranking");

    if (!isset($pub_date) || !isset($pub_type)) {
        die("<!-- [ErrorFatal=03] Données transmises incorrectes  -->");
    }

    switch ($pub_type) {
        case "points":
            $ranktable = TABLE_RANK_ALLY_POINTS;
            break;
        case "flotte":
            $ranktable = TABLE_RANK_ALLY_FLEET;
            break;
        case "research":
            $ranktable = TABLE_RANK_ALLY_RESEARCH;
            break;
        default:
            die("<!-- [ErrorFatal=04] Données transmises incorrectes  -->");
            ;
    }

    if (!check_var($pub_date, "Special", "#^(\d){4}-(\d){2}-(\d){2}\s(\d){2}:(\d){2}:(\d){2}$#")) {
        die("<!-- [ErrorFatal=04] Données transmises incorrectes  -->");
    }

    list($day, $hour) = explode(" ", $pub_date);
    list($year, $month, $day) = explode("-", $day);
    list($hour, $minute, $seconde) = explode(":", $hour);
    $timestamp = mktime($hour, 0, 0, $month, $day, $year);

    $request = "select rank, ally, number_member, points, points_per_member, user_name";
    $request .= " from " . $ranktable . " left join " . TABLE_USER;
    $request .= " on sender_id = user_id";
    $request .= " where datadate = " . $timestamp;
    $request .= " order by rank";
    $result = $db->sql_query($request);

    $i = 0;
    echo "rank=1,allytag=2,number_member=3,points=4,points_per_member=5,sendername=6,datetime=" .
        $pub_date . "<->";
    while (list($rank, $ally, $number_member, $points, $points_per_member, $user_name) =
        $db->sql_fetch_row($result)) {
        $texte .= $rank . "<||>";
        $texte .= utf8_decode($ally) . "<||>";
        $texte .= $number_member . "<||>";
        $texte .= $points . "<||>";
        $texte .= $points_per_member . "<||>";
        $texte .= utf8_decode($user_name) . "<||>";
        $texte .= "<->";
        echo $texte;

        $i++;
    }

    user_set_stat(null, null, null, null, null, null, null, null, null, $i);

    log_("get_rank", array($pub_type, $timestamp));

    //Statistiques serveur
    $request = "update " . TABLE_STATISTIC .
        " set statistic_value = statistic_value + " . $i;
    $request .= " where statistic_name = 'rankexport_ogs'";
    $db->sql_query($request);
    if ($db->sql_affectedrows() == 0) {
        $request = "insert ignore into " . TABLE_STATISTIC .
            " values ('rankexport_ogs', '" . $i . "')";
        $db->sql_query($request);
    }

    exit();
}    
    
    
    
    /**
 * Envoi du classement joueur vers OGS
 */
function galaxy_ExportRanking_player()
{
    global $db, $user_data;
    global $pub_date, $pub_type;

    galaxy_check_auth("export_ranking");

    if (!isset($pub_date) || !isset($pub_type)) {
        die("<!-- [ErrorFatal=05] Données transmises incorrectes  -->");
    }

    switch ($pub_type) {
        case "points":
            $ranktable = TABLE_RANK_PLAYER_POINTS;
            break;
        case "flotte":
            $ranktable = TABLE_RANK_PLAYER_FLEET;
            break;
        case "research":
            $ranktable = TABLE_RANK_PLAYER_RESEARCH;
            break;
        default:
            die("<!-- [ErrorFatal=06] Données transmises incorrectes  -->");
            ;
    }

    if (!check_var($pub_date, "Special", "#^(\d){4}-(\d){2}-(\d){2}\s(\d){2}:(\d){2}:(\d){2}$#")) {
        die("<!-- [ErrorFatal=07] Données transmises incorrectes  -->");
    }

    list($day, $hour) = explode(" ", $pub_date);
    list($year, $month, $day) = explode("-", $day);
    list($hour, $minute, $seconde) = explode(":", $hour);
    $timestamp = mktime($hour, 0, 0, $month, $day, $year);

    $request = "select rank, player, ally, points, user_name";
    $request .= " from " . $ranktable . " left join " . TABLE_USER;
    $request .= " on sender_id = user_id";
    $request .= " where datadate = " . $timestamp;
    $request .= " order by rank";
    $result = $db->sql_query($request);

    $i = 0;
    echo "playername=1,allytag=2,rank=3,points=4,sendername=5,datetime=" . $pub_date .
        "<->";
    while (list($rank, $player, $ally, $points, $user_name) = $db->sql_fetch_row($result)) {
        $texte = utf8_decode($player) . "<||>";
        $texte .= utf8_decode($ally) . "<||>";
        $texte .= $rank . "<||>";
        $texte .= $points . "<||>";
        $texte .= utf8_decode($user_name) . "<||>";
        $texte .= "<->";
        echo $texte;

        $i++;
    }

    user_set_stat(null, null, null, null, null, null, null, null, null, $i);

    log_("get_rank", array($pub_type, $timestamp));

    //Statistiques serveur
    $request = "update " . TABLE_STATISTIC .
        " set statistic_value = statistic_value + " . $i;
    $request .= " where statistic_name = 'rankexport_ogs'";
    $db->sql_query($request);
    if ($db->sql_affectedrows() == 0) {
        $request = "insert ignore into " . TABLE_STATISTIC .
            " values ('rankexport_ogs', '" . $i . "')";
        $db->sql_query($request);
    }

    exit();
}



/**
 * Récupération des données transmises par OGS
 */
function galaxy_ImportPlanets()
{
    global $db, $user_data, $server_config;
    global $pub_data;

    galaxy_check_auth("import_planet");

    $files = array();
    $start = benchmark();

    //		$test_ogs = file("test/Exp_Imp-planet.txt");
    //		$test_ogs = implode($test_ogs);
    //		$datas = explode("<->", $test_ogs);
    $datas = explode("<->", $pub_data);
    $files = $datas;

    $totalplanet = 0;
    $totalupdated = 0;
    $totalinserted = 0;
    $totalfailed = 0;
    $totalcanceled = 0;

    $require = array("galaxy", "system", "row", "datetime", "moon", "planetname",
        "allytag", "playername", "status", "sendername");
    $head = current($datas);
    $head = explode(",", $head);
    $structure = array();
    foreach ($head as $value) {
        @list($info, $position) = explode("=", $value);
        if (in_array($info, $require)) {
            $structure[$info] = $position;
        }
    }

    next($datas);
    while ($planetdata = current($datas)) {
        $totalplanet++;
        $arr = explode("<||>", $planetdata);

        $galaxy = trim($arr[($structure["galaxy"] - 1)]);
        $system = trim($arr[($structure["system"] - 1)]);
        $row = trim($arr[($structure["row"] - 1)]);

        if (isset($structure["datetime"])) {
            $datetime = trim($arr[($structure["datetime"] - 1)]);

            list($day, $hour) = explode(" ", $datetime);
            list($year, $month, $day) = explode("-", $day);
            list($hour, $minute, $seconde) = explode(":", $hour);
            $timestamp = mktime($hour, $minute, $seconde, $month, $day, $year);
        } else
            $timestamp = 0;

        if (isset($structure["moon"])) {
            $moon = intval(trim($arr[$structure["moon"] - 1]));
            if ($moon != 0 && $moon != 1)
                $moon = 0;
        } else
            $moon = 0;

        if (isset($structure["planetname"])) {
            $planetname = trim($arr[$structure["planetname"] - 1]);
        } else
            $planetname = "";

        if (isset($structure["allytag"])) {
            $allytag = trim($arr[$structure["allytag"] - 1]);
        } else
            $allytag = "";

        if (isset($structure["playername"])) {
            $playername = trim($arr[$structure["playername"] - 1]);
        } else
            $playername = "";

        if (isset($structure["status"])) {
            $status = trim($arr[$structure["status"] - 1]);
        } else
            $status = "";

        if (!check_var($galaxy, "Num") || !check_var($system, "Num") || !check_var($row,
            "Num") || !check_var($planetname, "Galaxy") || !check_var($allytag, "Galaxy") ||
            !check_var($playername, "Galaxy") || !check_var($status, "Char") || !check_var($timestamp,
            "Num")) {
            die("<!-- [ErrorFatal=12] Données transmises incorrectes  -->");
        }

        $result = galaxy_add_system($galaxy, $system, $row, $moon, $planetname, $allytag,
            $playername, $status, $timestamp, true);
        if ($result) {
            list($inserted, $updated, $canceled) = $result;
            if ($inserted)
                $totalinserted++;
            if ($updated)
                $totalupdated++;
            if ($canceled)
                $totalcanceled++;
        } else {
            $totalfailed++;
        }

        next($datas);
    }

    if ($server_config["debug_log"] == "1") {
        //Sauvegarde données transmises
        $nomfichier = PATH_LOG_TODAY . date("ymd_His") . "_ID" . $user_data["user_id"] .
            "_sys_OGS.txt";
        write_file($nomfichier, "w", $files);
    }

    galaxy_add_system_ally();

    $end = benchmark();
    $totaltime = ($end - $start);
    $totaltime = round($totaltime, 2);

    //Statistiques serveur
    /*//Incompatible MySQL 4.0
    $request = "insert into ".TABLE_STATISTIC." values ('planetimport_ogs', '".($totalinserted+$totalupdated)."')";
    $request .= " on duplicate key update statistic_value = statistic_value + ".($totalinserted+$totalupdated)."";
    $db->sql_query($request);*/
    $request = "update " . TABLE_STATISTIC .
        " set statistic_value = statistic_value + " . ($totalinserted + $totalupdated);
    $request .= " where statistic_name = 'planetimport_ogs'";
    $db->sql_query($request);
    if ($db->sql_affectedrows() == 0) {
        $request = "insert ignore into " . TABLE_STATISTIC .
            " values ('planetimport_ogs', '" . ($totalinserted + $totalupdated) . "')";
        $db->sql_query($request);
    }

    echo "Merci " . $user_data["user_name"] . "\n";
    echo "Nb de planètes soumises : " . $totalplanet . "\n";
    echo "Nb de planètes insérées : " . $totalinserted . "\n";
    echo "Nb de planètes mise à jour : " . $totalupdated . "\n";
    echo "Nb de planètes obsolètes : " . $totalcanceled . "\n";
    echo "Nb d'échec  : " . $totalfailed . "\n";
    echo "Durée de traitement : $totaltime sec" . "\n";

    log_("load_system_OGS", array($totalplanet, $totalinserted, $totalupdated, $totalcanceled,
        $totalfailed, $totaltime));

    exit();
}




/**
 * Récupération des rapports d'espionnage provenant de OGS
 */
function galaxy_ImportSpy()
{
    global $db, $user_data, $server_config;
    global $pub_data;

    galaxy_check_auth("import_spy");

    //		$test_ogs = file("test/spy-import.txt");
    //		$test_ogs = implode($test_ogs);
    //		$datas = explode("<->", $test_ogs);
    $datas = explode("<->", $pub_data);
    $files = $datas;

    $require = array("coordinates", "planet", "datatime", "report");
    $head = current($datas);
    $head = explode(",", $head);
    $structure = array();
    foreach ($head as $value) {
        @list($info, $position) = explode("=", $value);
        if (in_array($info, $require)) {
            $structure[$info] = $position;
        }
    }


    $spy_added = 0;
    next($datas);
    while ($spyreportdata = current($datas)) {
        $arr = explode("<||>", $spyreportdata);

        $coordinates = $arr[($structure["coordinates"] - 1)];
        list($galaxy, $system, $row) = explode(":", $coordinates);
        $galaxy = intval($galaxy);
        $system = intval($system);
        $row = intval($row);

        if (isset($structure["planet"])) {
            $planet = trim($arr[($structure["planet"] - 1)]);
        } else
            $planet = "";

        if (isset($structure["datatime"])) {
            $datatime = trim($arr[($structure["datatime"] - 1)]);
            list($day, $hour) = explode(" ", $datatime);
            list($year, $month, $day) = explode("-", $day);
            list($hour, $minute, $seconde) = explode(":", $hour);
            $timestamp = mktime($hour, $minute, $seconde, $month, $day, $year);
        } else
            $timestamp = 0;

        $report = $arr[($structure["report"] - 1)];

        if (!check_var($galaxy, "Num") || !check_var($system, "Num") || !check_var($row,
            "Num") || !check_var($planet, "Galaxy") || !check_var($timestamp, "Num") || !
            check_var($report, "Spyreport")) {
            die("<!-- [ErrorFatal=17] Données transmises incorrectes  -->");
        }

        //		if (galaxy_add_spy($galaxy, $system, $row, $planet, $timestamp, $report, 0, 0)) { // modif pour les noms de lunes http://ogsteam.fr/forums/sujet-1652-correctif-bug-phalange-porte-saut
        $pos = strpos($report, "Phalange de capteur") + strlen("Phalange de capteur") +
            1;
        $phalanx = "'" . substr("$report", $pos, $pos + 1) . "'";
        $pos2 = strpos($report, "Porte de saut spatial") + strlen("Porte de saut spatial") +
            1;
        $gate = substr("$report", $pos2, $pos2 + 1);
        if (galaxy_add_spy($galaxy, $system, $row, $planet, $timestamp, $report, $phalanx,
            $gate[0])) {
            $spy_added++;
        }

        next($datas);
    }

    if ($server_config["debug_log"] == "1") {
        //Sauvegarde données transmises
        $nomfichier = PATH_LOG_TODAY . date("ymd_His") . "_ID" . $user_data["user_id"] .
            "_spy_OGS.txt";
        write_file($nomfichier, "w", $files);
    }

    user_set_stat(null, null, null, null, $spy_added);

    log_("load_spy_OGS", $spy_added);

    //Statistiques serveur
    /*//Incompatible MySQL 4.0
    $request = "insert into ".TABLE_STATISTIC." values ('spyimport_ogs', '".$spy_added."')";
    $request .= " on duplicate key update statistic_value = statistic_value + ".$spy_added."";*/
    $request = "update " . TABLE_STATISTIC .
        " set statistic_value = statistic_value + " . $spy_added;
    $request .= " where statistic_name = 'spyimport_ogs'";
    $db->sql_query($request);
    if ($db->sql_affectedrows() == 0) {
        $request = "insert ignore into " . TABLE_STATISTIC .
            " values ('spyimport_ogs', '" . $spy_added . "')";
        $db->sql_query($request);
    }

    die("Merci " . $user_data["user_name"] . "\n" . "Nb de rapports chargés : " . $spy_added .
        "\n");
}

/**
 * Renvoi des rapports d'espionnage vers OGS
 */
/*
function galaxy_ExportSpy()
{
    global $db, $user_data;
    global $pub_galnum, $pub_sysnum;

    galaxy_check_auth("export_spy");

    if (!isset($pub_galnum) || !isset($pub_sysnum)) {
        die("<!-- [ErrorFatal=08] Données transmises incorrectes  -->");
    }
    if (!check_var($pub_galnum, "Num") || !check_var($pub_sysnum, "Num")) {
        die("<!-- [ErrorFatal=09] Données transmises incorrectes  -->");
    }
    $galaxy = intval($pub_galnum);
    $system = intval($pub_sysnum);

    $request = "select max(spy_id), un.name, s.rawdata, us.user_name" . " from " .
        TABLE_UNIVERSE . " un, " . TABLE_SPY . " s left join " . TABLE_USER . " us" .
        " on s.sender_id = us.user_id" . " where active = '1'" .
        " and un.galaxy = s.spy_galaxy" . " and un.system = s.spy_system" .
        " and un.row = s.spy_row" . " and galaxy = " . $galaxy . " and system = " . $system .
        " group by s.spy_galaxy, s.spy_system, s.spy_row";

    $result = $db->sql_query($request);

    $i = 0;
    echo "sendername=1,report=2<->";
    while (list($spy_id, $name, $data, $user_name) = $db->sql_fetch_row($result)) {
        $texte = $user_name . "<||>";
        $texte .= stripslashes($data) . "<->";

        echo $texte;
        $i++;
    }

    user_set_stat(null, null, null, null, null, null, null, null, $i);

    log_("export_spy_sector", array($i, $galaxy, $system));

    //Statistiques serveur
    //Incompatible MySQL 4.0
    //$request = "insert into ".TABLE_STATISTIC." values ('spyexport_ogs', '".$i."')";
    //$request .= " on duplicate key update statistic_value = statistic_value + ".$i."";
    $request = "update " . TABLE_STATISTIC .
        " set statistic_value = statistic_value + " . $i;
    $request .= " where statistic_name = 'spyexport_ogs'";
    $db->sql_query($request);
    if ($db->sql_affectedrows() == 0) {
        $request = "insert ignore into " . TABLE_STATISTIC .
            " values ('spyexport_ogs', '" . $i . "')";
        $db->sql_query($request);
    }

    exit();
}
*/
/**
 * Envoi des rapports d'espionnage vers OGS à partir d'une date
 */
/*
function galaxy_ExportSpy_since()
{
    global $db, $user_data;
    global $pub_since;

    galaxy_check_auth("export_spy");

    if (!isset($pub_since)) {
        die("<!-- [ErrorFatal=10] Données transmises incorrectes  -->");
    }
    if (!check_var($pub_since, "Special", "#^(\d){4}-(\d){2}-(\d){2}\s(\d){2}:(\d){2}:(\d){2}$#")) {
        die("<!-- [ErrorFatal=11] Données transmises incorrectes  -->");
    }

    list($day, $hour) = explode(" ", $pub_since);
    list($year, $month, $day) = explode("-", $day);
    list($hour, $minute, $seconde) = explode(":", $hour);
    $timestamp = mktime($hour, $minute, $seconde, $month, $day, $year);

    $request = "select s.rawdata, us.user_name" . " from " . TABLE_SPY .
        " s left join " . TABLE_USER . " us" . " on s.sender_id = us.user_id" .
        " where active = '1'";
    " and datadate >= " . $timestamp;
    $result = $db->sql_query($request);

    $i = 0;
    echo "sendername=1,report=2<->";
    while (list($data, $user_name) = $db->sql_fetch_row($result)) {
        $texte = $user_name . "<||>";
        $texte .= stripslashes($data) . "<->";

        echo $texte;
        $i++;
    }

    user_set_stat(null, null, null, null, null, null, null, null, $i);

    log_("export_spy_date", array($i, $timestamp));

    //Statistiques serveur
    $request = "update " . TABLE_STATISTIC .
        " set statistic_value = statistic_value + " . $i;
    $request .= " where statistic_name = 'spyexport_ogs'";
    $db->sql_query($request);
    if ($db->sql_affectedrows() == 0) {
        $request = "insert ignore into " . TABLE_STATISTIC .
            " values ('spyexport_ogs', '" . $i . "')";
        $db->sql_query($request);
    }

    exit();
}
*/


/**
 * Récupération du classement joueur via OGS
 */
function galaxy_ImportRanking_player($ranktype)
{
    global $db, $user_data, $server_config;
    global $pub_data;

    galaxy_check_auth("import_ranking");

    //		$test_ogs = file("test/Exp_Imp-ranking.txt");
    //		$ranking=explode("<->", $test_ogs[0]);
    $ranking = explode("<->", $pub_data);
    $files = $ranking;
    $countrank = 0;

    switch ($ranktype) {
        case "points":
            $ranktable = TABLE_RANK_PLAYER_POINTS;
            $ranktype = "general";
            break;

        case "flotte":
            $ranktable = TABLE_RANK_PLAYER_FLEET;
            $ranktype = "fleet";
            break;

        case "research":
            $ranktable = TABLE_RANK_PLAYER_RESEARCH;
            $ranktype = "research";
            break;

        default:
            die("<!-- [ErrorFatal=15] Données transmises incorrectes  -->");
            break;
    }

    $require = array("datetime", "playername", "allytag", "rank", "points",
        "sendername");
    $head = current($ranking);
    $head = explode(",", $head);
    $structure = array();
    foreach ($head as $value) {
        @list($info, $position) = explode("=", $value);
        if (in_array($info, $require)) {
            $structure[$info] = $position;
        }
    }

    @$datadate = $structure["datetime"];
    @list($day, $hour) = explode(" ", $datadate);
    @list($year, $month, $day) = explode("-", $day);
    @list($hour, $minute, $seconde) = explode(":", $hour);
    @$timestamp = mktime($hour, 0, 0, $month, $day, $year);

    next($ranking);
    while ($RankLine = current($ranking)) {
        if ($RankLine) {
            $arr = explode("<||>", $RankLine);
            $countrank = $countrank + 1;

            $rank = intval($arr[$structure["rank"] - 1]);

            if (isset($structure["playername"])) {
                $playername = trim($arr[$structure["playername"] - 1]);
            } else
                $playername = "";

            if (isset($structure["allytag"])) {
                $allytag = trim($arr[$structure["allytag"] - 1]);
            } else
                $allytag = "";

            if (isset($structure["points"])) {
                $points = intval($arr[$structure["points"] - 1]);
            } else
                $points = "";

            if (!check_var($rank, "Num") || !check_var($allytag, "Galaxy") || !check_var($playername,
                "Galaxy") || !check_var($points, "Num") || !check_var($timestamp, "Num")) {
                die("<!-- [ErrorFatal=16] Données transmises incorrectes  -->");
            }

            $request = "insert ignore into " . $ranktable;
            $request .= " (datadate, rank, player, ally, points, sender_id)";
            $request .= " values ('" . $timestamp . "', '" . $rank . "', '" . $db->
                sql_escape_string($playername) . "', '" . $db->sql_escape_string($allytag) .
                "','" . $points . "', '" . $user_data["user_id"] . "')";
            $db->sql_query($request);
        }

        next($ranking);
    }

    user_set_stat(null, null, null, null, null, null, $countrank);

    if ($server_config["debug_log"] == "1") {
        //Sauvegarde données transmises
        $nomfichier = PATH_LOG_TODAY . date("ymd_His") . "_ID" . $user_data["user_id"] .
            "_ranking_" . $ranktype . ".txt";
        write_file($nomfichier, "w", $files);
    }

    if ($countrank > 0) {
        log_("load_rank", array("OGS", $ranktype, "player", $timestamp, $countrank));
    }

    //Statistiques serveur
    $request = "update " . TABLE_STATISTIC .
        " set statistic_value = statistic_value + " . $countrank;
    $request .= " where statistic_name = 'rankimport_ogs'";
    $db->sql_query($request);
    if ($db->sql_affectedrows() == 0) {
        $request = "insert ignore into " . TABLE_STATISTIC .
            " values ('rankimport_ogs', '" . $countrank . "')";
        $db->sql_query($request);
    }

    die("\nMerci " . $user_data["user_name"] . "\n" . "Nb de lignes soumises : " . $countrank .
        "\n");
}

/**
 * Récupération du classement alliance via OGS
 */
function galaxy_ImportRanking_ally($ranktype)
{
    global $db, $user_data, $server_config;
    global $pub_data;

    galaxy_check_auth("import_ranking");

    $ranking = explode("<->", $pub_data);
    $files = $ranking;
    $countrank = 0;

    switch ($ranktype) {
        case "points":
            $ranktable = TABLE_RANK_ALLY_POINTS;
            $ranktype = "general";
            break;

        case "flotte":
            $ranktable = TABLE_RANK_ALLY_FLEET;
            $ranktype = "fleet";
            break;

        case "research":
            $ranktable = TABLE_RANK_ALLY_RESEARCH;
            $ranktype = "research";
            break;

        default:
            die("<!-- [ErrorFatal=13] Données transmises incorrectes  -->");
            break;
    }

    $require = array("rank", "allytag", "number_member", "points",
        "points_per_member", "sendername", "datetime");
    $head = current($ranking);
    $head = explode(",", $head);
    $structure = array();
    foreach ($head as $value) {
        @list($info, $position) = explode("=", $value);
        if (in_array($info, $require)) {
            $structure[$info] = $position;
        }
    }

    @$datadate = $structure["datetime"];
    @list($day, $hour) = explode(" ", $datadate);
    @list($year, $month, $day) = explode("-", $day);
    @list($hour, $minute, $seconde) = explode(":", $hour);
    @$timestamp = mktime($hour, 0, 0, $month, $day, $year);

    next($ranking);
    while ($RankLine = current($ranking)) {
        if ($RankLine) {
            $arr = explode("<||>", $RankLine);
            $countrank = $countrank + 1;

            $rank = intval($arr[$structure["rank"] - 1]);

            if (isset($structure["allytag"])) {
                $allytag = trim($arr[$structure["allytag"] - 1]);
            } else
                $allytag = "";

            if (isset($structure["number_member"])) {
                $number_member = trim($arr[$structure["number_member"] - 1]);
            } else
                $number_member = "";

            if (isset($structure["points"])) {
                $points = intval($arr[$structure["points"] - 1]);
            } else
                $points = "";

            if (isset($structure["points_per_member"])) {
                $points_per_member = intval($arr[$structure["points_per_member"] - 1]);
            } else
                $points_per_member = "";

            if (!check_var($rank, "Num") || !check_var($allytag, "Galaxy") || !check_var($number_member,
                "Num") || !check_var($points, "Num") || !check_var($points_per_member, "Num") ||
                !check_var($timestamp, "Num")) {
                die("<!-- [ErrorFatal=14] Données transmises incorrectes  -->");
            }

            $request = "insert ignore into " . $ranktable;
            $request .= " (datadate, rank, ally, number_member, points, points_per_member, sender_id)";
            $request .= " values ('" . $timestamp . "', '" . $rank . "', '" . $db->
                sql_escape_string($allytag) . "','" . $number_member . "','" . $points . "','" .
                $points_per_member . "', '" . $user_data["user_id"] . "')";
            $db->sql_query($request);
        }

        next($ranking);
    }

    user_set_stat(null, null, null, null, null, null, $countrank);

    if ($server_config["debug_log"] == "1") {
        //Sauvegarde données transmises
        $nomfichier = PATH_LOG_TODAY . date("ymd_His") . "_ID" . $user_data["user_id"] .
            "_ranking_" . $ranktype . ".txt";
        write_file($nomfichier, "w", $files);
    }

    if ($countrank > 0) {
        log_("load_rank", array("OGS", $ranktype, "ally", $timestamp, $countrank));
    }

    //Statistiques serveur
    $request = "update " . TABLE_STATISTIC .
        " set statistic_value = statistic_value + " . $countrank;
    $request .= " where statistic_name = 'rankimport_ogs'";
    $db->sql_query($request);
    if ($db->sql_affectedrows() == 0) {
        $request = "insert ignore into " . TABLE_STATISTIC .
            " values ('rankimport_ogs', '" . $countrank . "')";
        $db->sql_query($request);
    }

    die("\nMerci " . $user_data["user_name"] . "\n" . "Nb de lignes soumises : " . $countrank .
        "\n");
}



/**
 * Listing des classements disponibles sur le serveur pour envoi vers OGS
 */
function galaxy_ShowAvailableRanking($type)
{
    global $db;

    $ranking = array();

    switch ($type) {
        case "player":
            $table_points = TABLE_RANK_PLAYER_POINTS;
            $table_fleet = TABLE_RANK_PLAYER_POINTS;
            $table_research = TABLE_RANK_PLAYER_POINTS;
            break;

        case "ally":
            $table_points = TABLE_RANK_ALLY_POINTS;
            $table_fleet = TABLE_RANK_ALLY_POINTS;
            $table_research = TABLE_RANK_ALLY_POINTS;
            break;
    }

    $request = "select distinct datadate from " . $table_points .
        " order by datadate desc";
    $result = $db->sql_query($request);
    while (list($datadate) = $db->sql_fetch_row($result)) {
        $ranking[$datadate]["points"] = true;
    }

    $request = "select distinct datadate from " . $table_fleet .
        " order by datadate desc";
    $result = $db->sql_query($request);
    while (list($datadate) = $db->sql_fetch_row($result)) {
        $ranking[$datadate]["fleet"] = true;
    }

    $request = "select distinct datadate from " . $table_research .
        " order by datadate desc";
    $result = $db->sql_query($request);
    while (list($datadate) = $db->sql_fetch_row($result)) {
        $ranking[$datadate]["research"] = true;
    }

    while ($value = current($ranking)) {
        echo date("Y-m-d H:i:s", key($ranking) + 4) . "=";
        if (isset($value["points"]))
            echo "P";
        if (isset($value["fleet"]))
            echo "F";
        if (isset($value["research"]))
            echo "R";
        echo "<|>";
        next($ranking);
    }
    exit();
}


?>