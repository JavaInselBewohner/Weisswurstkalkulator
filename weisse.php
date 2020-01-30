<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Weißwurschtkalkulator</title>
  <meta name="description" content="The Weißwurschtkalkulator">
  <meta name="author" content="Leonhard Kunz"
  	<!--script src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script-->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>

  <!--link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script-->

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>

<body>
	<div class="jumbotron">
		<h1>The Weißwurschtkalkulator</h1>
	</div>
	<div class="container">
	<form method="POST">
	<?php
	$SCRIPT_URL = "SITE URL";
	if(isset($_POST["date"]) && isset($_POST["w"]) && isset($_POST["b"]) && isset($_POST["name"])) {
		//sanitize inputs
		$hasError = false;
		$error_msg = "";
		$date = $_POST["date"];

		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {
			$dateParsed =  $_POST["date"];
		} else {
			$error_msg .= '<p class="alert alert-danger">Falsches Datumsformat</p>';
			$hasError = true;
		}

		$name = $_POST["name"];
		if (preg_match("/^[A-z0-9 ]+$/",$name)) {
			$nameParsed =  $_POST["name"];
		} else {
			$error_msg .= '<p class="alert alert-danger">Unerlaubtes Zeichen im Namen</p>';
			$hasError = true;
		}

		if(isset($_POST["comment"])){
			$comment = $_POST["comment"];
		//	if (preg_match("/^[A-z0-9 ]+$/",$comment)) {
				$commentParsed =  $_POST["comment"];
		/*	} else {
				$error_msg .= '<p class="alert alert-danger">Unerlaubtes Zeichen im Kommentar</p>';
				$hasError = true;
			}
		*/
		}
		$w = ceil(floatval(str_replace(",", ".", $_POST["w"])));
		if (!$w>0.5) {
			$error_msg .= '<p class="alert alert-danger">Falsche Weißwurschtanzahl</p>';
			$hasError = true;
		}

		if(isset($_POST["wi"])){
			$wi = ceil(floatval(str_replace(",", ".", $_POST["wi"])));
			if (!$wi>0.5) {
				$error_msg .= '<p class="alert alert-danger">Falsche Wieneranzahl</p>';
				$hasError = true;
			}
		}

		$b = ceil(floatval(str_replace(",", ".", $_POST["b"])));
		if (!$b>0.5) {
			$error_msg .= '<p class="alert alert-danger">Falsche Breznanzahl</p>';
			$hasError = true;
		}
		//save data
		if($hasError){
			echo $error_msg;
		}else{
			$wi = isset($wi) ? $wi : "";
			$comment = isset($comment) ? base64_encode(json_encode($comment)) : "";
			$line = $date . ";" . $name . ";" . $w . ";" . $b .";".$wi.";".$comment.";";
			$data = $line.PHP_EOL;
			$fp = fopen('ww.csv', 'a');
			fwrite($fp, $data);
		}
		//display success message
	}
	else if(isset($_REQUEST["date"])){
		$date = $_REQUEST["date"];
		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {
			$dateParsed =  $_REQUEST["date"];
		} else {
			echo '<p class="alert alert-danger">Falsches Datumsformat</p>';
			$dateParsed = date("Y-m-d");
		}
;	}else{
		$dateParsed = date("Y-m-d");
	}
	$resArr = [];

	$row = 1;
	if (($handle = fopen("ww.csv", "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
			$num = count($data);
			$row++;
			if($data[0] == $dateParsed){
				$r = new stdClass();
				$r->n = $data[1];
				$r->w = $data[2];
				$r->b = $data[3];
				if(isset($data[4]) && $data[4] != ""){
					$r->wi = $data[4];
				}else{
					$r->wi = 0;
				}
				if(isset($data[5])){
					$r->c = json_decode(base64_decode($data[5]));
				}
				$resArr[] = $r;
			}
		}
		fclose($handle);
	}

	if(count($resArr) > 0){
		$hasEntries = true;
	}else{
		$hasEntries = false;
	}
	?>
		<div class="row">
			<div class="col-sm-12 text-center">
				<!-- Datepicker -->
				<label for="date">Datum:</label>
				<input type="date" required="required" name="date" class="form-control" value="<?php echo $dateParsed; ?>">
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<!-- data -->
				<?php
					if($hasEntries) {
	 					echo "<h3 class='text-center' style='margin-top: 15px; margin-bottom: 15px;'>Teilnehmer</h3>";

						echo '<div class="row"><div class="col-sm-3"><p>Name</p></div><div class="col-sm-2"><p>Weisse (Stück)</p></div><div class="col-sm-2"><p>Brezn</p></div><div class="col-sm-2"><p>Wiener</p></div><div class="col-sm-3"><p>Kommentar</p></div></div>';

						$wSum = 0;
						$bSum = 0;
						$wiSum = 0;
						foreach($resArr as $r){
							echo '<div class="row"><div class="col-sm-3"><p>'.$r->n.'</p></div><div class="col-sm-2"><p>'.$r->w.'</p></div><div class="col-sm-2"><p>'.$r->b.'</p></div><div class="col-sm-2"><p>'.$r->wi.'</p></div><div class="col-sm-3"><p>'.$r->c.'</p></div></div>';
							$wSum += floatval($r->w);
							$bSum += floatval($r->b);
							$wiSum += floatval($r->wi);
						}
						echo '<div class="row" style="font-weight: bold;"><div class="col-sm-3"><p>Gesamt:</p></div><div class="col-sm-2"><p>'.$wSum.' ('.($wSum/2).' Paar)</p></div><div class="col-sm-2"><p>'.$bSum.'</p></div><div class="col-sm-2"><p>'.$wiSum.'</p></div><div class="col-sm-3"><p></p></div></div>';

					}
				?>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-3 ">
				<!-- inputs -->
				<div class="form-group">
					<label for="name">Name:</label>
					<input type="text" required="required"  name="name" class="form-control" id="name">
				</div>
			</div>
			<div class="col-sm-2 ">
				<!-- inputs -->
				<div class="form-group">
					<label for="w">Weisse:</label>
					<input type="number" step="1" min="0" required="required"  name="w" class="form-control" id="w">
				</div>
			</div>
			<div class="col-sm-2 ">
				<!-- inputs -->
				<div class="form-group">
					<label for="b">Brezn:</label>
					<input type="number" step="1" min="0" required="required"  name="b" class="form-control" id="b">
				</div>
			</div>
			<div class="col-sm-2 ">
				<!-- inputs -->
				<div class="form-group">
					<label for="wi">Wiener:</label>
					<input type="number" step="1" min="0" required="required"  name="wi" class="form-control" id="wi">
				</div>
			</div>
			<div class="col-sm-3 ">
				<!-- inputs -->
				<div class="form-group">
					<label for="name">Kommentar:</label>
					<input type="text" name="comment" class="form-control" id="comment">
				</div>
			</div>

		</div>
		<div class="row">
			<div class="col-sm-12 text-right">
				<input type="submit" class="btn btn-primary" value="Eintragen">
			</div>
		</div>
		</form>
	</div>
</body>
<script>
	$('input[type="date"]').change(function(){
		window.location.href="<?php echo $SCRIPT_URL; ?>?date="+$(this).val();
	});
</script>

</html>


