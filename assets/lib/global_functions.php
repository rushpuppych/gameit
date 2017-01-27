<?php

/**
 * Read Json Config File
 */
function getAppConfig()
{
    $strConfig = file_get_contents("./data/config.json");
    $arrConfig = json_decode($strConfig, true);
    return $arrConfig;
}

/**
 * Read Json Config File
 * @param string $numPlayerId
 * @return mixed
 */
function getPlayerData($numPlayerId = '000')
{
    $strData = file_get_contents("./data/player.json");
    $arrData = json_decode($strData, true);

    foreach($arrData as $arrReord) {
        if($arrReord['id'] == $numPlayerId) {
            return $arrReord;
        }
    }
    return $arrData;
}

/**
 * Loading Quest Array
 * @param string $numQuestId
 * @return mixed
 */
function getQuestData($numQuestId = '000')
{
    $strData = file_get_contents("./data/quests.json");
    $arrData = json_decode($strData, true);

    foreach($arrData as $arrReord) {
        if($arrReord['id'] == $numQuestId) {
            return $arrReord;
        }
    }
    return $arrData;
}

/**
 * Loading Enemy Data
 * @param string $numEnemyId
 * @return mixed
 */
function getEnemyData($numEnemyId = '000')
{
    $strData = file_get_contents("./data/enemy.json");
    $arrData = json_decode($strData, true);

    foreach($arrData as $arrReord) {
        if($arrReord['id'] == $numEnemyId) {
            return $arrReord;
        }
    }
    return $arrData;
}

/**
 * Gibt zurück ob eine Quest bereits verteilt wurde oder nicht
 * @param $arrAllPlayerData
 * @param $strQuestId
 * @return bool
 */
function isQuestRunning($arrAllPlayerData, $strQuestId)
{
    foreach($arrAllPlayerData as $arrPlayer) {
        foreach($arrPlayer['quest'] as $arrQuest) {
            if($arrQuest['quest_id'] == $strQuestId && $arrQuest['status']) {
                return $arrPlayer['id'];
            }
        }
    }
    return 0;
}

/**
 * Set Alpha Chanell for a Image
 * @param $objImg
 * @return mixed
 */
function imgSetAlpha($objImg, $numR, $numG, $numB)
{
    imagealphablending($objImg, true);
    imagesavealpha($objImg, true);

    $objRgb = imagecolorexact($objImg, $numR, $numG, $numB);
    imagecolortransparent($objImg, $objRgb);

    return $objImg;
}

/**
 * Merge two images together to one Image
 * @param $objImgBack
 * @param $objImgFront
 * @param int $numTop
 * @param int $numLeft
 * @return mixed
 */
function imgMergeImages($objImgBack, $objImgFront, $numTop = 0, $numLeft = 0)
{
    $numWidth = imagesx($objImgFront);
    $numHeight = imagesy($objImgFront);

    imagecopymerge($objImgBack, $objImgFront, $numLeft, $numTop, 0, 0, $numWidth, $numHeight, 100);
    return $objImgBack;
}

/**
 * Add Background to a Image
 * @param $objImgBack
 * @param $objImgCharacter
 * @return mixed
 */
function imgAddBackground($objImgBack, $objImgCharacter)
{
    $numBgWidth = imagesx($objImgBack);
    $numBgHeight = imagesy($objImgBack);

    $numCharWidth = imagesx($objImgCharacter);
    $numCharHeight = imagesy($objImgCharacter);

    $numDrawTop = intval($numBgHeight / 2) - intval($numCharHeight / 2);
    $numDrawLeft = intval($numBgWidth / 2) - intval($numCharWidth / 2);

    imagecopymerge($objImgBack, $objImgCharacter, $numDrawLeft, $numDrawTop, 0, 0, $numCharWidth, $numCharHeight, 100);

    return $objImgBack;
}

/**
 * Extracting a Player Frame from a Character Image and adding the Alpha Chanel to it
 * @param $objImgCharacter
 * @param $numFrame
 * @param $numR
 * @param $numG
 * @param $numB
 * @return mixed|resource
 */
function imgCreateFrame($objImgCharacter, $numFrame, $numR, $numG, $numB)
{
    $numWidth = imagesx($objImgCharacter);
    $numHeight = imagesy($objImgCharacter);

    if($numFrame <= 2) {
        $numTop = 0;
        $numLeft = ($numWidth / 3) * $numFrame;

    } elseif($numFrame > 2 && $numFrame <=5) {
        $numTop = ($numHeight / 4) * 1;
        $numLeft = ($numWidth / 3) * ($numFrame - 3);

    } elseif($numFrame > 5 && $numFrame <=8) {
        $numTop = ($numHeight / 4) * 2;
        $numLeft = ($numWidth / 3) * ($numFrame - 6);

    } elseif($numFrame > 8 && $numFrame <=11) {
        $numTop = ($numHeight / 4) * 3;
        $numLeft = ($numWidth / 3) * ($numFrame - 9);

    } else {
        return $objImgCharacter;

    }

    $numWidth = $numWidth / 3;
    $numHeight = $numHeight / 4;

    // Create new Chara Image
    $objChara = imagecreatetruecolor($numWidth, $numHeight);
    $objRgb = imagecolorallocate($objChara,  $numR, $numG, $numB);
    imagefill ($objChara, 0, 0, $objRgb);
    $objChara = imgSetAlpha($objChara, $numR, $numG, $numB);

    imagecopy($objChara, $objImgCharacter, 0, 0, $numLeft, $numTop, $numWidth, $numHeight);

    return $objChara;
}

/**
 * Creates a Animated Gif Animation
 * @param $objCharacter
 * @param $objBackground
 * @param $arrFrames
 * @param $arrDurationTimes
 * @param $numR
 * @param $numG
 * @param $numB
 * @return \GifCreator\AnimGif
 */
function imgCreateAnimation($objCharacter, $objBackground, $arrFrames, $arrDurationTimes, $numR, $numG, $numB)
{
    // Create Temporary Background Images
    $arrTempFiles = [];

    foreach($arrFrames as $numFrame) {
        $objWorkBg = imgCloneResource($objBackground);
        $objCharFrame = imgCreateFrame($objCharacter, $numFrame, $numR, $numG, $numB);
        $objCharFrame = imgAddBackground($objWorkBg, $objCharFrame);

        $strRandFile = './temp/' . getUid() . '.png';
        imagepng($objCharFrame, $strRandFile);
        $arrTempFiles[] = $strRandFile;
    }

    // Create Animated Gif
    $objAnimatedGif = new GifCreator\AnimGif();
    $objAnimatedGif->create($arrTempFiles, $arrDurationTimes);

    // Remove Temp Files
    foreach($arrTempFiles as $strFile) {
        unlink($strFile);
    }

    return $objAnimatedGif;
}

/**
 * Animation for Fight
 * @param $objCharacter
 * @param $objEnemy
 * @param $objBackground
 * @param $arrFramesChar
 * @param $arrFramesEnemy
 * @param $arrDurationTimes
 * @param $numR
 * @param $numG
 * @param $numB
 * @return \GifCreator\AnimGif
 */
