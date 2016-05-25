<?php
//--------- S E R V I C E ----------//
$app->group('/service', function() use($db,$app){

//Obtener todos los usuarios
    $app->get('/', function(){
        $rows = getData("SELECT service_id AS id, service_name AS name, service_description AS description, service_price AS price, fk_business_id AS businessId FROM service");
        if(rowCount($rows))
            echo sendJSON(20, null, $rows);
        else 
            echo sendJSON(30, null, null);
    });

//Describe an service by the ID
    $app->get('/:id', function($id) use($db,$app){
        global $vId;
        $uid = validate($vId, $id);

        if($uid){
            try {
                $rows = getData("SELECT service_name AS name, service_description AS description, service_price AS price, fk_business_id AS businessId
                                 FROM service 
                                 WHERE service_id = $id");
                if(rowCount($rows)){
                    echo sendJSON(20, "service", $rows);   
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
        global $vFullName, $vDescription, $vId, $vPrice;
           $R           = $app->request;
           $name        = validate($vFullName, trim($R->params('name'))); 
           $description = validate($vDescription, trim($R->params('description'))); 
           $price       = validate($vPrice, trim($R->params('price'))); 
           $id          = validate($vId, $R->params('id')); 
           $password    = sha1($R->params('password')); 

            if($name && $description && $id && $price){
                try {
                    // Checks if the user exists 
                	if(rowCount(getData("SELECT user_id FROM user WHERE user_id = $id"))){
                		if(rowCount(getData("SELECT user_id FROM user WHERE user_id = $id AND user_password = '$password' "))){
                			$business_id = getData("SELECT fk_business_id FROM user WHERE user_id = $id");
                			if($business_id[0]['fk_business_id']){
                				SQL("INSERT INTO service(service_name, service_description, service_price, fk_business_id)
                						VALUES (
                							'$name',
                							'$description',
                							 $price,
                							 ".$business_id[0]['fk_business_id']."
                								); 
                					");
                				echo sendJSON(21, null, null);
                			} else {
                				echo sendJSON(46, null, null); // User doesn't have business
                			}
                		} else {
                			echo sendJSON(43, null, null); // Passwords don't match
                		}
                	} else {
                		echo sendJSON(44, null, null); // User doesnt exists
                	}

                } catch (Exception $e) {
                    echo sendJSON(40, null, null); // E
                }
            } else {
                echo sendJSON(60, null, null);
            }

        
    });

//update a service
    $app->put('/update/:sid', function($sid) use($db,$app){
        global $vFullName, $vDescription, $vId, $vPrice;
           $R           = $app->request;
           $name        = validate($vFullName,    $R->params('name')); 
           $description = validate($vDescription, $R->params('description')); 
           $price       = validate($vPrice,       $R->params('price')); 
           $id          = validate($vId,          $R->params('id'));  
           $password    = sha1($R->params('password')); 
           $sid         = validate($vId,          $sid);

        if($name && $description && $id && $price && $sid){
            try {
                // Checks if the user exists 
            	if(rowCount(getData("SELECT user_id FROM user WHERE user_id = $id"))){
            		if(rowCount(getData("SELECT user_id FROM user WHERE user_id = $id AND user_password = '$password' "))){
            			$business_id = getData("SELECT fk_business_id FROM user WHERE user_id = $id");
            			if($business_id[0]['fk_business_id']){
            				if(rowCount(getData("SELECT service_id FROM service WHERE service_id = $sid"))){
    	        				SQL("UPDATE service SET
    	        						
    	        						service_name        = '$name',
    	        						service_description = '$description',
    	        						service_price       =  $price
    	        					
    	        					WHERE service_id = $sid
    	        					;");
    	        				echo sendJSON(22, null, null); // Data update sucessfully
    	        			} else {
    	        				echo sendJSON(52, null, null); // service doesn't exist
    	        			}
            			} else {
            				echo sendJSON(46, null, null); // User doesn't have business
            			}
            		} else {
            			echo sendJSON(43, null, null); // Passwords don't match
            		}
            	} else {
            		echo sendJSON(44, null, null); // User doesnt exists
            	}
            } catch (Exception $e) {
                echo sendJSON(40, null, null); // E
            }
        } else {
            echo sendJSON(60, null, null);
        }

        
    });

//delete a service
    $app->post('/delete/:sid', function($sid) use($db,$app){
        global $vId;
           $R           = $app->request;
           $sid         = validate($vId, $sid);
           $id          = validate($vId, $R->params('id'));  
           $password    = sha1($R->params('password')); 

        if($id && $sid){
            try {

                // Checks: user -> password match -> business -> service 
            	if(rowCount(getData("SELECT user_id FROM user WHERE user_id = $id"))){
            		if(rowCount(getData("SELECT user_id FROM user WHERE user_id = $id AND user_password = '$password' "))){
            			$business_id = getData("SELECT fk_business_id FROM user WHERE user_id = $id");
            			if($business_id[0]['fk_business_id']){
            				if(rowCount(getData("SELECT service_id FROM service WHERE service_id = $sid"))){
    	        				SQL("DELETE FROM service WHERE service_id = $sid;");
    	        				echo sendJSON(23, null, null); // Data deleted sucessfully
    	        			} else {
    	        				echo sendJSON(52, null, null); // service doesn't exist
    	        			}
            			} else {
            				echo sendJSON(46, null, null); // User doesn't have business
            			}
            		} else {
            			echo sendJSON(43, null, null); // Passwords don't match
            		}
            	} else {
            		echo sendJSON(44, null, null); // User doesnt exists
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