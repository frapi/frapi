<?php

class Default_Model_ActionParam extends Lupin_Model_DB
{
    public function add($actionId, $param, $required)
    {
        $sql = 'INSERT INTO actions_params (action_id, name, required) VALUES (';
        $sql.= ':actionId, :name, :required)';
        $values = array(
            ':actionId' => $actionId,
            ':name'     => $param,
            ':required' => $required,
        );
        $this->db->query($sql, $values);
        return true;
    }

    public function edit($actionId, $param, $required, $id)
    {
        $sql = 'SELECT id FROM actions_params WHERE action_id = ' . $this->db->quote($actionId, 'INTEGER');
        $sql.= ' AND name = ' . $this->db->quote($param) . ' AND id <> ' . $this->db->quote($id, 'INTEGER');
        $result = $this->db->fetchRow($sql);
        if ($result !== false) {
            return false;
        }

        $sql = '
        UPDATE actions_params SET
            name     = :name,
            required = :required
        WHERE id = :id';
        $values = array(
            ':name'     => $param,
            ':required' => $required,
            ':id'       => $id,
        );
        $this->db->query($sql, $values);
        return true;
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM actions_params WHERE id = ' . $this->db->quote($id, 'INTEGER');
        $this->db->query($sql);
    }

    public function getParamsForActions($id)
    {
        $sql = 'SELECT 
                * 
                FROM actions_params 
                WHERE action_id = ' . $this->db->quote($id, 'INTEGER').'
                ORDER BY required DESC, name ASC';
        return $this->db->fetchAll($sql);
    }

    public function getByName($name, $id)
    {
        $sql = 'SELECT * FROM actions_params WHERE
            action_id = ' . $this->db->quote($id, 'INTEGER') . '
          AND
            name = ' . $this->db->quote($name);;
        return $this->db->fetchRow($sql);
    }
}