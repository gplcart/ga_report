<?php

/**
 * @package Google Analytics Report
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\ga_report;

use gplcart\core\Module;

/**
 * Main class for Google Analytics Report module
 */
class GaReport extends Module
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Implements hook "module.install.before"
     */
    public function hookModuleInstallBefore(&$result)
    {
        if (!function_exists('curl_init')) {
            $result = $this->getLanguage()->text('CURL library is not enabled');
        }
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
            'menu' => array('admin' => 'Google Analytics'),
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
        $permissions['ga_report'] = 'Google Analytics reports: access';
    }

    /**
     * Implements hook "oauth.providers"
     * @param array $providers
     */
    public function hookOauthProviders(array &$providers)
    {
        $providers['ga'] = array(
            'name' => 'Google analytics',
            'settings' => $this->config->module('ga_report'),
            'url' => array(
                'process' => 'https://www.googleapis.com/analytics/v3/data/ga',
                'token' => 'https://www.googleapis.com/oauth2/v4/token'
            ),
            'scope' => 'https://www.googleapis.com/auth/analytics.readonly',
            'handlers' => array(
                'token' => array('gplcart\\modules\\ga_report\\handlers\\Api', 'token'),
                'process' => array('gplcart\\modules\\ga_report\\handlers\\Api', 'process'),
            )
        );
    }

    /**
     * Implements hook "dashboard.handlers"
     * @param array $handlers
     */
    public function hookDashboardHandlers(array &$handlers)
    {
        $weight = count($handlers);
        $model = $this->getReportModel();
        $settings = $this->config->module('ga_report');

        foreach ($model->getHandlers() as $id => $handler) {

            if (!in_array($id, $settings['dashboard'])) {
                continue;
            }

            $weight++;

            $report = $model->get($id, $settings);

            $handlers["ga_$id"] = array(
                'status' => true,
                'weight' => $weight,
                'title' => $handler['name'],
                'template' => $handler['template'],
                'handlers' => array(
                    'data' => function() use ($report, $settings) {
                        return array('report' => $report, 'settings' => $settings);
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
            $this->getReportModel()->clearCache($handler_id, $store_id);
        }
    }

    /**
     * Returns the report model instance
     * @return \gplcart\modules\ga_report\models\Report
     */
    protected function getReportModel()
    {
        return $this->getModel('Report', 'ga_report');
    }

    /**
     * Returns an array of GA handlers
     * @return array
     */
    public function getHandlers()
    {
        return $this->getReportModel()->getHandlers();
    }

}
