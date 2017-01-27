<?php

/**
 * This is the Importer File For Jenkins CI
 * Todo: Please change the IP of your Jenkins Server
 * @param $arrPlayerStatus
 * @return mixed
 */
function questImportData($arrPlayerStatus)
{
    // Questing Percent Calculation
    if($arrPlayerStatus['status'] == 'quest' || $arrPlayerStatus['status'] == 'fight') {
        // Quest Info vom Jenkis Server laden
        $strQuestId = $arrPlayerStatus['player_quest']['quest_id'];
        $strJenkinsServer = 'http://138.197.90.62/';
        $strFile = $strJenkinsServer . $strQuestId . '.json';
        $strImportFile = file_get_contents($strFile);
        if($strImportFile == false) {
            return $arrPlayerStatus;
        }

        // Jenkins Quest Status Berechnen
        $arrImport = json_decode($strImportFile, true);
        $numQuestTotal = $arrImport['total_quest_data'];
        $numPercent = 100 - (100 / $numQuestTotal * count($arrImport['quest_data']));
        if($numPercent <= 0) {$numPercent = 0;}
        if($numPercent >= 100) {
            $arrPlayerStatus['player_quest']['status'] = "fight";
            $arrPlayerStatus['player_quest']['progress'] = 100;
            $boolFight = true;
        } else {
            $arrPlayerStatus['player_quest']['status'] = "running";
            $arrPlayerStatus['player_quest']['progress'] = round($numPercent, 2);
            $boolFight = false;
        }

        // Jenkins Fight Status Berechnen
        if($boolFight) {
            $numFightTotal = $arrImport['total_fight_data'];
            $numPercent = 100 - (100 / $numFightTotal * count($arrImport['fight_data']));
            $arrPlayerStatus['player_quest']['fight']['progress'] = round($numPercent, 2);
            $arrPlayerStatus['player_quest']['fight']['action'] = 'Versuch: ' . $arrImport['checkin_counter'];
        }

        // Write changes
        $strData = file_get_contents("./data/player.json");
        $arrData = json_decode($strData, true);

        foreach($arrData as $numPlayer => $arrRecord) {
            if($arrRecord['id'] == $arrPlayerStatus['player']['id']) {
                foreach($arrRecord['quest'] as $strQuest => $arrQuest) {
                    if($arrQuest['quest_id'] == $arrPlayerStatus['player_quest']['quest_id']) {
                        $arrData[$numPlayer]['quest'][$strQuest] = $arrPlayerStatus['player_quest'];
                    }
                }
            }
        }

        // Save Player Data
        $strEncode = json_encode($arrData);
        if($strEncode != false) {
            $boolSave = file_put_contents("./data/player.json", $strEncode);
            if(!$boolSave) {
                file_put_contents("./data/player.json", $strData);
            }
        }

    }

    return $arrPlayerStatus;
}
