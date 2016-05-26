$(document).ready( function () {
    dominio = "http://keyubers.esy.es/Index.php/luckey/";
    codeList = [
        {code:"20", description:"Data found"},
        {code:"21", description:"Created successfully."},
        {code:"22", description:"Data has been updated successfully."},
        {code:"23", description:"Data has been deleted successfully."},
        {code:"30", description:"No data found."},
        {code:"31", description:"This business is already in your favorite list"},
        {code:"32", description:"You dont have this business in your favorite list"},
        {code:"40", description:"Invalid data"},
        {code:"41", description:"Email or password don’t match"},
        {code:"42", description:"Email already exist"},
        {code:"43", description:"Passwords don’t match"},
        {code:"44", description:"User doesn’t exist"},
        {code:"45", description:"Business doesn’t exist"},
        {code:"46", description:"The user doesn’t have a business"},
        {code:"47", description:"The business not belongs to user"},
        {code:"48", description:"Credit card doesn’t exist"},
        {code:"49", description:"The credit card not belongs to the user"},
        {code:"50", description:"Credit card already exist"},
        {code:"51", description:"User already have a business."},
        {code:"52", description:"Service doesn’t exist"},
        {code:"53", description:"There is not an active service with that specifications"},
        {code:"60", description:"One or more fields do not meet the requirements."}
    ];
    conditionList = [
        {parameter:"First name", name:"firstname", description:"Only lowercase and uppercase letters are allowed and some accented letters (á, é, í, ó, ñ). (3 - 24 characters)"},
        {parameter:"Last name", name:"lastname", description:"Only lowercase and uppercase letters are allowed and some accented letters (á, é, í, ó, ñ). (3 - 24 characters)"},
        {parameter:"Email", name:"email", description:"The format of email addresses is 'local-part@domain.ext' where 'local-part' can be conbinated for the following characters(a-zA-Z0-9 _ .) following of the '@' 'domain' can be only letters following of a one or two 'ext' for example: example@domain.com or example.2@Domain.ite.mx"},
        {parameter:"Password", name:"password", description:"Only letters and some special characters(@ # $ % ! * _ - + /) are allowed. (8 - 64 characters)"}, 
        {parameter:"New password", name:"newpassword", description:"Only letters and some special characters(@ # $ % ! * _ - + /) are allowed. (8 - 64 characters)"},
        {parameter:"Username", name:"username", description:"Only Numbers, lowercase and uppercase letters are allowed. (5 - 64 characters)"},
        {parameter:"Role", name:"role", description:"For role 'user' the value is 1 and for role 'locksmith' the value is 2"},
        {parameter:"User preference", name:"preference", description:"Full name: 1 AND Username: 2"},
        {parameter:"User id", name:"id", description:"The user id must be only a positive entire number. It is extremely important that the user ID is entered, otherwise the http server response could be error 404 (not found)"},
        {parameter:"Business id", name:"bid", description:"The business id must be only a positive entire number. It is extremely important that the business ID is entered, otherwise the http server response could be error 404 (not found)"},
        {parameter:"Business Name", name:"bname", description:"Only lowercase, uppercase letters, numbers are allowed and some accented letters (á, é, í, ó, ñ, #). (3 - 64 characters)"},
        {parameter:"Business address", name:"address", description:"Only letters, numbers, special characters(# , . - _) and some accented letters (á, é, í, ó, ñ) are allowed. (15 - 120 characters)"},
        {parameter:"Business phone", name:"phone", description: "To represent a phone number the value must be equal to 10 digits."},
        {parameter:"Schedule", name:"schedule", description:""},
        {parameter:"Credit card name", name:"name", description:"Only lowercase and uppercase letters are allowed and some accented letters (á, é, í, ó, ñ). (3 - 64 characters)"},
        {parameter:"Credit card number", name:"number", description:"To represent a credit card number the value must be equal to 16 digits."},
        {parameter:"Date", name:"date", description:"Date value must be have the following structure MM/YY where MM: Is a numeric month value, and YY: are the last 2 digits of the year, for example: 12/18 (Month: December & Year: 2018)"},
        {parameter:"Full Date", name:"fulldate", description:"Date value must be have the following structure YY/MM/DD Hr/Min/Sec WHERE YY: Year, MM: Mounth, DD: Day ESPACE Hr: Hour, Min: Minute, Sec: Seconds "},
        {parameter:"CVC", name:"cvc", description:"To represent a CVC the value must be equal to 3 digits."},
        {parameter:"Postal code", name:"cp", description:"To represent a Postal code the value must be equal to 5 digits."},
        {parameter:"Service name", name:"name", description:"Only lowercase and uppercase letters are allowed and some accented letters (á, é, í, ó, ñ). (3 - 64 characters)"},
        {parameter:"Service Description", name:"description", description:"Only letters and some special characters(_ . , - / * + % ( ) @) are allowed. (8 - 64 characters)"},
        {parameter:"Service price", name:"price", description:"Price value must be a positive entire or double number, only one dot are allowed and it must be after of a number."},
        {parameter:"Comment", name:"comment", description:"Only letters and some special characters(_ . , - / * + % ( ) @) are allowed. (8 - 64 characters)"},
        {parameter:"Rate", name:"rate", description:"Value 1 means a good work, and value 2 is a bad work"},
        {parameter:"Credit card id", name:"ccid", description:"The Credit card id must be only a positive entire number. It is extremely important that the Credit card ID is entered, otherwise the http server response could be error 404 (not found)"},
        {parameter:"Service id", name:"sid", description:"The service id must be only a positive entire number. It is extremely important that the service ID is entered, otherwise the http server response could be error 404 (not found)"},
        {parameter:"Done sevice id", name:"dsid", description:"The done service id must be only a positive entire number. It is extremely important that the done service ID is entered, otherwise the http server response could be error 404 (not found)"},
        {parameter:"Method for update", name:"_METHOD", description:"The value MUST be PUT"},
        {parameter:"Latitude",  name:"lat", description:"Only entire or decimal, positive or negative, numbers are allowed"},
        {parameter:"Longitude", name:"lng", description:"Only entire or decimal, positive or negative, numbers are allowed"},
        {parameter:"Kilometer", name:"km",  description:"Only positive entire numbers are allowed"},
        {parameter:"Service status", name:"status",  description:"Describe the actual situation of an service it could be 1, 2, 3, 4 or 5"}
    ];

    exampleMessage = "To see an example for this function, try using the Form situated inside of the input section. ";
    
    $("#content").load("templates/introduction.html");

    $("#URL").html(dominio);

    
	$("#sidebarbtn").on('click', function () {
		$("#sidebar").toggleClass("hide");
        $('#dim')
          .dimmer('toggle')
        ;
	});


	$("#dim, #item").on('click', function () {

		if(!$("#sidebar").hasClass("hide"))
			$("#sidebar").addClass("hide");
        $('#dim')
          .dimmer('toggle')
        ;
        $("#log").html("The function has been changed!");
	});

    
    
    $("#DesktopIcon, #TabletIcon").on("click", function () {
        $("#InputCont").toggleClass("sixteen wide column five wide column");
        $("#OutputCont").toggleClass("sixteen  wide column eleven wide column");
        $("#DividerCont").toggleClass("horizontal vertical");
        $("#DesktopIcon").toggleClass("large grey desktop icon large green desktop icon");
        $("#TabletIcon").toggleClass("large green tablet icon large grey tablet icon");
    });


var offset = 300,
        //browser window scroll (in pixels) after which the "back to top" link opacity is reduced
        offset_opacity = 1200,
        //duration of the top scrolling animation (in ms)
        scroll_top_duration = 700,
        //grab the "back to top" link
        $back_to_top = $('.cd-top');

    //hide or show the "back to top" link
    $(window).scroll(function(){
        ( $(this).scrollTop() > offset ) ? $back_to_top.addClass('cd-is-visible') : $back_to_top.removeClass('cd-is-visible cd-fade-out');
        if( $(this).scrollTop() > offset_opacity ) { 
            $back_to_top.addClass('cd-fade-out');
        }
    });

    //smooth scroll to top
    $back_to_top.on('click', function(event){
        event.preventDefault();
        $('body,html').animate({
            scrollTop: 0 ,
            }, scroll_top_duration
        );
    });


    $(document).keypress(function(e) {
        //alert(e.which);
        if(e.which == 13) {
            $("#btn, #testbtn").click();

        } 

        if(e.which == 9786) {
            $("#console").html("");
        }

        if(e.which == 9565){
            $("#console").html("<div class='ui inverted segment' id='log'><p>Welcome developer!</p></div>");
            $('body,html').animate({
                scrollTop: $("#Descripcion").height() + 140 ,
                }, 700
            );
        }

        if(e.which == 9829) {
            $("#log").html("Thanks for clear me developer ♥!");
        }

        if(e.which == 173) {
            pass = "";
            $('#lgmdl')
              .modal({
                blurring: true
              })
              .modal('show')
            ;
            $("#scra").html("<audio autoplay><source src='sound/bim.mp3' type='audio/mpeg'></audio>");            
            $("a#lgbtn").on('click', function () {
                
                if(!parseInt($(this).attr("value"))){
                    alert(pass);
                } else {
                    pass = pass.replace(/,/, "");
                    pass += $(this).attr("value") + ",";
                }

            });
        }

    });



});

