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
     * Required parameters
     *
     * @var Array An array of required parameters.
     */
    protected $requiredParams = array(
        'name',
    );

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
        $data = array(
            'name' => $this->getParam('name', self::TYPE_OUTPUT),
        );

        return $data;
    }

    // Execute action
    public function executeAction()
    {
        $valid = $this->hasRequiredParameters($this->requiredParams);
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }

        return $this->toArray();
    }
}
