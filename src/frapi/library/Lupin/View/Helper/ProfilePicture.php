<?php

/**
 * Helper to generate a profile picture
 * @category   Lupin
 * @package    Lupin_View
 * @subpackage Helper
 * @license    New BSD License
 */
class Zend_View_Helper_ProfilePicture extends Zend_View_Helper_Abstract
{
    public function profilePicture($size, $pictureFile)
    {
        if (empty($pictureFile) || $pictureFile == '') {
            $pictureFile = 'default.png';
        }
        
        $path = '/img/thumbnails/teachers/' . $size . DIRECTORY_SEPARATOR;
        echo $path . $pictureFile;
    }
}
