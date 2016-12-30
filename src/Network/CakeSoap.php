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

use CakeSoap\Network\SoapClient;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Core\InstanceConfigTrait;
use Cake\Log\LogTrait;
use SoapFault;

/**
 * CakePHP SoapClient Wrapper
 */
class CakeSoap
{

    use InstanceConfigTrait;
    use LogTrait;

    /**
     * Log Error
     *
     * @var bool
     */
    public $logErrors = false;


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
     * ### Options:
     *
     * - `debug` boolean - Do you want to see debug info? Default is false
     * - `logErrors` boolean - Do you want to log errors? Default is false
     * - `options` array - Context options for `stream_context` SoapClient Option
     *
     * @param array $config An array defining the configuration settings
     * @param array $options The options
     */
    public function __construct(array $config = [], array $options = [])
    {
        $this->config($config);

        if (!isset($options['debug'])) {
            $this->debug = Configure::read('debug');
        }
        if (isset($options['debug']) && $options['debug'] === true) {
            $this->debug = true;
        }
        if (isset($options['logErrors']) && $options['logErrors'] === true) {
            $this->logErrors = true;
        }

        if (!isset($options['options'])) {
            $options['options'];
        }

        $this->connect($options['options']);
    }

    /**
     * Setup Configuration options
     *
     * @param array $options The options
     * @return array Configuration options
     */
    protected function _parseConfig(array $options = [])
    {
        if (!class_exists('SoapClient')) {
            $this->handleError('Class SoapClient not found, please enable Soap extensions');
        }

        $opts = [
            'http' => [
                'user_agent' => $this->config('userAgent')
            ]
        ];

        $opts += $options;

        $context = stream_context_create($opts);
        $config = [
            'trace' => $this->debug,
            'stream_context' => $context,
            'cache_wsdl' => WSDL_CACHE_NONE
        ];
        if (!empty($this->config('location'))) {
            $config['location'] = $this->config('location');
        }
        if (!empty($this->config('uri'))) {
            $config['uri'] = $this->config('uri');
        }
        if (!empty($this->config('login'))) {
            $config['login'] = $this->config('login');
            $config['password'] = $this->config('password');
            $config['authentication'] = $this->config('authentication');
        }

        return $config;
    }

    /**
     * Connects to the SOAP server using the WSDL in the configuration
     *
     * @param array $options The options
     * @return bool True on success, false on failure
     */
    public function connect(array $options = [])
    {
        $config = $this->_parseConfig($options);
        try {
            $this->client = new SoapClient($this->config('wsdl'), $config);
        } catch (SoapFault $fault) {
            $this->handleError($fault->faultstring);
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
        if (!$this->connected) {
            $this->connect();
        }
        try {
            $result = $this->client->__soapCall($action, $data);
        } catch (SoapFault $fault) {
            $this->handleError($fault->faultstring);
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
     * @param string $error The Error
     * @return void
     */
    public function handleError($error = null)
    {
        if ($this->logErrors === true) {
            $this->log($error);
            if ($this->client) {
                $this->log($this->client->__getLastRequest());
            }
        }
        throw new Exception($error);
    }
}
