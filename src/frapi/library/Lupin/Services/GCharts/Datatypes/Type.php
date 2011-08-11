<?php
/**
 */
class Lupin_Services_GCharts_Datatypes_Type
{
    private $type;
    
    public function __construct($type)
    {
        $this->type = $type;
    }
    
    public function __toString()
    {
        return $this->type;
    }
    
}