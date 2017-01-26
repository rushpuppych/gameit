<?php

include_once("../assets/lib/generator.php");

// Get Form Data
$strUid = uniqid('quest_', true);
$strName = $_GET['quest_name'];
$strGold = $_GET['quest_gold'];
$strExp = $_GET['quest_exp'];
$strPlace = $_GET['quest_place'];
$strItems = $_GET['quest_items'];

if(empty($strName)) {
    $strName = generateQuestName('');
}

// Create Quest Code
$arrQuest = [];
$arrQuest['id'] = $strUid;
$arrQuest['name'] = $strName;
$arrQuest['gold'] = $strGold;
$arrQuest['exp'] = $strExp;
$arrQuest['place'] = $strPlace;
$arrQuest['type'] = getPlaceType($strPlace);
$arrQuest['rewards'] = [];
$arrQuest['enemy_id'] = getRandomEnemy();

// Create Item Rewards
$numPercentSteps = intval(100 / intval($strItems));
for($numIndex = 0; $numIndex < intval($strItems); $numIndex++) {
    $arrItem = [];
    $arrItem['percent'] = $numIndex * $numPercentSteps;
    $arrItem['type'] = getRandomItemTypes();
    $arrItem['name'] = generateItemName($arrItem['type']);
    $arrItem['img_src'] = getRandomImg($arrItem['type']);
}

$strFile = file_get_contents('../data/quests.json', true);
$arrFile = json_decode($strFile);
$arrFile[] = $arrQuest;

$strFile = json_encode($arrFile);
file_put_contents('../data/quests.json', $strFile);

// Echo Quest
echo "<h1>Quests</h1>";
echo "<p>Generate a new Quest Card</p>";

echo '<div class="row">';
echo '    <div class="col-md-12">';
echo '        <form>';
echo '            <div class="form-group">';
echo '                <label for="quest_code">MarkDown Quest Code</label>';
echo '                <textarea class="form-control" id="quest_code" name="quest_code">';
echo "Checkin Id: #" . $strUid . "\n";
echo "![](http://localhost:8888/gi/img.php?img=0-0&quest=" . $strUid . ")";
echo "[![](http://localhost:8888/gi/img.php?img=1-0&quest=" . $strUid . ")](http://localhost:8888/gi/quest.php?src=ext&quest=" . $strUid . ") ";
echo '                </textarea>';
echo '            </div>';
echo '        </form>';
echo '    </div>';
echo '</div>';

/**
 * ===================================================================================================================
 * FUNKTIONEN
 * ===================================================================================================================
 */
function getRandomItemTypes()
{
    $numType = rand(0, 6);
    $arrTypes[] = 'helmet';
    $arrTypes[] = 'boots';
    $arrTypes[] = 'shield';
    $arrTypes[] = 'armor';
    $arrTypes[] = 'weapon';
    $arrTypes[] = 'ring';
    $arrTypes[] = 'amulet';
    return $arrTypes[$numType];
}

function getPlaceType($strType) {
    $arrPlace['002'] = 'Grüne Wiese';
    $arrPlace['003'] = 'Scary Wald';
    $arrPlace['004'] = 'Höhle';
    $arrPlace['005'] = 'Castle';
    $arrPlace['006'] = 'Minen Schacht';
    $arrPlace['007'] = 'Ruine';
    $arrPlace['008'] = 'Burg keller';

    return $arrPlace[$strType];
}

function getRandomImg($strType)
{
    $arrFiles = scandir('../assets/img/' . $strType . '/', SCANDIR_SORT_ASCENDING);
    $numItem = rand(2, count($arrFiles) -1);
    $strItem = $arrFiles[$numItem];
    $strItem = str_replace('.png', '', $strItem);
    return $strItem;
}

function getRandomEnemy()
{
    $strFile = file_get_contents('../data/enemy.json');
    $arrFile = json_decode($strFile, true);
    $numIndex = rand(0, count($arrFile) -1);
    $strEnemyId = $arrFile[$numIndex]['id'];
    return $strEnemyId;
}

