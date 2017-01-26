
<?php

// NEXT CALL: http://localhost:8888/gi/download.php?number=500

$numStart = $_GET['number'];
$numMax = 100;
for($numIndex = $numStart; $numIndex < $numStart + $numMax; $numIndex++) {
	$strUrl = 'http://www.charas-project.net/charas2/res_viewer.php?sk=1484542800&scale=3&img=' . $numIndex . '_1_100_t_t_t_t_t_t_t_t_t_t_t_t&texts=';
	file_put_contents('./download/' . $numIndex . '.png', file_get_contents($strUrl));	
	if(filesize('./download/' . $numIndex . '.png') == 0) {
		unlink('./download/' . $numIndex . '.png');
	} else {
		echo $strUrl . '<br>';	
	}
}