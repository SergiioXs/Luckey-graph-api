<?php 

	
	require "Slim/LDS.php";
	$str = "";
	
	function getModulo($String, $x, $y) {
		if(strlen($String) > 0){
			$Rule = preg_split("[,]", $String);
			for ($i = 0; $i < sizeof($Rule); $i++) { 
				
				if($Rule[$i] == $x){ //Lo ha encontrado
					if($y > 0) {//Si es positivo
						for ($a=1; $a <= $y; $a++) {$i++; }//Recorrer n cantidad de pasos hacia enfrente		
					}
					if($y < 0) {//Si es egativo
						$y = $y * (-1);
						for ($a=1; $a <= $y; $a++) {$i--; }//Recorrer n cantidad de pasos hacia atras
					}

					return $Rule[$i];
				}
			}

			return 0; // No existe

		} else {
			return 0; // La cadena es muy corta no contiene datos
		}
	}

	echo var_dump(getModulo($str,4231,0));



?>