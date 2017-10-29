<?php

/**
 * @package Google Analitics Report module
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\ga_report\handlers;

use gplcart\core\helpers\Curl as CurlHelper;

/**
 * Provides methods to work with Google Analitics API
 */
class Api
{

    /**
     * CURL helper class instance
     * @var \gplcart\core\helpers\Curl $curl
     */
    protected $curl;

    /**
     * @param CurlHelper $curl
     */
    public function __construct(CurlHelper $curl)
    {
        $this->curl = $curl;
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
            $result = $this->curl->get($provider['url']['process'], array('query' => $params));
            return json_decode($result, true);
        } catch (\Exception $ex) {
            trigger_error($ex->getMessage());
            return array();
        }
    }

}
