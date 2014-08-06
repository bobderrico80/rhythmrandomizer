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
        <script src="js/imagesloaded.js"></script>
        <link rel="stylesheet" type="text/css" href="stylesheet.css"/>
        <meta name="viewport" content="width=device-width"/>
    </head>
    <body>
        <div id="banner">
            <h1>The Rhythm Randomizer</h1>
        </div>
        <div id="rhythmContainer" title="Click the rhythm to generate a new one!">
            <div class="preloader">
                <img src="preloader.gif" alt="loading..." title="loading..."/>
            </div>
            <div id="rhythm">

            </div>
        </div>
        <div id="options">
            <div id="optionTabs">
                <ul>
                    <li id="generalOptionsTab">General Options</li>
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
                        echo '<div class="noteOptContainer">';
                        echo '<input type="checkbox" id="' . $row['noteGroupID'] . 
                                '" title="' . $row['noteGroupName'] . '" value="' . 
                                $row['noteGroupID'] . '" '. $checked . '/>';
                        echo '<div class="noteLabelWrapper">';
                        echo '<img class="noteLabel" src="notes/' . $row['noteGroupGraphic'] . 
                                '.png" title="' . $row['noteGroupName'] . '" alt="' . $row['noteGroupName'] . '"/>';
                        echo '</div>'; //close noteLabelWrapper div
                        echo '</div>'; //close noteOptContainer div
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
            
            //Scales the rhythm to fit the width of the screen.
            function scaleRhythm() {
                //Get width of rhythm at full resolution
                var rhythmWidth = $("#rhythm").width();
                
                //Get current screen/window width
                var screenWidth = window.innerWidth;
                
                //Compute ratio between current screen and window widths
                var ratio = screenWidth / rhythmWidth;
                
                //Get the total number of systems in the rhythm
                var systemCount = $("#rhythm").find(".system").length;
                
                //Compute the new note height
                var newNoteHeight = $(".note").height() * ratio * .9;
                
                //Compute the new rhythm height
                var newRhythmHeight = systemCount * newNoteHeight + (systemCount - 1) * 40;
                
                //If the new rhythm height is larger than the container, shrink notes
                if (newRhythmHeight > 300) {
                    newNoteHeight -= (newRhythmHeight - 300) / systemCount;
                }
                
                //Set new note height, and new top margin (to center rhythm in container)
                $(".note").css("height",newNoteHeight);
                $("#rhythm").css("margin-top",(300 - $("#rhythm").height()) / 2);
            }
            
            //spaces notes evenly across multiple systems
            function spaceNotes() {
                $(".system").each(function(){
                    $(this).find('.spacer').css("width",0);
                    if ($(this).width() < $("#rhythm").width()) {
                        var sizeDiff = $("#rhythm").width() - $(this).width();
                        var spacerCount = $(this).find('.spacer').length;
                        var spacerSize = sizeDiff / spacerCount;
                        $(this).find('.spacer').css("width",spacerSize);
                    } else {
                        $(this).find('.spacer').css("width",0);
                    }
                });
            }
            
            //Slides down note options
            function showNoteOptions(){
                $("#noteOptionsTab").addClass("selected");
                $("#generalOptionsTab").removeClass("selected");
                $("#noteOptions").slideDown();
                $("#generalOptions").hide();
            }
            
            //Slides down general options
            function showGeneralOptions(){
                $("#generalOptionsTab").addClass("selected");
                $("#noteOptionsTab").removeClass("selected");
                $("#generalOptions").slideDown();
                $("#noteOptions").hide();
            }
            
            //Slides up options panels
            function clearOptions() {
                $("#generalOptionsTab").removeClass("selected");
                $("#noteOptionsTab").removeClass("selected");
                $("#generalOptions").slideUp();
                $("#noteOptions").slideUp();
            }
            
            /*********************/
            /** EVENT LISTENERS **/
            /*********************/
            
            //Random Rhythm Click
            $("#rhythmContainer").click(function(){
                
                //slide up options panels
                clearOptions();
                
                //display preloader
                $(".preloader").css("display","block");
                
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
                        $(".note").imagesLoaded(function(){
                            scaleRhythm();
                            spaceNotes();
                            $(".preloader").css("display","none");
                            $(".note").css("opacity","1");
                        });
                    },
                    error : function(xhr, status, errorThrown) {
                        $("#rhythm").html("<p>There was a problem getting your rhythm.</p>" + 
                                "<p>Sorry about that.  Please click the Randomize " +
                                "button again.  If you are still getting this error, " +
                                "please <a href=\"mailto:bob@derricowebdesign.com\">" +
                                "send us an e-mail.</a></p>" +
                                "<p>Error information: xhr: " + xhr + " | status: " +
                                status + " | errorThrown: " + errorThrown + "</p>");
                    }
                }); 
            });
            
            //Page load
            $(function(){$("#rhythmContainer").click();});
            
            //Resize window
            var resizeTimer;
            $(window).resize(function(){
                if (resizeTimer) {
                    clearTimeout(resizeTimer);
                }
                scaleRhythm();
                resizeTimer = setTimeout(function(){
                    spaceNotes();
                    resizeTimer = null;
                }, 100);
                
            });
            
            //Note Options Tab Click
            $("#noteOptionsTab").click(function(){
                if ($("#noteOptions").is(":visible")) {
                    clearOptions();
                } else {
                    showNoteOptions();
                }
            });
            
            //General Options Tab Click
            $("#generalOptionsTab").click(function(){
                if ($("#generalOptions").is(":visible")) {
                    clearOptions();
                } else {
                    showGeneralOptions();
                }
            });
            
            //Note label click
            $(".noteLabelWrapper").click(function(){
                $(this).siblings("input[type='checkbox']").click();
            });

            //Note options legend click
            $("legend").click(function(){
                
                //checks if all options in fieldset are checked
                var checkedFlag = true;
                $(this).parent().find("input[type='checkbox']").each(function(){
                    if (!($(this).prop("checked"))) {
                        checkedFlag = false;
                    }
                });
                
                if (checkedFlag) {
                    $(this).parent().find("input[type='checkbox']").each(function(){
                        $(this).prop("checked",false);
                    });
                } else {
                    $(this).parent().find("input[type='checkbox']").each(function(){
                        $(this).prop("checked",true);
                    });
                }
            });
        </script>
    </body>
</html>
