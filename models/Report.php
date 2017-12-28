<?php

/**
 * @package Google Analytics Report
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\ga_report\models;

use Exception;
use gplcart\core\Cache,
    gplcart\core\Hook;
use gplcart\core\models\Oauth as OauthModel,
    gplcart\core\models\Translation as TranslationModel;

/**
 * Manages basic behaviors and data related to Google Analytics Report
 */
class Report
{

    /**
     * Hook class instance
     * @var \gplcart\core\Hook $hook
     */
    protected $hook;

    /**
     * Translation UI model instance
     * @var \gplcart\core\models\Translation $translation
     */
    protected $translation;

    /**
     * Oauth model instance
     * @var \gplcart\core\models\Oauth $oauth
     */
    protected $oauth;

    /**
     * Cache class instance
     * @var \gplcart\core\Cache $cache
     */
    protected $cache;

    /**
     * @param Hook $hook
     * @param Cache $cache
     * @param OauthModel $oauth
     * @param TranslationModel $translation
     */
    public function __construct(Hook $hook, Cache $cache, OauthModel $oauth,
            TranslationModel $translation)
    {
        $this->hook = $hook;
        $this->cache = $cache;
        $this->oauth = $oauth;
        $this->translation = $translation;
    }

    /**
     * Returns reporting data
     * @param string $handler_id
     * @param string|array $data
     * @return array
     */
    public function get($handler_id, $data)
    {
        $data += array(
            'cache' => 0,
            'store_id' => 1,
            'query' => array()
        );

        $handler = $this->getHandler($handler_id);

        if (empty($handler)) {
            return array('error' => $this->translation->text('Invalid Oauth provider'));
        }

        $cache_key = "ga_report.$handler_id.{$data['store_id']}";

        $provider = $this->oauth->getProvider('ga');
        $report = array('handler' => $handler, 'provider' => $provider);
        $cache = $this->cache->get($cache_key, array('lifespan' => $data['cache']));

        if (!empty($data['cache']) && isset($cache)) {
            return $report + array(
                'data' => $cache,
                'updated' => $this->cache->getFileMtime()
            );
        }

        try {
            $token = $this->oauth->exchangeTokenServer($provider, $provider['settings']);
        } catch (Exception $ex) {
            return $report + array('error' => $ex->getMessage());
        }

        if (empty($token['access_token'])) {
            return $report + array('error' => $this->translation->text('Failed to get access token'));
        }

        $data['query']['access_token'] = $token['access_token'];
        $results = $this->oauth->process($provider, array_merge($handler['query'], $data['query']));

        if (isset($results['error']['message'])) {
            return $report + array('error' => $results['error']['message']);
        }

        $report += array(
            'data' => $results,
            'updated' => GC_TIME
        );

        $this->cache->set($cache_key, $results);
        return $report;
    }

    /**
     * Clear cached report data
     * @param string|null $handler_id
     * @param integer|string|null $store_id
     */
    public function clearCache($handler_id = null, $store_id = null)
    {
        $pattern = 'ga_report.';

        if (isset($handler_id)) {
            $pattern .= "$handler_id.";
        }

        if (isset($store_id)) {
            $pattern .= "$store_id";
        }

        $this->cache->clear('', array('pattern' => "$pattern*"));
    }

    /**
     * Returns a handler data
     * @param string $handler_id
     * @return array
     */
    public function getHandler($handler_id)
    {
        $handlers = $this->getHandlers();
        return empty($handlers[$handler_id]) ? array() : $handlers[$handler_id];
    }

    /**
     * Returns an array of handlers
     * @return array
     */
    public function getHandlers()
    {
        $handlers = &gplcart_static(__METHOD__);

        if (isset($handlers)) {
            return $handlers;
        }

        $handlers = $this->getDefaultHandlers();

        $default = array(
            'end-date' => date('Y-m-d'),
            'start-date' => date('Y-m-d', strtotime('-1 month'))
        );

        foreach ($handlers as $id => &$handler) {
            $handler += array('template' => "ga_report|panels/$id");
            $handler['query'] += $default;
            $handler['id'] = $id;
        }

        $this->hook->attach('module.ga.report.handlers', $handlers);
        return $handlers;
    }

    /**
     * Returns an array of default handlers
     * @return array
     */
    protected function getDefaultHandlers()
    {
        $handlers = gplcart_config_get(__DIR__ . '/../config/handlers.php');
        foreach ($handlers as &$handler) {
            $handler['name'] = $this->translation->text($handler['name']);
        }

        return $handlers;
    }

}
