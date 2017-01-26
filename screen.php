<?php

// Include
include_once("assets/lib/GifCreator.php");
include_once("assets/lib/global_functions.php");
include_once("interface/jenkins_import.php");

// Cookie Informationen auslesen
$strLoginPlayer = getCookie('player', 'anonym');

// Welches Image wurde geklickt
$strScreen = getParam('img','');
$strSource = getParam('src','');
$strShowPlayer = getParam('player','');

// Get Config
$arrConfig = getAppConfig();
$numR = explode(':', $arrConfig['alpha_color'])[0];
$numG = explode(':', $arrConfig['alpha_color'])[1];
$numB = explode(':', $arrConfig['alpha_color'])[2];

// Wenn der Eingeloggte Player nicht bekannt ist dann Login
if($strLoginPlayer == 'anonym') {
    header('Location: login.php');
    die;
}

// Player Status ermitteln
$arrPlayerStatus = getPlayerScreenStatus($strShowPlayer);
$arrPlayerData = $arrPlayerStatus['player'];

// Char is Idle in City
if($arrPlayerStatus['status'] == "idle") {
    echo '<span style="float: left">';
    echo imgStringToHtmlImg(createPlayerTitle($arrPlayerData, $arrConfig)) . '<br>';
    echo imgStringToHtmlImg(createScreenCharacter($arrPlayerData, '001', $numR, $numG, $numB)) . '<br>';
    echo imgStringToHtmlImg(createPlayerMinimalStats($arrPlayerData, $arrConfig)) . '<br>';
    echo "</span>";
}

// WebHook
$arrPlayerStatus = questImportData($arrPlayerStatus);

// Char is Questing
if($arrPlayerStatus['status'] == "quest") {
    $strQuestPlace = $arrPlayerStatus['quest_data']['place'];
    $numQuestProcess = $arrPlayerStatus['player_quest']['progress'];
    $strPlaceName = $arrPlayerStatus['quest_data']['name'];

    echo '<span style="float: left">';
    echo imgStringToHtmlImg(createPlayerTitle($arrPlayerData, $arrConfig)) . '<br>';
    echo imgStringToHtmlImg(createScreenCharacter($arrPlayerData, $strQuestPlace, $numR, $numG, $numB, $strPlaceName, 1, $numQuestProcess)) . '<br>';
    echo imgStringToHtmlImg(createPlayerMinimalStats($arrPlayerData, $arrConfig)) . '<br>';
    echo "</span>";
}

// Char is in Boss Fight
if($arrPlayerStatus['status'] == "fight") {
    $arrFight = $arrPlayerStatus['player_quest']['fight'];
    $strQuestPlace = $arrPlayerStatus['quest_data']['place'];
    $numQuestProcess = $arrPlayerStatus['player_quest']['progress'];
    $strPlaceName = $arrPlayerStatus['quest_data']['name'];

    echo '<span style="float: left">';
    echo imgStringToHtmlImg(createPlayerTitle($arrPlayerData, $arrConfig)) . '<br>';
    echo imgStringToHtmlImg(createScreenCharacterFight($arrPlayerData, $strQuestPlace, $numR, $numG, $numB, $strPlaceName, $arrFight)) . '<br>';
    echo imgStringToHtmlImg(createPlayerMinimalStats($arrPlayerData, $arrConfig)) . '<br>';
    echo "</span>";
}

// Selected Button
if($strScreen == 'stats') { $boolStats = true; } else { $boolStats = false; }
if($strScreen == 'score') { $boolScore = true; } else { $boolScore = false; }
if($strScreen == 'badge') { $boolBadge = true; } else { $boolBadge = false; }
if($strScreen == 'fight') { $boolFight = true; } else { $boolFight = false; }
if($strScreen == 'shop') { $boolShop = true; } else { $boolShop = false; }
if($strScreen == 'equip') { $boolEquip = true; } else { $boolEquip = false; }

// Public Navigation
echo '<span style="float: left">&nbsp;';
echo getScreenLink(imgStringToHtmlImg(createBtn('SCORE', 1, $boolScore)), 'score', $strShowPlayer) . '&nbsp;';
echo getScreenLink(imgStringToHtmlImg(createBtn('STATS', 1, $boolStats)), 'stats', $strShowPlayer) . '&nbsp;';
echo getScreenLink(imgStringToHtmlImg(createBtn('BADGE', 1, $boolBadge)), 'badge', $strShowPlayer) . '<br>&nbsp;';
echo getScreenLink(imgStringToHtmlImg(createBtn('EQUIP', 1, $boolEquip)), 'equip', $strShowPlayer) . '&nbsp;';