function imgCreateFightAnimation($objCharacter, $objEnemy, $objBackground, $arrFramesChar, $arrFramesEnemy, $arrDurationTimes, $numR, $numG, $numB, $numProgress)
{
    // Create Temporary Background Images
    $arrTempFiles = [];

    $boolMarchIn = true;
    $numMarch = 0;
    foreach($arrFramesChar as $numIndex => $numFrameChar) {
        if($boolMarchIn) { $numMarch++; } else { $numMarch--; }
        if($numMarch > 4 and $boolMarchIn == true) { $boolMarchIn = false; }
        if($numMarch < 1 and $boolMarchIn == false) { $boolMarchIn = true; }
        $numEnemyMarch = $numMarch;
        $objWorkBg = imgCloneResource($objBackground);
        $objCharFrame = imgCreateFrame($objCharacter, $numFrameChar, $numR, $numG, $numB);
        if($numProgress < 100) {
            $objEnemyFrame = imgCreateFrame($objEnemy, $arrFramesEnemy[$numIndex], $numR, $numG, $numB);
        } else {
            $numEnemyMarch = 0;
            $arrFramesEnemy = [1, 4, 7, 10, 1, 4, 7, 10];
            $objEnemyFrame = imgCreateFrame($objEnemy, $arrFramesEnemy[$numIndex], $numR, $numG, $numB);
        }

        // Create new Player Enemy image
        $numWidth = imagesx($objWorkBg);
        $numHeight = imagesy($objWorkBg);
        $objMergeImage = imagecreatetruecolor($numWidth, $numHeight);
        imagefill($objMergeImage, 0, 0, imagecolorexact($objMergeImage, $numR, $numG, $numB));

        // Merge all together
        $numWidthChar = imagesx($objCharFrame);
        $numHeightChar = imagesy($objCharFrame);
        imagecopymerge($objMergeImage, $objCharFrame, 20 + ($numMarch * 5), $numHeight / 2 - 50, 0, 0, $numWidthChar, $numHeightChar, 100);
        imagecopymerge($objMergeImage, $objEnemyFrame, 165 - ($numEnemyMarch * 5), $numHeight / 2 - 50, 0, 0, $numWidthChar, $numHeightChar, 100);
        imgSetAlpha($objMergeImage, $numR, $numG, $numB);

        $objFrame = imgAddBackground($objWorkBg, $objMergeImage);

        $strRandFile = './temp/' . getUid() . '.png';
        imagepng($objFrame, $strRandFile);
        $arrTempFiles[] = $strRandFile;
    }

    // Create Animated Gif
    $objAnimatedGif = new GifCreator\AnimGif();
    $objAnimatedGif->create($arrTempFiles, $arrDurationTimes);

    // Remove Temp Files
    foreach($arrTempFiles as $strFile) {
        unlink($strFile);
    }

    return $objAnimatedGif;
}

/**
 * Fügt eine Progressbar in ein Bestehendes Bild ein
 * @param $objImg
 * @param $numTop
 * @param $numProgress
 * @param int $numLeft
 * @param int $numWidth
 * @param int $numColor
 * @return mixed
 */
function imgAddProgressBar($objImg, $numTop, $numProgress, $numLeft = -1, $numWidth = -1, $numColor = 0)
{
    $numImgWidth = imagesx($objImg);

    if($numLeft < 0 ) {
        $numLeft = 10;
    }
    if($numWidth < 0 ) {
        $numWidth = $numImgWidth - 10;
    }

    $numHeight = $numTop + 20;

    $objColorWhite = imagecolorallocate ($objImg, 255, 255, 255);
    $objColorBlack = imagecolorallocate ($objImg, 20, 20, 20);
    $objColorRed = getColorByCode(2, $objImg);
    $objColor = getColorByCode($numColor, $objImg);

    imagefilledrectangle($objImg , $numLeft, $numTop, $numWidth, $numHeight, $objColorWhite);
    imagefilledrectangle($objImg , $numLeft + 2, $numTop + 2, $numWidth - 2, $numHeight - 2, $objColorBlack);

    $numProgesWidth = ($numWidth - $numLeft) / 100 * $numProgress;
    if($numProgesWidth > 0) {
        imagefilledrectangle($objImg , $numLeft + 2, $numTop + 2, $numProgesWidth + $numLeft, $numHeight - 2, $objColorRed);
    }

    return $objImg;
}

/**
 * Adding Title to a Image
 * @param $objImg
 * @param $numTop
 * @param $numLeft
 * @param $strText
 * @param $numColor
 * @return mixed
 */
function imgAddTitle($objImg, $numTop, $numLeft, $strText, $numColor)
{
    // Font Farbe
    $objColor = getColorByCode($numColor, $objImg);

    // Text
    imagettftext($objImg, 16, 0, $numLeft, $numTop, $objColor, "./assets/font/Gamer.ttf", $strText);

    return $objImg;
}

/**
 * Creates the Player Title Image
 * @param $arrPlayerData
 * @param $arrConfig
 * @return string
 */
function createPlayerTitle($arrPlayerData, $arrConfig)
{
    $strName = ucfirst($arrPlayerData['login']['name']);
    $numExp = intval($arrPlayerData['attributes']['exp']);
    $arrLevel = getPlayerLevel($numExp, $arrConfig);;
    $strRank = getPlayerRanking($arrLevel['level'], $arrPlayerData);

    $objImg = imagecreate (259, 65);
    imagecolorallocate($objImg, 0, 0, 0);
    $objColorWhite = imagecolorallocate ($objImg, 255, 255, 255);

    imagettftext($objImg, 40, 0, 10, 30, $objColorWhite, "./assets/font/Gamer.ttf", strtoupper($strName));
    imagettftext($objImg, 20, 0, 10, 55, $objColorWhite, "./assets/font/Gamer.ttf", $strRank);
    $strImageData = imgResourceToString($objImg);

    return $strImageData;
}

/**
 * Creates the Player Minimal Stats image (mostly used as footer)
 * @param $arrPlayerData
 * @return string

 */
function createPlayerMinimalStats($arrPlayerData, $arrConfig)
{
    $numExp = intval($arrPlayerData['attributes']['exp']);
    $arrLevel = getPlayerLevel($numExp, $arrConfig);
    $numCoins = number_format($arrPlayerData['character']['coins'], 0, ".", "'");;

    $objImg = imagecreate (259, 45);
    imagecolorallocate($objImg, 0, 0, 0);
    $objColorWhite = imagecolorallocate ($objImg, 255, 255, 255);

    imagettftext($objImg, 20, 0, 10, 20, $objColorWhite, "./assets/font/Gamer.ttf", 'Level: ' . $arrLevel['level']);
    imagettftext($objImg, 18, 0, 10, 35, $objColorWhite, "./assets/font/Gamer.ttf", 'Exp: ' . $numExp . '/' . $arrLevel['nextlevel_exp']);
    imagettftext($objImg, 18, 0, 160, 20, $objColorWhite, "./assets/font/Gamer.ttf", $numCoins . " $");
    $strImageData = imgResourceToString($objImg);

    return $strImageData;
}

/**
 * Rendering the Quest Image Box
 * @param $strQuestId
 * @return string
 */
