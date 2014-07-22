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

//Get values from GET
$timeSignature = $_GET['timeSignature'];
$phraseLength = $_GET['phraseLength'];
$noteOptString = $_GET['noteOptions'];

//Split up note options string
$noteOptions = explode('a', $noteOptString);

//Create sql query
$sql = 'SELECT
            noteName,
            noteValue,
            noteGraphic
        FROM
            notes
        WHERE';

//append noteOptions as WHERE clauses
foreach ($noteOptions as $opt) {
    $sql = $sql . ' noteGroupID = ' . $opt . ' OR';
}

//remove final " OR"
$sql = substr($sql, 0, strlen($sql) - 3);

//query the database and get all results as an array
/* This will return a table with the name, graphic, and value of 
 * the notes that the user selected prior to submitting the form
 */
$stmt = $db->query($sql);
$result = $stmt->fetchAll();

//Get the total number of options selected
$numOpts = count($result);

/***************************/
/** BEGIN PRINTING RHYTHM **/
/***************************/

//div to begin the first measure
echo '<div class="measure" id="m1' . $measure . '">';

//Print time signature
echo '<img class="note" src="notes/' . $timeSignature . '.png" title="time signature ' . 
        $timeSignature . '/4" alt="time signature ' . $timeSignature . '/4"/>';

//Prints as many measures as indicated by the phrase length selection
$measure = 1;
while ($measure <= $phraseLength) {
    
    //begin a new div for other measures.
    if ($measure != 1) {
        echo '<div class="measure" id="m' . $measure . '">';
    }
    
    //Prints random measure according to time signature
    $beats = 0;
    while ($beats < $timeSignature) {
        //Generate a random number
        $random = rand(0, $numOpts - 1);
        
        //Get the random note from results
        $note = $result[$random];
        
        //Continues if chosen note will not fit in the measure
        if ($beats + $note['noteValue'] > $timeSignature) {
            continue;
        }
        
        //Prints random note
        echo '<img class="note" src="notes/' . $note['noteGraphic'] . '.png" title="' .
                $note['noteName'] . '" alt="' . $note['noteName'] . '"/>';
        
        //Adds random note's value to total number of beats
        $beats += $note['noteValue'];
        //$beats++;
    }
    
    //If last measure
    if ($measure == $phraseLength) {
        echo '<img class="note" src="notes/1.png" title="double barline" alt="double barline"/>';
        echo '</div>';
    } else {
        echo '<img class="note" src=notes/b.png title="barline" alt="barline"/>';
        echo '</div>';
    }
    
    //Increment to next measure
    $measure++;
}