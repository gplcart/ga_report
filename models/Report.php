<?php

/**
 * @package Google Analytics Report
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\ga_report\models;

use Exception;
use OutOfRangeException;
use gplcart\core\Cache;
use gplcart\core\Hook;
use gplcart\core\Module;

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
     * Cache class instance
     * @var \gplcart\core\Cache $cache
     */
    protected $cache;

    /** Module class instance
     * @var \gplcart\core\Module $module
     */
    protected $module;

    /**
     * @param Hook $hook
     * @param Cache $cache
     * @param Module $module
     */
    public function __construct(Hook $hook, Cache $cache, Module $module)
    {
        $this->hook = $hook;
        $this->cache = $cache;
        $this->module = $module;
    }

    /**
     * Returns an array of parsed reporting data
     * @param array $handler
     * @param array $settings
     * @return array
     * @throws OutOfRangeException
     */
    public function get(array $handler, array $settings)
    {
        $report = null;
        $this->hook->attach('module.ga_report.get.before', $handler, $settings, $report, $this);

        if (isset($report)) {
            return $report;
        }

        if (empty($settings['ga_profile_id'][$settings['store_id']])) {
            throw new OutOfRangeException("Google Analytics profile ID is empty for store {$settings['store_id']}");
        }

        $settings += array('cache' => 0);
        $settings['ga_profile_id'] = $settings['ga_profile_id'][$settings['store_id']];

        $cache_key = "ga_report.{$handler['id']}.{$settings['store_id']}";
        $cache = $this->cache->get($cache_key, array('lifespan' => $settings['cache']));

        if (!empty($settings['cache']) && isset($cache)) {
            return array(
                'data' => $cache,
                'updated' => $this->cache->getFileMtime()
            );
        }

        try {
            $response = $this->request($settings, $handler);
            $results = $this->getResults($response);
        } catch (Exception $ex) {
            return array('error' => $ex->getMessage());
        }

        $report = array('data' => $results, 'updated' => GC_TIME);
        $this->cache->set($cache_key, $results);

        $this->hook->attach('module.ga_report.get.after', $handler, $settings, $report, $this);
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
        $handlers = &gplcart_static('module.ga_report.handlers');

        if (isset($handlers)) {
            return $handlers;
        }

        $handlers = gplcart_config_get(__DIR__ . '/../config/reports.php');

        foreach ($handlers as $id => &$handler) {
            $handler['id'] = $id;
        }

        $this->hook->attach('module.ga_report.handlers', $handlers);
        return $handlers;
    }

    /**
     * Returns Google Analytics Reporting class instance
     * @param array $settings
     * @return \Google_Service_AnalyticsReporting
     */
    protected function getService(array $settings)
    {
        $client = $this->getClient($settings);
        return new \Google_Service_AnalyticsReporting($client);
    }

    /**
     * Returns an object of Google Analytics service response
     * @param array $settings
     * @param array $handler
     * @return \Google_Service_AnalyticsReporting_GetReportsResponse
     * @throws OutOfRangeException
     */
    public function request(array $settings, array $handler)
    {
        if (empty($settings['ga_profile_id'])) {
            throw new OutOfRangeException('Google Analytics profile ID is empty in the request settings');
        }

        $service = $this->getService($settings); // Also loads all needed libraries

        $request = new \Google_Service_AnalyticsReporting_ReportRequest;
        $request->setViewId($settings['ga_profile_id']);

        $this->setRequestDate($request, $handler);
        $this->setRequestMetrics($request, $handler);
        $this->setRequestSorting($request, $handler);
        $this->setRequestDimensions($request, $handler);

        $body = new \Google_Service_AnalyticsReporting_GetReportsRequest;
        $body->setReportRequests(array($request));

        return $service->reports->batchGet($body);
    }

    /**
     * Sets request date range
     * @param \Google_Service_AnalyticsReporting_ReportRequest $request
     * @param array $handler
     */
    protected function setRequestDate($request, array $handler)
    {
        if (!empty($handler['query']['date']) && count($handler['query']['date']) == 2) {

            list($from, $to) = $handler['query']['date'];

            $date = new \Google_Service_AnalyticsReporting_DateRange;
            $date->setStartDate($from);
            $date->setEndDate($to);

            $request->setDateRanges($date);
        }
    }

    /**
     * Sets request metrics from a handler
     * @param \Google_Service_AnalyticsReporting_ReportRequest $request
     * @param array $handler
     */
    protected function setRequestMetrics($request, array $handler)
    {
        if (empty($handler['query']['metrics'])) {
            throw new OutOfRangeException('No query metrics data found in the handler');
        }

        $metrics = array();

        foreach ((array) $handler['query']['metrics'] as $i => $name) {
            $metric = new \Google_Service_AnalyticsReporting_Metric;
            $metric->setExpression($name);
            $metrics[] = $metric;
        }

        $request->setMetrics($metrics);
    }

    /**
     * Sets request sorting from a handler
     * @param \Google_Service_AnalyticsReporting_ReportRequest $request
     * @param array $handler
     */
    protected function setRequestSorting($request, array $handler)
    {
        if (!empty($handler['query']['sort'])) {

            $orders = array();
            foreach ((array) $handler['query']['sort'] as $field => $params) {

                $params += array('VALUE', 'ASCENDING');

                list($type, $direction) = array_map('strtoupper', $params);

                $order = new \Google_Service_AnalyticsReporting_OrderBy;

                $order->setFieldName($field);
                $order->setOrderType($type);
                $order->setSortOrder($direction);

                $orders[] = $order;
            }

            $request->setOrderBys($orders);
        }
    }

    /**
     * Sets request dimensions from a handler
     * @param \Google_Service_AnalyticsReporting_ReportRequest $request
     * @param array $handler
     */
    protected function setRequestDimensions($request, array $handler)
    {
        if (!empty($handler['query']['dimensions'])) {

            $dimensions = array();

            foreach ((array) $handler['query']['dimensions'] as $name) {
                $dimension = new \Google_Service_AnalyticsReporting_Dimension;
                $dimension->setName($name);
                $dimensions[] = $dimension;
            }

            $request->setDimensions($dimensions);
        }
    }

    /**
     * Returns an array of results from the response object
     * @param \Google_Service_AnalyticsReporting_GetReportsResponse $response
     * @return array
     */
    public function getResults($response)
    {
        $results = array('rows' => array());

        for ($report_index = 0; $report_index < count($response); $report_index++) {

            $report = $response[$report_index];
            $header = $report->getColumnHeader();

            $dimension_headers = $header->getDimensions();
            $metric_headers = $header->getMetricHeader()->getMetricHeaderEntries();
            $rows = $report->getData()->getRows();

            for ($row_index = 0; $row_index < count($rows); $row_index++) {

                $row = $rows[$row_index];
                $dimensions = $row->getDimensions();
                $metrics = $row->getMetrics();

                for ($i = 0; $i < count($dimension_headers) && $i < count($dimensions); $i++) {
                    $results['rows'][$row_index][$dimension_headers[$i]] = $dimensions[$i];
                }

                for ($j = 0; $j < count($metrics); $j++) {
                    $values = $metrics[$j]->getValues();
                    for ($k = 0; $k < count($values); $k++) {
                        $entry = $metric_headers[$k];
                        $results['rows'][$row_index][$entry->getName()] = $values[$k];
                    }
                }
            }
        }

        return $results;
    }

    /**
     * Returns Google Client class instance
     * @param array $settings
     * @return \Google_Client
     * @throws OutOfRangeException
     */
    protected function getClient(array $settings)
    {
        if (empty($settings['credential_id'])) {
            throw new OutOfRangeException('Credential ID is empty in Google client settings');
        }

        $client = $this->getApiModule()->getGoogleClient($settings['credential_id']);
        $client->setApplicationName('Analytics Reporting');
        $client->setScopes(array('https://www.googleapis.com/auth/analytics.readonly'));

        return $client;
    }

    /**
     * Returns Google Api module instance
     * @return \gplcart\modules\gapi\Main
     */
    protected function getApiModule()
    {
        /** @var \gplcart\modules\gapi\Main $module */
        $module = $this->module->getInstance('gapi');
        return $module;
    }


}