function createQuestInformation($strQuestId, $strLoginPlayer = "000")
{
    $arrQuestData = getQuestData($strQuestId);

    $objImg = imagecreate(400, 90);
    imagecolorallocate($objImg, 88, 88, 88);
    $objColorWhite = imagecolorallocate ($objImg, 255, 255, 255);

    imagettftext($objImg, 18, 0, 10, 50, $objColorWhite, "./assets/font/Gamer.ttf", 'QUEST: ' . $arrQuestData['name']);

    if($strLoginPlayer == "000") {
        $strLoginPlayer = getCookie('player', 'anonym');
        $arrPlayerData = getPlayerData($strLoginPlayer);
        $numExp = $arrPlayerData['attributes']['exp'];
        $arrLevel = getPlayerLevel($numExp, getAppConfig());
        $numLevel = $arrLevel['level'];

        imagettftext($objImg, 18, 0, 10, 20, $objColorWhite, "./assets/font/Gamer.ttf", 'EXP: ' . intval($arrQuestData['exp']) * $numLevel);
        imagettftext($objImg, 18, 0, 150, 20, $objColorWhite, "./assets/font/Gamer.ttf", 'GOLD: ' . intval($arrQuestData['gold']) * $numLevel);
        imagettftext($objImg, 18, 0, 300, 20, $objColorWhite, "./assets/font/Gamer.ttf", 'ITEMS: ' . count($arrQuestData['rewards']));
        imagettftext($objImg, 18, 0, 10, 70, $objColorWhite, "./assets/font/Gamer.ttf", 'TYPE: ' . $arrQuestData['type']);
    } else {
        $arrPlayer = getPlayerData($strLoginPlayer);
        foreach($arrPlayer['quest'] as $arrPlayerQuest) {
            if($arrPlayerQuest['quest_id'] == $strQuestId) {
                if($arrPlayerQuest['status'] == 'close') {
                    imagettftext($objImg, 26, 0, 10, 28, getColorByCode(6, $objImg), "./assets/font/Gamer.ttf", 'ERLEDIGT BY: ' . ucfirst($arrPlayer['login']['name']));
                } else {
                    imagettftext($objImg, 26, 0, 10, 28, getColorByCode(5, $objImg), "./assets/font/Gamer.ttf", 'QUEST GEHÖRT: ' . ucfirst($arrPlayer['login']['name']));
                }
                if($arrPlayerQuest['status'] == 'pause') {
                    imagettftext($objImg, 18, 0, 10, 75, getColorByCode(7, $objImg), "./assets/font/Gamer.ttf", 'STATUS: ' . ucfirst($arrPlayerQuest['status']));
                } else if($arrPlayerQuest['status'] == 'close') {
                    imagettftext($objImg, 18, 0, 10, 75, getColorByCode(6, $objImg), "./assets/font/Gamer.ttf", 'STATUS: ' . ucfirst($arrPlayerQuest['status']));
                } else {
                    imagettftext($objImg, 18, 0, 10, 75, getColorByCode(1, $objImg), "./assets/font/Gamer.ttf", 'STATUS: ' . ucfirst($arrPlayerQuest['status']));
                }
                $objImg = imgAddProgressBar($objImg, 60, $arrPlayerQuest['fight']['progress'], 200, 390, 3);
                $objImg = imgAddTitle($objImg, 75, 210, $arrPlayerQuest['fight']['progress'] . ' %', 1);
            }
        }
    }

    $strImageData = imgResourceToString($objImg);

    return $strImageData;
}

/**
 *
 * @param $strTitle
 * @param int $numSize
 * @return string
 */
function createBtn($strTitle, $numSize = 1, $boolSelected = false)
{
    $numWidth = ($numSize * 84);
    if($numSize == 0) {
        $numWidth = 30;
    }
    if($numSize == 1) {
        $numWidth = 84;
    }
    if($numSize == 2) {
        $numWidth = 172;
    }
    if($numSize == 3) {
        $numWidth = 240;
    }
    if($numSize == 4) {
        $numWidth = 140;
    }
    if($numSize == 5) {
        $numWidth = 400;
    }

    $objImg = imagecreate($numWidth, 30);

    if($boolSelected) {
        $objColorBg = imagecolorallocate($objImg, 1, 116, 223);
    } else {
        $objColorBg = imagecolorallocate($objImg, 8, 75, 138);
    }

    $objColorWhite = imagecolorallocate ($objImg, 255, 255, 255);
    imagefill ($objImg, 0, 0, $objColorBg);

    imagettftext($objImg, 20, 0, 10, 20, $objColorWhite, "./assets/font/Gamer.ttf", $strTitle);
    $strImageData = imgResourceToString($objImg);

    return $strImageData;
}

/**
 * Creates the Player Character ImageSet
 * @param $arrPlayerData
 * @param $numR
 * @param $numG
 * @param $numB
 * @return mixed
 */
function imgCreateCharacter($arrPlayerData, $numR, $numG, $numB)
{
    // Get Body
    $strBodyPath = './assets/img/body/' . $arrPlayerData['character']['body'] .'.png';
    $objBodyImg = imagecreatefrompng($strBodyPath);
    $objBodyImg = imgSetAlpha($objBodyImg, $numR, $numG, $numB);

    // Get Face
    $strFacePath = './assets/img/face/' . $arrPlayerData['character']['face'] . '.png';
    $objFaceImg = imagecreatefrompng($strFacePath);
    $objFaceImg = imgSetAlpha($objFaceImg, $numR, $numG, $numB);
    $objCharacter = imgMergeImages($objBodyImg, $objFaceImg);

    // Get Armor OPTIONAL
    if(!empty($arrPlayerData['equipment']['armor'])) {
        $strArmorPath = './assets/img/armor/' . $arrPlayerData['equipment']['armor'] . '.png';
        $objArmorImg = imagecreatefrompng($strArmorPath);
        $objArmorImg = imgSetAlpha($objArmorImg, $numR, $numG, $numB);
        $objCharacter = imgMergeImages($objCharacter, $objArmorImg);
    }

    // Get Helmet OPTIONAL
    if(!empty($arrPlayerData['equipment']['helmet'])) {
        $strHelmetPath = './assets/img/helmet/' . $arrPlayerData['equipment']['helmet'] . '.png';
        $objHelmetImg = imagecreatefrompng($strHelmetPath);
        $objHelmetImg = imgSetAlpha($objHelmetImg, $numR, $numG, $numB);
        $objCharacter = imgMergeImages($objCharacter, $objHelmetImg);
    } else {
        // Get Hair OPTIONAL
        if(!empty($arrPlayerData['equipment']['hair'])) {
            $strHairPath = './assets/img/hair/' . $arrPlayerData['equipment']['hair'] . '.png';
            $objHairImg = imagecreatefrompng($strHairPath);
            $objHairImg = imgSetAlpha($objHairImg, $numR, $numG, $numB);
            $objCharacter = imgMergeImages($objCharacter, $objHairImg);
        }
    }

    return $objCharacter;
}

/**
 * Creates a Enemy Sprite
 * @param $strSrc
 * @param $numR
 * @param $numG
 * @param $numB
 * @return mixed|resource
 */
function imgCreateEnemy($strSrc, $numR, $numG, $numB)
{
    // Get Body
    $strBodyPath = './assets/img/enemy/' . $strSrc . '.png';
    $objBodyImg = imagecreatefrompng($strBodyPath);
    $objBodyImg = imgSetAlpha($objBodyImg, $numR, $numG, $numB);

    return $objBodyImg;
}

