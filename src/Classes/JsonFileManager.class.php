<?php
namespace Flacox;

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."iFileManager.interface.php");

class JsonFileManager implements iFileManager
{
    public function readFile($file)
    {
        $fileData = file_get_contents($file);
        $jsonData = json_decode($fileData, true);
        
        return $jsonData;
    }
    
    public function updateFile($file)
    {
        return true;
    }
    
    public function deleteFile($file)
    {
        return unlink($file);
    }

    public function saveToFile($fileName, $data)
    {
        $jsonString = json_encode($data);
        return file_put_contents($fileName, $jsonString);
    }
}
?>
