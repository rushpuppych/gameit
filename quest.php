<?php

// Include
include_once("assets/lib/GifCreator.php");
include_once("assets/lib/global_functions.php");

// Cookie Informationen auslesen
$strLoginPlayer = getCookie('player', 'anonym');
$strQuestId = getParam('quest','');

// Abbruch wenn Quest Hack versuch
$strQuestPlayer = isQuestRunning(getPlayerData(), $strQuestId);
if($strQuestPlayer > 0) {
    echo "Quest ist bereits vergeben.";
    die;
}

// Quest Informationen Laden und Speichern
$arrQuest = getQuestData($strQuestId);

// Get Player Data
$strData = file_get_contents("./data/player.json");
$arrData = json_decode($strData, true);

foreach($arrData as $numPlayer => $arrRecord) {
    if($arrRecord['id'] == $strLoginPlayer) {

        // Alle laufenden Quests Pausieren
        foreach($arrRecord['quest'] as $numQuest => $arrPlayerQuest) {
            if($arrPlayerQuest['status'] != 'close') {
                $arrData[$numPlayer]['quest'][$numQuest]['status'] = 'pause';
            }
        }

        // Neues Quest hinzufügen
        $arrNewQuest = [];
        $arrNewQuest['quest_id'] = $strQuestId;
        $arrNewQuest['status'] = 'running';
        $arrNewQuest['fight']['enemy_id'] = '002';
        $arrNewQuest['fight']['progress'] = 0;
        $arrNewQuest['fight']['action'] = '-';
        $arrNewQuest['start_date'] = time();
        $arrData[$numPlayer]['quest'][] = $arrNewQuest;

    }
}

// Save Player Data
$strData = json_encode($arrData);
$strData = file_put_contents("./data/player.json", $strData);


// Redirect
header('Location: screen.php?src=int&img=fight&player=' . $strLoginPlayer);