/**
 * This Creates the Character Screen
 * @param $arrPlayerData
 * @param $strBackground
 * @param $numR
 * @param $numG
 * @param $numB
 * @param string $strTitle
 * @param $numColor
 * @param string $numProgress
 * @return string
 */
function createScreenCharacter($arrPlayerData, $strBackground, $numR, $numG, $numB, $strTitle = '', $numColor = 0, $numProgress = '')
{
    // Create Player Character
    $objCharacter = imgCreateCharacter($arrPlayerData, $numR, $numG, $numB);

    // Image Background
    $strBackgroundPath = './assets/img/background/' . $strBackground . '.png';
    $objBackground = imagecreatefrompng($strBackgroundPath);

    // Add Progress and title to Background
    if($strTitle != '') {
        $arrTitle = explode(' ',$strTitle);
        if($arrTitle < 4) {
            $objBackground = imgAddProgressBar($objBackground, 160, $numProgress);
            $objBackground = imgAddTitle($objBackground, 174, 17, $strTitle, $numColor);
        } else {
            $strTitle_1 = '';
            $strTitle_2 = '';
            foreach($arrTitle as $numIndex => $strWord) {
                if($numIndex < 4) {
                    $strTitle_1 .= $strWord . ' ';
                } else {
                    $strTitle_2 .= $strWord . ' ';
                }
            }
            $objBackground = imgAddProgressBar($objBackground, 146, $numProgress);
            $objBackground = imgAddTitle($objBackground, 160, 17, $strTitle_1, $numColor);
            $objBackground = imgAddProgressBar($objBackground, 165, $numProgress);
            $objBackground = imgAddTitle($objBackground, 179, 17, $strTitle_2, $numColor);
        }
    }

    // Create Animation
    $objAnimatedGif = imgCreateAnimation($objCharacter, $objBackground, [7, 6, 7, 8], [20, 30, 20, 30], $numR, $numG, $numB);

    // Save Animation
    $strAnimatedGif = $objAnimatedGif->get();
    return $strAnimatedGif;
}

/**
 * This Creates the Character Screen
 * @param $arrPlayerData
 * @param $strBackground
 * @param $numR
 * @param $numG
 * @param $numB
 * @param $strTitle
 * @param $arrFight
 * @return string
 */
function createScreenCharacterFight($arrPlayerData, $strBackground, $numR, $numG, $numB, $strTitle, $arrFight)
{
    // Create Player Character
    $objCharacter = imgCreateCharacter($arrPlayerData, $numR, $numG, $numB);

    // Create Enemy Player
    $arrEnemyData = getEnemyData($arrFight['enemy_id']);
    $objEnemy = imgCreateEnemy($arrEnemyData['file_src'], $numR, $numG, $numB);
    $strEnemyName = ucfirst($arrEnemyData['name']);

    // Image Background
    $strBackgroundPath = './assets/img/background/' . $strBackground . '.png';
    $objBackground = imagecreatefrompng($strBackgroundPath);

    // Name Bar
    $objBackground = imgAddProgressBar($objBackground, 11, 0, 10, 120, 2);
    $objBackground = imgAddTitle($objBackground, 25, 20, $arrFight['action'], 1);
    $objBackground = imgAddProgressBar($objBackground, 11, 0, 140, 250, 2);
    $objBackground = imgAddTitle($objBackground, 25, 150, $strEnemyName, 1);

    // Progress Bar
    $objBackground = imgAddProgressBar($objBackground, 30, $arrFight['progress'], 10, 120, 3);
    $objBackground = imgAddTitle($objBackground, 44, 20, $arrFight['progress'] . ' %', 1);
    $objBackground = imgAddProgressBar($objBackground, 30, 100 - $arrFight['progress'], 140, 250, 3);
    $objBackground = imgAddTitle($objBackground, 44, 150, 100 - $arrFight['progress'] . ' %', 1);

    // Create Animation
    $objAnimatedGif = imgCreateFightAnimation($objCharacter, $objEnemy, $objBackground, [4, 3, 4, 5, 4, 3, 4, 5], [10, 9, 10, 11, 10, 9, 10, 11], [20, 30, 20, 30, 20, 30, 20, 30], $numR, $numG, $numB , $arrFight['progress']);

    // Save Animation
    $strAnimatedGif = $objAnimatedGif->get();
    return $strAnimatedGif;
}

/**
 * Creates the Stats Screen for a Player
 * @param $arrPlayerData
 * @return string
 */
function createScreenStatus($arrPlayerData, $arrConfig, $boolIsPlayer)
{
    // Image Background
    $strBackgroundPath = './assets/img/misc/pergament.jpg';
    $objImg = imagecreatefromjpeg($strBackgroundPath);
    $objImg = imagecrop($objImg, ['x' => 130, 'y' => 130, 'width' => 259, 'height' => 239]);

    $objColorBlack = getColorByCode(0, $objImg);
    $objColorBlue = getColorByCode(3, $objImg);
    $objColorRed = getColorByCode(2, $objImg);
    $objColorGreen = getColorByCode(4, $objImg);

    // Texte Generieren
    imagettftext($objImg, 20, 0, 20, 30, $objColorBlack, "./assets/font/Gamer.ttf", 'ATK');
    imagettftext($objImg, 20, 0, 80, 30, $objColorBlue, "./assets/font/Gamer.ttf", sprintf("%'04d", $arrPlayerData['attributes']['atk']));
    imagettftext($objImg, 20, 0, 130, 30, $objColorGreen, "./assets/font/Gamer.ttf", '+ 0');

    imagettftext($objImg, 20, 0, 20, 65, $objColorBlack, "./assets/font/Gamer.ttf", 'DEF');
    imagettftext($objImg, 20, 0, 80, 65, $objColorBlue, "./assets/font/Gamer.ttf", sprintf("%'04d", $arrPlayerData['attributes']['def']));
    imagettftext($objImg, 20, 0, 130, 65, $objColorGreen, "./assets/font/Gamer.ttf", '+ 0');

    imagettftext($objImg, 20, 0, 20, 100, $objColorBlack, "./assets/font/Gamer.ttf", 'MAT');
    imagettftext($objImg, 20, 0, 80, 100, $objColorBlue, "./assets/font/Gamer.ttf", sprintf("%'04d", $arrPlayerData['attributes']['mat']));
    imagettftext($objImg, 20, 0, 130, 100, $objColorGreen, "./assets/font/Gamer.ttf", '+ 0');

    imagettftext($objImg, 20, 0, 20, 135, $objColorBlack, "./assets/font/Gamer.ttf", 'MDE');
    imagettftext($objImg, 20, 0, 80, 135, $objColorBlue, "./assets/font/Gamer.ttf", sprintf("%'04d", $arrPlayerData['attributes']['mde']));
    imagettftext($objImg, 20, 0, 130, 135, $objColorGreen, "./assets/font/Gamer.ttf", '+ 0');

    imagettftext($objImg, 20, 0, 20, 170, $objColorBlack, "./assets/font/Gamer.ttf", 'LUK');
    imagettftext($objImg, 20, 0, 80, 170, $objColorBlue, "./assets/font/Gamer.ttf", sprintf("%'04d", $arrPlayerData['attributes']['luk']));
    imagettftext($objImg, 20, 0, 130, 170, $objColorGreen, "./assets/font/Gamer.ttf", '+ 0');

    // Points Calculation
    $numExp = intval($arrPlayerData['attributes']['exp']);
    $arrLevel = getPlayerLevel($numExp, $arrConfig);

    $numPoints = ($arrLevel['level'] * 10) + 15 + $arrLevel['level'];
    $numPoints -= intval($arrPlayerData['attributes']['atk']);
    $numPoints -= intval($arrPlayerData['attributes']['def']);
    $numPoints -= intval($arrPlayerData['attributes']['mat']);
    $numPoints -= intval($arrPlayerData['attributes']['mde']);
    $numPoints -= intval($arrPlayerData['attributes']['luk']);

    if($boolIsPlayer) {
        imagettftext($objImg, 25, 0, 20, 221, $objColorBlack, "./assets/font/Gamer.ttf", 'Points:');
        imagettftext($objImg, 25, 0, 105, 221, $objColorRed, "./assets/font/Gamer.ttf", $numPoints);
    }

    $strImgData = imgResourceToString($objImg);

    return $strImgData;
}

