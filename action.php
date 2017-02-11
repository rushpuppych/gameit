<?php

// Include
include_once("assets/lib/GifCreator.php");
include_once("assets/lib/global_functions.php");

// Debugger
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cookie Informationen auslesen
$strLoginPlayer = getCookie('player', 'anonym');
$strAction = getParam('action', '');

// Get Player Data
$arrPlayerStatus = getPlayerScreenStatus($strLoginPlayer);
$arrPlayerData = $arrPlayerStatus['player'];

// Quest Action
if($strAction == 'quitquest') {
    $strQuestId = getParam('quest', '');

    // Set Gold and Exp
    $numPercent = $arrPlayerStatus['player_quest']['fight']['progress'];
    $numLevel = getPlayerLevel($arrPlayerData['attributes']['exp'], getAppConfig())['level'];
    $numGold = ($arrPlayerStatus['quest_data']['gold'] * $numLevel) / 100 * $numPercent;
    $numExp = ($arrPlayerStatus['quest_data']['exp'] * $numLevel) / 100 * $numPercent;

    $arrPlayerData['attributes']['exp'] += round($numExp,0);
    $arrPlayerData['character']['coins'] += round($numGold,0);

    // Quit Quest
    foreach($arrPlayerData['quest'] as $numQuest => $arrQuest) {
        if($arrQuest['quest_id'] == $strQuestId) {
            // Set Close Quest
            $arrPlayerData['quest'][$numQuest]['status'] = 'close';
            $numProgress = $arrQuest['fight']['progress'];

            // Get Rewards
            foreach($arrPlayerStatus['quest_data']['rewards'] as $arrReward) {
                if($arrReward['progress'] <= $numProgress) {
                    $strType = $arrReward['type'];
                    unset($arrReward['type']);
                    unset($arrReward['percent']);
                    $arrPlayerData['inventory'][$strType][] = $arrReward;
                }
            }
        }
    }

    // Save Player
    savePlayerData($arrPlayerData);
    header("location:screen.php?src=int&img=fight&player=" . $strLoginPlayer);
}

// Use Equipment
if($strAction == 'useequip') {
    $strItemId = getParam('item', '');
    $strPos = getParam('pos', '');
    $strType = getParam('type', '');

    if(!empty($strPos)) {
        $strPos = '_' . $strPos;
    }

    if ($strItemId != 'noequip') {
        // Get Item
        $arrItem = $arrPlayerData['inventory'][$strType][$strItemId];
        $arrItem['id'] = $strItemId;
        $arrPlayerData['equipment'][$strType . $strPos] = $arrItem;
    } else {
        $arrPlayerData['equipment'][$strType . $strPos] = "";
    }

    // Save Player
    savePlayerData($arrPlayerData);
    header("location:screen.php?src=int&img=equip&player=" . $strLoginPlayer);
}