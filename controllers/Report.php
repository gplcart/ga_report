<?php

/**
 * @package Google Analytics Report
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\ga_report\controllers;

use gplcart\core\models\Oauth as OauthModel;
use gplcart\core\controllers\backend\Controller as BackendController;
use gplcart\modules\ga_report\models\Report as GaReportModuleReportModel;

/**
 * Handles incoming requests and outputs data related to Google Analytics Report module
 */
class Report extends BackendController
{

    /**
     * Google Analytics Report Report model instance
     * @var \gplcart\modules\ga_report\models\Report $ga_report_model
     */
    protected $ga_report_model;

    /**
     * Oauth model instance
     * @var \gplcart\core\models\Oauth $oauth
     */
    protected $oauth;

    /**
     * @param OauthModel $oauth
     * @param GaReportModuleReportModel $model
     */
    public function __construct(OauthModel $oauth,
            GaReportModuleReportModel $model)
    {
        parent::__construct();

        $this->oauth = $oauth;
        $this->ga_report_model = $model;
    }

    /**
     * Route page callback to display the Google Analytics report page
     */
    public function listReport()
    {
        $this->setTitleListReport();
        $this->setBreadcrumbListReport();

        $this->clearCacheReport();

        $this->setData('stores', $this->store->getList());
        $this->setData('panels', $this->getPanelsReport());

        $default = $this->config->module('ga_report', 'store_id');
        $store_id = $this->getQuery('ga.update.store_id', $default);
        $this->setData('ga_store_id', $store_id);

        $this->outputListReport();
    }

    /**
     * Clear GA cache
     */
    protected function clearCacheReport()
    {
        if ($this->isQuery('ga.update')) {
            $store_id = $this->getQuery('ga.update.store_id', '');
            $handler_id = $this->getQuery('ga.update.handler_id', '');
            $this->ga_report_model->clearCache($handler_id, $store_id);
        }
    }

    /**
     * Returns an array of Google Analytics panels
     * @return array
     */
    protected function getPanelsReport()
    {
        $settings = $this->config->module('ga_report');
        $store_id = $this->getQuery('ga.update.store_id', '');

        if (isset($store_id)) {
            $settings['store_id'] = $store_id;
        }

        if (empty($settings['ga_profile_id'][$settings['store_id']])) {
            $settings['query'] = array();
        } else {
            $settings['query'] = array(
                'max-results' => $settings['limit'],
                'end-date' => date('Y-m-d', strtotime($settings['end_date'])),
                'start-date' => date('Y-m-d', strtotime($settings['start_date'])),
                'ids' => 'ga:' . $settings['ga_profile_id'][$settings['store_id']]
            );
        }

        $panels = array();
        foreach ($this->ga_report_model->getHandlers() as $handler) {
            $report = $this->ga_report_model->get($handler['id'], $settings);
            if (isset($report['handler']['template'])) {
                // Place data under "content => data" kaey to make compatible with dashboard templates
                $data = array('content' => array('data' => array('report' => $report, 'settings' => $settings)));
                $panels[$handler['id']] = array('rendered' => $this->render($report['handler']['template'], $data));
            }
        }

        return gplcart_array_split($panels, 3);
    }

    /**
     * Set title on the Google Analytics report page
     */
    protected function setTitleListReport()
    {
        $this->setTitle($this->text('Google Analytics'));
    }

    /**
     * Set breadcrumbs on the Google Analytics report page
     */
    protected function setBreadcrumbListReport()
    {
        $this->setBreadcrumbHome();
    }

    /**
     * Render and output the Google Analytics report page
     */
    protected function outputListReport()
    {
        $this->output('ga_report|list');
    }

}