/**
 * Generating the Player Score Board based on the Experience a
 * Player has.
 * @param $strShowPlayer
 * @return string
 */
function createScreensScore($strShowPlayer)
{
    // Image Background
    $strBackgroundPath = './assets/img/misc/space.png';
    $objImg = imagecreatefrompng($strBackgroundPath);
    $objImg = imagecrop($objImg, ['x' => 0, 'y' => 0, 'width' => 259, 'height' => 239]);

    // Order By Exp
    $arrPlayers = getPlayerData();
    $arrSortArray = [];
    foreach($arrPlayers as $numIndex => $arrPlayer) {
        $numId = $arrPlayer['id'];
        $numExp = $arrPlayer['attributes']['exp'];
        $arrSortArray[$numId] = $numExp;
    }
    arsort($arrSortArray);

    // Only show the Top 10
    $numScore = 1;
    foreach($arrSortArray as $numId => $strValue) {
        if($numScore <= 10) {
            $arrSortArray[$numId] = $numScore;
        } else {
            unset($arrSortArray[$numId]);
        }
        $numScore++;
    }

    // Show Score Board
    foreach($arrPlayers as $arrPlayer) {
        // Nur wer in der Scorliste ist wird gezeigt
        if(isset($arrSortArray[$arrPlayer['id']])) {
            $numScore = $arrSortArray[$arrPlayer['id']];
        } else {
            continue;
        }

        $numPosition = sprintf("%'02d\n", $numScore);
        $numExp = sprintf("%' 10d\n", $arrPlayer['attributes']['exp']);

        $objColor = imagecolorallocate ($objImg, 255, 255, 255);
        if($arrPlayer['id'] == $strShowPlayer) {
            $objColor = imagecolorallocate ($objImg, 240, 250, 88);;
        }

        // Show Only the First 10 Players
        imagettftext($objImg, 17, 0, 20, 30 + ($numScore - 1) * 21,  $objColor, "./assets/font/Gamer.ttf", $numPosition);
        imagettftext($objImg, 17, 0, 60, 30 + ($numScore - 1) * 21, $objColor, "./assets/font/Gamer.ttf", ucfirst($arrPlayer['login']['name']));
        imagettftext($objImg, 17, 0, 160, 30 + ($numScore - 1) * 21, $objColor, "./assets/font/Gamer.ttf", $numExp);
    }
    $strImgData = imgResourceToString($objImg);

    return $strImgData;
}

/**
 * Generating the Player Score Board based on the Experience a
 * Player has.
 * @param $arrPlayerData
 * @return string
 */
function createScreensBadge($arrPlayerData)
{
    // Image Background
    $strBackgroundPath = './assets/img/misc/badge.jpg';
    $objImg = imagecreatefromjpeg($strBackgroundPath);
    $objImg = imagecrop($objImg, ['x' => 25, 'y' => 40, 'width' => 259, 'height' => 239]);

    // Unknown Badges
    $strUnknown = './assets/img/misc/unknown_badge.png';
    $objUnknown = imagecreatefrompng($strUnknown);
    //imgSetAlpha($objUnknown ,0 ,0 ,0);

    // Add Unknown Badges
    $arrBadges = generateEmptyBadgesArray($objUnknown);
    foreach($arrBadges as $numRow => $arrRow) {
        foreach($arrBadges as $numCol => $arrCol) {
            $numTop = (($numRow) * 46) + 13;
            $numLeft = (($numCol) * 40) + 13;
            imagecopymerge($objImg, $objUnknown, $numLeft, $numTop, 0, 0, 32, 32, 100);
        }
    }

    $strImgData = imgResourceToString($objImg);
    return $strImgData;
}

/**
 * Generiert the Player Equipment Screen
 * @param $arrPlayerData
 * @param $numR
 * @param $numG
 * @param $numB
 * @param $boolIsPlayer
 * @return string
 */
function createScreenEquipment($arrPlayerData, $numR, $numG, $numB, $boolIsPlayer)
{
    // Image Background
    $strBackgroundPath = './assets/img/misc/equip.jpg';
    $objImg = imagecreatefromjpeg($strBackgroundPath);
    $objImg = imagecrop($objImg, ['x' => 40, 'y' => 100, 'width' => 259, 'height' => 239]);

    // Empty Fields
    $objImg = addEquipmentField($objImg, 130, 100, 'armor');
    $objImg = addEquipmentField($objImg, 85, 180, 'shield');
    $objImg = addEquipmentField($objImg, 85, 20, 'weapon');
    $objImg = addEquipmentField($objImg, 20, 100, 'helmet');
    $objImg = addEquipmentField($objImg, 20, 35, 'hair');
    $objImg = addEquipmentField($objImg, 170, 100, 'boots');
    $objImg = addEquipmentField($objImg, 20, 165, 'bracelet');
    $objImg = addEquipmentField($objImg, 150, 20, 'ring');
    $objImg = addEquipmentField($objImg, 150, 180, 'ring');

    // Get Armor
    if(!empty($arrPlayerData['equipment']['armor'])) {
        $strArmorPath = './assets/img/armor/' . $arrPlayerData['equipment']['armor'] . '.png';
        $objArmorImg = imagecreatefrompng($strArmorPath);
        $objArmorImg = imgCreateFrame($objArmorImg, 7, $numR, $numG, $numB);
        $objArmorImg = imgSetAlpha($objArmorImg, $numR, $numG, $numB);
        $objImg = imgMergeImages($objImg, $objArmorImg, 62, 89);
    }

    // Get Helmet
    if(!empty($arrPlayerData['equipment']['helmet'])) {
        $strHelmetPath = './assets/img/helmet/' . $arrPlayerData['equipment']['helmet'] . '.png';
        $objHelmetImg = imagecreatefrompng($strHelmetPath);
        $objHelmetImg = imgCreateFrame($objHelmetImg, 7, $numR, $numG, $numB);
        $objHelmetImg = imgSetAlpha($objHelmetImg, $numR, $numG, $numB);
        $objImg = imgMergeImages($objImg, $objHelmetImg, 10, 89);
    }

    // Get Hair
    if(!empty($arrPlayerData['equipment']['hair'])) {
        $strHairPath = './assets/img/hair/' . $arrPlayerData['equipment']['hair'] . '.png';
        $objHairImg = imagecreatefrompng($strHairPath);
        $objHairImg = imgCreateFrame($objHairImg, 7, $numR, $numG, $numB);
        $objHairImg = imgSetAlpha($objHairImg, $numR, $numG, $numB);
        $objImg = imgMergeImages($objImg, $objHairImg, 3, 23);
    }

    // Get Shield
    if(!empty($arrPlayerData['equipment']['shield'])) {
        $strShieldPath = './assets/img/shield/' . $arrPlayerData['equipment']['shield'] . '.png';
        $objShieldImg = imagecreatefrompng($strShieldPath);
        $objShieldImg = imgCreateFrame($objShieldImg, 7, $numR, $numG, $numB);
        $objShieldImg = imgSetAlpha($objShieldImg, $numR, $numG, $numB);
        $objImg = imgMergeImages($objImg, $objShieldImg, 38, 154);
    }

    // Get Weapon
    if(!empty($arrPlayerData['equipment']['weapon'])) {
        $strWeaponPath = './assets/img/weapon/' . $arrPlayerData['equipment']['weapon'] . '.png';
        $objWeaponImg = imagecreatefrompng($strWeaponPath);
        $objWeaponImg = imgCreateFrame($objWeaponImg, 7, $numR, $numG, $numB);
        $objWeaponImg = imgSetAlpha($objWeaponImg, $numR, $numG, $numB);
        $objImg = imgMergeImages($objImg, $objWeaponImg, 53, 10);
    }

    // TODO: Ringe Bracelet und Boots

    $strImgData = imgResourceToString($objImg);
    return $strImgData;
}

