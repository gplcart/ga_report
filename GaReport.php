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
            $result = 'CURL library is not enabled';
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
        $settings = $this->config->module('ga_report');

        /* @var $ga_model \gplcart\modules\ga_report\models\Report */
        $ga_model = $this->getInstance('gplcart\\modules\\ga_report\\models\\Report');

        $weight = count($handlers);

        foreach ($ga_model->getHandlers() as $id => $ga_handler) {

            if (!in_array($id, $settings['dashboard'])) {
                continue;
            }

            $weight++;

            $handlers["ga_{$ga_handler['id']}"] = array(
                'status' => true,
                'weight' => $weight,
                'title' => $ga_handler['name'],
                'template' => $ga_handler['template'],
                'handlers' => array(
                    'data' => function() use ($ga_handler, $ga_model, $settings) {
                        return array('report' => $ga_model->get($ga_handler['id'], $settings), 'settings' => $settings);
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

            /* @var $ga_model \gplcart\modules\ga_report\models\Report */
            $ga_model = $this->getInstance('gplcart\\modules\\ga_report\\models\\Report');

            $store_id = $controller->getQuery('ga.update.store_id', '', 'string');
            $handler_id = $controller->getQuery('ga.update.handler_id', '', 'string');
            $ga_model->clearCache($handler_id, $store_id);
        }
    }

}
