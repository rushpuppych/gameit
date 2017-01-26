<?php

/**
 * Dieses Script gibt ein einzelnes Bild auf dem GameScreen zurück
 */

// Include
include_once("assets/lib/GifCreator.php");
include_once("assets/lib/global_functions.php");
include_once("interface/jenkins_import.php");

// Cookie Informationen auslesen
$strLoginPlayer = getCookie('player', 'anonym');

// Welches Image soll gerechnet werden
$strQuest = getParam('quest','');
$strImgCoords = getParam('img','');
$numImgRow = explode('-', $strImgCoords)[0];
$numImgCol = explode('-', $strImgCoords)[1];
$strShowPlayer = getParam('player','');

// Get Config
$arrConfig = getAppConfig();
$numR = explode(':', $arrConfig['alpha_color'])[0];
$numG = explode(':', $arrConfig['alpha_color'])[1];
$numB = explode(':', $arrConfig['alpha_color'])[2];

// Create Clear Screen
$arrScreenArray = generateClearScreenArray();

// Player Status ermitteln
$arrPlayerStatus = getPlayerScreenStatus($strShowPlayer);
$arrPlayerData = $arrPlayerStatus['player'];

// WebHook Integration
$arrPlayerStatus = questImportData($arrPlayerStatus);

// Char is Idle in City
if($arrPlayerStatus['status'] == "idle") {
    // Player Screen Laden
    if($strImgCoords == '0-0') {$arrScreenArray[0][0] = createPlayerTitle($arrPlayerData, $arrConfig);}
    if($strImgCoords == '1-0') {$arrScreenArray[1][0] = createScreenCharacter($arrPlayerData, '001', $numR, $numG, $numB);}
    if($strImgCoords == '2-0') {$arrScreenArray[2][0] = createPlayerMinimalStats($arrPlayerData, $arrConfig);}
}

// Char is Questing
if($arrPlayerStatus['status'] == "quest") {
    $strQuestPlace = $arrPlayerStatus['quest_data']['place'];
    $numQuestProcess = $arrPlayerStatus['player_quest']['progress'];
    $strPlaceName = $arrPlayerStatus['quest_data']['name'];

    // Player Screen Laden
    if($strImgCoords == '0-0') {$arrScreenArray[0][0] = createPlayerTitle($arrPlayerData, $arrConfig);}
    if($strImgCoords == '1-0') {$arrScreenArray[1][0] = createScreenCharacter($arrPlayerData, $strQuestPlace, $numR, $numG, $numB, $strPlaceName, 1, $numQuestProcess);}
    if($strImgCoords == '2-0') {$arrScreenArray[2][0] = createPlayerMinimalStats($arrPlayerData, $arrConfig);}
}

// Char is in Fight
if($arrPlayerStatus['status'] == "fight") {
    $arrFight = $arrPlayerStatus['player_quest']['fight'];
    $strQuestPlace = $arrPlayerStatus['quest_data']['place'];
    $strPlaceName = $arrPlayerStatus['quest_data']['name'];

    // Player Screen Laden
    if($strImgCoords == '0-0') {$arrScreenArray[0][0] = createPlayerTitle($arrPlayerData, $arrConfig);}
    if($strImgCoords == '1-0') {$arrScreenArray[1][0] = createScreenCharacterFight($arrPlayerData, $strQuestPlace, $numR, $numG, $numB, $strPlaceName, $arrFight);}
    if($strImgCoords == '2-0') {$arrScreenArray[2][0] = createPlayerMinimalStats($arrPlayerData, $arrConfig);}
}

if($arrPlayerStatus['status'] == "idle") {
    // Player Screen Laden
    if($strImgCoords == '0-0') {$arrScreenArray[0][0] = createPlayerTitle($arrPlayerData, $arrConfig);}
    if($strImgCoords == '1-0') {$arrScreenArray[1][0] = createScreenCharacter($arrPlayerData, '001', $numR, $numG, $numB);}
    if($strImgCoords == '2-0') {$arrScreenArray[2][0] = createPlayerMinimalStats($arrPlayerData, $arrConfig);}
}

// Quest Button
if($strQuest != '') {
    $arrScreenArray = generateClearScreenArray();

    if($strLoginPlayer == 'anonym') {
        $arrScreenArray[0][0] = createBtn('PLEASE LOGIN', 4, true);
    }

    // Check Quest Status
    $strQuestPlayer = isQuestRunning(getPlayerData(), $strQuest);
    if($strQuestPlayer > 0) {
        // Quest in Work by Player
        $arrScreenArray[0][0] = createQuestInformation($strQuest, $strQuestPlayer);
    } else {
        // Quest Ready to take
        $arrScreenArray[0][0] = createQuestInformation($strQuest);
        $arrScreenArray[1][0] = createBtn('QUEST ANNEHMEN', 5, true);
    }
}

// Render Image
$strImageData = $arrScreenArray[$numImgRow][$numImgCol];
header('Content-Type: image/gif');
echo $strImageData;
die;