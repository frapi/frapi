<?php
/**
 */
class Lupin_Services_GCharts_Datatypes_Size
{
    private $width;
    private $height;
    
    public function __construct($width, $height)
    {
        $this->width  = $width;
        $this->height = $height;
    }
    
    public function __toString()
    {
        return sprintf("%sx%s", $this->width, $this->height);
    }
    
}