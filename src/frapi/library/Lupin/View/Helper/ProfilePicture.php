<?php

/**
 * Helper to generate a profile picture
 * @category   echolibre
 * @package    Lupin_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2009 echolibre
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
