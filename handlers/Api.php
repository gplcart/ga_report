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
     */
    protected $curl;

    /**
     */
    public function __construct(CurlHelper $curl)
    {
        $this->curl = $curl;
    }

    /**
     */
    public function process(array $params, $provider)
    {
        $result = $this->curl->get($provider['url']['process'], array('query' => $params));
        return json_decode($result, true);
    }

}