function showTemplate(x){
    var pre = "";
    if(x[0] == "."){
        pre = "../";
        x = x.substr(3);
    }
    if(($("#contentData").length == 0)) {
        $("#content").load(pre+"templates/contentData.html", function () {
            $("#input").load(pre+"templates/"+x+".html");
            $("#output").html("");
            
            $('body,html').animate({
                    scrollTop: 0 ,
                    }, 700
                );
            
        });    
        
    } else {
        $("#input").load(pre+"templates/"+x+".html");
        $("#output").html("");
        
        $('body,html').animate({
                scrollTop: 0 ,
                }, 700
            );
        
    }
}

function showTextTemplate(x){
      var pre = "";
    if(x[0] == "."){
        pre = "../";
        x = x.substr(3);
    }
    $("#output").html("");
    $("#input").html("");
    $("#content").load(pre+"templates/"+x+".html");
    
    if(!$("#sidebar").hasClass("hide"))
        $("#sidebar").addClass("hide");
    $('#dim').dimmer('toggle');
    
    $('body,html').animate({
            scrollTop: 0 ,
        }, 700
    );    
}

function json_dump(json) {
    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
        var cls = 'number';
        if (/^"/.test(match)) {
            if (/:$/.test(match)) {
                cls = 'key';
            } else {
                cls = 'string';
            }
        } else if (/true|false/.test(match)) {
            cls = 'boolean';
        } else if (/null/.test(match)) {
            cls = 'null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
    });
}


