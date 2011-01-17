<?php
/**
 * Custom Authorization Example
 *
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://getfrapi.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@getfrapi.com so we can send you a copy immediately.
 *
 * The custom authorization class
 *
 * @uses      Frapi_Authorization_HTTP_Digest
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
/**
 * This model class is used as an example for anyone who would like to use granular 
 * authentication per HTTP verb. 
 *
 * All one has to do to use the built-in frapi changes is to invoke:
 * <code>
 * $auth = new Custom_Model_Auth();
 * </code>
 *
 * In their "Action/File.php" actions.
 *
 * @license   New BSD
 * @copyright echolibre ltd.
 * @package   frapi
 */
class Custom_Model_Auth
{
    /**
     * Constructor
     *
     * This is the constructor so people only have to instantiate
     * the object and benefit from the authentication mechanism provided
     * and built-in FRAPI.
     */
    public function __construct()
    {
        $authorization = new Frapi_Authorization_HTTP_Digest();

        $authParams = array(
            'digest' => isset($_SERVER['PHP_AUTH_DIGEST']) ? $_SERVER['PHP_AUTH_DIGEST'] : null
        );

        $authorization->setAuthorizationParams($authParams);
        $authorization->authorize();
    }
}
