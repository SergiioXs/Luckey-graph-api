<?php

//---------  C R E D I T    C A R D  ----------//
$app->group('/credit_card', function() use($db,$app){

//Obtener todos los negocios
    $app->get('/', function() use($db,$app){
       $rows = getData("SELECT credit_card_id AS id, credit_card_name AS name, credit_card_number AS number, credit_card_valid_thru AS date, credit_card_cvc AS cvc, credit_card_postal_code AS postalCode FROM credit_card");
 
       echo sendJSON(20, null, $rows);
    });

//Describe a credit card by ID
    $app->get('/:id', function($id) use($db,$app){
        global $vId;
        $id = validate($vId, $id);
        if($id) {
            try {
            $rows = getData("SELECT credit_card_id AS id, credit_card_name AS name, credit_card_number AS number, credit_card_valid_thru AS date,credit_card_cvc AS cvc, credit_card_postal_code AS postalCode 
                             FROM credit_card 
                             WHERE credit_card_id = $id");

            if(rowCount($rows))
                echo sendJSON(20, null, $rows);
            else
                echo sendJSON(30, null, null);
            } catch (Exception $e) {
               echo sendJSON(40, null, null); 
            }    
        } else {
            echo sendJSON(60, null, null);
        }
        
    });

//Create a new credit card
    $app->post('/create', function() use($db,$app){
        global $vFullName, $vNumber, $vDate, $vCvc, $vCp, $vId;
        $R         = $app->request;
        $name      = validate($vFullName,   $R->params('name')); 
        $number    = validate($vNumber, $R->params('number'));  
        $date      = validate($vDate,   $R->params('date'));     
        $cvc       = validate($vCvc,    $R->params('cvc'));
        $cp        = validate($vCp,     $R->params('cp'));
        $userid    = validate($vId,     $R->params('id'));
        echo $name."/".$number."/".$date."/".$cvc."/".$cp."/".$userid;
        if($name && $number && $date && $cvc && $cp && $userid){
            try {
                /* CHECK IF USER EXIST */
                $existUser = getData("SELECT user_id FROM user WHERE user_id = $userid");
                if(rowCount($existUser)){
                    /* CHECK IF CREDIT CARD EXIST */
                    $existCreditCard = getData("SELECT credit_card_id FROM credit_card WHERE credit_card_number = '$number'");
                    if(!rowCount($existCreditCard)){
                         /* CREATE A NEW BUSINESS */
                        
                        SQL("INSERT INTO credit_card(credit_card_name, credit_card_number, credit_card_valid_thru, credit_card_cvc, credit_card_postal_code)
                                VALUES ('$name', '$number', '$date', $cvc, '$cp');
                             
                             /* GET The new cc id */
                             SET @ccid = (SELECT MAX(credit_card_id) FROM credit_card);
                             
                             /* UPDATE user with his new business id */
                             INSERT INTO user_has_credit_card(fk_user_id, fk_credit_card_id) 
                                VALUES ($userid, @ccid);
                        ");
                        
                        echo sendJSON(21, null, null); //CC Created successfully
                    } else {
                        echo sendJSON(50, null, null); //CC already exist
                    } 
                } else {
                    echo sendJSON(44, null, null);     // Error
                }

            } catch (Exception $e) {
                echo sendJSON(40, null, null);
            }
        } else {
            echo sendJSON(60, null, null);
        }
    });

//update business
    $app->put('/update/:ccid', function($ccid) use($db,$app){
        global $vFullName, $vNumber, $vDate, $vCvc, $vCp, $vId;
        $R         = $app->request;
        $name      = validate($vFullName, $R->params('name')); 
        $number    = validate($vNumber,   $R->params('number'));  
        $date      = validate($vDate,     $R->params('date'));    
        $cvc       = validate($vCvc,      $R->params('cvc'));
        $cp        = validate($vCp,       $R->params('cp'));
        $ccid      = validate($vId,       $ccid);
        $id        = validate($vId,       $R->params('id'));
        $password  = sha1($R->params('password'));
        if($name && $number && $date && $cvc && $cp && $id && $ccid){
            if(rowCount(getData("SELECT user_id FROM user WHERE user_id = $id"))){
                if(rowCount(getData("SELECT user_id FROM user WHERE user_id = $id AND user_password = '$password'"))){
                    if(rowCount(getData("SELECT credit_card_id FROM credit_card WHERE credit_card_id = $ccid"))){
                            if(rowCount(getData("SELECT fk_user_id FROM user_has_credit_card WHERE fk_credit_card_id = $ccid AND fk_user_id = $id"))){
                                try {
                                         /* UPDATE CREDIT CARD */
                                    SQL("UPDATE credit_card SET
                                          credit_card_name       = '$name',
                                          credit_card_number     = '$number',
                                          credit_card_valid_thru = '$date',
                                          credit_card_cvc        =  $cvc,
                                          credit_card_postal_code= '$cp'
                                        WHERE credit_card_id     =  $ccid;
                                    ");
                                    echo sendJSON(22, null, null); // All data has been changed sucessfully
                               
                                } catch (Exception $e) {
                                    echo sendJSON(40, null, null); // Error
                                }
                            } else {
                                echo sendJSON(49, null, null); // Wrong owner
                            }
                    } else {
                        echo sendJSON(48, null, null); // Credit card doesnt exit
                    }
                } else {
                    echo sendJSON(43, null, null); // Passwords dont match
                }
            } else {
                echo sendJSON(44, null, null); //User doesnt exist
            }
        } else {
            echo sendJSON(60, null, null);
        }
    });

//Delete an credit card by the user ID and credit card
    $app->post('/delete/:ccid', function($ccid) use($db,$app){
        global $vId;
        $R           = $app->request;
        $password    = sha1($R->params('password')); //Solo letras
        $ccid        = validate($vId, $ccid); //Solo letras
        $id          = validate($vId, $R->params('id'));

        if($id && $ccid){
            // Check if the user exist
            $ExistUser = getData("SELECT user_id FROM user WHERE user_id = $id");
            if(rowCount($ExistUser)){
                    
                    // Check if the password match
                    if(rowCount(getData("SELECT user_id FROM user WHERE user_id = $id AND user_password = '$password'"))){

                            // Check if the credit card exits
                            if(rowCount(getData("SELECT credit_card_id FROM credit_card WHERE credit_card_id = $ccid"))){

                                // Checks if the credit card belongs to the user.
                                if(rowCount(getData("SELECT fk_credit_card_id FROM user_has_credit_card WHERE fk_credit_card_id = $ccid AND fk_user_id = $id"))){
                                    try {

                                        /* DELETE CREDIT CARD */ 
                                        SQL("DELETE FROM credit_card WHERE credit_card_id = $ccid;");

                                        echo sendJSON(23, null, null); // Credit card has been deleted successfully.

                                    } catch (Exception $e) {
                                       echo sendJSON(40,null, null); //Error 
                                    }
                                } else {
                                    echo sendJSON(49, null, null); // Credit card doesnt belogs to the user
                                }
                            } else {
                                echo sendJSON(48, null, null);// Credit card doesnt exists
                            }          
                    } else {
                        echo sendJSON(43, null, null); //Password doesn't match
                    }
                    
            } else {
                echo sendJSON(44, null, null); //Doesn't exist that user 
            }
        } else {
            echo sendJSON(60, null, null);
        }
        
    });


});
//-----------------------------------------------------------//

?>