<?php
/**
 */
// http://chart.apis.google.com/chart?
// chs=128x18
// cht=ls
// chco=0077CC
// chd=t:27,2,60,2222,25,39,25,31,26,28,80,28,27,3,27,29,26,1000,70,25
abstract class Lupin_Services_GCharts
{
    //protected $endpoint = 'http://chart.apis.google.com/chart?';
    protected $endpoint = '';

    protected $params;

    protected $size, $type, $color, $data, $labels, $datasetlabel;
    protected $min, $max;

    public function setData(array $data)
    {
        $this->min = min($data);
        $this->max = max($data);

        $this->data = implode(',', $data);
    }

    public function setSize(Lupin_Services_GCharts_Datatypes_Size $size)
    {
        $this->size = $size;
    }

    public function setType(Lupin_Services_GCharts_Datatypes_Type $type)
    {
        $this->type = $type;
    }

    public function setColor(Lupin_Services_GCharts_Datatypes_Color $color)
    {
        $this->color = $color;
    }

    public function get()
    {
        $query = http_build_query($this->params);
        $url = $this->endpoint . $query;
        return $url;
    }
}
