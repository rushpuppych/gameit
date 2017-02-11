<?php

// Include
include_once("assets/lib/GifCreator.php");
include_once("assets/lib/global_functions.php");
include_once("interface/jenkins_import.php");

// Debugger
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
$arrPlayerStatus = questImportData($arrPlayerStatus);
$arrPlayerData = $arrPlayerStatus['player'];

// Include Javascript and CSS
echo '<script src="./assets/js/opentip-native.min.js"></script>';
echo '<link href="./assets/js/opentip.css" rel="stylesheet" type="text/css" />';

// Char is Idle in City
if($arrPlayerStatus['status'] == "idle") {
    echo '<span style="float: left">';
    echo imgStringToHtmlImg(createPlayerTitle($arrPlayerData, $arrConfig)) . '<br>';
    echo imgStringToHtmlImg(createScreenCharacter($arrPlayerData, '001', $numR, $numG, $numB)) . '<br>';
    echo imgStringToHtmlImg(createPlayerMinimalStats($arrPlayerData, $arrConfig)) . '<br>';
    echo "</span>";
}

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
    if(isset($arrPlayerStatus['player_quest']['fight'])) {
        $arrFight = $arrPlayerStatus['player_quest']['fight'];
    } else {
        $arrFight['progress'] = 0;
    }
    echo imgStringToHtmlImg(createScreenFight($arrFight, $arrConfig, $arrPlayerStatus['status'], $arrPlayerStatus['player_quest']), 'style="margin-top:-2px;"') . '<br>&nbsp;';


    if ($arrFight['progress'] > 0 && $arrPlayerStatus['player_quest']['progress'] == 100) {
        echo '<a href="action.php?action=quitquest&quest=' . $arrPlayerStatus['player_quest']['quest_id'] . '">';
        echo imgStringToHtmlImg(createBtn('Quit', '1', false), 'style="position: absolute; top: 274px; left: 280px;"');
        echo '</a>';
        echo '<a target="_blank" href="print.php?quest=' . $arrPlayerStatus['player_quest']['quest_id'] . '" style="position: absolute; top: 274px; left: 378px;">';
        echo imgStringToHtmlImg(createBtn('Show Repport', '4', false));
        echo '</a>';
        echo '<div style="position: absolute; top: 104px; left: 280px; width:110px; height: 160px; border: 2px solid #ffffff; background-color: #04B404; opacity: 0.5;"></div>';
        echo '<div style="position: absolute; top: 104px; left: 280px; width:110px; height: 160px; overflow: auto; border: 2px solid #ffffff;">';
        echo getQuestRewards($arrPlayerStatus, true);
        echo '</div>';

        echo '<div style="position: absolute; top: 104px; left: 405px; width:110px; height: 160px; border: 2px solid #ffffff; background-color: #DF0101; opacity: 0.5 "></div>';
        echo '<div style="position: absolute; top: 104px; left: 405px; width:110px; height: 160px; overflow: auto; border: 2px solid #ffffff;">';
        echo getQuestRewards($arrPlayerStatus, false);
        echo '</div>';
    } else {
        if(isset($arrPlayerStatus['player_quest']['status'])) {
            if ($arrPlayerStatus['player_quest']['status'] == 'fight' || $arrPlayerStatus['player_quest']['status'] == 'running') {
                echo '<a target="_blank" href="print.php?quest=' . $arrPlayerStatus['player_quest']['quest_id'] . '" style="position: absolute; top: 174px; left: 330px;">';
                echo imgStringToHtmlImg(createBtn('Show Repport', '4', false));
                echo '</a>';
            }
        }
    }

}

// Shop Screen
if($strScreen == 'shop' && $strShowPlayer == $strLoginPlayer) {
    echo imgStringToHtmlImg(createScreenShop($arrPlayerData), 'style="margin-top:-2px;"') . '<br>&nbsp;';
}

