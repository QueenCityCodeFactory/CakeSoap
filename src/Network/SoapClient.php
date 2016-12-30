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
use Psr\Log\LogLevel;
use SoapClient as Client;

/**
 * SoapClient Override
 */
class SoapClient extends Client
{

    use LogTrait;

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
            $this->log($request, LogLevel::INFO);
            $this->log($location, LogLevel::INFO);
            $this->log($action, LogLevel::INFO);
            $this->log($version, LogLevel::INFO);
        }

        return parent::__doRequest($request, $location, $action, $version, $oneWay);
    }

    /**
     * Calls a SOAP function
     *
     * @param string $functionName The name of the SOAP function to call.
     * @param array $arguments An array of the arguments to pass to the function.
     * @param array $options An associative array of options to pass to the client.
     * @param array $inputHeaders An array of headers to be sent along with the SOAP request.
     * @param array $outputHeaders If supplied, this array will be filled with the headers from the SOAP response.
     * @return mixed
     */
    public function __soapCall($functionName, $arguments, $options = null, $inputHeaders = null, &$outputHeaders = null)
    {
        if (Configure::read('debug') === true) {
            $this->log($functionName, LogLevel::INFO);
            $this->log($arguments, LogLevel::INFO);
            $this->log($options, LogLevel::INFO);
            $this->log($inputHeaders, LogLevel::INFO);
            $this->log($outputHeaders, LogLevel::INFO);
        }

        return parent::__soapCall($functionName, $arguments, $options, $inputHeaders, $outputHeaders);
    }
}
