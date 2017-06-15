<?php
namespace Flacox;

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."cms_chart.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."calculateVelocity.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."JsonFileManager.class.php");

/**
 * @params user, Tuleap user
 * @params projectID, id of the chosen project
 */
function drawGraph(TuleapUser $user, $projectID)
{
    $filesNames = calculateVelocity($user, $projectID);
    $jsonFileManager = new JsonFileManager();
    
    foreach ($filesNames as $fileName) {
        $jsonData = $jsonFileManager->readFile($fileName);
        $formatedData = formatData($jsonData);
        $init_chart = setChartProperties();
        cms_chart($formatedData, $init_chart);
    }
}

/**
 * @params rawData, a JSON with data that need to be formatted 
 * in order to be drawn in a chart
 */
function formatData($rawData)
{
    $cookedData = array();
    $keysList = array_keys($rawData[0]);
    array_splice($keysList, -1, 1);

    for ($i = 0; $i < count($keysList); $i++) {
        $measurement = $keysList[$i];
        $temporaryValues = array();
        
        for ($j = 0; $j < count($rawData); $j++) {
            $temporaryValues[$rawData[$j]['label']] = $rawData[$j][$measurement];
        }
        
        $cookedData[$measurement] = $temporaryValues;
    }

    return $cookedData;
}

/**
 *
 */
function setChartProperties()
{
    $init_chart = array();
    $init_chart['title'] = "Velocity";
    $init_chart['valShow']= 1;
    $init_chart['gapR'] = 5;
    $init_chart['gapL'] = -30;
    $init_chart['css'] = 1;
    return $init_chart;
}


$userName = $argv[1];
$password = $argv[2];

$u = new TuleapUser($userName, $password);
if ($u->getToken() !== NULL) {
    drawGraph($u, $argv[3]);
}
?>
