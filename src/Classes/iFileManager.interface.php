<?php
namespace Flacox;

interface iFileManager
{
    public function readFile($file);
    public function updateFile($file);
    public function deleteFile($file);
    public function saveToFile($fileName, $data);
}
?>
