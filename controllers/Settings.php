<?php

/**
 * @package Google Analytics Report
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\ga_report\controllers;

use gplcart\core\controllers\backend\Controller;
use gplcart\modules\ga_report\models\Report;

/**
 * Handles incoming requests and outputs data related to Google Analytics Report module
 */
class Settings extends Controller
{

    /**
     * Google Analytics Report Report model instance
     * @var \gplcart\modules\ga_report\models\Report $report_model
     */
    protected $report_model;

    /**
     * Settings constructor.
     * @param Report $report_model
     */
    public function __construct(Report $report_model)
    {
        parent::__construct();

        $this->report_model = $report_model;
    }

    /**
     * Route page callback
     * Displays the module settings page
     */
    public function editSettings()
    {
        $this->setTitleEditSettings();
        $this->setBreadcrumbEditSettings();

        $this->setData('stores', $this->store->getList());
        $this->setData('credentials', $this->getCredentialSettings());
        $this->setData('handlers', $this->report_model->getHandlers());
        $this->setData('settings', $this->module->getSettings('ga_report'));

        $this->submitSettings();
        $this->outputEditSettings();
    }

    /**
     * Returns an array of Google API credentials
     * @return array
     */
    protected function getCredentialSettings()
    {
        /** @var \gplcart\modules\gapi\Main $instance */
        $instance = $this->module->getInstance('gapi');
        return $instance->getCredentials(array('type' => 'service'));
    }

    /**
     * Set title on the module settings page
     */
    protected function setTitleEditSettings()
    {
        $title = $this->text('Edit %name settings', array('%name' => $this->text('Google Analytics Report')));
        $this->setTitle($title);
    }

    /**
     * Set breadcrumbs on the module settings page
     */
    protected function setBreadcrumbEditSettings()
    {
        $breadcrumbs = array();

        $breadcrumbs[] = array(
            'url' => $this->url('admin'),
            'text' => $this->text('Dashboard')
        );

        $breadcrumbs[] = array(
            'text' => $this->text('Modules'),
            'url' => $this->url('admin/module/list')
        );

        $this->setBreadcrumbs($breadcrumbs);
    }

    /**
     * Saves the submitted settings
     */
    protected function submitSettings()
    {
        if ($this->isPosted('clear_cache')) {
            $this->deleteCacheSettings();
        } else if ($this->isPosted('save') && $this->validateSettings()) {
            $this->updateSettings();
        }
    }

    /**
     * Deletes all Google Analytics cached data
     */
    protected function deleteCacheSettings()
    {
        $this->report_model->clearCache();
        $this->redirect('', $this->text('Cache has been deleted'), 'success');
    }

    /**
     * Validate submitted module settings
     */
    protected function validateSettings()
    {
        $this->setSubmitted('settings');

        $this->validateElement('limit', 'regexp', '/^[\d]{1,3}$/');
        $this->validateElement('cache', 'regexp', '/^[\d]{1,8}$/');
        $this->validateElement('credential_id', 'regexp', '/^[\d]{1,10}$/');

        $this->validateGaProfileSettings();

        return !$this->hasErrors();
    }

    /**
     * Validates Google Analytics profiles
     */
    protected function validateGaProfileSettings()
    {
        $profiles = $this->getSubmitted('ga_profile_id', array());

        if (empty($profiles)) {
            $this->setError('ga_profile_id', $this->text('Profile ID is required'));
            return false;
        }

        $stores = $this->store->getList();

        foreach ($profiles as $store_id => $profile_id) {

            if (empty($profile_id)) {
                $this->setError('ga_profile_id', $this->text('Profile ID is required'));
                return false;
            }

            if (empty($stores[$store_id])) {
                $this->setError('ga_profile_id', $this->text('Unknown store ID'));
                return false;
            }
        }

        return true;
    }

    /**
     * Update module settings
     */
    protected function updateSettings()
    {
        $this->controlAccess('module_edit');

        $this->module->setSettings('ga_report', $this->getSubmitted());
        $this->redirect('', $this->text('Settings have been updated'), 'success');
    }

    /**
     * Render and output the module settings page
     */
    protected function outputEditSettings()
    {
        $this->output('ga_report|settings');
    }

}
