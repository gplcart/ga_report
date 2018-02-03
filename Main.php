<?php

/**
 * @package Google Analytics Report
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\ga_report;

use gplcart\core\Container;
use gplcart\core\Module;

/**
 * Main class for Google Analytics Report module
 */
class Main
{

    /**
     * Module class instance
     * @var \gplcart\core\Module $model
     */
    protected $module;

    /**
     * @param Module $module
     */
    public function __construct(Module $module)
    {
        $this->module = $module;
    }

    /**
     * Implements hook "route.list"
     * @param array $routes
     */
    public function hookRouteList(array &$routes)
    {
        $routes['admin/module/settings/ga_report'] = array(
            'access' => 'module_edit',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\ga_report\\controllers\\Settings', 'editSettings')
            )
        );

        $routes['admin/report/ga'] = array(
            'menu' => array(
                'admin' => 'Google Analytics' // @text
            ),
            'access' => 'ga_report',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\ga_report\\controllers\\Report', 'listReport')
            )
        );
    }

    /**
     * Implements hook "user.role.permissions"
     * @param array $permissions
     */
    public function hookUserRolePermissions(array &$permissions)
    {
        $permissions['ga_report'] = 'Google Analytics Report: access'; // @text
    }

    /**
     * Implements hook "dashboard.handlers"
     * @param array $handlers
     */
    public function hookDashboardHandlers(array &$handlers)
    {
        $weight = count($handlers);
        $model = $this->getModel();
        $settings = $this->module->getSettings('ga_report');

        foreach ($model->getHandlers() as $id => $handler) {

            if (!in_array($id, $settings['dashboard'])) {
                continue;
            }

            $weight++;

            $report = $model->get($handler, $settings);

            $handlers["ga_$id"] = array(
                'status' => true,
                'weight' => $weight,
                'title' => $handler['name'],
                'template' => $handler['template'],
                'handlers' => array(
                    'data' => function () use ($handler, $report, $settings) {
                        return array(
                            'report' => $report,
                            'handler' => $handler,
                            'settings' => $settings
                        );
                    }
                )
            );
        }
    }

    /**
     * Implements hook "construct.controller.backend"
     * @param \gplcart\core\controllers\backend\Controller $controller
     */
    public function hookConstructControllerBackend($controller)
    {
        if ($controller->isQuery('ga.update')) {
            $store_id = $controller->getQuery('ga.update.store_id', '');
            $handler_id = $controller->getQuery('ga.update.handler_id', '');
            $this->getModel()->clearCache($handler_id, $store_id);
        }
    }

    /**
     * Returns an array of GA handlers
     * @return array
     */
    public function getHandlers()
    {
        return $this->getModel()->getHandlers();
    }

    /**
     * Returns the report model instance
     * @return \gplcart\modules\ga_report\models\Report
     */
    protected function getModel()
    {
        /** @var \gplcart\modules\ga_report\models\Report $instance */
        $instance = Container::get('gplcart\\modules\\ga_report\\models\\Report');
        return $instance;
    }

}
