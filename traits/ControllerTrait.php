<?php

/**
 * @package Google Analytics Report
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\ga_report\traits;

trait ControllerTrait
{

    /**
     * Returns an array of rendered GA panels
     * @param array $settings
     * @param \gplcart\modules\ga_report\models\Report $model
     * @param \gplcart\core\controllers\backend\Controller $controller
     * @return array
     */
    protected function getPanelsGaReport(array $settings, $model, $controller)
    {
        $store_id = $controller->getQuery('ga.update.store_id', '', 'string');

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
        foreach ($model->getHandlers() as $handler) {
            $report = $model->get($handler['id'], $settings);
            if (isset($report['handler']['template'])) {
                $panels[$handler['id']] = array(
                    'rendered' => $controller->render($report['handler']['template'], array('report' => $report, 'settings' => $settings)));
            }
        }

        return $panels;
    }

    /**
     * Clear cache by handler and store ID taken from GET query
     * @param \gplcart\modules\ga_report\models\Report $model
     * @param \gplcart\core\controllers\backend\Controller $controller
     */
    protected function clearCacheGaReport($model, $controller)
    {
        if ($controller->isQuery('ga.update')) {
            $store_id = $controller->getQuery('ga.update.store_id', '', 'string');
            $handler_id = $controller->getQuery('ga.update.handler_id', '', 'string');
            $model->clearCache($handler_id, $store_id);
        }
    }

}
