<?php
/**
 */
class Lupin_Model
{
    /**
     * Inserts or Updates the model and dependent tables.
     *
     * @param array        $data The data posted in the navigation type form
     * @param integer|null $id  Id of the entity
     *
     * @return bool Whether the save was successful
     */
    public function save(array $data, $id = null)
    {
        if ($id === null) {
            return $this->add($data);
        }

        return $this->update($data, $id);
    }

    /**
     * Includes the authorship data into the data array
     *
     * @param array &$data  Reference to the array to add authorship
     * @param bool  $create Whether or not to append the creation data
     *
     * @return void         Array is passed by reference, no need to return
     */
    public function updateAuthorship(array &$data, $create = false)
    {
        $auth = Zend_Auth::getInstance()->getStorage()->read();

        if ($create) {
            $data['createdBy']    = $auth->id;
            $data['creationAt']   = date('Y-m-d H:i:s', time());
        }

        $data['updatedBy'] = $auth->id;
        $data['updatedAt'] = date('Y-m-d H:i:s', time());
    }

    /**
     * Applies a white list to an array of data, removing elements not contained
     * in it.
     *
     * @param array $whiteList The allowed keys
     * @param array &$data     The full data array
     *
     * @return void            Data is passed by reference
     */
    protected function whiteList($whiteList, &$data)
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $whiteList)) {
                unset($data[$key]);
            }
        }
    }
}
