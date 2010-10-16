<?php

class Lupin_Model_DB extends Lupin_Model
{
    protected $db;

    public function __construct()
    {
        $this->db = Zend_Registry::get('db');
    }

    /**
     * Encapsulates the insertions and updates within a DB transation
     *
     * @param array $data    The data being inserted in the database
     * @param mixed $primary The primary key value if updating
     *
     * @return bool
     */
    public function save(array $data, $id = null)
    {
        $res = false;
        $this->db->beginTransaction();
        try {
            $res = $this->_save($data, $id);
            if (true === $res) {
                $this->db->commit();
                return true;
            }
        } catch(Exception $e) {
            if (APPLICATION_ENV === 'development') {
                echo '<pre>'; print_r($e);exit;
            }
        }

        $this->db->rollBack();
        return $res;
    }
}
