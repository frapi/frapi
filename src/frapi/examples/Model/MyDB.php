<?php

class Vendor_MyDB
{
    public $db;
    
    public function __construct()
    {
        $this->db = Frapi_Database::getInstance();
    }
}