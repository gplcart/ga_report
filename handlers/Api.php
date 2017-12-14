<?php

/**
 * @package Google Analitics Report module
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\ga_report\handlers;

use gplcart\core\helpers\Socket as SocketClientHelper;

/**
 * Provides methods to work with Google Analitics API
 */
class Api
{

    /**
     * Socket client helper class instance
     * @var \gplcart\core\helpers\SocketClient $socket
     */
    protected $socket;

    /**
     * @param SocketClientHelper $socket
     */
    public function __construct(SocketClientHelper $socket)
    {
        $this->socket = $socket;
    }

    /**
     * Performs request to get API data
     * @param array $params
     * @param array $provider
     * @return array
     */
    public function process(array $params, array $provider)
    {
        try {
            $result = $this->socket->request($provider['url']['process'], array('query' => $params));
            return json_decode($result['data'], true);
        } catch (\Exception $ex) {
            trigger_error($ex->getMessage());
            return array();
        }
    }

}
