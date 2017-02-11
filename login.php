<?php

/**
 * Setzt den Screen im Cookie auf die entsprechende Action
 * Jeder Link kommt hier durch.
 * Nach dem Cookie Setzen wird wieder zum referer umgeleitet
 */

// Include
include_once("assets/lib/GifCreator.php");
include_once("assets/lib/global_functions.php");

// Debugger
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cookie Informationen auslesen
$strLoginPlayer = getCookie('player', 'anonym');

// Post Formular auslesen
$strUser = strtolower(getPost('uid', ''));
$strPassword = getPost('pwd', '');

// Html Buffer
$strHtml = '';

// Player ist bereits eingeloggt
if($strLoginPlayer != 'anonym') {
    $strHtml .= '<div style="font-family: arial;">';
    $strHtml .= '   <br><b style="color: darkgreen;">Sie sind eingeloggt.</b><br><br>';
    $strHtml .= '</div>';
    die($strHtml);
}

// Player Login Formular rendern
if($strUser . $strPassword == '') {
    $strFail = getParam('fail', 0);
    $strHtml .= '<div style="font-family: arial;">';
    if($strFail > 0) {
        $strHtml .= '<br><b style="color: darkred;">Login Fehlgeschlagen!</b><br><br>';
    }
    $strHtml .= '   <br><b>GameIt Player Login</b><br><br>';
    $strHtml .= '   <form action="login.php" method="post">';
    $strHtml .= '      Username:<br>';
    $strHtml .= '      <input type="text" name="uid"><br><br>';
    $strHtml .= '      Password:<br>';
    $strHtml .= '      <input type="password" name="pwd"><br><br>';
    $strHtml .= '      <input type="submit" value="Login">';
    $strHtml .= '   </form>';
    $strHtml .= '</div>';
}

// Player Login durchführen
if($strUser . $strPassword != '') {
    $strPassword = md5($strPassword);

    // Checken ob user vorhanden (heist login erfolgreich)
    $arrPlayerData = getPlayerData();
    foreach($arrPlayerData as $arrUser) {
        if(strtolower($arrUser['login']['name']) == $strUser && $arrUser['login']['pwd_md5'] == $strPassword) {
            $numPlayerId = $arrUser['id'];
            setcookie('player', $numPlayerId, time()+60*60*24*30);
        }
    }

    // Wenn Kein User gefunden zurück
    header('Location: login.php?fail=1');
}

// Buffer Ausgeben
echo $strHtml;



