<?php
/**
 * QueenCityCodeFactory(tm) : Web application developers (http://queencitycodefactory.com)
 * Copyright (c) Queen City Code Factory, Inc. (http://queencitycodefactory.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Queen City Code Factory, Inc. (http://queencitycodefactory.com)
 * @link          https://github.com/QueenCityCodeFactory/CakeSoap CakePHP Soap Plugin
 * @since         0.1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace CakeSoap\Network;

use Cake\Core\Configure;
use Cake\Log\LogTrait;
use SoapClient as Client;

/**
 * SoapClient Override
 */
class SoapClient extends Client
{

    /**
     * Performs a SOAP request
     *
     * @param string $request The XML SOAP request.
     * @param string $location The URL to request.
     * @param string $action The SOAP action.
     * @param int  $version The SOAP version.
     * @param int $oneWay If set to 1, this method returns nothing. Use this where a response is not expected.
     * @return string The XML SOAP response.
     */
    public function __doRequest($request, $location, $action, $version, $oneWay = 0)
    {
        if (Configure::read('debug') === true) {
            $this->log($request);
            $this->log($location);
            $this->log($action);
            $this->log($version);
        }
        return parent::__doRequest($request, $location, $action, $version, $oneWay);
    }
}
