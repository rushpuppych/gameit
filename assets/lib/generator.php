<?php

function generateItemName($strItemType)
{
	// Set Pattern
	$arrPatterns[] = ['was_zusatz_1', 'was_name', 'von', 'wer_name', 'wer_zusatz_2', 'wie_adjektiv_1'];
	$arrPatterns[] = ['was_zusatz_1', 'was_name', 'wer_zusatz_1', 'wer_name'];
	$arrPatterns[] = ['was_zusatz_1', 'was_name', 'wer_zusatz_1', 'wie_adjektiv_1', 'wer_name'];
	$arrPatterns[] = ['was_zusatz_1', 'wie_adjektiv_2', 'was_name', 'wer_zusatz_1', 'wie_adjektiv_1', 'wer_name'];
	$arrPatterns[] = ['was_zusatz_1', 'aus_material', 'was_name', 'von', 'wer_name', 'wer_zusatz_2', 'wie_adjektiv_1'];
	$arrPatterns[] = ['was_zusatz_1', 'aus_material', 'was_name', 'wer_zusatz_1', 'wer_name'];
	$arrPatterns[] = ['was_zusatz_1', 'aus_material', 'was_name', 'wer_zusatz_1', 'wie_adjektiv_1', 'wer_name'];
	$arrPatterns[] = ['was_zusatz_1', 'wie_adjektiv_2', 'aus_material', 'was_name', 'wer_zusatz_1', 'wie_adjektiv_1', 'wer_name'];
	$arrPatterns[] = ['aus_material', 'was_name'];
	
	// Build String
	$strReturn = patternFiller($arrPatterns, $strItemType);
	return $strReturn;
}

function generateQuestName($strQuestType)
{	
	// Set Pattern
	$arrPatterns[] = ['was_zusatz_1', 'wie_adjektiv_2', 'was_name'];
	$arrPatterns[] = ['was_zusatz_1', 'wie_adjektiv_2', 'was_name', 'wo_zusatz_1', 'wo_ortschaft'];
	$arrPatterns[] = ['was_zusatz_1', 'wie_adjektiv_2', 'was_name', 'wer_zusatz_1', 'wer_name'];
	$arrPatterns[] = ['was_zusatz_1', 'was_name'];
	
	// Build String
	$strReturn = patternFiller($arrPatterns, 'quest', $strQuestType);
	return $strReturn;	
}


function patternFiller($arrPatterns, $strType, $strSubType = '')
{	
	// Get Random Pattern
	$numRandom = rand(0, count($arrPatterns) - 1);
	$arrPattern = $arrPatterns[$numRandom];	

    // Pattern Word Generator
    do {
        $boolAllFound = true;
        $arrWords = getRandomWords($arrPatterns, $strType, $strSubType);
        foreach($arrPattern as $strKey) {
            if(strpos($strKey, '_') > 0) {
                if(!isset($arrWords[$strKey])) {
                    $boolAllFound = false;
                }
            }
        }
    } while(!$boolAllFound);

	// Fill Pattern
	$strReturn = '';
	foreach($arrPattern as $strKey) {
		$arrKey = explode('_', $strKey);
		if(count($arrKey) > 1) {
            $strReturn .= $arrWords[$strKey] . ' ';
		} else {
			// Fix Werte
			$strReturn .= $strKey . ' ';
		}
	}
	return $strReturn;
}

function getRandomWords($arrPatterns, $strType, $strSubType = '')
{
	$arrWords = getRandomWas($strType, $strSubType);
	$arrWords = array_merge($arrWords, getRandomWer());
	$arrWords = array_merge($arrWords, getRandomWie());
	$arrWords = array_merge($arrWords, getRandomAus());
	$arrWords = array_merge($arrWords, getRandomWo());
	return $arrWords;
}

