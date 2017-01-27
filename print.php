<?php

// Include
include_once("assets/lib/global_functions.php");
include_once("assets/lib/fpdf/fpdf.php");
include_once("assets/lib/fpdf/rapport_print.php");
include_once("interface/jenkins_import.php");

// Get Quest To Print
$strQuestId = getParam('quest', '');

// Quest Info vom Jenkis Server laden
$strJenkinsServer = 'http://138.197.90.62/';
$strFile = $strJenkinsServer . $strQuestId . '.json';
$strImportFile = file_get_contents($strFile);
if($strImportFile == false) {
    die;
}

// Jenkins Quest Status Berechnen
$arrImport = json_decode($strImportFile, true);
$numPercentQuest = round(100 - (100 / intval($arrImport['total_quest_data']) * count($arrImport['quest_data'])), 2);
$numPercentFight = round(100 - (100 / intval($arrImport['total_fight_data']) * count($arrImport['fight_data'])), 2);

// Print PDF Rapport
printRapport($arrImport['quest_data'], $arrImport['fight_data'], $numPercentQuest, $numPercentFight);
