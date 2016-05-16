<?php

//---------  B U S I N E S S  ----------//
$app->group('/business', function() use($db,$app){

//Obtener todos los negocios
    $app->get('/', function() use($db,$app){
       $rows = getData("SELECT business_id AS id, business_name AS name, business_address AS address, business_phone AS phone, business_schedule AS schedule FROM business");
       
        for($i=0;$i < rowCount($rows); $i++){
            $rows[$i]['schedule'] = json_decode($rows[$i]['schedule']); 
        }
        
        if(rowCount($rows)){
            echo sendJSON(20, "business", $rows);
        } else {
            echo sendJSON(30, null, null);
        }
    });
    //Change position
    $app->put('/update/position/:id', function($id) use($db,$app){
        global $vId, $vCoord;
        $R    = $app->request;
        $uid  = validate($vId, $id);
        $long = validate($vCoord, $R->post('longitude'));
        $lat  = validate($vCoord, $R->post('latitude'));
        if($uid && $long && $lat){
            try {
                $user = getData("SELECT fk_business_id FROM user WHERE user_id = $uid");
                if(rowCount($user)){
                        if($user[0]['fk_business_id']){
                            try {
                                SQL("UPDATE business
                                        SET longitude = $long,
                                            latitude  = $lat
                                        WHERE business_id = ".$user[0]['fk_business_id']
                                    );
                                echo sendJSON(22, null, null);
                            } catch (Exception $e) {
                                echo sendJSON(40, null, null);
                            }
                        } else {
                            echo sendJSON(46, null, null); 
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

    //all locations
    $app->get('/geolocation/all', function() use($db,$app){
        try {
            $r = getData("SELECT business_id AS id, business_name AS name, latitude, longitude FROM business");
            if(rowCount($r)){
            	echo sendJSON(20, "list", $r);
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
	        	echo sendJSON(20, "list", $r);
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
                $rows = getData("SELECT business_name AS name, business_address AS address, business_phone AS phone, business_schedule AS schedule
                                 FROM business 
                                 WHERE business_id = $id");
                if(rowCount($rows)){
                    for($i=0;$i < rowCount($rows); $i++){
                        $rows[$i]['schedule'] = json_decode($rows[$i]['schedule']); 
                    }
                    echo sendJSON(20, "business", $rows);    
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
        global $vBusinessName, $vAddress, $vPhone, $vSchedule, $vId; 
        $R         = $app->request;
        $name      = validate($vBusinessName, $R->post('bname')); 
        $address   = validate($vAddress,      $R->post('address'));  
        $phone     = validate($vPhone,        $R->post('phone'));     
        $schedule  = $R->post('schedule');
        $userid    = validate($vId,           $R->post('id'));
        $password  = sha1($R->post('password'));
        if($name && $address && $phone && $userid){
            if(rowCount(getData("SELECT user_id FROM user WHERE user_id = $userid "))) {
                if(rowCount(getData("SELECT user_id FROM user WHERE user_id = $userid AND user_password = '$password'"))) {
                    try {
                        $HasBusiness = getData("SELECT fk_business_id FROM user WHERE user_id = $userid");
                        if($HasBusiness[0]['fk_business_id'] == null){
                                 
                                 /* CREATE A NEW BUSINESS */
                            SQL("INSERT INTO business (business_name, business_address, business_phone, business_schedule)
                                    VALUES ('$name', '$address', '$phone', '$schedule');
                                 
                                 /* GET The new business id */ 
                                 SET @businessid = (SELECT MAX(business_id) FROM business);
                                 
                                 /* UPDATE user with his new business id */
                                 UPDATE user SET fk_business_id = @businessid WHERE user_id = $userid;
                            ");
                            echo sendJSON(21, null, null); // Created sucessfully
                        } else {
                            echo sendJSON(51, null, null); // User already have a business
                        }

                    } catch (Exception $e) {
                        echo sendJSON(40, null, null); // ERROR
                    }

                } else {
                    echo sendJSON(43, null, null); // Password dont match
                }

            } else {
                echo sendJSON(44, null, null); // User doesnt exist
            }
        } else {  
            echo sendJSON(60, null, null);
        }
    });

//update business
    $app->put('/update/:id', function($id) use($db,$app){
        global $vFullName, $vAddress, $vPhone, $vSchedule;
        $R         = $app->request;
        $name      = validate($vFullName, $R->post('name')); //Solo letras
        $address   = validate($vAddress, $R->post('address'));  //Solo letras
        $phone     = validate($vPhone, $R->post('phone'));     //
        $schedule  = $R->post('schedule');
        $password  = sha1($R->post('password'));

        if($name && $phone && $address){
            // If exist user
            if(rowCount(getData("SELECT user_id FROM user WHERE user_id = $id"))){
                if(rowCount(getData("SELECT user_id FROM user WHERE user_id = $id AND user_password = '$password'"))){
                    $HasBusiness = getData("SELECT fk_business_id FROM user WHERE user_id = $id");
                    if($HasBusiness[0]['fk_business_id'] != NULL){
                        try {
                                 /* UPDATE BUSINESS */
                            SQL("UPDATE business SET
                                  business_name    = '$name',
                                  business_address = '$address',
                                  business_phone   = '$phone',
                                  business_schedule= '$schedule'
                                WHERE business_id  = ".$HasBusiness[0]['fk_business_id']."
                            ");
                            echo sendJSON(22, null, null); // All data has been changed sucessfully
                       
                        } catch (Exception $e) {
                            echo sendJSON(40, null, null);
                        }
                    } else {
                        echo sendJSON(46, null, null); // User doesn't have bussines DUH!
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

//Delete an business by the user ID
    $app->post('/delete/:id', function($id) use($db,$app){
        global $vId;
        $R           = $app->request;
        $password    = sha1($R->post('password')); //Solo letras
        $uid         = validate($vId, $id);

        if($uid){
            // Check if the user exist
            $ExistUser = getData("SELECT user_id FROM user WHERE user_id = $id");
            if(rowCount($ExistUser)){
                    
                    // Check if the password match
                    $MatchPass = getData("SELECT fk_business_id FROM user WHERE user_id = $id AND user_password = '$password'");
                    if(rowCount($MatchPass)){

                            // Get Business ID
                            $bussines_id = $MatchPass[0]['fk_business_id'];
                            if($bussines_id){
                                try {

                                    //Delete all done services 
                                    $Services = getData("SELECT service_id FROM service WHERE fk_business_id = ".$bussines_id);
                                    if(rowCount($Services)){    
                                        $ServicesIDs = "";
                                        for ($i=0; $i < rowCount($Services); $i++) { 
                                            if($i == 0)
                                                $ServicesIDs .= "fk_service_id = ".$Services[$i]['service_id'];
                                            else
                                                $ServicesIDs .= " AND fk_service_id = ".$Services[$i]['service_id']; 
                                        }
                                        SQL("DELETE FROM done_service WHERE $ServicesIDs;
                                             DELETE FROM service      WHERE fk_business_id = $bussines_id;
                                            ");
                                    }

                                /* DELETE USER */
                                        /* Delete user's favorites and user's business of others users */ 
                                SQL("DELETE FROM user_has_favorite         WHERE fk_business_id = $bussines_id;
                                        /* Delete user's preferences */
                                     UPDATE user SET fk_business_id = NULL WHERE user_id        = $id;
                                        /* Delete business */
                                     DELETE FROM business                  WHERE business_id    = $bussines_id;
                                    ");    
                                echo sendJSON(23, null, null); // All data has been deleted successfully.
                            } catch (Exception $e) {
                               echo sendJSON(40, null, null); //Error 
                            }
                        } else {
                            echo sendJSON(46, null, null); // User doesnt have business
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
