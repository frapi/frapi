<?php

interface Lupin_Services_GCharts_Interface
{
    public function setSize(Lupin_Services_GCharts_Datatypes_Size $size);
    public function setData(array $data);
    public function setType(Lupin_Services_GCharts_Datatypes_Type $type);
    public function setColor(Lupin_Services_GCharts_Datatypes_Color $color);
}