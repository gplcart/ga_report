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

    use \gplcart\modules\ga_report\traits\ControllerTrait;

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

        $this->clearCacheGaReport($this->ga_report_model, $this);

        $this->setData('stores', $this->store->getList());
        $this->setData('panels', $this->getGaPanelsReport());

        $default = $this->config->module('ga_report', 'store_id');
        $store_id = $this->getQuery('ga.update.store_id', $default, 'string');
        $this->setData('ga_store_id', $store_id);

        $this->outputListReport();
    }

    /**
     * Returns an array of Google Analytics panels
     * @return array
     */
    protected function getGaPanelsReport()
    {
        $settings = $this->config->module('ga_report');
        $panels = $this->getPanelsGaReport($settings, $this->ga_report_model, $this);
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
        $breadcrumb = array(
            'text' => $this->text('Dashboard'),
            'url' => $this->url('admin')
        );

        $this->setBreadcrumb($breadcrumb);
    }

    /**
     * Render and output the Google Analytics report page
     */
    protected function outputListReport()
    {
        $this->output('ga_report|list');
    }

}
