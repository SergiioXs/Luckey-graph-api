<?php
//---------D O    S E R V I C E ----------//
$app->group('/do_service', function() use($db,$app){
//1 - Waiting for business aprobation 
//2 - Service pending
//3 - Service done 
//4 - Service canceled
//Obtener todos los usuarios
    $app->get('/', function(){
        $rows = getData("SELECT do_service_id AS id, business_id AS businessId, user_id AS userId, credit_card_id AS creditCardId, service_name AS name, service_description AS description, service_price AS price, date, status 
            FROM do_service");
        if(rowCount($rows))
            echo sendJSON(20, null, $rows);
        else 
            echo sendJSON(30, null, null);
    });

//Describe an service by the ID
    $app->get('/:dsid', function($dsid) use($db,$app){
        global $vId;
        $dsid = validate($vId, $dsid);

        if($dsid){
            try {
                $rows = getData("SELECT 
                                    do_service_id AS id, 
                                    business_id AS businessId, 
                                    user_id AS userId, 
                                    credit_card_id AS creditCardId, 
                                    service_name AS name, 
                                    service_description AS description, 
                                    service_price AS price, 
                                    service_rate AS rate,
                                    date, 
                                    status, 
                                    Latitude AS latitude, 
                                    Longitude AS longitude 
                                 FROM do_service 
                                 WHERE do_service_id = $dsid");
                if(rowCount($rows)){
                    echo sendJSON(20, null, $rows);   
                } else {
                    echo sendJSON(30, null, null);
                }
            } catch (Exception $e) {
            echo sendJSON(40, null, null);
            }
        } else {
            echo sendJSON(60, null, null);
        }
    });


//Create a service
    $app->post('/create', function() use($db,$app){
        global $vFullName, $vDescription, $vId, $vPrice, $vCoords;
           $R           = $app->request;
           $uid         = validate($vId, $R->params('id'));
           $ccid        = validate($vId, $R->params('ccid'));
           $sid         = validate($vId, $R->params('sid'));
           $lat         = validate($vCoords, $R->params('lat'));
           $lng         = validate($vCoords, $R->params('lng'));
           
            if($uid && $ccid && $sid && $lat && $lng){
                try {
                    
                    //check if service exist
                    $service = getData("SELECT service_name, service_description, service_price, fk_business_id FROM service WHERE service_id = $sid");
                    if(rowCount($service)){
                            //check if business exist
                        if(rowCount(getData("SELECT business_id FROM business WHERE business_id = ".$service[0]['fk_business_id']))){    
                            //check if user exist
                            if(rowCount(getData("SELECT user_id FROM user WHERE user_id = $uid"))){
                                //check if credit card exist
                                if(rowCount(getData("SELECT credit_card_id FROM credit_card WHERE credit_card_id = $ccid "))){
                                    $name        = $service[0]['service_name'];
                                    $description = $service[0]['service_description'];
                                    $price       = $service[0]['service_price'];
                                    $bid         = $service[0]['fk_business_id'];
                                    try {
                                        SQL("INSERT INTO do_service(business_id, user_id, credit_card_id, service_name, service_description, service_price, status, Latitude, Longitude)
                                            VALUES ($bid, $uid, $ccid, '$name', '$description', $price, 1, $lat, $lng);
                                        ");
                                        echo sendJSON(21, null, null);
                                    } catch (Exception $e) {
                                        echo sendJSON(40, null, null);
                                    }
                                } else {
                                    echo sendJSON(48, null, null);
                                }
                            } else {
                                echo sendJSON(44, null, null);
                            }
                        } else {
                            echo sendJSON(45,null, null);
                        }
                    } else {
                        echo sendJSON(52, null, null);
                    }
                } catch (Exception $e) {
                    echo sendJSON(40, null, null); // E
                }
            } else {
                echo sendJSON(60, null, null);
            }
        return 0;
    });

//update a status service
    $app->post('/update/status/:dsid', function($dsid) use($db,$app){
        global $vId, $vStatus, $vRate;
            $R      = $app->request;
            $dsid   = validate($vId, $dsid);
            $status = validate($vStatus, $R->params('status'));
            if($R->params('rate') !== null)
               $rate = validate($vRate, $R->params('rate'));
            else {
                $rate = 2;
            }
        if($dsid && $status && $rate){
            try {
                if(rowCount(getData("SELECT do_service_id FROM do_service WHERE do_service_id = $dsid"))){
                    switch ($status) {
                        case 1: //Waiting for business aprobation
                            SQL("UPDATE do_service SET status = 1 WHERE do_service_id = $dsid");
                            echo sendJSON(22 ,null, null);
                            break;
                        case 2: //Service pending
                            SQL("UPDATE do_service SET status = 2 WHERE do_service_id = $dsid");
                            echo sendJSON(22 ,null, null);
                            break;
                        case 3: // Service done 
                            SQL("UPDATE do_service SET status = 3 WHERE do_service_id = $dsid");
                            echo sendJSON(22 ,null, null);
                            break;
                        case 4: // Service canceled
                            SQL("UPDATE do_service SET status = 4 WHERE do_service_id = $dsid");
                            echo sendJSON(22 ,null, null);
                            break;
                        case 5: // Waiting for user payment
                            SQL("UPDATE do_service SET status = 5 WHERE do_service_id = $dsid");
                            echo sendJSON(22 ,null, null);
                            break;
                        
                        default:
                            echo sendJSON(60, null, null);                
                            break;
                    }
                } else {
                    echo sendJSON(53, null, null);
                }
            } catch (Exception $e) {
                echo sendJSON(40, null, null);   
            }
        } else {
            echo sendJSON(60, null, null);
        }
        return 0;
    });

});

?>