<?php
/**
 */
class Lupin_Pdf extends Zend_Pdf
{
    private static $_pdf;
    private static $file;
    
    public static function load($source = null, $revision = null)
    {
        self::$file = $source;
        self::$_pdf = parent::load($source);
    }
    
    public static function getPagesCount()
    {
        return count(self::$_pdf->pages);
    }
    
    public static function convert($fileName, $position, $newName)
    {
        $fileName = escapeshellarg($fileName);
        $position = (int)$position;
        $newName  = escapeshellarg($newName);
        
        $command = '/usr/bin/convert ' . $fileName . '[' . $position . '] ' . $newName;

        $res = system($command);
        
        if ($res) {
            return $newName;
        }
        
        return false;
    }
}
