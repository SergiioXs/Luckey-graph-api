<?php 

date_default_timezone_set("Etc/GMT+8");
  #Convierte el formato fecha a/m/d a un formato que desees mostrar
    # $date: En este parametro pondremos la fecha que deseemos modificar
    # $mostrar: Se mostrara el contenido como nosotros deseemos que se muestre a:d:m o d:m:a
      # O simplemente puede mostrar solo el año o dia o mes.. para escribirlo que la funcion lo reconosca se tendra
      # que escribir a (para año), m(para mes), d(para dia) y ':'(dos puntos) para separar 
 
  #Obtiene la hora actual
  function getCurrentTime(){
    return date("H:i:s");
  }
 
  #Obtiene la fecha actual
  function getCurrentDate(){
    return date("Y-m-d");
  }

  # Convierte la hora a como nosotros le indiquemos en la variable $show, 
    # ejemplo: getCurrentTime_Format("h:m", false); No mostrara 05:28 p. m.
    # El segundo parametro ($format) es para decir si es de 12 o 24 horas,
    # True = 24, False = 12
  function getCurrentTime_Format($show, $format){
    $time = getCurrentTime();
    
    $hours =   substr($time, -8, 2);
    $minutes = substr($time, -5, 2);
    $seconds = substr($time, -2);
    $pref = "";

    if(!$format){
      if($hours >= 13){
        $hours = $hours - 12;
        $pref = " p. m.";
      }else{
        $pref = " a. m.";
      }
    }

    switch ($show) {
      case 'h': return $hours.$pref; break;
      case 'm': return $minutes.$pref; break;
      case 's': return $seconds.$pref; break;
      case 'h:m': return $hours.":".$minutes.$pref; break;
      case 'm:h': return $minutes.":".$hours.$pref; break;
      case 'm:s': return $minutes.":".$seconds.$pref; break;
      case 'h:m:s': return $hours.":".$minutes.":".$seconds.$pref; break;
      case 'm:s:h': return $minutes.":".$seconds.":".$hours.$pref; break;
      case 's:m:h': return $seconds.":".$minutes.":".$hours.$pref; break;
      
      default:
        return $hours.":".$minutes.":".$seconds;
        break;
    }
  }

  function cnvTime_Format($time, $show, $format){
    $hours =   substr($time, -8, 2);
    $minutes = substr($time, -5, 2);
    $seconds = substr($time, -2);
    $pref = "";

    if(!$format){
      if($hours >= 13){
        $hours = $hours - 12;
        $pref = " p. m.";
      }else{
        $pref = " a. m.";
      }
    }

    switch ($show) {
      case "h": return $hours.$pref; break;
      case "m": return $minutes.$pref; break;
      case "s": return $seconds.$pref; break;
      case "h:m": return $hours.":".$minutes.$pref; break;
      case "m:h": return $minutes.":".$hours.$pref; break;
      case "m:s": return $minutes.":".$seconds.$pref; break;
      case "h:m:s": return $hours.":".$minutes.":".$seconds.$pref; break;
      case "m:s:h": return $minutes.":".$seconds.":".$hours.$pref; break;
      case "s:m:h": return $seconds.":".$minutes.":".$hours.$pref; break;
      
      default:
        return $hours.":".$minutes.":".$seconds.$pref;
        break;
    }
  }
####### MENEJO DE BASE DE DATOS ########

function getLocalData($sql){
    include_once("config.php");  
  $con = new Conexion();
  $db = $con->getConexion();
  if(is_null($db))
    echo "error";
  $result = $db->query($sql);
   if(!$result)
    echo "error";
   else 
    return $rows = $result->fetchAll(PDO::FETCH_ASSOC);
}

function LocalSQL($sql){
 include_once("config.php");  
            $con = new Conexion();
            $db = $con->getConexion();
            if(is_null($db))
              echo "error";
  $result = $db->query($sql); 
}

?>