/**
 * Generiert the Player Fight Screen
 * @param $arrFight
 * @param $arrConfig
 * @param $strStatus
 * @return string
 * @internal param $arrPlayerData
 */
function createScreenFight($arrFight, $arrConfig, $strStatus, $arrQuest)
{
    // Image Background
    $strBackgroundPath = './assets/img/misc/quest.jpg';
    $objImg = imagecreatefromjpeg($strBackgroundPath);
    $objImg = imagecrop($objImg, ['x' => 600, 'y' => 400, 'width' => 259, 'height' => 239]);

    if($arrFight['progress'] == 0) {
        if($strStatus == 'idle') {
            $objImg = imgAddProgressBar($objImg, 100, 0, 10, 245, 2);
            $objImg = imgAddTitle($objImg, 115, 20, $arrConfig['no_quest_message_title'], 1);
        }

    } else {
        if($arrQuest['progress'] == 100) {
            // You and Enemy label
            $objImg = imgAddProgressBar($objImg, 11, 0, 10, 122, 2);
            $objImg = imgAddTitle($objImg, 25, 20, 'YOU WINS', 1);

            $arrEnemyData = getEnemyData($arrFight['enemy_id']);
            $strEnemyName = ucfirst($arrEnemyData['name']);

            $objImg = imgAddProgressBar($objImg, 11, 0, 134, 247, 2);
            $objImg = imgAddTitle($objImg, 25, 144, $strEnemyName, 1);
        }
    }


    $strImgData = imgResourceToString($objImg);
    return $strImgData;
}

/**
 * Generiert the Player Shop Screen
 * @param $arrPlayerData
 * @return string
 */
function createScreenShop($arrPlayerData)
{
    // Image Background
    $strBackgroundPath = './assets/img/misc/shop.jpg';
    $objImg = imagecreatefromjpeg($strBackgroundPath);
    $objImg = imagecrop($objImg, ['x' => 600, 'y' => 400, 'width' => 259, 'height' => 239]);

    $strImgData = imgResourceToString($objImg);
    return $strImgData;
}

/**
 * Adds a Equipment Box to a Screen Image
 * @param $objImg
 * @param $numTop
 * @param $numLeft
 * @param $strPlaceholderImg
 * @return mixed
 */
function addEquipmentField($objImg, $numTop, $numLeft, $strPlaceholderImg) {

    $objColorGray = imagecolorallocate ($objImg, 87, 84, 81);
    $objColorWhite = imagecolorallocate ($objImg, 255, 255, 255);

    if($strPlaceholderImg == 'armor') {
        imagefilledrectangle($objImg , $numLeft - 2, $numTop - 52, $numLeft + 50, $numTop + 30, $objColorWhite);
        imagefilledrectangle($objImg , $numLeft, $numTop - 50, $numLeft + 48, $numTop + 28, $objColorGray);
    } else {
        imagefilledrectangle($objImg , $numLeft - 2, $numTop - 2, $numLeft + 50, $numTop + 50, $objColorWhite);
        imagefilledrectangle($objImg , $numLeft, $numTop, $numLeft + 48, $numTop + 48, $objColorGray);
    }

    if($strPlaceholderImg != '') {
        $strPath = './assets/img/misc/placeholder_' . $strPlaceholderImg . '.jpg';
        $objImgPlaceholder =imagecreatefromjpeg($strPath);
        $numWidth = imagesx($objImgPlaceholder);
        $numHeight = imagesy($objImgPlaceholder);

        if($strPlaceholderImg == 'armor') {
            imagecopymerge($objImg, $objImgPlaceholder, $numLeft + 5, $numTop - 15, 0, 0, $numWidth, $numHeight, 100);
        } else {
            imagecopymerge($objImg, $objImgPlaceholder, $numLeft + 5, $numTop + 5, 0, 0, $numWidth, $numHeight, 100);
        }
    }

    return $objImg;
}

/**
 * Returns a Clean Screen Array
 * @return array
 */
function generateClearScreenArray()
{
    // Global Screen Dimensions
    $numMaxRows = 3;
    $numMaxCols = 1;

    // Build Clear Screen
    $strEmptyData = file_get_contents('./assets/img/misc/empty.gif');
    $arrClearScreen = [];
    for($numRows = 0; $numRows < $numMaxRows; $numRows++) {
        for($numCols = 0; $numCols < $numMaxCols; $numCols++) {

            $arrClearScreen[$numRows][$numCols] = $strEmptyData;
        }
    }

    return $arrClearScreen;
}

/**
 * Returns Clear Badge Array
 * @param $objImg
 * @return array
 */
function generateEmptyBadgesArray($objImg)
{
    $numHeight = 5;
    $numWidth = 6;

    $arrClearBadges = [];
    for($numRow = 0; $numRow < $numWidth; $numRow++) {
        for($numCol = 0; $numCol < $numHeight; $numCol++) {
            $arrClearBadges[$numRow][$numCol] = $objImg;
        }
    }

    return $arrClearBadges;
}

/**
 * Renders the Game in MarkDown
 * @param $strPlayerId
 * @param bool $boolHtmlCopy
 * @return string
 */
