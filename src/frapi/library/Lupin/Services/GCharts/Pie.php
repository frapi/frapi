<?php
/**
 */
class Lupin_Services_GCharts_Pie extends Lupin_Services_GCharts implements Lupin_Services_GCharts_Interface
{
    protected $type = 'p';
    
    public function setLabels(Lupin_Services_GCharts_Labels $label)
    {
        $this->labels = $label;
    }
    
    public function setDatasetLabels(Lupin_Services_GCharts_Datatypes_DatasetLabels $label)
    {
        $this->datasetLabels = $label;
    }
    
    public function get()
    {
        $this->params = array(
            'cht'  => (string)$this->type,
            'chco' => (string)$this->color,
            'chf'  => 'bg,s,65432100', // That's called transparency bitches.
            'chs'  => (string)$this->size,
            'chd'  => 't:' . $this->data
        );
        
        if (isset($this->datasetLabels)) {
            $this->params['chdl'] = (string)$this->datasetLabels;
        }
        
        if (isset($this->labels)) {
            $this->params['chl'] = (string)$this->labels;
        }
        
        return parent::get();
    }
}
