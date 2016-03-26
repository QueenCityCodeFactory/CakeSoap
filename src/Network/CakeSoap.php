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

namespace QueenCityCodeFactory\Network;

use Cake\Core\Configure;
use Cake\Core\InstanceConfigTrait;
use Cake\Log\LogTrait;
use Cake\Network\Request;
use Cake\Network\Response;
use QueenCityCodeFactory\Network\SoapClient;

/**
 * CakePHP SoapClient Wrapper
 */
class CakeSoap
{

    use InstanceConfigTrait;
    use LogTrait;

    /**
     * SoapClient instance
     *
     * @var SoapClient
     */
    public $client = null;

    /**
     * Connection status
     *
     * @var boolean
     */
    public $connected = false;

    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [
        'wsdl' => null,
        'userAgent' => 'SoapClient',
        'location' => '',
        'uri' => '',
        'login' => '',
        'password' => '',
        'authentication' => 'SOAP_AUTHENTICATION_`IC',
        'trace' => false
    ];

    /**
     * Constructor
     *
     * @param array $config An array defining the configuration settings
     */
    public function __construct(array $config = [])
    {
        $this->config($config);
        $this->connect();
    }

    /**
     * Setup Configuration options
     *
     * @return array Configuration options
     */
    protected function _parseConfig()
    {
        if (!class_exists('SoapClient')) {
            $this->error = 'Class SoapClient not found, please enable Soap extensions';
            $this->showError();
            return false;
        }

        $opts = [
            'http' => [
                'user_agent' => $this->config('userAgent')
            ]
        ];

        $context = stream_context_create($opts);
        $options = [
            'trace' => Configure::read('debug'),
            'stream_context' => $context,
            'cache_wsdl' => WSDL_CACHE_NONE
        ];
        if (!empty($this->config('location'))) {
            $options['location'] = $this->config('location');
        }
        if (!empty($this->config('uri'))) {
            $options['uri'] = $this->config('uri');
        }
        if (!empty($this->config('login'))) {
            $options['login'] = $this->config('login');
            $options['password'] = $this->config('password');
            $options['authentication'] = $this->config('authentication');
        }
        return $options;
    }

    /**
     * Connects to the SOAP server using the WSDL in the configuration
     *
     * @return bool True on success, false on failure
     */
    public function connect()
    {
        $options = $this->_parseConfig();
        try {
            $this->client = new SoapClient($this->config('wsdl'), $options);
        } catch (SoapFault $fault) {
            $this->error = $fault->faultstring;
            $this->showError();
        }
        if ($this->client) {
            $this->connected = true;
        }
        return $this->connected;
    }

    /**
     * Sets the SoapClient instance to null
     *
     * @return bool True
     */
    public function close()
    {
        $this->client = null;
        $this->connected = false;
        return true;
    }

    /**
     * Returns the available SOAP methods
     *
     * @return array List of SOAP methods
     */
    public function listSources()
    {
        return $this->client->__getFunctions();
    }

    /**
     * Query the SOAP server with the given method and parameters
     *
     * @param string $action The WSDL Action
     * @param array $data The data array
     * @return mixed Returns the result on success, false on failure
     */
    public function sendRequest($action, $data)
    {
        $this->error = false;
        if (!$this->connected) {
            $this->connect();
        }

        try {
            $result = $this->client->__soapCall($action, $data);
        } catch (SoapFault $fault) {
            $this->error = $fault->faultstring;
            $this->showError();
            return false;
        }
        return $result;
    }

    /**
     * Returns the last SOAP response
     *
     * @return string The last SOAP response
     */
    public function getResponse()
    {
        return $this->client->__getLastResponse();
    }

    /**
     * Returns the last SOAP request
     *
     * @return string The last SOAP request
     */
    public function getRequest()
    {
        return $this->client->__getLastRequest();
    }

    /**
     * Shows an error message and outputs the SOAP result if passed
     *
     * @param string $result A SOAP result
     * @return void
     */
    public function showError($result = null)
    {
        if (Configure::read('debug') > 0) {
            if ($this->error) {
                trigger_error('<span style = "color:Red;text-align:left"><b>SOAP Error:</b> ' . $this->error . '</span>', E_USER_WARNING);
            }
            if (!empty($result)) {
                echo sprintf("<p><b>Result:</b> %s </p>", $result);
            }
        }

        $this->log($this->client->__getLastRequest());
    }
}
