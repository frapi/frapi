<?php
/**
 */
class Lupin_Services_GCharts_Datatypes_Labels
{
    private $labels;
    
    public function __construct($labels)
    {
        $this->labels = $labels;
    }
    
    public function __toString()
    {
        return implode('|', $this->labels);
    }
    
}