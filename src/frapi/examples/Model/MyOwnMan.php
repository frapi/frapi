<?php

class Vendor_MyOwnMan extends Vendor_MyDB
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function fun()
    {
        $sql = "
            SELECT * FROM testing
        ";
        
        return $this->db->fetchAll($sql);
    }
}