// Player Navigation
if($strShowPlayer == $strLoginPlayer) {
    echo getScreenLink(imgStringToHtmlImg(createBtn('QUEST', 1, $boolFight)), 'fight', $strShowPlayer) . '&nbsp;';
    echo getScreenLink(imgStringToHtmlImg(createBtn('SHOP', 1, $boolShop)), 'shop', $strShowPlayer);
} else {
    echo imgStringToHtmlImg(createBtn('', 2, false));
}
echo '<br>&nbsp;';

// Score Screen
if($strScreen == 'score') {
    echo imgStringToHtmlImg(createScreensScore($strShowPlayer), 'style="margin-top:-2px;"') . '<br>&nbsp;';
}

// Badge Screen
if($strScreen == 'badge') {
    echo imgStringToHtmlImg(createScreensBadge($arrPlayerData), 'style="margin-top:-2px;"') . '<br>&nbsp;';
}

// Stats Screen
if($strScreen == 'stats') {
    if($strShowPlayer == $strLoginPlayer) {
        echo imgStringToHtmlImg(createScreenStatus($arrPlayerData, $arrConfig, true), 'style="margin-top:-2px;"') . '<br>&nbsp;';

        // Buttons Setzen
        echo imgStringToHtmlImg(createBtn('+', '0', false), 'style="position: absolute; top: 82px; left: 480px;"');
        echo imgStringToHtmlImg(createBtn('+', '0', false), 'style="position: absolute; top: 117px; left: 480px;"');
        echo imgStringToHtmlImg(createBtn('+', '0', false), 'style="position: absolute; top: 152px; left: 480px;"');
        echo imgStringToHtmlImg(createBtn('+', '0', false), 'style="position: absolute; top: 187px; left: 480px;"');
        echo imgStringToHtmlImg(createBtn('+', '0', false), 'style="position: absolute; top: 222px; left: 480px;"');
        echo imgStringToHtmlImg(createBtn('Reset', '1', false), 'style="position: absolute; top: 274px; left: 440px;"');

    } else {
        echo imgStringToHtmlImg(createScreenStatus($arrPlayerData, $arrConfig, false), 'style="margin-top:-2px;"') . '<br>&nbsp;';
    }
}

// Fight Screen
if($strScreen == 'fight' && $strShowPlayer == $strLoginPlayer) {
    $arrFight = $arrPlayerStatus['player_quest']['fight'];
    echo imgStringToHtmlImg(createScreenFight($arrFight, $arrConfig, $arrPlayerStatus['status']), 'style="margin-top:-2px;"') . '<br>&nbsp;';
    if($arrFight['progress'] > 0) {
        echo imgStringToHtmlImg(createBtn('Attack', '1', false), 'style="position: absolute; top: 274px; left: 280px;"');
        echo imgStringToHtmlImg(createBtn('Show Repport', '4', false), 'style="position: absolute; top: 274px; left: 378px;"');
        echo '<div style="position: absolute; top: 104px; left: 280px; width:110px; height: 160px; border: 2px solid #ffffff; background-color: #04B404; opacity: 0.5;"></div>';
        echo '<div style="position: absolute; top: 104px; left: 280px; width:110px; height: 160px; overflow: auto; border: 2px solid #ffffff;">';
        echo '</div>';

        echo '<div style="position: absolute; top: 104px; left: 405px; width:110px; height: 160px; border: 2px solid #ffffff; background-color: #DF0101; opacity: 0.5 "></div>';
        echo '<div style="position: absolute; top: 104px; left: 405px; width:110px; height: 160px; overflow: auto; border: 2px solid #ffffff;">';
        echo '</div>';
    }
}

// Shop Screen
if($strScreen == 'shop' && $strShowPlayer == $strLoginPlayer) {
    echo imgStringToHtmlImg(createScreenShop($arrPlayerData), 'style="margin-top:-2px;"') . '<br>&nbsp;';
}

// Equip Screen
if($strScreen == 'equip') {
    echo imgStringToHtmlImg(createScreenEquipment($arrPlayerData, $numR, $numG, $numB, true), 'style="margin-top:-2px;"') . '<br>&nbsp;';
}

echo "</span>";

