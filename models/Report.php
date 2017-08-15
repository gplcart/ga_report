<?php

/**
 * @package Google Analytics Report
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\ga_report\models;

use gplcart\core\Model,
    gplcart\core\Cache;
use gplcart\core\models\Oauth as OauthModel,
    gplcart\core\models\Language as LanguageModel;
use gplcart\core\exceptions\OauthAuthorization as OauthAuthorizationException;

/**
 * Manages basic behaviors and data related to Google Analytics Report
 */
class Report extends Model
{

    /**
     * Language model instance
     * @var \gplcart\core\models\Language $language
     */
    protected $language;

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
     * @param Cache $cache
     * @param OauthModel $oauth
     * @param LanguageModel $language
     */
    public function __construct(Cache $cache, OauthModel $oauth,
            LanguageModel $language)
    {
        parent::__construct();

        $this->cache = $cache;
        $this->oauth = $oauth;
        $this->language = $language;
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
            return array('error' => $this->language->text('Invalid Oauth provider'));
        }

        $cache_key = "ga_report.$handler_id.{$data['store_id']}";

        $provider = $this->oauth->getProvider('ga');
        $report = array('handler' => $handler, 'provider' => $provider);
        $cache = $this->cache->get($cache_key, array('lifespan' => $data['cache']));

        if (!empty($data['cache']) && isset($cache)) {
            return $report + array('data' => $cache, 'updated' => $this->cache->getFileMtime());
        }

        try {
            $token = $this->oauth->exchangeTokenServer($provider, $provider['settings']);
        } catch (OauthAuthorizationException $ex) {
            return $report + array('error' => $ex->getMessage());
        }

        if (empty($token['access_token'])) {
            return $report + array('error' => $this->language->text('Failed to get access token'));
        }

        $data['query']['access_token'] = $token['access_token'];
        $results = $this->oauth->process($provider, array_merge($handler['query'], $data['query']));

        if (isset($results['error']['message'])) {
            return $report + array('error' => $results['error']['message']);
        }

        $report += array('data' => $results, 'updated' => GC_TIME);
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
        return array(
            'visit_date' => array(
                'name' => $this->language->text('Visits by date'),
                'query' => array(
                    'metrics' => 'ga:visits',
                    'dimensions' => 'ga:date',
                )
            ),
            'visit_country' => array(
                'name' => $this->language->text('Visits by countries'),
                'query' => array(
                    'sort' => '-ga:visits',
                    'metrics' => 'ga:visits',
                    'dimensions' => 'ga:country'
                )
            ),
            'visit_city' => array(
                'name' => $this->language->text('Visits by cities'),
                'query' => array(
                    'sort' => '-ga:visits',
                    'metrics' => 'ga:visits',
                    'dimensions' => 'ga:city'
                )
            ),
            'visit_language' => array(
                'name' => $this->language->text('Visits by languages'),
                'query' => array(
                    'sort' => '-ga:visits',
                    'metrics' => 'ga:visits',
                    'dimensions' => 'ga:language'
                )
            ),
            'visit_browser' => array(
                'name' => $this->language->text('Visits by browsers'),
                'query' => array(
                    'sort' => '-ga:visits',
                    'metrics' => 'ga:visits',
                    'dimensions' => 'ga:browser'
                )
            ),
            'visit_os' => array(
                'name' => $this->language->text('Visits by OS'),
                'query' => array(
                    'sort' => '-ga:visits',
                    'metrics' => 'ga:visits',
                    'dimensions' => 'ga:operatingSystem'
                )
            ),
            'visit_resolution' => array(
                'name' => $this->language->text('Visits by screen resolution'),
                'query' => array(
                    'sort' => '-ga:visits',
                    'metrics' => 'ga:visits',
                    'dimensions' => 'ga:screenResolution'
                )
            ),
            'visit_mobile_os' => array(
                'name' => $this->language->text('Visits by mobile OS'),
                'query' => array(
                    'sort' => '-ga:visits',
                    'segment' => 'gaid::-11',
                    'metrics' => 'ga:visits',
                    'dimensions' => 'ga:operatingSystem'
                )
            ),
            'visit_mobile_resolution' => array(
                'name' => $this->language->text('Visits by mobile resolution'),
                'query' => array(
                    'sort' => '-ga:visits',
                    'segment' => 'gaid::-11',
                    'metrics' => 'ga:visits',
                    'dimensions' => 'ga:screenResolution'
                )
            ),
            'pageview_date' => array(
                'name' => $this->language->text('Pageviews by date'),
                'query' => array(
                    'dimensions' => 'ga:date',
                    'metrics' => 'ga:pageviews'
                )
            ),
            'content_statistic' => array(
                'name' => $this->language->text('Content statistic'),
                'query' => array(
                    'metrics' => 'ga:pageviews,ga:uniquePageviews'
                )
            ),
            'top_pages' => array(
                'name' => $this->language->text('Top pages'),
                'query' => array(
                    'sort' => '-ga:pageviews',
                    'metrics' => 'ga:pageviews',
                    'dimensions' => 'ga:pagePath'
                )
            ),
            'source' => array(
                'name' => $this->language->text('Traffic sources'),
                'query' => array(
                    'metrics' => 'ga:visits',
                    'dimensions' => 'ga:medium',
                )
            ),
            'keyword' => array(
                'name' => $this->language->text('Keywords'),
                'query' => array(
                    'sort' => '-ga:visits',
                    'metrics' => 'ga:visits',
                    'dimensions' => 'ga:keyword'
                )
            ),
            'referral' => array(
                'name' => $this->language->text('Referrals'),
                'query' => array(
                    'sort' => '-ga:visits',
                    'metrics' => 'ga:visits',
                    'dimensions' => 'ga:source'
                )
            ),
            'audience' => array(
                'name' => $this->language->text('Audience'),
                'query' => array(
                    'metrics' => 'ga:visitors,ga:newVisits,ga:percentNewVisits,ga:visits,ga:pageviews,ga:bounces,ga:visitBounceRate,ga:timeOnSite,ga:avgTimeOnSite',
                )
            )
        );
    }

}
