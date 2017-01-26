
<?php

/**
 * Ugly Jenkins Script
 * Sorry for poor dokumentation
 * in the end its the $arrWebHook that matters
 * this is the correct format :-D
 */

// Config
$strPath = '/var/lib/jenkins/jobs/OmegaSix-CommitBuild/builds';
//$strPath = 'jobs/OmegaSix-CommitBuild/builds';

$arrBuilds = scandir($strPath);
$numCommit = 0;
echo "[game] RUN INTERFACE\n";
foreach($arrBuilds as $numBuild) {
    if(intval($numBuild) > $numCommit) {
        $numCommit = intval($numBuild);
    }
}
echo "[game] Found Commit: " . $numCommit . "\n";
$strPath .= '/' . $numCommit;

// Alle FileNames Ermitteln
echo "[game] Generate File Names \n";
$strDirRepport = '/';
$strFileChangelog = $strPath . '/changelog.xml';
$strFileCheckStyle = $strPath . '/checkstyle-warnings-fixed.xml';
$strFilePhpUnit = $strPath . '/junitResult.xml';

// Git Change Log auslesen
echo "[game] Process Git Change Log\n";
$strChangeLog = file_get_contents($strFileChangelog);
if($strChangeLog == false) {
    echo "[game] Change Log not Found\n";
    echo "[game] QUIT SCRIPT\n";
    die;
}

echo "\n[game] Detect Changed Files\n";
$arrChangeLog = explode("\n", $strChangeLog);
$arrChangedFiles = [];
foreach($arrChangeLog as $numLine => $strRecord) {
    if($numLine > 6) {
        if(!empty($strRecord)) {
            $arrRecord = explode(' ', $strRecord);
            $strChangeFile = substr($arrRecord[4], 2);
            $strFileHash = md5($strChangeFile);
            $arrChangedFiles[$strFileHash] = $strChangeFile;
            echo "[game] Found Changed File: " . $strChangeFile . "\n";
        }
    }
}

// Commit Quest Reference Auslesen (TAG)
echo "\n[game] Get commit Data\n";
$strCommitter = $arrChangeLog[4];
$strMessage = $arrChangeLog[6];

// Nach der Quest Referenz Suchen
echo "[game] Detect Quest Reference\n";
$strQuestReference = '';
if(strpos($strMessage, '#quest_') != false) {
    $numPos = strpos($strMessage, '#quest_');
    $strQuestReference = substr($strMessage, $numPos, 30);
    echo "[game] Quest Reference found: " . $strQuestReference . "\n";
} else {
    echo "[game] Quest Reference not found!\n";
    echo "[game] QUIT SCRIPT\n";
    die;
}

// Alle geänderten Files ermitteln
echo "[game] Detect changes\n";
$arrFiles = [];
foreach($arrChangeLog as $numLine => $strRecord) {
    if($numLine > 6 && strlen($strRecord) > 10) {
        $strFileName = explode(' ', $strRecord)[4];
        $arrFiles[] = substr($strFileName, 2);
        echo "[game] Change Found: " . substr($strFileName, 2) . "\n";
    }
}

// Arrays für Repporting
echo "[game] Create Repporting Arrays\n\n";
$arrJsonData = [];
$arrQuestData = [];
$arrFightData = [];

// Check PHP Unit Repport auslesen
echo "[game] Process PHPUnit Repport\n";
$strXmlData = file_get_contents($strFilePhpUnit);
$objXml = new SimpleXMLElement($strXmlData);
$numQuestTotal = 0;
foreach($objXml->suites[0]->suite[0]->cases as $objCases) {
    foreach($objCases as $objCase) {
        $strClass = (string) $objCase->className;
        $strMethode = (string) $objCase->testName;
        $strErrorMsg = (string) $objCase->errorStackTrace;

        if(!empty($strErrorMsg)) {
            $arrError = explode("\n", $strErrorMsg);
            $arrCheckin = [];
            $arrCheckin['title'] = 'UnitTest: Error';
            $arrCheckin['file'] = $arrError[1];
            $arrCheckin['message'][] = $strErrorMsg;
            $arrCheckin['message'][] = $strClass . ':' . $strMethode;
            echo "[game] Found PHPUnit Error: " . $strClass . ":"  . $strMethode . "\n";
            $arrJsonData[] = $arrCheckin;
            $arrQuestData[] = $arrCheckin;
        }
        $numQuestTotal++;
    }
}
echo "[game] PHPUnit Error: " . $numQuestError . "\n";
echo "[game] PHPUnit Success: " . $numQuestSuccess . "\n";

