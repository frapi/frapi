<?php
/**
 */
class Lupin_Services_GCharts_Line extends Lupin_Services_GCharts implements Lupin_Services_GCharts_Interface
{
    protected $type = 'ls';

    public function get()
    {
        $this->reduceDataset();

        $this->params = array(
            'cht'  => (string)$this->type,
            'chco' => (string)$this->color,
            'chf'  => 'bg,s,65432100', // That's called transparency bitches.
            'chs'  => (string)$this->size,
            'chd'  => 't:' . $this->data,
            //'chds' => (string)$this->min . ',' . (string)$this->max
        );

        return parent::get();
    }

    private function reduceDataset()
    {
        //return true;

        $data = explode(',', $this->data);
        $final = array();
        foreach ($data as $value) {
            if ($value == $this->max && $this->max > 0) {
                $final[] = (($this->max / $this->max) * 100);
            } elseif ($value == 0) {
                $final[] = 0;
            } else {
                $final[] =  ($value / $this->max)*100;
            }
        }

        $final = implode(',', $final);

        $this->data = $final;
    }
}
