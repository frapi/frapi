<?php
/**
 * Action Template
 *
 * An example of what an actionType
 * can actually look like.
 *
 * This should be used as a skeleton or as a reference
 * in case one forgets how to create a new action.
 *
 * Remember, this HAS to return an array!
 *
 * @package Action
 */
class Action_Template extends Frapi_Action implements Frapi_Action_Interface
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
            'code' => 0,
        );

        return $arr;
    }

    /**
     * Execute the action
     *
     * This is the method that is getting called from
     * and that is then calling toArray()
     *
     * @return mixed Error or the array for the output to display.
     */
    public function executeAction()
    {
        return $this->toArray();
    }
}