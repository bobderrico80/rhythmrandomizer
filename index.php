<?php
//MySQL connection variables
$hostname = 'localhost';
$user = ini_get('mysqli.default_user');
$pw = ini_get('mysqli.default_pw');
$database = 'rhytxfpd_rhythmrandomizer';

//Connect to database
try {
    $db = new PDO('mysql:host=' . $hostname . ';dbname=' . $database,$user,$pw);
} catch(PDOException $e) {
    echo $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>The Rhythm Randomizer</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <link rel="stylesheet" type="text/css" href="stylesheet.css"/>
        <meta name="viewport" content="width=device-width"/>
    </head>
    <body>
        <div id="banner">
            <h1>The Rhythm Randomizer</h1>
        </div>
        <div id="rhythmContainer">
            <div id="rhythm">

            </div>
        </div>
        <div id="buttonBar">
            <input type="button" id="randomize" value="Randomize"/>
        </div>
        <div id="options">
            <div id="optionTabs">
                <ul>
                    <li class="selected" id="generalOptionsTab">General Options</li>
                    <li id="noteOptionsTab">Note Options</li>
                </ul>
            </div>
            <div id="generalOptions" class="optionsPage">
                <div class="selectionWrapper">
                <label for="timeSignature">Time Signature:</label>
                    <select id="timeSignature">
                        <option value="4" selected>4/4 Time</option>
                        <option value="3">3/4 Time</option>
                        <option value="2">2/4 Time</option>
                    </select>
                </div>
                <div class="selectionWrapper">
                <label for="phraseLength">Phrase Length:</label>
                    <select id="phraseLength">
                        <option value="1">1 measure</option>
                        <option value="2" selected>2 measures</option>
                        <option value="4">4 measures</option>
                        <option value="8">8 measures</option>
                    </select>
                </div>
            </div>
            <?php
                //SELECT available note groups
                $sql = 'SELECT
                            noteGroups.noteGroupID,
                            noteGroups.noteGroupName,
                            noteGroups.noteGroupGraphic,
                            noteCats.noteCatName,
                            noteGroups.noteGroupDefault
                        FROM
                                noteGroups
                        INNER JOIN
                                noteCats
                        ON
                                noteGroups.noteGroupNoteCatID = noteCats.noteCatID
                        ORDER BY
                                noteCats.noteCatID,
                                noteGroups.noteGroupID';
                $stmt = $db->query($sql);
            ?>
            <div id="noteOptions" class="optionsPage">
                <?php
                    $lastCat = '';
                    while ($row = $stmt->fetch()) {
                        //creates field set for items in the same category
                        if ($lastCat != $row['noteCatName']) {
                            if ($lastCat != '') {
                                echo '</fieldset>';
                            }
                            echo '<fieldset>';
                            echo '<legend>' . $row['noteCatName'] . '</legend>';
                        }

                        //Determines if checkbox should be pre-selected
                        if ($row['noteGroupDefault'] == 1) {
                            $checked = 'checked';
                        } else {
                            $checked = '';
                        }

                        //creates checkboxes and images for each note group option
                        echo '<input type="checkbox" id="' . $row['noteGroupID'] . 
                                '" title="' . $row['noteGroupName'] . '" value="' . 
                                $row['noteGroupID'] . '" '. $checked . '/>';
                        echo '<div class="noteLabelWrapper">';
                        echo '<img class="noteLabel" src="notes/' . $row['noteGroupGraphic'] . 
                                '.png" title="' . $row['noteGroupName'] . '" alt="' . $row['noteGroupName'] . '"/>';
                        echo '</div>';
                        $lastCat = $row['noteCatName'];

                    }
                    echo '</fieldset>';
                ?>
            </div>
        </div>
        <script>
            /***************/
            /** FUNCTIONS **/
            /***************/
            
            function scaleRhythm() {
                //Get width of rhythm at full resolution
                var rhythmWidth = $("#rhythm").width();
                
                //Get current screen/window width
                var screenWidth = window.innerWidth;
                
                //Compute ratio between curren screen and window widths
                var ratio =   screenWidth / rhythmWidth;
                
                //Multiply img note height by ratio, then by 90% to provide some
                //breathing room on either side of the rhythm
                var newHeight = ($(".note").height() * ratio) * .9;
                
                //Set img note height to new height or 300px, whichever is smaller
                if (newHeight < 300) {
                    $(".note").css("height",newHeight);
                    //code to center rhythm horizontally
                    $("#rhythm").css("margin-top",(300-newHeight)/2);
                } else {
                    $(".note").css("height",300);
                    $("#rhythm").css("margin-top",0);
                }
            }
            
            /*********************/
            /** EVENT LISTENERS **/
            /*********************/
            
            //Randomize button
            $("#randomize").click(function(){
                //get general options from form
                var timeSignature = $("#timeSignature").val();
                var phraseLength = $("#phraseLength").val();
                
                //get note options from form
                var checked = [];
                $("#noteOptions :checked").each(function() {
                    checked.push($(this).val());
                });
                
                //alert user and exit function if nothing is selected
                if (checked.length < 1) {
                    alert("Please select at least one note value");
                    return;
                }
                
                
                //format note option ids into a comma-delimited string
                var noteOptions = "";
                for (var i=0; i < checked.length; i++) {
                    noteOptions += checked[i] + "a";
                }
                
                //remove the final comma and space
                noteOptions = noteOptions.substr(0, noteOptions.length - 1);    
                //ajax call
                $.ajax("randomize.php", {
                    data : {
                        timeSignature : timeSignature,
                        phraseLength : phraseLength,
                        noteOptions : noteOptions
                    },
                    type : "GET",
                    success : function(response) {
                        $("#rhythm").html(response);
                        scaleRhythm();
                    },
                    error : function(xhr, status, errorThrown) {
                        console.log(status + " | " + errorThrown);
                    }
                }); 
            });
            
            //Resize window
            $(window).resize(function(){
               scaleRhythm(); 
            });
            
            //Note Options Tab Click
            $("#noteOptionsTab").click(function(){
                $("#noteOptionsTab").addClass("selected");
                $("#generalOptionsTab").removeClass("selected");
                $("#noteOptions").show();
                $("#generalOptions").hide();
            });
            
            //General Options Tab Click
            $("#generalOptionsTab").click(function(){
                $("#generalOptionsTab").addClass("selected");
                $("#noteOptionsTab").removeClass("selected");
                $("#generalOptions").show();
                $("#noteOptions").hide();
            });
        </script>
    </body>
</html>
