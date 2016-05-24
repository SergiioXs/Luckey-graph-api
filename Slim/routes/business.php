<?php

//---------  B U S I N E S S  ----------//
$app->group('/business', function() use($db,$app){

//Obtener todos los negocios
    $app->get('/', function() use($db,$app){
       $rows = getData("SELECT business_id AS id, business_name AS name, business_address AS address, business_phone AS phone, latitude, longitude, business_schedule AS schedule FROM business");
       
        for($i=0;$i < rowCount($rows); $i++){
            $rows[$i]['schedule'] = json_decode($rows[$i]['schedule']); 
        }
        
        if(rowCount($rows)){
            echo sendJSON(20, null, $rows);
        } else {
            echo sendJSON(30, null, null);
        }
    });
    //Change position
    $app->put('/update/position/:bid', function($bid) use($db,$app){
        global $vId, $vCoords;
        $R    = $app->request;
        $bid  = validate($vId, $bid);
        $long = validate($vCoords, $R->params('lng'));
        $lat  = validate($vCoords, $R->params('lat'));
        if($bid && $long && $lat){
            try {
                if(rowCount(getData("SELECT user_id FROM user WHERE fk_business_id = $bid"))){
                    try {
                        SQL("UPDATE business
                                SET longitude = $long,
                                    latitude  = $lat
                                WHERE business_id = $bid"
                            );
                        echo sendJSON(22, null, null);
                    } catch (Exception $e) {
                        echo sendJSON(40, null, null);
                    }
                } else {
                    echo sendJSON(45, null, null);
                }
            } catch (Exception $e) {
               echo sendJSON(40, null, null); 
            }
        } else {
            echo sendJSON(60, null, null);
        }
    });

    //all locations
    $app->get('/geolocation/all', function() use($db,$app){
        try {
            $r = getData("SELECT business_id AS id, business_name AS name, latitude, longitude FROM business");
            if(rowCount($r)){
            	echo sendJSON(20, null, $r);
            } else {
                echo sendJSON(30, null, null);
            }
        } catch (Exception $e) {
           echo sendJSON(40, null, null); 
        }
});

      //near to me
$app->get('/geolocation/near', function() use($db,$app){
    global $vCoords, $vId;
    $R   = $app->request;
    $lat = validate($vCoords, $R ->get("lat"));
    $lng = validate($vCoords, $R ->get("lng"));
    $km  = validate($vId, $R ->get("km"));
    if($lat && $lng && $km){
	    try {
	    	$r = getData("SELECT business_id AS id, business_name AS name, latitude, longitude,  
	    					(6371 * ACOS( 
                                SIN(RADIANS(latitude)) * SIN(RADIANS($lat)) 
                                + COS(RADIANS(longitude - $lng)) * COS(RADIANS(latitude)) 
                                * COS(RADIANS($lat))
                                )
                  			) AS distance
							FROM business
							HAVING distance < $km /* 1 KM  a la redonda */
							ORDER BY distance ASC");
	        if(rowCount($r)){
	        	echo sendJSON(20, null, $r);
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


//Describe a business by his ID
    $app->get('/:id', function($id) use($db,$app){
        global $vId;
        $uid = validate($vId, $id);
        if($uid){
            try {
                $rows = getData("SELECT business_name AS name, business_address AS address, business_phone AS phone, latitude, longitude, business_schedule AS schedule
                                 FROM business 
                                 WHERE business_id = $id");
                if(rowCount($rows)){
                    for($i=0;$i < rowCount($rows); $i++){
                        $rows[$i]['schedule'] = json_decode($rows[$i]['schedule']); 
                    }
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

//Create a new business
    $app->post('/create', function() use($db,$app){
        global $vBusinessName, $vAddress, $vPhone, $vSchedule, $vEmail, $vId, $vPassword; 
        $R         = $app->request;
        $name      = validate($vBusinessName, $R->params('bname')); 
        $address   = validate($vAddress,      $R->params('address'));  
        $phone     = validate($vPhone,        $R->params('phone'));     
        $schedule  = $R->params('schedule');
        //$userid    = validate($vId,           $R->params('id'));

        //Datos para crear el usuario que contendra el business
        $email     = validate($vEmail,        $R->params('email'));
        $password  = validate($vPassword,     $R->params('password')); //Nueva password del business
        if($name && $address && $phone && $email && $password){
            if(!rowCount(getData("SELECT user_id FROM user WHERE user_email = '$email'"))) {
                    try {        
                        $password = sha1($password);
    
                        /* CREATE A NEW BUSINESS */
                        SQL("INSERT INTO business (business_name, business_address, business_phone, business_schedule)
                                VALUES ('$name', '$address', '$phone', '$schedule');
                             
                             /* GET The new business id */ 
                             SET @businessid = (SELECT MAX(business_id) FROM business);
                             
                            /* CREATE A NEW USER */
                            INSERT INTO user (user_username, user_firstname, user_lastname, user_email, user_password, fk_business_id)
                                VALUES('Empty', 'Empty', 'Empty', '$email', '$password', @businessid);
                            SET @userid = (SELECT MAX(user_id) FROM user);

                            INSERT INTO user_setting (user_setting_view_username, fk_user_id)
                                VALUES (2, @userid);
                        ");
                        echo sendJSON(21, null, null); // Created sucessfully
                    } catch (Exception $e) {
                        echo sendJSON(40, null, null); // ERROR
                    }

            } else {
                echo sendJSON(42, null, null); // email already registered
            }
        } else {  
            echo sendJSON(60, null, null);
        }
    });

//update business
    $app->put('/update/:bid', function($bid) use($db,$app){
        global $vFullName, $vAddress, $vEmail, $vPhone, $vSchedule, $VId;
        $R         = $app->request;
        $name      = validate($vFullName, $R->params('name')); //Solo letras
        $address   = validate($vAddress,  $R->params('address'));  //Solo letras
        $phone     = validate($vPhone,    $R->params('phone'));     //
        $email     = validate($vEmail,    $R->params('email'));
        $schedule  = $R->params('schedule');
        $password  = sha1($R->params('password'));
        $bid       = validate($vId, $bid);
        if($name && $phone && $address && $bid && $email){
            
            $ExistUser = getData("SELECT user_email FROM user WHERE fk_business_id = $bid");
            if(rowCount($ExistUser)){
                if(rowCount(getData("SELECT user_id FROM user WHERE fk_business_id = $bid AND user_password = '$password'"))){
                    
                    //Check if the user entered a new email
                    if($email != $ExistUser){
                        //Check if the new email is available
                        if(rowCount(getData("SELECT user_id FROM user WHERE user_email = '$email'"))){
                            echo sendJSON(42, null, null); //Email already registered
                            return 0;
                        } 
                    }

                   
                    try {
                             /* UPDATE BUSINESS */
                        SQL("UPDATE business SET
                              business_name    = '$name',
                              business_address = '$address',
                              business_phone   = '$phone',
                              business_schedule= '$schedule'
                            WHERE business_id  = $bid
                        ");
                        
                        if($email != $ExistUser){
                            SQL("UPDATE user SET
                                    user_email = '$email'
                                WHERE fk_business_id = $bid
                            ");
                        }

                        echo sendJSON(22, null, null); // All data has been changed sucessfully
                    } catch (Exception $e) {
                        echo sendJSON(40, null, null);
                    }
                } else {
                    echo sendJSON(43, null, null); // Passwords dont match
                }
            } else {
                echo sendJSON(45, null, null); //business doesnt exist
            }
        } else {
            echo sendJSON(60, null, null);
        }


    });

//Delete an business by the user ID
    $app->post('/delete/:bid', function($bid) use($db,$app){
        global $vId;
        $R           = $app->request;
        $password    = sha1($R->params('password')); //Solo letras
        $bid         = validate($vId, $bid);

        if($bid){
            // Check if the business exist
            if(rowCount(getData("SELECT user_id FROM user WHERE fk_business_id = $bid"))){
                    
                    // Check if the password match
                    $MatchPass = getData("SELECT fk_business_id FROM user WHERE user_id = $id AND user_password = '$password'");
                    if(rowCount($MatchPass)){

                            // Get Business ID
                            try {

                                //Delete all done services 
                                $Services = getData("SELECT service_id FROM service WHERE fk_business_id = ".$bid);
                                if(rowCount($Services)){    
                                    $ServicesIDs = "";
                                    for ($i=0; $i < rowCount($Services); $i++) { 
                                        if($i == 0)
                                            $ServicesIDs .= "fk_service_id = ".$Services[$i]['service_id'];
                                        else
                                            $ServicesIDs .= " AND fk_service_id = ".$Services[$i]['service_id']; 
                                    }
                                    SQL("DELETE FROM done_service WHERE $ServicesIDs;
                                         DELETE FROM service      WHERE fk_business_id = $bid;
                                        ");
                                }

                            /* DELETE USER */
                                    /* Delete user's favorites and user's business of others users */ 
                            SQL("DELETE FROM user_has_favorite         WHERE fk_business_id = $bid;
                                    /* Delete user's preferences */
                                 DELETE FROM user                      WHERE fk_business_id = $bid;
                                    /* Delete business */
                                 DELETE FROM business                  WHERE business_id    = $bid;
                                ");    
                            echo sendJSON(23, null, null); // All data has been deleted successfully.
                        } catch (Exception $e) {
                           echo sendJSON(40, null, null); //Error 
                        }

                    } else {
                        echo sendJSON(43, null, null); //Password doesn't match
                    }
                    
            } else {
                echo sendJSON(45, null, null); //Doesn't exist that user 
            }
        } else {
            echo sendJSON(60, null, null);
        }
        
    });


    //Add business to favorite list
    $app->post('/add_to_favorite/:bid', function($bid) use($db,$app){
        global $vId;
        $R   = $app->request;
        $bid = validate($vId, $bid);
        $uid = validate($vId, $R->params("id"));
        if($uid && $bid){
            try {
                $rows = getData("SELECT user_id FROM user WHERE user_id = $uid");
                if(rowCount($rows)){
                    $ebus = getData("SELECT business_id FROM business WHERE business_id = $bid");
                    if(rowCount($ebus)){
                        $exist = getData("SELECT fk_user_id FROM user_has_favorite WHERE fk_business_id = $bid && fk_user_id = $uid");
                        if(!rowCount($exist)){
                            SQL("INSERT INTO user_has_favorite(fk_user_id, fk_business_id) VALUES($uid, $bid);");
                            echo sendJSON(22, null, null);    
                        } else {
                            echo sendJSON(31, null, null);
                        }
                    } else {
                        echo sendJSON(45, null, null);
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


    //Remove business from favorite list
    $app->post('/remove_from_favorite/:bid', function($bid) use($db,$app){
        global $vId;
        $R   = $app->request;
        $bid = validate($vId, $bid);
        $uid = validate($vId, $R->params("id"));
        if($uid && $bid){
            try {
                $rows = getData("SELECT user_id FROM user WHERE user_id = $uid");
                if(rowCount($rows)){
                    $ebus = getData("SELECT business_id FROM business WHERE business_id = $bid");
                    if(rowCount($ebus)){
                        $exist = getData("SELECT fk_user_id FROM user_has_favorite WHERE fk_business_id = $bid && fk_user_id = $uid");
                        if(rowCount($exist)){
                            SQL("DELETE FROM user_has_favorite WHERE fk_business_id = $bid && fk_user_id = $uid;");
                            echo sendJSON(23, null, null);    
                        } else {
                            echo sendJSON(32, null, null);
                        }
                    } else {
                        echo sendJSON(45, null, null);
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
//-----------------------------------------------------------//


?>