function getRandomWas($strType, $strSubType = '')
{
	$arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Klinge', 'was_type' => 'weapon'];
	$arrList[] = ['was_zusatz_1' => 'Das', 'was_name' => 'Schwert', 'was_type' => 'weapon'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Dolch', 'was_type' => 'weapon'];
	$arrList[] = ['was_zusatz_1' => 'Das', 'was_name' => 'Eisen', 'was_type' => 'weapon'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Totschläger', 'was_type' => 'weapon'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Prügel', 'was_type' => 'weapon'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Sumpf', 'was_type' => 'quest'];
	$arrList[] = ['was_zusatz_1' => 'Das', 'was_name' => 'Wurmloch', 'was_type' => 'quest'];
	$arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Hölle', 'was_type' => 'quest'];
	$arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Höhle', 'was_type' => 'quest'];
	$arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'katakomben', 'was_type' => 'quest'];
	$arrList[] = ['was_zusatz_1' => 'Das', 'was_name' => 'Schloss', 'was_type' => 'quest'];
	$arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Burg', 'was_type' => 'quest'];
	$arrList[] = ['was_zusatz_1' => 'Das', 'was_name' => 'Verlies', 'was_type' => 'quest'];
	$arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Ruine', 'was_type' => 'quest'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Berg', 'was_type' => 'quest'];
	$arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Wüste', 'was_type' => 'quest'];
	$arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Oase', 'was_type' => 'quest'];
	$arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Festung', 'was_type' => 'quest'];
	$arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Einöde', 'was_type' => 'quest'];
	$arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Rüstung', 'was_type' => 'armor'];
	$arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Schale', 'was_type' => 'armor'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Umhang', 'was_type' => 'armor'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Anzug', 'was_type' => 'armor'];
	$arrList[] = ['was_zusatz_1' => 'Das', 'was_name' => 'Gewand', 'was_type' => 'armor'];
	$arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Platte', 'was_type' => 'armor'];
	$arrList[] = ['was_zusatz_1' => 'Das', 'was_name' => 'Flies', 'was_type' => 'armor'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Helm', 'was_type' => 'helmet'];
	$arrList[] = ['was_zusatz_1' => 'Das', 'was_name' => 'Chäpi', 'was_type' => 'helmet'];
	$arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Kappe', 'was_type' => 'helmet'];
	$arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Schuhe', 'was_type' => 'boots'];
	$arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Stiefel', 'was_type' => 'boots'];
	$arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Sandalen', 'was_type' => 'boots'];
    $arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Tretter', 'was_type' => 'boots'];
    $arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Latschen', 'was_type' => 'boots'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Ring', 'was_type' => 'ring'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Anstecker', 'was_type' => 'ring'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Ehering', 'was_type' => 'ring'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Siegelring', 'was_type' => 'ring'];
	$arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Kette', 'was_type' => 'amulet'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Anhänger', 'was_type' => 'amulet'];
	$arrList[] = ['was_zusatz_1' => 'Das', 'was_name' => 'Halsband', 'was_type' => 'amulet'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Schild', 'was_type' => 'shield'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Schüzer', 'was_type' => 'shield'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Rundschild', 'was_type' => 'shield'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Langschild', 'was_type' => 'shield'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Pfannendeckel', 'was_type' => 'shield'];
	$arrList[] = ['was_zusatz_1' => 'Das', 'was_name' => 'Lichtschwert', 'was_type' => 'weapon'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Klapspatten', 'was_type' => 'Weapon'];
	$arrList[] = ['was_zusatz_1' => 'Die', 'was_name' => 'Zipfelmütze', 'was_type' => 'helmet'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Samenlöser', 'was_type' => 'weapon'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Beckenbrecher', 'was_type' => 'weapon'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Eierschohner', 'was_type' => 'armor'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Lendenschurz', 'was_type' => 'armor'];
	$arrList[] = ['was_zusatz_1' => 'Das', 'was_name' => 'Rattennest', 'was_type' => 'quest'];
	$arrList[] = ['was_zusatz_1' => 'Der', 'was_name' => 'Zellblock', 'was_type' => 'quest'];
	$arrList[] = ['was_zusatz_1' => 'Das', 'was_name' => 'Katana', 'was_type' => 'weapon'];

	// Typen Filter
	$arrFilter = [];
	foreach($arrList as $numIndex => $arrRecord) {
		if($arrRecord['was_type'] == $strType) {
			$arrFilter[] = $arrRecord;
		}
	}
	
	// Get Random Pattern
	$numRandom = rand(0, count($arrFilter) - 1);
	$arrRecord = $arrFilter[$numRandom];	
	return $arrRecord;
}

function getRandomWer()
{
	$arrList[] = ['wer_zusatz_1' => 'der', 'wer_name' => 'Zigeunerin'];
	$arrList[] = ['wer_zusatz_1' => 'des', 'wer_name' => 'Zigeuners'];
	$arrList[] = ['wer_zusatz_1' => 'des', 'wer_name' => 'Beduinen'];
	$arrList[] = ['wer_zusatz_1' => 'des', 'wer_name' => 'Barbaren'];
	$arrList[] = ['wer_zusatz_1' => 'der', 'wer_name' => 'Barbarin'];
	$arrList[] = ['wer_zusatz_1' => 'der', 'wer_name' => 'Amazone'];
	$arrList[] = ['wer_zusatz_1' => 'des', 'wer_name' => 'Anton', 'wer_zusatz_2' => 'dem'];
	$arrList[] = ['wer_zusatz_1' => 'des', 'wer_name' => 'Gustaf', 'wer_zusatz_2' => 'dem'];
	$arrList[] = ['wer_zusatz_1' => 'des', 'wer_name' => 'Goliath', 'wer_zusatz_2' => 'dem'];
	$arrList[] = ['wer_zusatz_1' => 'des', 'wer_name' => 'Igor', 'wer_zusatz_2' => 'dem'];
	$arrList[] = ['wer_zusatz_1' => 'der', 'wer_name' => 'Ingrid', 'wer_zusatz_2' => 'der'];
	$arrList[] = ['wer_zusatz_1' => 'des', 'wer_name' => 'Drachentöter'];
	$arrList[] = ['wer_zusatz_1' => 'des', 'wer_name' => 'Hofnarren'];
	$arrList[] = ['wer_zusatz_1' => 'des', 'wer_name' => 'Whistelblower'];
	$arrList[] = ['wer_zusatz_1' => 'des', 'wer_name' => 'Lüstlings'];
	$arrList[] = ['wer_zusatz_1' => 'des', 'wer_name' => 'Fetischisten'];
	$arrList[] = ['wer_zusatz_1' => 'des', 'wer_name' => 'Hobbygärtners'];
	$arrList[] = ['wer_zusatz_1' => 'des', 'wer_name' => 'Sonderlings'];
    $arrList[] = ['wer_zusatz_1' => 'der', 'wer_name' => 'Heidi', 'wer_zusatz_2' => 'der'];
    $arrList[] = ['wer_zusatz_1' => 'deiner', 'wer_name' => 'Mutter'];
    $arrList[] = ['wer_zusatz_1' => 'der', 'wer_name' => 'Helene', 'wer_zusatz_2' => 'der'];
	$arrList[] = ['wer_zusatz_1' => 'des', 'wer_name' => 'Kiffers'];
	$arrList[] = ['wer_zusatz_1' => 'des', 'wer_name' => 'Versifften'];
	$arrList[] = ['wer_zusatz_1' => 'des', 'wer_name' => 'Perversen'];

	// Get Random Pattern
	$numRandom = rand(0, count($arrList) - 1);
	$arrRecord = $arrList[$numRandom];		
	return $arrRecord;
}

function getRandomWie()
{	
	$arrList[] = ['wie_adjektiv_1' => 'ekligen', 'wie_adjektiv_2' => 'eklige'];
	$arrList[] = ['wie_adjektiv_1' => 'hässlichen', 'wie_adjektiv_2' => 'hässliche'];
	$arrList[] = ['wie_adjektiv_1' => 'grässlichen', 'wie_adjektiv_2' => 'grässliche'];
	$arrList[] = ['wie_adjektiv_1' => 'fürchterlichen', 'wie_adjektiv_2' => 'fürchterliche'];
	$arrList[] = ['wie_adjektiv_1' => 'bösartigen', 'wie_adjektiv_2' => 'bösartige'];
	$arrList[] = ['wie_adjektiv_1' => 'schönnen', 'wie_adjektiv_2' => 'schönne'];
	$arrList[] = ['wie_adjektiv_1' => 'dummen', 'wie_adjektiv_2' => 'dumme'];
	$arrList[] = ['wie_adjektiv_1' => 'beknacksten', 'wie_adjektiv_2' => 'beknackste'];
	$arrList[] = ['wie_adjektiv_1' => 'seriösen', 'wie_adjektiv_2' => 'seriöse'];
	$arrList[] = ['wie_adjektiv_1' => 'mysteriösen', 'wie_adjektiv_2' => 'mysteriöse'];
	$arrList[] = ['wie_adjektiv_1' => 'brutalen', 'wie_adjektiv_2' => 'brutale'];
	$arrList[] = ['wie_adjektiv_1' => 'dämlichen', 'wie_adjektiv_2' => 'dämliche'];
	$arrList[] = ['wie_adjektiv_1' => 'grünen', 'wie_adjektiv_2' => 'grüne'];
	$arrList[] = ['wie_adjektiv_1' => 'geheimen', 'wie_adjektiv_2' => 'geheime'];
	$arrList[] = ['wie_adjektiv_1' => 'legendären', 'wie_adjektiv_2' => 'legendäre'];
	$arrList[] = ['wie_adjektiv_1' => 'roten', 'wie_adjektiv_2' => 'rote'];
	$arrList[] = ['wie_adjektiv_1' => 'blauen', 'wie_adjektiv_2' => 'blaue'];
	$arrList[] = ['wie_adjektiv_1' => 'schwarzen', 'wie_adjektiv_2' => 'schwarze'];
	$arrList[] = ['wie_adjektiv_1' => 'weissen', 'wie_adjektiv_2' => 'weisse'];
	$arrList[] = ['wie_adjektiv_1' => 'rostenden', 'wie_adjektiv_2' => 'rostende'];
	$arrList[] = ['wie_adjektiv_1' => 'schwulen', 'wie_adjektiv_2' => 'schwule'];
	$arrList[] = ['wie_adjektiv_1' => 'kleinwüchsigen', 'wie_adjektiv_2' => 'kleinwüchsige'];
	$arrList[] = ['wie_adjektiv_1' => 'spitzen', 'wie_adjektiv_2' => 'spize'];
	$arrList[] = ['wie_adjektiv_1' => 'eiskalten', 'wie_adjektiv_2' => 'eiskalte'];
	$arrList[] = ['wie_adjektiv_1' => 'grossen', 'wie_adjektiv_2' => 'grosse'];
	$arrList[] = ['wie_adjektiv_1' => 'kleinen', 'wie_adjektiv_2' => 'kleine'];
	$arrList[] = ['wie_adjektiv_1' => 'komischen', 'wie_adjektiv_2' => 'komische'];
	$arrList[] = ['wie_adjektiv_1' => 'behinderten', 'wie_adjektiv_2' => 'behinderte'];
	$arrList[] = ['wie_adjektiv_1' => 'stinkenden', 'wie_adjektiv_2' => 'stinkende'];
	$arrList[] = ['wie_adjektiv_1' => 'infizierten', 'wie_adjektiv_2' => 'infizierte'];
	$arrList[] = ['wie_adjektiv_1' => 'perversen', 'wie_adjektiv_2' => 'perverse'];
	
	// Get Random Pattern
    $numRandom_1 = rand(0, count($arrList) - 1);
	$numRandom_2 = rand(0, count($arrList) - 1);
    $arrRecord = [];
	$arrRecord['wie_adjektiv_1'] = $arrList[$numRandom_1]['wie_adjektiv_1'];
    $arrRecord['wie_adjektiv_2'] = $arrList[$numRandom_2]['wie_adjektiv_2'];
	return $arrRecord;
}

function getRandomAus()
{
	$arrList[] = ['aus_material' => 'Holz'];
	$arrList[] = ['aus_material' => 'Kupfer'];
	$arrList[] = ['aus_material' => 'Silber'];
	$arrList[] = ['aus_material' => 'Gold'];
	$arrList[] = ['aus_material' => 'Diamant'];
	$arrList[] = ['aus_material' => 'Obsidian'];
	$arrList[] = ['aus_material' => 'Mithril'];
	$arrList[] = ['aus_material' => 'Bronze'];
	$arrList[] = ['aus_material' => 'Gummi'];
	$arrList[] = ['aus_material' => 'Silikon'];
	$arrList[] = ['aus_material' => 'Stahl'];
	$arrList[] = ['aus_material' => 'Kaugummi'];
	$arrList[] = ['aus_material' => 'Rost'];
	$arrList[] = ['aus_material' => 'Granitt'];
	$arrList[] = ['aus_material' => 'Bambus'];
	$arrList[] = ['aus_material' => 'Laser'];
	$arrList[] = ['aus_material' => 'Kokain'];
	$arrList[] = ['aus_material' => 'Canabinol'];
	$arrList[] = ['aus_material' => 'Eis'];
	$arrList[] = ['aus_material' => 'Vorhautleder'];
	$arrList[] = ['aus_material' => 'Eichelkässe'];
	$arrList[] = ['aus_material' => 'Plutonium'];
	$arrList[] = ['aus_material' => 'Uran'];
	$arrList[] = ['aus_material' => 'Drachenzahn'];
	$arrList[] = ['aus_material' => 'Drachenfeuer'];

	// Get Random Pattern
	$numRandom = rand(0, count($arrList) - 1);
	$arrRecord = $arrList[$numRandom];		
	return $arrRecord;
}

function getRandomWo()
{
	$arrList[] = ['wo_zusatz_1' => 'vom', 'wo_ortschaft' => 'Spreewald'];
	$arrList[] = ['wo_zusatz_1' => 'von', 'wo_ortschaft' => 'Arkanum'];
	$arrList[] = ['wo_zusatz_1' => 'von', 'wo_ortschaft' => 'Gundam'];
	$arrList[] = ['wo_zusatz_1' => 'von', 'wo_ortschaft' => 'Atlantis'];
	$arrList[] = ['wo_zusatz_1' => 'vom', 'wo_ortschaft' => 'Mond'];
	$arrList[] = ['wo_zusatz_1' => 'vom', 'wo_ortschaft' => 'Dominastudio'];
	$arrList[] = ['wo_zusatz_1' => 'vom', 'wo_ortschaft' => 'Appenzel'];
	$arrList[] = ['wo_zusatz_1' => 'vom', 'wo_ortschaft' => 'Thurgau'];
	$arrList[] = ['wo_zusatz_1' => 'vom', 'wo_ortschaft' => 'Bodensee'];
	$arrList[] = ['wo_zusatz_1' => 'von', 'wo_ortschaft' => 'Transilvanien'];
	$arrList[] = ['wo_zusatz_1' => 'von', 'wo_ortschaft' => 'Nordkorea'];
	$arrList[] = ['wo_zusatz_1' => 'vom', 'wo_ortschaft' => 'Südpol'];

	// Get Random Pattern
	$numRandom = rand(0, count($arrList) - 1);
	$arrRecord = $arrList[$numRandom];		
	return $arrRecord;
}
