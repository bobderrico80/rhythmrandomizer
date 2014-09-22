<?php
require_once('sqlconn.php');

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

$measure = 1;
$system = 1;

//div to begin the first system
echo '<div class="system first" id="s' . $system . '">';

//div to begin the first measure
echo '<div class="measure" id="m' . $measure . '">';

//Print the percussion clef
echo '<img class="note" src="notes/0.png" title="percussion clef" '
    . 'alt="percussion clef"/>';

//Print time signature
echo '<img class="note" src="notes/' . $timeSignature . '.png" alt="time signature ' . $timeSignature . '/4"/>';
echo '<div class="spacer"></div>';

//Prints as many measures as indicated by the phrase length selection

while ($measure <= $phraseLength) {

    //begin a new system after every 4th measure on phrases longer than 4 measures.
    if (((($measure - 1) % 4) == 0) && ($phraseLength > 4) && ($system != 1)) {
        echo '<div class="system" id="s' . $system . '">';
        echo '<div class="measure" id="m' . $measure . '">';
        echo '<img class="note" src="notes/0.png" alt="percussion clef"/>';
        echo '<div class="spacer"></div>';
    }

    //begin a new div for other measures.
    if (!(($measure == 1) || ((($measure - 1) % 4) == 0))) {
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
        echo '<img class="note" src="notes/' . $note['noteGraphic'] .
                '.png" alt="' . $note['noteName'] . '"/>';
        echo '<div class="spacer"></div>';

        //Adds random note's value to total number of beats
        $beats += $note['noteValue'];
        //$beats++;
    }

    if ($measure == $phraseLength) {
        //If final measure, print double bar line
        echo '<img class="note" src="notes/1.png" alt="double barline"/>';
        echo '</div>'; //closes measure div
        echo '</div>'; //closes system div
    } elseif ((($measure % 4) == 0) && ($phraseLength > 4)) {
        //End the system after every 4th measure in phrases longer than 4.
        echo '<img class="note" src="notes/bb.png" alt="barline"/>';
        echo '</div>'; //closes measure div
        echo '</div>'; //closes system div
        echo '<br>';
        $system++;
    } else {
        echo '<img class="note" src=notes/b.png alt="barline"/>';
        echo '</div>'; //closes measure div
    }

    //Increment to next measure
    $measure++;
}
