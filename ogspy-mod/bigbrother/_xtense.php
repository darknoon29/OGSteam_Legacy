<?php

if (!defined('IN_SPYOGAME'))
    die("Hacking attempt");

global $db, $table_prefix, $user, $xtense_version;
$xtense_version = "2.2";
//constante table  :
define("TABLE_PLAYER", $table_prefix . "bigb_player");
define("TABLE_ALLY", $table_prefix . "bigb_ally");
define("TABLE_STORY_PLAYER", $table_prefix . "bigb_story_player");
define("TABLE_STORY_ALLY", $table_prefix . "bigb_story_ally");
define("MOD_URL", "mod/bigbrother/");


// appel des classes
require_once (MOD_URL . "include/ally.php");
require_once (MOD_URL . "include/player.php");


if (class_exists("Callback")) {
    class bigbrother_Callback extends Callback
    {
        public $version = '2.3.9';

        public function getCallbacks()
        {
            return array(array('function' => 'addsystem', 'type' => 'system'), array('function' =>
                'addrankplayerpoints', 'type' => 'ranking_player_points'), array('function' =>
                'addrankplayerflotte', 'type' => 'ranking_player_fleet'), array('function' =>
                'addrankplayersearch', 'type' => 'ranking_player_research'));
        }

        //////////////////////////////////////  SYSTEME \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
        public function addsystem($system)
        {
            global $io;
            if (addsystem($system))
                return Io::SUCCESS;
            else
                return Io::ERROR;
        }
        //////////////////////////////////// FIN SYSTEME \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\


        ///////////////////////////////////// CLASSEMENT JOUEUR \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

        // POINT \\
        public function addrankplayerpoints($ranking_player_points)
        {
            global $io;
            if (addrankplayerpoints($ranking_player_points))
                return Io::SUCCESS;
            else
                return Io::ERROR;
        }
        // FIN POINT \\

        // FLOTTE \\
        public function addrankplayerflotte($ranking_player_fleet)
        {
            global $io;
            if (addrankplayerflotte($ranking_player_fleet))
                return Io::SUCCESS;
            else
                return Io::ERROR;
        }
        // FIN FLOTTE \\

        // RESEARC \\
        public function addrankplayersearch($ranking_player_research)
        {
            global $io;
            if (addrankplayersearch($ranking_player_research))
                return Io::SUCCESS;
            else
                return Io::ERROR;
        }
        // FIN RESEARCH \\

        //////////////////////////////////// FIN CLASSEMENT JOUEUR\\\\\\\\\\\\\\\\\\\\\\\\\\\\\


    }
}


/**
 * addsystem()
 * 
 * @param mixed $system
 * @return true si reussite
 */
function addsystem($system)
{
    global $sql, $user, $db, $table_prefix;
    //  var_dump($system);
    $tab_player []=0;
    $time = time();
    for ($i = 1; $i < 16; $i++) {
        if (isset($system['data'][$i]['player_id']) && is_numeric($system['data'][$i]['player_id'])) {
            if (!in_array($system['data'][$i]['player_id'], $tab_player)) { // on ne cherche pas si deja mis a jour

                if ($system['data'][$i]['ally_id'] != -1) { // on va attendre que grease monkey soit compatible avec id alliance ... comme ca la base sera saine meme si moins complete
                    $player = player::get_player_by_system($system['data'][$i], $time);
                    $player->save();
                    $player = null;
                    $tab_player[] = $system['data'][$i]['player_id']; // on sauvegarde les index

               }
            }
        }
    }
    return true;
}


// todo voir pour systeme de cache ou de verif prealable pour soulager la base !!!
function abstract_rankplayer($ranking, $type)
{
    $times = $ranking['time'];
    //var_dump($ranking_player_points);
    for ($i = 0; $i < 100; $i++) {
        if (isset($ranking['data'][$i])) {
            if ($ranking['data'][$i]['player_id'] != -1) {
                $rank = $i + $ranking['offset'];
                $player = player::get_player_by_rank($ranking['data'][$i], $times);
                $player->update_rank($type, $rank); 
                $player->save();
                $player = null;

            }
        }
    }
    return true;
}

function addrankplayerpoints($ranking_player_points)
{
    return abstract_rankplayer($ranking_player_points, 'point');

}

function addrankplayerflotte($ranking_player_fleet)
{
    return abstract_rankplayer($ranking_player_fleet, 'fleet');

}

function addrankplayersearch($ranking_player_research)
{
    return abstract_rankplayer($ranking_player_research, 'research');

}






?>