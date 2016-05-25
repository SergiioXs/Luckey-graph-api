<?php
//--------- S E R V I C E ----------//
$app->group('/done_service', function() use($db,$app){

//Obtener todos los usuarios
    $app->get('/', function(){
        $rows = getData("SELECT done_service_id AS id, done_service_date AS date, done_service_rate AS rate, done_service_comment AS comment, fk_service_id AS serviceId, fk_credit_card_id AS creditCardId FROM done_service");
       
       if(rowCount($rows))
            echo sendJSON(20, null, $rows);
        else 
            echo sendJSON(30, null, null);
    });

//Describe an service by the ID
    $app->get('/:id', function($id) use($db,$app){
        try {
            $rows = getData("SELECT done_service_id AS id, done_service_date AS date, done_service_rate AS rate, done_service_comment AS comment, fk_service_id AS service, fk_credit_card_id AS credit_card
                             FROM done_service 
                             WHERE done_service_id = $id");
            if(rowCount($rows)){
                echo sendJSON(20, null, null);    
            } else {
                echo sendJSON(30, null, null);
            }
                
        } catch (Exception $e) {
           echo sendJSON(60, null, null); 
        }
    });

//Create a service
    $app->post('/create', function() use($db,$app){
        global $vId, $vRate, $vComment, $vFullDate;
           $R       = $app->request;
           $ccid    = validate($vId, $R->params('ccid')); 
           $sid     = validate($vId, $R->params('sid')); 
           $rate    = validate($vRate, $R->params('rate')); 
           $comment = validate($vComment, $R->params('comment')); 
           $date    = validate($vFullDate, $R->params('fulldate')); 
        if($ccid && $sid && $rate && $comment && $date){
            try {

                // Checks credit card -> service -> insert
                if(rowCount(getData("SELECT credit_card_id FROM credit_card WHERE credit_card_id = $ccid"))){
                    if(rowCount(getData("SELECT service_id FROM service WHERE service_id = $sid"))){
                        
                        SQL("INSERT INTO done_service(done_service_comment, done_service_date, done_service_rate, fk_service_id, fk_credit_card_id)
                                VALUES (
                                    '$comment',
                                    '$date',
                                     $rate,
                                     $sid,
                                     $ccid
                                )
                            ");
                        echo sendJSON(21, null, null);
                    } else {
                        echo sendJSON(52, null, null); // Service doesnt exist
                    }
                } else {
                    echo sendJSON(48, null, null); // credit card doesnt exists
                }

            } catch (Exception $e) {
                echo sendJSON(40, null, null); // E
            }
        } else {
            echo sendJSON(60, null, null);  
        }
        
    });

});

?>