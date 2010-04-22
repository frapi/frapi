<?php
/**
 * Action List our services
 *
 * An example of what an actionContextType
 * can actually look like.
 *
 * This should be used as a skeleton or as a reference
 * in case one forgets how to create a new action.
 *
 * Remember, this HAS to return an array!
 *
 * @package Action
 */
class Action_Listservices extends Frapi_Action implements Frapi_Action_Interface
{

    /**
     * To Array
     *
     * This method returns the value found in the database
     * into an associative array.
     *
     * @return array  An array of the data received.
     */
    public function toArray()
    {
        $arr = array(
            'services' => $this->getServices(),
        );

        return $arr;
    }

    // Execute action
    public function executeAction()
    {
        return $this->toArray();
    }

    private function getServices()
    {
        $db = Frapi_Database::getInstance();
        $results = $db->fetchAll('SELECT * FROM services');
        
        $services = array(
            'php', 'api', 'systems architecture', 'architecture', 'training', 'web applications', 'mentoring',
        );

        return $services;
    }
}
