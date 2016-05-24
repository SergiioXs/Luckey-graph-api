<?php

//Regular expresion Variables
global $vId, $vLastName, $vFirstName, $vEmail, $vPassword, $vUsername, $vPhone, $vSchedule, $vAddress, $vDate, $vFullDate, $vCvc, $vCp, $vNumber, $vFullName, $vDescription, $vPrice, $vPreference, $vRole, $vComment, $vRate, $vCoords;

$vId        = "/^[0-9]{1,}$/"; //Only positive entire numbers
$vFirstName = "/^[ a-zA-Záéíóúñ\-]{3,24}$/"; //Only letters
$vLastName  = "/^[ a-zA-Záéíóúñ\-]{3,24}$/"; //Only letters
$vEmail     = "/^[a-zA-Z_0-9\.]{3,70}@[a-zA-Z]{3,20}\.([a-zA-Z]{2,10}|[a-zA-Z]{2,20}\.[a-zA-Z]{2,10})$/"; // Email text@text.text OR text@text.text.text
$vPassword  = "/^[a-zA-Z0-9\@\#\%\!\*\_\-\+\/]{8,64}$/"; // Numbers, letters and @ # $ % ! * _ - + /
$vUsername  = "/^[a-zA-Z0-9]{5,64}$/"; //Numbers and letters
$vPhone     = "/^[0-9]{10}$/"; //10 Numbers (lada) and phone number
$vRole      = "/^(1|2)$/"; //1 User, 2 Locksimith
$vRate      = "/^(0|1)$/"; //0 means bad and 1 good
/* [{"open": false, "from": "00:00:00", "to": "00:00:00"},{"open": false, "from": "00:00:00", "to": "00:00:00"},{"open": false, "from": "00:00:00", "to": "00:00:00"},{"open": false, "from": "00:00:00", "to": "00:00:00"},{"open": false, "from": "00:00:00", "to": "00:00:00"},{"open": false, "from": "00:00:00", "to": "00:00:00"},{"open": false, "from": "00:00:00", "to": "00:00:00"}] */

$vSchedule  = "/\"^\[(\{\"open\"\: ((true)|(false))\, \"from\"\: \"((0[0-9])|(1[0-9])|(2[0-3]))\:([0-5][0-9])\:([0-5][0-9])\"\, \"to\"\: \"((0[0-9])|(1[0-9])|(2[0-3]))\:([0-5][0-9])\:([0-5][0-9])\"\},){6}(\{\"open\"\: ((true)|(false))\, \"from\"\: \"((0[0-9])|(1[0-9])|(2[0-3]))\:([0-5][0-9])\:([0-5][0-9])\"\, \"to\"\: \"((0[0-9])|(1[0-9])|(2[0-3]))\:([0-5][0-9])\:([0-5][0-9])\"\})\]$\"/"; //[{"open": true|false, "from": "hh:mm:ss", "to": "hh:mm:ss"} <7times> ] 
$vAddress   = "/^[a-zA-Z0-9\#\. \,\-\_]{15,120}$/";
$vDate      = "/^((1[0-2]{1})|(0[1-9]{1}))\/((1[6-9]{1})|([2-9]{1}[0-9]{1}))$/"; 
$vFullDate    = "/^20[1-9][0-9]\-(1[0-2]|0[1-9])\-([1-2][0-9]|3[0-1]|0[1-9])\ (0[0-9]|1[0-9]|2[0-3])\:([0-5][0-9])\:([0-5][0-9])$/"; // yy/mm/dd hh/mm/ss
$vCvc         = "/^[0-9]{3}$/"; // 3 digits
$vCp          = "/^[0-9]{5}$/"; // 5 digits
$vNumber      = "/^[0-9]{16}$/";// 16 digits
$vFullName    = "/^[ a-zA-Záéíóúñ\-\.]{3,64}$/"; // Only letter
$vBusinessName= "/^[ a-zA-Záéíóúñ\-\.\#0-9]{3,64}$/";
$vDescription = "/^[a-zA-Z0-9áéíóúñ\_\.\,\-\/\*\+\%\(\)\@ ]+$/"; // letters . , - / * + % ( )  
$vComment     = "/^[a-zA-Z0-9\_\.\,\-\/\*\+\%\(\)\@]{3}[ a-zA-Z0-9\_\.\,\-\/\*\+\%\(\)\@]{1,64}$/"; // letters . , - / * + % ( )
$vPrice       = "/^[0-9]+([\.]{1}[0-9]+)?$/"; // Pendiente
$vPreference  = "/^(1|2)$/";
$vCoords       = "/^(-)?[0-9]+([\.]{1}[0-9]+)?$/";

//Obtenemos todos los valores de una tabla
 function getAllData($table){
    global $db;
    $consulta = $db->prepare("SELECT * FROM $table");
    $consulta->execute();
    return $consulta->fetchAll(PDO::FETCH_ASSOC);        
 }


//Obtenemos resultados de un query
function getData($sql){
    global $db;
    $consulta = $db->prepare($sql);
    $consulta->execute();
    return $rows = $consulta->fetchAll(PDO::FETCH_ASSOC);
}


//Realiza querys que no necesitan retornar valores
function SQL($sql){
    global $db;
    $result = $db->query($sql); 
}


//Cuenta cuantos registros se tienen en el resultado de un query
function rowCount($sql){

    $cont = 0;
    for($i=0;;$i++){
       
      if(isset($sql[$i]))
        $cont++;
      else
        return $cont;
    }
    return 0;
}

function getStatusMSG($code) {
     switch ($code) {
        case 20:
            $msg = "Data found";
            break;
        case 21:
            $msg = "Created successfully.";
            break;
        case 22:
            $msg = "Data has been updated successfully.";
            break;
        case 23:
            $msg = "Data has been deleted successfully.";
            break;
        case 30:
            $msg = "No data found";
            break;
        case 31:
            $msg = "This business is already in your favorite list";
            break;
        case 32:
            $msg = "You dont have this business in your favorite list";
            break;
        case 40:
            $msg = "Invalid data";
            break;
        case 41:
            $msg = "Email or password don’t match";
            break;
        case 42:
            $msg = "Email already registered";
            break;
        case 43:
            $msg = "Passwords don’t match";
            break;
        case 44:
            $msg = "User doesn’t exists.";
            break;
        case 45:
            $msg = "Business doesn’t exists.";
            break;
        case 46:
            $msg = "The user doesn’t have a business.";
            break;
         case 48:
            $msg = "Credit card doesn’t exist.";
            break;    
        case 49:
            $msg = "The credit card not belongs to the user.";
            break;    
        case 50:
            $msg = "Credit card already exists.";
            break;
        case 51:
            $msg = "User already have a business.";
            break; 
        case 52:
            $msg = "Service doesn’t exist";
            break;            
        case 60:
            $msg = "One or more fields do not meet the requirements.";
            break; 
    

        default:
            $msg = "";
            break;
    }
    return $msg;
} 

function sendJSON($status, $tittle, $data){

    $count = rowCount($data);
    $content = json_encode($data);
    $msg = getStatusMSG($status);   
    $body = "";

    $a = "{";
    $b = "}";

    if($tittle) {
        $body = "[$a $tittle: $content $b]";
    } else {
        $body = "$content";
    }

    $data = "({_status: $status, _message: '$msg', _count: $count, data: $body})";
    return $data;
}

function validate($regexp, $value){
    $value = trim($value);
    if (!preg_match($regexp, $value)) {
        $value = null;
    }
    return $value;
}

?>