function ConsoleLog(status, data, url){
    var color = "";
    var showmore = "";
    var content = status.responseText;
    fullContent = status.responseText;
    if(status.status >= 100 && status.status < 200){ 
        color="#00FFFF"; //Info
    }
    if(status.status >= 200 && status.status < 300){ 
        color="#00FF00"; //Success
    }
    if(status.status >= 300 && status.status < 400){ 
        color="#00FF00"; //Redirection
    }
    if(status.status >= 400 && status.status < 600){ 
        color="#FF0000"; //Client error
    }
    if(data.length){
        data = "<br>"+data;
        data = data.replace(/&/g, "\"<br>");
        data = data.replace(/=/g, " <span style='color: red;'>=></span> \"");
        data += "\"<br>";
    }

    try {
        if(status.responseText.length > 500){
            showmore = "<span id='btnshowall'><br><button id='showall' onClick='fill();' class='ui fluid basic inverted gray button'>Show all</button></span>"; 
            content = status.responseText.substr(0, 500);  
        }
    } catch (e){
        
    } 

   return "URL: <span style='color:white;'>"+url+"</span><br>Http status: <span style='color: "+color+";'>"+status.statusText+"</span> ("+status.status+")<br>data: "+data+"<br>Server responds: <br><span id='contentlog'>"+content+"</span><br>"+showmore;
}

function fill() {
    $("#contentlog").html(fullContent);
    $("#btnshowall").html("");
}

    function getObjCode(code){
        for(i = 0; i < codeList.length; i++){
            if(codeList[i].code == code)
                return codeList[i];
            if(codeList.length == i)
                return false;
        }
    }

function CodeTable(codes){
    var table = "<br><br>Here is a table of status code that the API could send.<table class='ui table'><thead><tr><th>Code</th><th>Message</th></tr></thead><tbody>";
    var description ="";
    for (var i = 0; i < codes.length; i++) {
        description = getObjCode(codes[i]);
        table += "<tr><td>"+codes[i]+"</td><td>"+description.description+"</td></tr>";
    }
    table += "</tbody></table>";
    return table;
}

    function getObjCondition(condition){
        for(i = 0; i < conditionList.length; i++){
            if(conditionList[i].name == condition)
                return conditionList[i];
            if(conditionList.length == i)
                return false;
        }
    }

function ConditionTable(conditions){
    var table = "There is a few conditions: <table class='ui table'><thead><tr><th>Parameter</th><th>Name</th><th>Description</th></tr></thead><tbody>";
    var obj ="";
    for (var i = 0; i < conditions.length; i++) {
        obj = getObjCondition(conditions[i]);
        table += "<tr><td>"+obj.parameter+"</td><td>"+obj.name+"</td><td>"+obj.description+"</td></tr>";
    }
    table += "</tbody></table>";
    return table;
}

function AJAX(url, method, data){
   $('body,html').animate({
                scrollTop: $("#Descripcion").height() + 140 ,
                }, 700
            );
    try{
        $.ajax({
            type: method,
            url: url,
            data:  data,
            complete: function(result){
                $("#log").html(ConsoleLog(result, data, url));
            },
            success: function(result) {
                try { 
                    var str = JSON.stringify(eval(result), undefined, 4);
                    $("#output").html(json_dump(str));
                } catch(e) {
                   $("#log").html(ConsoleLog(result, e, url));
                   $("#output").html("\n   <i class='remove red icon'></i>Woops! There is a problem with the API. Please try again later.<br>");
                }
            },
            error: function(error) {

                $("#output").html("<i class='remove red icon'></i>Invalid input.<br> Status code <span style='color: red;'>"+error.status+"</span> ("+error.statusText+")");

            }
        });
    } catch(e){
        $("#log").html(e);
        $("#output").html("\n   <i class='remove red icon'></i>Woops! There is a problem with the site. Please try again later.<br>");         
    }
}