function renderScreenArrayToMarkDown($strPlayerId, $boolHtmlCopy = false)
{
    $arrScreenArray = generateClearScreenArray();

    // Get Base Url
    $arrConfig = getAppConfig();
    $strBaseUrl = $arrConfig['base_url'];

    // Generate Markdon Code
    $strMarkDown = "";
    foreach($arrScreenArray as $numRow => $arrCols) {
        foreach($arrCols as $numCol => $strImage) {
            $strImgKey = $numRow . '-' . $numCol;
            $strImg = $strBaseUrl . 'img.php?img=' . $strImgKey . '&player=' . $strPlayerId;
            $strLink = $strBaseUrl . 'screen.php?src=ext&img=score&player=' . $strPlayerId;
            $strMarkDown .= "[![](" . $strImg . ")](" . $strLink . ") ";
        }
        if(!$boolHtmlCopy) {
            $strMarkDown .= "\n";
        } else {
            $strMarkDown .= "<br>";
        }
    }

    return $strMarkDown;
}

/**
 * Renders the Game in Html
 * @return string
 */
function renderScreenArrayToHtml()
{
    $arrScreenArray = generateClearScreenArray();
    // renderd alle images aus dem ClearScreen in einen MarkDown Array so das das komplette spiel laufen würde
    $strHtml = '';
    return $strHtml;
}

/**
 * This returns the Players Level by its Exp Points
 * @param $numExp
 * @param $arrConfig
 * @return array
 */
function getPlayerLevel($numExp, $arrConfig)
{
    $numExpFactor = $arrConfig['exp_factor'];
    $numExpBase = $arrConfig['exp_base'];

    $arrReturn = ['level' => '', 'nextlevel_exp' => ''];
    $numLastLexelExp = -1;
    for($numLevel = 1; $numLevel <= 100; $numLevel++) {
        $numNextLevelExp = floor($numExpBase * pow($numLevel, $numExpFactor)) - 1;

        if($numExp > $numLastLexelExp && $numExp <= $numNextLevelExp) {
            $arrReturn = ['level' => $numLevel, 'nextlevel_exp' => $numNextLevelExp + 1];
            return $arrReturn;
        }
        $numLastLexelExp = $numNextLevelExp;
    }
    if($numExp > $numLastLexelExp) {
        return $arrReturn;
    }
}

/**
 * This Returns the Players Ranking by its Level
 * @param $numLevel
 * @param $arrCharacter
 * @return string
 */
function getPlayerRanking($numLevel, $arrCharacter)
{
    $strData = file_get_contents("./data/ranking.json");
    $arrData = json_decode($strData, true);

    $strRanking = 'Player';
    $strGender = $arrCharacter['character']['gender'];

    foreach($arrData as $arrReord) {
        $numFromLevel = intval($arrReord['from_level']);
        $numToLevel = intval($arrReord['to_level']);

        if($numLevel >= $numFromLevel && $numLevel < $numToLevel) {
            $strRanking = $arrReord['ranking_' . $strGender];
        }
    }
    return $strRanking;

}

/**
 * Mit der Player nummer ermitteln in welchem Status der Player sich befindet
 * und wenn in einem Quest dann in welchem
 * @param $strShowPlayer
 * @return array
 */
function getPlayerScreenStatus($strShowPlayer)
{
    $strStatus = 'idle';
    $arrScreenQuest = [];
    $arrQuestData = [];
    $arrPlayerData = getPlayerData($strShowPlayer);
    if(isset($arrPlayerData['id'])) {
        foreach($arrPlayerData['quest'] as $arrQuest) {
            // Quest Process Rendering
            if($arrQuest['status'] == "running") {
                $strStatus = "quest";
                $arrScreenQuest = $arrQuest;
            }

            // Quest Boss Fight Rendering
            if($arrQuest['status'] == "fight") {
                $strStatus = "fight";
                $arrScreenQuest = $arrQuest;
            }

            // Quest Info ermitteln
            $numQuestId = $arrScreenQuest['quest_id'];
            $arrQuestData = getQuestData($numQuestId);

        }
    }

    $arrReturn = [
        'player' => $arrPlayerData,
        'status' => $strStatus,
        'player_quest' => $arrScreenQuest,
        'quest_data' => $arrQuestData
    ];

    return $arrReturn;
}

/**
 * Returns a Unique MD5 id
 * @return string
 */
function getUid()
{
    $strUid = md5(uniqid(rand(), true));
    return $strUid;
}

/**
 * Get Parameters from $_GET Request
 * @param $strName
 * @param $strDefault
 * @return mixed
 */
function getParam($strName, $strDefault)
{
    if(isset($_GET[$strName])) {
        return $_GET[$strName];
    } else {
        return $strDefault;
    }
}

/**
 * Get Parameters from $_POST Request
 * @param $strName
 * @param $strDefault
 * @return mixed
 */
function getPost($strName, $strDefault)
{
    if(isset($_POST[$strName])) {
        return $_POST[$strName];
    } else {
        return $strDefault;
    }
}

/**
 * Get Cookie Values from Cookie Array
 * @param $strName
 * @param $strDefault
 * @return mixed
 */
function getCookie($strName, $strDefault)
{
    if(isset($_COOKIE[$strName])) {
        return $_COOKIE[$strName];
    } else {
        return $strDefault;
    }
}

/**
 * Image Resource to String
 * @param $objImgResource
 * @return string
 */
function imgResourceToString($objImgResource)
{
    $strRandFile = './temp/' . getUid() . '.png';
    imagegif($objImgResource, $strRandFile);
    $strContentData = file_get_contents($strRandFile);
    unlink($strRandFile);
    imagedestroy($objImgResource);

    return $strContentData;
}

/**
 * Image Data to Base64 HTML Embeded Image
 * @param $strImgData
 * @param string $strCustom
 * @return string
 */
function imgStringToHtmlImg($strImgData, $strCustom = '')
{
    $strImgData = base64_encode($strImgData);
    $strImgString = '<img alt="" src="data:image/png;base64,' . $strImgData .'" ' . $strCustom . ' />';
    return $strImgString;
}

/**
 * Clones a Image Resource Object
 * @param $objImg
 * @return resource
 */
function imgCloneResource($objImg)
{
    $numWidth = imagesx($objImg);
    $numHeight = imagesy($objImg);
    $objTransparent = imagecolortransparent($objImg);

    if (imageistruecolor($objImg)) {
        $objClone = imagecreatetruecolor($numWidth, $numHeight);
        imagealphablending($objClone, false);
        imagesavealpha($objClone, true);

    } else {
        $objClone = imagecreate($numWidth, $numHeight);
        if($objTransparent >= 0) {
            $rgb = imagecolorsforindex($objImg, $objTransparent);
            imagesavealpha($objClone, true);
            $trans_index = imagecolorallocatealpha($objClone, $rgb['red'], $rgb['green'], $rgb['blue'], $rgb['alpha']);
            imagefill($objClone, 0, 0, $trans_index);
        }
    }

    //Create the Clone!!
    imagecopy($objClone, $objImg, 0, 0, 0, 0, $numWidth, $numHeight);
    return $objClone;
}

/**
 * Gibt den Item Array zurück der angibt wie viel gewinn mann mit einem Quest machen kann.
 * @param $arrPlayerStatus
 * @param $isPlayer
 * @return array
 */
