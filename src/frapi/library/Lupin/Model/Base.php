<?php

interface Lupin_Model_Base
{
    /**
     * Encapsulates the insertions and updates
     *
     * @param array $data    The data being inserted in the database
     * @param mixed $primary The primary key value if updating
     *
     * @return void
     * @throws echolibre_Form_Excetion    In case the form does not have a token
     */
    public function save(array $data, $id = null);

    public function add(array $data);

    public function update(array $data, $id);

    public function delete($id);
}
