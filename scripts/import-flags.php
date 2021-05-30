<?php
	use Lib\Database;
	define('FLAG_SVG_DIR','flags'.DIRECTORY_SEPARATOR.'svg');

    require '../include.php';
    $settings = include('../settings/settings.php');	

    Database::init($settings);
    $db = Database::get();

    $flags = scandir(FLAG_SVG_DIR);
    foreach ($flags as $flag) {
    	$ext = pathinfo($flag, PATHINFO_EXTENSION);
    	if ($ext == 'svg') {
    		$countryCode = pathinfo($flag, PATHINFO_FILENAME);
    		echo $countryCode.PHP_EOL;
    		$stmt = $db->prepare("SELECT * FROM flag WHERE country_code=:countryCode");
			$stmt->execute(['countryCode' => $countryCode]); 
			$flagRecord = $stmt->fetch();
			if (!$flagRecord) {
				$data = [
    				'countryCode' => $countryCode
    			];
				$sql = "INSERT INTO flag (country_code) VALUES (:countryCode)";
				$stmt= $db->prepare($sql);
				$stmt->execute($data);
				$flagId = $db->lastInsertId();
			} else {
				$flagId = $flagRecord['flag_id'];	
			}
//			echo filesize(FLAG_SVG_DIR.DIRECTORY_SEPARATOR.$flag).PHP_EOL;
			copy(FLAG_SVG_DIR.DIRECTORY_SEPARATOR.$flag,DIR_FLAGS.DIRECTORY_SEPARATOR.$flagId.'.svg');
    	}
    }

	$string = file_get_contents("json/countries.json");
	$json = json_decode($string, true);
	foreach ($json as $value) {
		$countryCode = strtolower($value['alpha-2']);
		$countryName = strtolower($value['name']);
		$stmt = $db->prepare("SELECT * FROM flag WHERE country_code=:countryCode");
		$stmt->execute(['countryCode' => $countryCode]); 
		$flagRecord = $stmt->fetch();
		if ($flagRecord) {
			echo "found: ".$countryCode.PHP_EOL;
			$data = [
			    'countryName' => $countryName,
			    'id' => $flagRecord['flag_id'],
			];
			$sql = "UPDATE flag SET country_name=:countryName WHERE flag_id=:id";
			$stmt= $db->prepare($sql);
			$stmt->execute($data);			
		}
	}

    echo "END".PHP_EOL;