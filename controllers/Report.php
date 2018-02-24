<?php

/**
 * @package Google Analytics Report
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\ga_report\controllers;

use gplcart\core\controllers\backend\Controller;
use gplcart\modules\ga_report\models\Report as ReportModel;

/**
 * Handles incoming requests and outputs data related to Google Analytics Report module
 */
class Report extends Controller
{

    /**
     * Report model instance
     * @var \gplcart\modules\ga_report\models\Report $report_model
     */
    protected $report_model;

    /**
     * @param ReportModel $model
     */
    public function __construct(ReportModel $model)
    {
        parent::__construct();

        $this->report_model = $model;
    }

    /**
     * Route callback
     * Displays the report page
     */
    public function listReport()
    {
        $this->setTitleListReport();
        $this->setBreadcrumbListReport();
        $this->clearCacheReport();

        $this->setData('stores', $this->store->getList());
        $this->setData('panels', $this->getPanelsReport());

        $default = $this->module->getSettings('ga_report', 'store_id');
        $this->setData('ga_store_id', $this->getQuery('ga.update.store_id', $default));

        $this->outputListReport();
    }

    /**
     * Set title on the report page
     */
    protected function setTitleListReport()
    {
        $this->setTitle($this->text('Google Analytics'));
    }

    /**
     * Set breadcrumbs on the report page
     */
    protected function setBreadcrumbListReport()
    {
        $breadcrumb = array(
            'url' => $this->url('admin'),
            'text' => $this->text('Dashboard')
        );

        $this->setBreadcrumb($breadcrumb);
    }

    /**
     * Clear cache
     */
    protected function clearCacheReport()
    {
        if ($this->isQuery('ga.update')) {
            $store_id = $this->getQuery('ga.update.store_id', '');
            $handler_id = $this->getQuery('ga.update.handler_id', '');
            $this->report_model->clearCache($handler_id, $store_id);
        }
    }

    /**
     * Returns an array of report panels
     * @return array
     */
    protected function getPanelsReport()
    {
        $settings = $this->module->getSettings('ga_report');
        $store_id = $this->getQuery('ga.update.store_id');

        if (!empty($store_id)) {
            $settings['store_id'] = $store_id;
        }

        $panels = array();

        foreach ($this->report_model->getHandlers() as $handler) {

            $report = $this->report_model->get($handler, $settings);

            $data = array(
                'content' => array(
                    'data' => array( // We need so deep nesting for compatibility with dashboard panel templates
                        'report' => $report,
                        'handler' => $handler,
                        'settings' => $settings
                    )
                )
            );

            $panels[$handler['id']] = array('rendered' => $this->render($handler['template'], $data));
        }

        return gplcart_array_split($panels, 3); // Split by columns
    }

    /**
     * Render and output the Google Analytics report page
     */
    protected function outputListReport()
    {
        $this->output('ga_report|list');
    }

}
