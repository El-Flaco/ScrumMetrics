<?php
namespace Flacox;

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."cms_chart.php");
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Functions".DIRECTORY_SEPARATOR."calculateVelocity.php");
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."Classes".DIRECTORY_SEPARATOR."JsonFileManager.class.php");

function drawVelocity($userID, $userToken, $projectID)
{
    $filesNames = calculateVelocity($userID, $userToken, $projectID);
    $jsonFileManager = new JsonFileManager();
    
    foreach ($filesNames as $fileName) {
        $jsonData = $jsonFileManager->readFile($fileName);
        $formatedData = formatData($jsonData);
        $init_chart = setChartProperties();
        cms_chart($formatedData, $init_chart);
        $jsonFileManager->deleteFile($fileName);
    }
}

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

function setChartProperties()
{
    $init_chart = array();
    $init_chart['title'] = "Velocity";
    $init_chart['valShow']= 1;
    $init_chart['gapR'] = 5;
    $init_chart['gapL'] = -30;
    $init_chart['css'] = 1;
    $init_chart['colorDel'] = '0,1,2';
    return $init_chart;
}
?>
