<?php
/**
 * @package Google Analytics Report
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */
?>
<form method="post" class="form-horizontal">
  <input type="hidden" name="token" value="<?php echo $_token; ?>">
  <div class="form-group required<?php echo $this->error('credential_id', ' has-error'); ?>">
    <label class="col-md-2 control-label"><?php echo $this->text('Oauth credential'); ?></label>
    <div class="col-md-4">
      <select name="settings[credential_id]" class="form-control">
        <option value=""><?php echo $this->text('- select -'); ?></option>
        <?php foreach ($credentials as $credential) { ?>
        <option value="<?php echo $this->e($credential['credential_id']); ?>"<?php echo isset($settings['credential_id']) && $settings['credential_id'] == $credential['credential_id'] ? ' selected' : ''; ?>><?php echo $this->e($credential['name']); ?></option>
        <?php } ?>
      </select>
      <div class="help-block">
        <?php echo $this->error('credential_id'); ?>
        <div class="text-muted">
          <?php echo $this->text('Select a <a href="@url">credential</a> for Oauth authorization', array('@url' => $this->url('admin/report/oauth'))); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group<?php echo $this->error('limit', ' has-error'); ?>">
    <label class="col-md-2 control-label"><?php echo $this->text('Limit'); ?></label>
    <div class="col-md-4">
      <input name="settings[limit]" class="form-control" value="<?php echo $this->e($settings['limit']); ?>">
      <div class="help-block">
        <?php echo $this->error('limit'); ?>
        <div class="text-muted">
          <?php echo $this->text('How many rows to fetch from Google Analytics API. Maximum allowed value - 100'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group<?php echo $this->error('cache', ' has-error'); ?>">
    <label class="col-md-2 control-label"><?php echo $this->text('Cache lifespan'); ?></label>
    <div class="col-md-4">
      <input name="settings[cache]" class="form-control" value="<?php echo $this->e($settings['cache']); ?>">
      <div class="help-block">
        <?php echo $this->error('cache'); ?>
        <div class="text-muted">
          <?php echo $this->text('Cached Google Analytics data will not be automatically updated at least this much time has elapsed. Enter seconds. 0 disables cache at all'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label class="col-md-2 control-label"><?php echo $this->text('Default store'); ?></label>
    <div class="col-md-4">
      <select name="settings[store_id]" class="form-control">
        <?php foreach ($stores as $id => $store) { ?>
        <option value="<?php echo $this->e($id); ?>"<?php echo $settings['store_id'] == $id ? ' selected' : ''; ?>><?php echo $this->e($store['name']); ?></option>
        <?php } ?>
      </select>
      <div class="help-block"><?php echo $this->text('By default show reports for the selected store'); ?></div>
    </div>
  </div>
  <div class="form-group">
     <label class="col-md-2 control-label"><?php echo $this->text('Panels on dashboard'); ?></label>
     <div class="col-md-4">
        <?php foreach ($handlers as $handler) { ?>
        <div class="checkbox">
          <label>
            <input type="checkbox" name="settings[dashboard][]" value="<?php echo $this->e($handler['id']); ?>"<?php echo isset($settings['dashboard']) && in_array($handler['id'], $settings['dashboard']) ? ' checked' : ''; ?>> <?php echo $this->e($handler['name']); ?>
          </label>
        </div>
        <?php } ?>
       <div class="help-block"><?php echo $this->text('Select which panels with Google Analytics data to be shown on admin dashboard'); ?></div>
      </div>
  </div>
  <div class="form-group required<?php echo $this->error('ga_profile_id', ' has-error'); ?>">
    <label class="col-md-2 control-label"><?php echo $this->text('Profile ID'); ?></label>
    <div class="col-md-10">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th><?php echo $this->text('Store'); ?></th>
            <th><?php echo $this->text('Profile ID'); ?></th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($stores as $id => $store) { ?>
          <tr>
            <td>
              <?php echo $this->e($store['name']); ?>
              <p class="small">http://<?php echo $this->e($store['domain']); ?>/<?php echo $this->e($store['basepath']); ?></p>
            </td>
            <td>
              <input name="settings[ga_profile_id][<?php echo $this->e($id); ?>]" class="form-control" value="<?php echo isset($settings['ga_profile_id'][$id]) ? $this->e($settings['ga_profile_id'][$id]) : ''; ?>">
            </td>
          </tr>
          <?php } ?>
          </tbody>
        </table>
        <div class="help-block">
          <?php echo $this->error('ga_profile_id'); ?>
          <div class="text-muted">
            <?php echo $this->text('Specify a Google Analytics profile ID for each existing store'); ?>
          </div>
        </div>
      </div>
    </div>
  <div class="form-group">
    <div class="col-md-4 col-md-offset-2">
      <div class="btn-toolbar">
        <button name="clear_cache" class="btn btn-default" value="1"><?php echo $this->text('Clear cache'); ?></button>
        <a href="<?php echo $this->url('admin/module/list'); ?>" class="btn btn-default"><?php echo $this->text('Cancel'); ?></a>
        <button class="btn btn-default save" name="save" value="1"><?php echo $this->text("Save"); ?></button>
      </div>
    </div>
  </div>
</form>