// Check Style Repport auslesen aus dem Ordner voilations file
// Nur die die auch im Release sind
echo "\n[game] Process CheckStyle Repport\n";
$strXmlData = file_get_contents($strFileCheckStyle);
$objXml = new SimpleXMLElement($strXmlData);
foreach($objXml as $objRecord) {
    $strFilePath = str_replace('/var/lib/jenkins/workspace/OmegaSix-CommitBuild/', '', $objRecord->fileName);
    $strMessage = (string) $objRecord->message;
    $arrCheckin = [];
    $numBefore = count($arrFightData) - 1;

    if(empty($strFilePath)) {
        $strFilePath = $arrFightData[$numBefore]['file'];
    }
    if(empty($strMessage)) {
        $strMessage = $arrFightData[$numBefore]['message'][0];
    }

    $arrCheckin['title'] = 'CheckStyle: ' . $objRecord->type . ' - ' . $objRecord->category;
    $arrCheckin['file'] = $strFilePath;
    $arrCheckin['message'][] = $strMessage;
    $arrCheckin['message'][] = $strFilePath . ':' . $objRecord->primaryLineNumber;
    echo "[game] Found CheckStyle Warning: " . 'Bug: (' . $objRecord->type . ' - ' . $objRecord->category . ')' . "\n";
    $arrJsonData[] = $arrCheckin;
    $arrFightData[] = $arrCheckin;
}

// Read Last File and addQuest Files
$strSaveFile = '/var/www/html/' . substr($strQuestReference, 1) . '.json';
//$strSaveFile = './' . $strQuestReference . '.json';
if(file_exists($strSaveFile)) {
    $strLogData = file_get_contents($strSaveFile);
    $arrLastFile = json_decode($strLogData, true);
    $numCheckin = $arrLastFile['checkin_counter'] + 1;
    $numMaxFight = $arrLastFile['max_fight_data'];
    if(empty($arrLastFile['quest_files'])) {
        $arrQuestFiles = $arrChangedFiles;
    } else {
        $arrQuestFiles = array_merge($arrLastFile['quest_files'], $arrChangedFiles);
    }
} else {
    $arrQuestFiles = $arrChangedFiles;
    $numCheckin = 1;
    $numMaxFight = 0;
}

// Quest File Checker
foreach ($arrQuestData as $numIndex => $arrMessage) {
    $boolFound = false;
    foreach($arrQuestFiles as $strFile) {
        if($arrMessage['file'] == $strFile) {
            $boolFound = true;
        }
    }
    if(!$boolFound) {
        unset($arrQuestData[$numIndex]);
    }
}


// Fight File Checker
foreach ($arrFightData as $numIndex => $arrMessage) {
    $boolFound = false;
    foreach($arrQuestFiles as $strFile) {
        if($arrMessage['file'] == $strFile) {
            $boolFound = true;
        }
    }
    if(!$boolFound) {
        unset($arrFightData[$numIndex]);
    }
}
if(count($arrFightData) > $numMaxFight) {
    $numMaxFight = count($arrFightData);
}

// Call WebHook
echo "[game] Create Publish Data Package\n";
$arrWebHook = [];
$arrWebHook['quest_id'] = $strQuestReference;
$arrWebHook['quest_files'] = $arrQuestFiles;
$arrWebHook['checkin_counter'] = $numCheckin;
$arrWebHook['report_header'] = $strCommitter;
$arrWebHook['total_quest_data'] = $numQuestTotal;
$arrWebHook['quest_data'] = $arrQuestData;
$arrWebHook['total_fight_data'] = $numMaxFight;
$arrWebHook['fight_data'] = $arrFightData;

// Save File to WebServer
echo "[game] Save File to Server and save for Pulling\n";
$strFile = json_encode($arrWebHook);
file_put_contents($strSaveFile, $strFile);

echo "[game] FINISH INTERFACE\n\n";



