<?php
//--------- app ----------//
$app->group('/app', function() use($db,$app){

//Obtener todos los usuarios
    $app->get('/', function(){
       $rows = getData("SELECT * FROM digital");
       echo sendJSON(20, null, $rows);
    });

//Listado de esperas menos el primero
    $app->get('/list', function(){
       $rows = getData("SELECT type, text FROM digital WHERE state = 1 ORDER BY date ASC");
        if(rowCount($rows))
            echo sendJSON(20, null, $rows);
        else
            echo sendJONS(30, null, null); 

    });

//Saber que bebida servir
    $app->get('/service', function(){
       $rows = getData("SELECT type, text FROM digital WHERE state = 1 ORDER BY date ASC limit 1");
        if(rowCount($rows))
            echo sendJSON(20, null, $rows);
        else
            echo sendJONS(30, null, null); 

    });

//Ultima bebida lista
    $app->get('/ready', function(){
       $rows = getData("SELECT text FROM digital WHERE state = 0 ORDER BY date DESC limit 1");
       
        if(rowCount($rows))
            echo sendJSON(20, null, $rows);
        else
            echo sendJONS(30, null, null);
    });

//Ultima bebida lista
    $app->get('/add', function() use($db,$app){
        

        $R        = $app->request;
        $apodo    = validate("/^[a-zA-Z0-9ñÑáéíó]{2}[a-zA-Z0-9ñÑáéíó ]+$/", $R->params('apodo'));
        $bebida   = validate("/^[1-9]+$/", $R->params('bebida'));   
        if($apodo && $bebida){
            try {
                SQL("INSERT INTO digital(state, type, text) VALUES (1, $bebida, '$apodo');");
                echo sendJSON(21, null, null);
            } catch (Exception $e) {
                echo sendJSON(60, null, null);
            }
            
        } else {
            echo sendJSON(60, null, null);
        }
    });



});

?>