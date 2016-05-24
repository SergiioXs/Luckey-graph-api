<?php
//--------- U S E R ----------//
$app->group('/user', function() use($db,$app){

//Obtener todos los usuarios
    $app->get('/', function(){
       $rows = getData("SELECT user_id AS id, user_username AS username, user_firstname AS firstName, user_lastname AS lastName, user_email AS email,
                               user_password AS password, fk_business_id AS businessId 
       					FROM user");
       echo sendJSON(20, null, $rows);
    });

//Obtener el ID de una cuenta
    $app->get('/login', function() use($db,$app){
        global $vEmail;
        $R        = $app->request;
        $email    = validate($vEmail, $R->params('email')); 
        $password = sha1($R->params('password')); 
       
        if($email){
            $uid = getData("SELECT user_id FROM user WHERE user_email = '$email' AND user_password = '$password'");
            if($uid){
                $accountType = getData("SELECT user_setting_view_username AS type FROM user_setting WHERE fk_user_id = ".$uid[0]['user_id']);
                try {
                    if($accountType[0]['type'] == 1) { //ROLE User
                        $rows = getData("SELECT 1 AS _accountType, user_id AS id, user_username AS username, user_firstname AS firstName, user_lastname AS lastName, user_email AS email, fk_business_id AS businessId FROM user WHERE user_email = '$email' && user_password = '$password'");
                        if(rowCount($rows)){    
                            echo sendJSON(20, null, $rows); // Send user data
                    
                        } else {
                            echo sendJSON(41, null, null); //invalid login
                        }
                    } else { //ROLE Locksmith
                        $rows = getData("SELECT user_id AS id, fk_business_id AS businessId FROM user WHERE user_email = '$email' && user_password = '$password' && fk_business_id != 'null'");
                        if(rowCount($rows)){  
                            $usid = $rows[0]['id'];  
                            $business = getData("SELECT 2 AS _accountType, '$usid' AS _userId, business_id AS id, business_name AS name, business_address AS address, business_phone AS phone, business_schedule AS schedule FROM business WHERE business_id = ".$rows[0]['businessId']);
                            if(rowCount($business)) {
                                $business[0]['schedule'] = json_decode($business[0]['schedule']); 
                                echo sendJSON(20, null, $business); // Send business    
                            } else {
                                echo sendJSON(45, null, null); // Business doesnt exists
                            }
                        } else {
                            echo sendJSON(41, null, null);
                        }
                    }
                } catch (Exception $e) {
                   echo sendJSON(40, null, null); 
                }
            } else {
                echo sendJSON(41, null, null);
            }



        } else {
            echo sendJSON(60, null, null);
        }
    });

//Describe an user by the ID
    $app->get('/:id', function($id) use($db,$app){
        global $vId;
        $uid = validate($vId, $id);
        if($uid){    
            try {
                $rows = getData("SELECT user.user_firstname AS firstName, user.user_lastname AS lastName, user.user_username AS username, user.user_email AS email, 
                                        user_setting.user_setting_view_username AS accountType, user.fk_business_id AS businessId
                                 FROM user 
                                 INNER JOIN user_setting
                                 ON user_setting.fk_user_id = user.user_id 
                                 WHERE user.user_id = $id");
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

//Create a new user
    $app->post('/create', function() use($db,$app){
        global $vFirstName, $vLastName, $vEmail, $vPassword, $vUsername;
           $R         = $app->request;
           $firstname = validate($vFirstName,$R->params('firstname')); 
           $lastname  = validate($vLastName, $R->params('lastname'));  
           $email     = validate($vEmail,    $R->params('email'));     
           $password  = validate($vPassword, $R->params('password'));
           $username  = validate($vUsername, $R->params('username')); 
           
        if($firstname && $lastname && $email && $password && $username){
            $password = sha1($password);
            try {
                $ExistEmail = getData("SELECT user_id FROM user WHERE user_email = '$email'");
                if(!rowCount($ExistEmail)){
                         
                         /* CREATE A NEW USER */
                    SQL("INSERT INTO user (user_username, user_firstname, user_lastname, user_email, user_password)
                            VALUES ('$username', '$firstname', '$lastname', '$email', '$password');
                         
                         /* GET The new user id */ 
                         SET @userid = (SELECT user_id FROM user WHERE user_email = '$email');
                         
                         /* Create a new user preferences (account type)*/
                         INSERT INTO user_setting(user_setting_view_username, fk_user_id) values (1, @userid);
                    ");
                    echo sendJSON(21, null, null);
                } else {
                    echo sendJSON(42, null, null);
                }

            } catch (Exception $e) {
                echo sendJSON(40, null, null);
            }
        } else {
            echo sendJSON(60, null, null);
        }
        
    });

//Update an user by the ID
    $app->put('/update/:id', function($id) use($db,$app){
        global $vFirstName, $vLastName, $vUsername, $vPassword, $vId, $vEmail; 
        $R           = $app->request;
        $firstname   = validate($vFirstName,  $R->params('firstname')); //Solo letras
        $lastname    = validate($vLastName,   $R->params('lastname'));  //Solo letras
        $username    = validate($vUsername,   $R->params('username'));     //
        $password    = validate($vPassword,   $R->params('password'));
        $email       = validate($vEmail,      $R->params('email'));
        $uid         = validate($vId, $id);

        if($firstname && $lastname && $username && $password && $uid && $email){
            $password = sha1($password);
            $ExistUser = getData("SELECT user_email FROM user WHERE user_id = $id");

            if(rowCount($ExistUser)){
                // Password match
                if(rowCount(getData("SELECT user_id FROM user WHERE user_id = $id AND user_password = '$password'"))) {
                    
                    
                    if($email != $ExistUser){
                        if(rowCount(getData("SELECT user_id FROM user WHERE user_email = '$email'"))){
                            echo sendJSON(42, null, null); //Email already registered
                            return 0;
                        } 
                    }

                    try {

                        /* UPDATE BASIC INFORMATION*/
                        SQL("UPDATE user SET
                                user_firstname = '$firstname',
                                user_lastname  = '$lastname',
                                user_username  = '$username',
                                user_email     = '$email'
                            WHERE user_id = $id;


                            ");    
                        echo sendJSON(22, null, null); // All data has been changed successfully.
                    } catch (Exception $e) {
                       echo sendJSON(40, null, null); //Error 
                    }
                    
                } else {
                    echo sendJSON(43, null, null); //Passwords don't match
                }
            } else {
                echo sendJSON(44, null, null); //Doesn't exist that user 
            }
        } else {
            echo sendJSON(60, null, null);
        }
        
    });

//Update an user by the ID
    $app->put('/update/password/:id', function($id) use($db,$app){
        global $vId, $vPassword;
        $R           = $app->request;
        $newpassword = validate($vPassword, $R->params('newpassword'));
        $password    = $R->params('password');
        $uid         = validate($vId, $id); 
        
        if($newpassword && $uid){

            $newpassword = sha1($newpassword);
            $password    = sha1($password);
            $ExistUser = getData("SELECT user_id FROM user WHERE user_id = $id");
            if(rowCount($ExistUser)){

                // Password match
                if(rowCount(getData("SELECT user_id FROM user WHERE user_id = $id AND user_password = '$password'"))) {
                    try {

                        /* UPDATE BASIC INFORMATION*/
                        SQL("UPDATE user SET
                                user_password = '$newpassword'
                             WHERE user_id = $id;

                            ");    
                        echo sendJSON(22, null, null); // All data has been changed successfully.
                    } catch (Exception $e) {
                       echo sendJSON(40, null, null); //Error 
                    }
                } else {
                    echo sendJSON(43, null, null); //Passwords don't match
                }
            } else {
                echo sendJSON(44, null, null); //Doesn't exist that user 
            }
        } else {
            echo sendJSON(60, null, null);
        }
        
    });

//Delete an user by the ID
    $app->post('/delete/:id', function($id) use($db,$app){
        global $vId, $vPassword;
        $R           = $app->request;
        $password    = validate($vPassword, $R->params('password')); //Solo letras
        $uid         = validate($vId, $id);

        if($uid && $password) {
            $password = sha1($password);
            // Check if the user exist
            $ExistUser = getData("SELECT user_id FROM user WHERE user_id = $id");
            if(rowCount($ExistUser)){
                    
                    // Check if the password match
                    $MatchPass = getData("SELECT fk_business_id FROM user WHERE user_id = $id AND user_password = '$password'");
                    if(rowCount($MatchPass)){

                        // Get Business ID
                        $bussines_id = $MatchPass[0]['fk_business_id'];

                        try {
                        /* DELETE USER */

                            //DELETE CREDIT CARDS
                            $CCs = getData("SELECT fk_credit_card_id FROM user_has_credit_card WHERE fk_user_id = $id");
                            if(rowCount($CCs)){
                                $CCids = "";
                                for ($i=0; $i < rowCount($CCs); $i++) { 
                                    if($i == 0){
                                        $CCids .= " credit_card_id = ".$CCs[$i]['fk_credit_card_id'];
                                    } else {
                                        $CCids .= " AND credit_card_id = ".$CCs[$i]['fk_credit_card_id'];
                                    }
                                }
                                SQL("DELETE FROM user_has_credit_card WHERE fk_user_id = $id;
                                     DELETE FROM credit_card WHERE $CCids;
                                    ");
                            }
                                /* Delete user */ 
                            SQL("DELETE FROM user     WHERE user_id      = $id;
                                    /* Delete business */
                                 DELETE FROM business WHERE business_id  = $bussines_id;
                                ");    
                            echo sendJSON(23, null, null);; // All data has been deleted successfully.
                        } catch (Exception $e) {
                           echo sendJSON(40, null, null);; //Error 
                        }
                    } else {
                        echo sendJSON(43, null, null);; //Password doesn't match
                    }  
            } else {
                echo sendJSON(44, null, null);; //Doesn't exist that user 
            }
        } else {
            echo sendJSON(60, null, null);
        }
        
    });

// Show favorite business list
 $app->get('/favoritelist/:id', function($id) use($db,$app){
        global $vId;
        $uid = validate($vId, $id);
        if($uid){    
            try {
                if(rowCount(getData("SELECT user_id FROM user WHERE user_id = $id"))){
                    $rows = getData("SELECT business.business_id AS id, business.business_name AS name, business.business_address AS address, business.business_schedule AS schedule, business.business_phone AS phone
                         FROM user_has_favorite 
                         INNER JOIN business
                         ON user_has_favorite.fk_business_id = business.business_id 
                         WHERE user_has_favorite.fk_user_id = $id");
                    if(rowCount($rows)){
                        echo sendJSON(20, null, $rows);
                    } else {
                        echo sendJSON(30, null, null);
                    }
                } else {
                    echo sendJSON(44, null, null);
                }
            } catch (Exception $e) {
               echo sendJSON(40, null, null); 
            }
        } else {
            echo sendJSON(60, null, null);
        }
    });


// Show credit card list
 $app->get('/credit_card_list/:id', function($id) use($db,$app){
        global $vId;
        $uid = validate($vId, $id);
        if($uid){    
            try {
                if(rowCount(getData("SELECT user_id FROM user WHERE user_id = $id"))){
                    $rows = getData("SELECT credit_card.credit_card_id AS id, credit_card.credit_card_name AS name, credit_card.credit_card_number AS number, credit_card.credit_card_valid_thru AS date, credit_card.credit_card_cvc AS cvc, credit_card.credit_card_postal_code AS postalCode
                         FROM user_has_credit_card 
                         INNER JOIN credit_card
                         ON user_has_credit_card.fk_credit_card_id = credit_card.credit_card_id 
                         WHERE user_has_credit_card.fk_user_id = $id");
                    if(rowCount($rows)){
                        echo sendJSON(20, null, $rows);
                    } else {
                        echo sendJSON(30, null, null);
                    }
                } else {
                    echo sendJSON(44, null, null);
                }
            } catch (Exception $e) {
               echo sendJSON(40, null, null); 
            }
        } else {
            echo sendJSON(60, null, null);
        }
    });

    // History
    $app->get('/history/:id', function($id) use($db,$app){
        global $vId;
        $uid = validate($vId, $id);
        if($uid){    
            try {
                if(rowCount(getData("SELECT user_id FROM user WHERE user_id = $id"))){
                    //obtener las tarjetas del usuario
                    $ccs = getData("SELECT fk_credit_card_id FROM user_has_credit_card WHERE fk_user_id = $uid");
                    if(rowCount($ccs)){
                        $crec = "";
                        for($i = 0; $i < rowCount($ccs); $i++){
                            if($i == 0)
                                $crec = "fk_credit_card_id = ".$ccs[$i]['fk_credit_card_id'];
                            else
                                $crec .= " OR fk_credit_card_id = ".$ccs[$i]['fk_credit_card_id'];
                        }

                    //Obtener la lista combinada de servicios y servicios terminados
                        $rows = getData("SELECT 
                            (SELECT business_name FROM business WHERE business_id = service.fk_business_id) AS businessName, service.service_name AS serviceName, service.service_description AS description, service.service_price AS price, done_service.done_service_rate AS rate, done_service.done_service_date AS date
                                        FROM done_service
                                        INNER JOIN service
                                        ON service.service_id = done_service.fk_service_id
                                        WHERE $crec
                                        order by done_service.done_service_date
                                        ;");
                    
                        if(rowCount($rows)){
                            echo sendJSON(20, null, $rows);
                        } else {
                            echo sendJSON(30, null, null);
                        }
                    } else {
                        echo sendJSON(30, null, null);
                    }
                } else {
                    echo sendJSON(44, null, null);
                }
            } catch (Exception $e) {
               echo sendJSON(40, null, null); 
            }
        } else {
            echo sendJSON(60, null, null);
        }
    });

    

});

?>