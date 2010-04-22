<?php
/**
 * Example of how to return an error to be output.
 *
 * It demonstrates the different ways in which
 * an error can be specified and the fallbacks
 * present in this process.
 *
 * @package Action
 */
class Action_ThrowErrorFromDatabase extends Frapi_Action implements Frapi_Action_Interface
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
        $error_to_show = '1.1';
        
        switch ($error_to_show)
        {
            case '1.1':
            {
                //1.1 Database-defined error name and code.
                //Frapi_Error will look it up in APC or Database.
                throw new Frapi_Error('error_name_in_db');   
            }
            case '1.2':
            {
                //1.2 Database-defined error name but user-defined code.
                //Frapi_Error will look it up in APC or Database but use specified code.
                throw new Frapi_Error('error_name_in_db', 500);
            }
            case '1.3':
            {
                //1.3 Database-defined error name but invalid error code.
                //Code must be numeric, so the error below will have the code from the database.
                throw new Frapi_Error('error_name_in_db', 'invalid error code (not numeric)');
            }
            case '2.1':
            {
                //2.1 Undefined error name.
                //Supplied error name is not found in database, so it's used as the message.
                //Error code will be 0, because none specified.
                throw new Frapi_Error('Undefined something or other.');
            }
            case '2.2':
            {
                //2.2 Undefined error name, specified error code.
                //As above we use the error name as message, and use specified error code.
                throw new Frapi_Error('Undefined something or other.', 500);
            }
            
            case '3.1':
            default:
                // 3.1 Undefined error code with different http code
                throw new Frapi_Error('Undefined something or other.', 'Does not exist', 333);
        }
    }

    // Execute action
    public function executeAction()
    {
        return $this->toArray();
    }
}
