
<?php 
	// Globale PHP Scripts
	function getParam($strKey, $strDefault) {
		if(isset($_GET[$strKey])) {
			return $_GET[$strKey];
		} else {
			return $strDefault;
		}
	}
?>

<!doctype html>
<html>
    <head>
		<title>GI Motivator - Administration</title>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
		<!-- Include Navigation -->
		<?php include_once("html/navigation.html"); ?>
		
		<!-- Include BodyPart -->
		<div id="body_container" class="container">
			<?php $test = getParam('page', 'html/welcome.html'); ?>
			<?php include_once(getParam('page', 'html/welcome.html')); ?>
		</div>
		
		<!-- Include Bootstrap -->
        <script src="https://code.jquery.com/jquery-1.9.1.min.js" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
