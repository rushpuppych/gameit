<?php

// Get Form Data
$strUid = uniqid('player_', true);
$strName = $_GET['player_name'];
$strPwd = md5($_GET['player_pwd']);
$strExp = $_GET['player_exp'];
$strCoins = $_GET['player_gold'];

// Create New Player
$arrPlayer = [];
$arrPlayer['id'] = $strUid;
$arrPlayer['login']['name'] = $strName;
$arrPlayer['login']['pwd_md5'] = $strPwd;
$arrPlayer['character']['body'] = "115";
$arrPlayer['character']['gender'] = 'male';
$arrPlayer['character']['face'] = "125";
$arrPlayer['character']['race'] = "human";
$arrPlayer['character']['ethnicity'] = "european";
$arrPlayer['character']['coins'] = $strCoins;

$arrPlayer['attributes']['exp'] = $strExp;
$arrPlayer['attributes']['atk'] = 0;
$arrPlayer['attributes']['def'] = 0;
$arrPlayer['attributes']['mat'] = 0;
$arrPlayer['attributes']['mde'] = 0;
$arrPlayer['attributes']['luk'] = 0;

$arrPlayer['equipment']['helmet'] = "";
$arrPlayer['equipment']['hair'] = "";
$arrPlayer['equipment']['armor'] = ['name' => 'Starter Klamoten', 'img_src' => '423'];
$arrPlayer['equipment']['shield'] = "";
$arrPlayer['equipment']['weapon'] = "";
$arrPlayer['equipment']['ring_left'] = "";
$arrPlayer['equipment']['ring_right'] = "";
$arrPlayer['equipment']['boots'] = "";

$arrPlayer['inventory']['helmet'] = [];
$arrPlayer['inventory']['hair'] = [];
$arrPlayer['inventory']['armor'][] = ['name' => 'Starter Klamoten', 'img_src' => '423'];
$arrPlayer['inventory']['shield'] = [];
$arrPlayer['inventory']['weapon'] = [];
$arrPlayer['inventory']['ring_left'] = [];
$arrPlayer['inventory']['ring_right'] = [];
$arrPlayer['inventory']['boots'] = [];

$arrPlayer['badges'] = [];
$arrPlayer['quest'] = [];

// Add Player and Save
$strFile = file_get_contents('../data/player.json', true);
$arrFile = json_decode($strFile);
$arrFile[] = $arrPlayer;

// Save Player Data
$strEncode = json_encode($arrFile);
if($strEncode != false) {
    $boolSave = file_put_contents('../data/player.json', $strEncode);
    if(!$boolSave) {
        file_put_contents('../data/player.json', $strFile);
    }
}


// Echo Player File
echo "<h1>Player</h1>";
echo "<p>Generate a new Player</p>";

echo '<div class="row">';
echo '    <div class="col-md-12">';
echo '        <form>';
echo '            <div class="form-group">';
echo '                <label for="player_code">MarkDown Player Code</label>';
echo '                <textarea class="form-control" id="player_code" name="player_code">';
echo '[![](http://138.197.90.62/gi/img.php?img=0-0&player=' . $strUid . ')](http://138.197.90.62/gi/screen.php?src=ext&img=score&player=' . $strUid . ")\n";
echo '[![](http://138.197.90.62/gi/img.php?img=1-0&player=' . $strUid . ')](http://138.197.90.62/gi/screen.php?src=ext&img=score&player=' . $strUid . ")\n";
echo '[![](http://138.197.90.62/gi/img.php?img=2-0&player=' . $strUid . ')](http://138.197.90.62/gi/screen.php?src=ext&img=score&player=' . $strUid . ")\n";
echo '                </textarea>';
echo '            </div>';
echo '        </form>';
echo '    </div>';
echo '</div>';
