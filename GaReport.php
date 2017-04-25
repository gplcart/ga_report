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

    use \gplcart\modules\ga_report\traits\ControllerTrait;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Implements hook "route.list"
     * @param array $routes
     */
    public function hookRouteList(array &$routes)
    {
        // Module settings page
        $routes['admin/module/settings/ga_report'] = array(
            'access' => 'module_edit',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\ga_report\\controllers\\Settings', 'editSettings')
            )
        );

        // Google Analytics reports
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
     * Implements hook "template.dashboard"
     * @param array $panels 
     * @param \gplcart\core\controllers\backend\Controller $controller
     */
    public function hookTemplateDashboard(array &$panels, $controller)
    {
        /* @var $model \gplcart\modules\ga_report\models\Report */
        $model = $this->getInstance('gplcart\\modules\\ga_report\\models\\Report');

        $this->clearCacheGaReport($model, $controller);

        $settings = $this->config->module('ga_report');
        $ga_panels = $this->getPanelsGaReport($settings, $model, $controller);

        $weight = count($panels);
        foreach ($ga_panels as $id => $panel) {
            if (in_array($id, $settings['dashboard']) && $controller->access('ga_report')) {
                $panel['weight'] = $weight;
                $panels[$id] = $panel;
                $weight++;
            }
        }
    }

}