// Equip Screen
if($strScreen == 'equip') {
    $strInventoryScreen = getParam('inventory', 'all');

    if($strInventoryScreen == 'all') {
        // Show All Inventory
        echo imgStringToHtmlImg(createScreenEquipment($arrPlayerData, $numR, $numG, $numB, true), 'style="margin-top:-2px;"') . '<br>&nbsp;';

        // Links
        $strToolTip = 'Nothing Equiped';
        if($strShowPlayer == $strLoginPlayer) {
            echo '<a data-ot="' . getEquip($arrPlayerData, 'hair', $strToolTip) . '" style="position: absolute; top: 91px; left: 304px; width:54px; height: 54px;" href="screen.php?src=int&img=equip&player=' . $strLoginPlayer . '&inventory=hair"></a>';
            echo '<a data-ot="' . getEquip($arrPlayerData, 'helmet', $strToolTip) . '" style="position: absolute; top: 91px; left: 368px; width:54px; height: 54px;" href="screen.php?src=int&img=equip&player=' . $strLoginPlayer . '&inventory=helmet"></a>';
            echo '<a data-ot="' . getEquip($arrPlayerData, 'amulet', $strToolTip) . '" style="position: absolute; top: 91px; left: 433px; width:54px; height: 54px;" href="screen.php?src=int&img=equip&player=' . $strLoginPlayer . '&inventory=amulet"></a>';
            echo '<a data-ot="' . getEquip($arrPlayerData, 'weapon', $strToolTip) . '" style="position: absolute; top: 156px; left: 288px; width:54px; height: 54px;" href="screen.php?src=int&img=equip&player=' . $strLoginPlayer . '&inventory=weapon"></a>';
            echo '<a data-ot="' . getEquip($arrPlayerData, 'armor', $strToolTip) . '" style="position: absolute; top: 156px; left: 368px; width:54px; height: 76px;" href="screen.php?src=int&img=equip&player=' . $strLoginPlayer . '&inventory=armor"></a>';
            echo '<a data-ot="' . getEquip($arrPlayerData, 'shield', $strToolTip) . '" style="position: absolute; top: 156px; left: 447px; width:54px; height: 54px;" href="screen.php?src=int&img=equip&player=' . $strLoginPlayer . '&inventory=shield"></a>';
            echo '<a data-ot="' . getEquip($arrPlayerData, 'ring_right', $strToolTip) . '" style="position: absolute; top: 220px; left: 447px; width:54px; height: 54px;" href="screen.php?src=int&img=equip&player=' . $strLoginPlayer . '&inventory=ring&pos=right"></a>';
            echo '<a data-ot="' . getEquip($arrPlayerData, 'ring_left', $strToolTip) . '" style="position: absolute; top: 220px; left: 288px; width:54px; height: 54px;" href="screen.php?src=int&img=equip&player=' . $strLoginPlayer . '&inventory=ring&pos=left"></a>';
            echo '<a data-ot="' . getEquip($arrPlayerData, 'boots', $strToolTip) . '" style="position: absolute; top: 240px; left: 368px; width:54px; height: 54px;" href="screen.php?src=int&img=equip&player=' . $strLoginPlayer . '&inventory=boots"></a>';
        } else {
            echo '<span data-ot="' . getEquip($arrPlayerData, 'hair', $strToolTip) . '" style="position: absolute; top: 91px; left: 304px; width:54px; height: 54px;"></span>';
            echo '<span data-ot="' . getEquip($arrPlayerData, 'helmet', $strToolTip) . '" style="position: absolute; top: 91px; left: 368px; width:54px; height: 54px;"></span>';
            echo '<span data-ot="' . getEquip($arrPlayerData, 'amulet', $strToolTip) . '" style="position: absolute; top: 91px; left: 433px; width:54px; height: 54px;"></span>';
            echo '<span data-ot="' . getEquip($arrPlayerData, 'weapon', $strToolTip) . '" style="position: absolute; top: 156px; left: 288px; width:54px; height: 54px;"></span>';
            echo '<span data-ot="' . getEquip($arrPlayerData, 'armor', $strToolTip) . '" style="position: absolute; top: 156px; left: 368px; width:54px; height: 76px;"></span>';
            echo '<span data-ot="' . getEquip($arrPlayerData, 'shield', $strToolTip) . '" style="position: absolute; top: 156px; left: 447px; width:54px; height: 54px;"></span>';
            echo '<span data-ot="' . getEquip($arrPlayerData, 'ring_right', $strToolTip) . '" style="position: absolute; top: 220px; left: 447px; width:54px; height: 54px;"></span>';
            echo '<span data-ot="' . getEquip($arrPlayerData, 'ring_left', $strToolTip) . '" style="position: absolute; top: 220px; left: 288px; width:54px; height: 54px;"></span>';
            echo '<span data-ot="' . getEquip($arrPlayerData, 'boots', $strToolTip) . '" style="position: absolute; top: 240px; left: 368px; width:54px; height: 54px;"></span>';
        }
    } else {
        // Show One Inventory Categorie
        $strPos = getParam('pos', '');
        echo imgStringToHtmlImg(createScreenEquipmentBackground($strInventoryScreen), 'style="margin-top:-2px;"') . '<br>&nbsp;';
        echo '<div style="position: absolute; top: 106px; left: 278px; width:251px; height: 204px; overflow: auto;">';
        echo getPlayerInventory($arrPlayerStatus, $strInventoryScreen, $strPos);
        echo '</div>';
    }
}

echo "</span>";

