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

use SoapClient as Client;

/**
 * SoapClient Override
 */
class SoapClient extends Client
{
    public function __doRequest($request, $location, $action, $version, $one_way = 0) {
        debug($request);
        debug($location);
        debug($action);
        debug($version);
        exit;
        return parent::__doRequest($request, $location, $action, $version, $one_way);
    }
}
