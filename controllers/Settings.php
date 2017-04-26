<?php

/**
 * @package Google Analytics Report
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\ga_report\controllers;

use gplcart\core\models\File as FileModel,
    gplcart\core\models\Module as ModuleModel;
use gplcart\core\controllers\backend\Controller as BackendController;
use gplcart\modules\ga_report\models\Report as GaReportModuleReportModel;

/**
 * Handles incoming requests and outputs data related to Google Analytics Report module
 */
class Settings extends BackendController
{

    /**
     * Module model instance
     * @var \gplcart\core\models\Module $module
     */
    protected $module;

    /**
     * File model instance
     * @var \gplcart\core\models\File $file
     */
    protected $file;

    /**
     * Google Analytics Report Report model instance
     * @var \gplcart\modules\ga_report\models\Report $ga_report_model
     */
    protected $ga_report_model;

    /**
     * @param FileModel $file
     * @param ModuleModel $module
     * @param GaReportModuleReportModel $ga_report_model
     */
    public function __construct(FileModel $file, ModuleModel $module,
            GaReportModuleReportModel $ga_report_model)
    {
        parent::__construct();

        $this->file = $file;
        $this->module = $module;
        $this->ga_report_model = $ga_report_model;
    }

    /**
     * Route page callback to display the module settings page
     */
    public function editSettings()
    {
        $this->setTitleEditSettings();
        $this->setBreadcrumbEditSettings();

        $settings = $this->config->module('ga_report');

        $this->setData('settings', $settings);
        $this->setData('stores', $this->store->getNames());
        $this->setData('handlers', $this->ga_report_model->getHandlers());
        $this->setData('certificate_file', $settings['certificate_file']);

        $this->submitSettings();
        $this->outputEditSettings();
    }

    /**
     * Set title on the module settings page
     */
    protected function setTitleEditSettings()
    {
        $vars = array('%name' => $this->text('Google Analytics Report'));
        $title = $this->text('Edit %name settings', $vars);
        $this->setTitle($title);
    }

    /**
     * Set breadcrumbs on the module settings page
     */
    protected function setBreadcrumbEditSettings()
    {
        $breadcrumbs = array();

        $breadcrumbs[] = array(
            'text' => $this->text('Dashboard'),
            'url' => $this->url('admin')
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
            return null;
        }

        $this->setSubmitted('settings');

        if ($this->isPosted('delete_certificate')) {
            $this->deleteCertificateSettings();
        }

        if ($this->isPosted('save') && $this->validateSettings()) {
            $this->updateSettings();
        }
    }

    /**
     * Deletes all Google Analytics cached data
     */
    protected function deleteCacheSettings()
    {
        $this->ga_report_model->clearCache();
        $this->redirect('', $this->text('Cache has been deleted'), 'success');
    }

    /**
     * Deletes a certificate file
     */
    protected function deleteCertificateSettings()
    {
        $file = GC_PRIVATE_MODULE_DIR . '/' . $this->config->module('ga_report', 'certificate_file');

        if (file_exists($file) && unlink($file)) {
            $this->setMessage($this->text('Certificate has been deleted from the server'), 'success', true);
        }

        $this->setSubmitted('certificate_file', '');
    }

    /**
     * Validate submitted module settings
     */
    protected function validateSettings()
    {
        $this->validateElement('end_date', 'dateformat');
        $this->validateElement('start_date', 'dateformat');
        $this->validateElement('limit', 'regexp', '/^[\d]{1,3}$/');
        $this->validateElement('cache', 'regexp', '/^[\d]{1,8}$/');

        $this->validateCertificateSettings();

        return !$this->hasErrors();
    }

    /**
     * Validates uploaded certificate file
     */
    protected function validateCertificateSettings()
    {
        $upload = $this->request->file('file');

        if (empty($upload)) {
            return null;
        }

        $this->validateElement('certificate_secret', 'required');

        $result = $this->file->upload($upload, false, GC_PRIVATE_MODULE_DIR . '/google-analytics');

        if ($result !== true) {
            $this->setError('file', $result);
            return null;
        }

        $file = $this->file->getTransferred();

        $certs = array();
        $secret = $this->getSubmitted('certificate_secret');
        if (!openssl_pkcs12_read(file_get_contents($file), $certs, $secret)) {
            $this->setError('file', $this->text('Failed to read certificate'));
            return null;
        }

        $this->setSubmitted('certificate_file', $this->file->path($file));
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