function getQuestRewards($arrPlayerStatus, $boolIsPlayer)
{
    // Get Player Level
    $numLevel = getPlayerLevel($arrPlayerStatus['player']['attributes']['exp'], getAppConfig())['level'];

    // Get The Players and Enemy Percent
    $numPercent = $arrPlayerStatus['player_quest']['fight']['progress'];

    // Split Gold and Experience
    $numGold = ($arrPlayerStatus['quest_data']['gold'] * $numLevel) / 100 * $numPercent;
    $numExp = ($arrPlayerStatus['quest_data']['exp'] * $numLevel) / 100 * $numPercent;
    if(!$boolIsPlayer) {
        $numGold = ($arrPlayerStatus['quest_data']['gold'] * $numLevel) - $numGold;
        $numExp =  ($arrPlayerStatus['quest_data']['exp'] * $numLevel) - $numExp;
    }

    // Generate Info Array
    $arrInformation = [];
    $arrInformation[] = ['img' => '', 'value' => $numGold, 'data' => 'gold'];
    $arrInformation[] = ['img' => '', 'value' => $numExp, 'data' => 'exp'];

    // Get all the Reward Items
    foreach($arrPlayerStatus['quest_data']['rewards'] as $arrReward) {
        if($boolIsPlayer) {
            if($arrReward['percent'] <= $numPercent) {
                $strFile = 'assets/img/' . $arrReward['type'] . '/' . $arrReward['img_src'] . '.png';
                $arrInformation[] = ['img' => $strFile, 'value' => $arrReward['type'], 'data' => $arrReward['name']];
            }
        } else {
            if($arrReward['percent'] > $numPercent) {
                $strFile = 'assets/img/' . $arrReward['type'] . '/' . $arrReward['img_src'] . '.png';
                $arrInformation[] = ['img' => $strFile, 'value' => $arrReward['type'], 'data' => $arrReward['name']];
            }
        }
    }

    // Generate HTML Output
    $strHtml = '';
    foreach($arrInformation as $arrRecord) {
        $strToolTip = $arrRecord['data'];
        if($arrRecord['data'] == 'gold' || $arrRecord['data'] == 'exp') {
            $strToolTip = ucfirst($arrRecord['data']) . ': ' . round($arrRecord['value'], 0);
        }
        $strHtml .= '<div style="font-family: arial; margin: 10px; margin-left: 15px;" class="reward" data-ot="' . $strToolTip . '">';
        $strHtml .= imgStringToHtmlImg(getRewardImage($arrRecord['img'], $arrRecord['value'], $arrRecord['data']));
        $strHtml .= '</div>';
    }

    return $strHtml;
}

/**
 * Generates The Reward image for the Screen
 * @param $strImagePath
 * @param $strType
 * @param $strData
 * @return string
 */
function getRewardImage($strImagePath, $strType, $strData)
{
    $arrItem = getItemPositionCorrection($strType);
    $strBackgroundPath = './assets/img/misc/equip.jpg';
    $objImg = imagecreatefromjpeg($strBackgroundPath);
    $objImg = imagecrop($objImg, ['x' => 40, 'y' => 100, 'width' => 71, 'height' => 96]);

    if($strImagePath != '') {
        $objItem = imagecreatefrompng($strImagePath);
        $objItem = imgCreateFrame($objItem, $arrItem['frame'], 32, 156, 0);
        $objItem = imgSetAlpha($objItem, 32, 156, 0);
        $objImg = imgMergeImages($objImg, $objItem, $arrItem['y'], $arrItem['x']);
    } else {
        // Font Farbe
        $objColor = getColorByCode(1, $objImg);

        // Text
        if($strData == 'gold') {
            $numValue = sprintf("%' 5d\n", round($strType, 0));
            imagettftext($objImg, 16, 0, 5, 60, $objColor, "./assets/font/Gamer.ttf", $numValue);
            $strType = 'Gold';
        }
        if($strData == 'exp') {
            $numValue = sprintf("%' 5d\n", round($strType, 0));
            imagettftext($objImg, 16, 0, 5, 60, $objColor, "./assets/font/Gamer.ttf", $numValue);
            $strType = 'EXP';
        }

    }

    // Add Title
    if(!empty($arrItem)) {
        $strType = $arrItem['text'];
    }
    $objImg = imgAddProgressBar($objImg, 0, 0, 0, 70);
    $objImg = imgAddTitle($objImg, 15, 5, $strType, 1);
    $strImage = imgResourceToString($objImg);

    return $strImage;
}

/**
 * Gibt die Item Korrektur Position an
 * @param $strType
 * @return array
 */
function getItemPositionCorrection($strType)
{
    $arrCorrection = [];
    switch($strType) {
        case 'armor':
            $arrCorrection['x'] = 0;
            $arrCorrection['y'] = 0;
            $arrCorrection['text'] = 'Rüstung';
            $arrCorrection['frame'] = 7;
            break;
        case 'boots':
            $arrCorrection['x'] = 0;
            $arrCorrection['y'] = 0;
            $arrCorrection['text'] = 'Stiefel';
            $arrCorrection['frame'] = 0;
            break;
        case 'weapon':
            $arrCorrection['x'] = 18;
            $arrCorrection['y'] = 0;
            $arrCorrection['text'] = 'Waffe';
            $arrCorrection['frame'] = 7;
            break;
        case 'shield':
            $arrCorrection['x'] = -15;
            $arrCorrection['y'] = -10;
            $arrCorrection['text'] = 'Schild';
            $arrCorrection['frame'] = 7;
            break;
        case 'helmet':
            $arrCorrection['x'] = 0;
            $arrCorrection['y'] = 10;
            $arrCorrection['text'] = 'Helm';
            $arrCorrection['frame'] = 7;
            break;
        case 'ring':
            $arrCorrection['x'] = 0;
            $arrCorrection['y'] = 0;
            $arrCorrection['text'] = 'Ring';
            $arrCorrection['frame'] = 0;
            break;
        case 'amulet':
            $arrCorrection['x'] = 0;
            $arrCorrection['y'] = 0;
            $arrCorrection['text'] = 'Amulett';
            $arrCorrection['frame'] = 0;
            break;
    }

    return $arrCorrection;
}

/**
 * Get a Link for some action
 * @param $strContent
 * @param $strScreen
 * @param $strShowPlayer
 * @return string
 */
function getScreenLink($strContent, $strScreen, $strShowPlayer)
{
    $strLink = '<a href="screen.php?src=int&img=' . $strScreen . '&player=' . $strShowPlayer . '">' . $strContent . '</a>';
    return $strLink;
}

function getColorByCode($numColor = 0, $objImg)
{
    // Set Black as Default
    $objColor = imagecolorallocate ($objImg, 0, 0, 0);

    switch($numColor) {
        case 1: // White
            $objColor = imagecolorallocate ($objImg, 255, 255, 255);
            break;

        case 2: // Red
            $objColor = imagecolorallocate ($objImg, 240, 5, 5);
            break;

        case 3: // Blue
            $objColor = imagecolorallocate ($objImg, 4, 95, 180);
            break;

        case 4: // Green
            $objColor = imagecolorallocate ($objImg, 11, 97, 33);
            break;

        case 5: // Orange
            $objColor = imagecolorallocate ($objImg, 255, 128, 0);
            break;

        case 6: // Light Green
            $objColor = imagecolorallocate ($objImg, 0, 255, 0);
            break;

        case 7: // Light Red
            $objColor = imagecolorallocate ($objImg, 250, 88, 88);
            break;
    }
    return $objColor;
}