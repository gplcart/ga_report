<?php
/**
 * @package Google Analytics Report
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */
?>
<form method="post" enctype="multipart/form-data" class="form-horizontal">
  <input type="hidden" name="token" value="<?php echo $_token; ?>">
  <div class="panel panel-default">
    <div class="panel-heading"><?php echo $this->text('API credentials'); ?></div>
    <div class="panel-body">
      <div class="form-group<?php echo $this->error('file', ' has-error'); ?>">
        <label class="col-md-2 control-label"><?php echo $this->text('Certificate file'); ?></label>
        <div class="col-md-4">
          <input type="file" name="file" class="form-control">
          <div class="help-block">
            <?php echo $this->error('file'); ?>
            <div class="text-muted">
              <?php echo $this->text('A .p12 certificate file you got from <a href="@url">Google API Console</a>', array('@url' => 'https://console.developers.google.com/apis/credentials/serviceaccountkey')); ?>
            </div>
          </div>
        </div>
      </div>
      <?php if ($certificate_file) { ?>
      <div class="form-group">
        <div class="col-md-10 col-md-offset-2">
          <input name="settings[certificate_file]" type="hidden" value="<?php echo $this->escape($certificate_file); ?>">
          <input type="checkbox" name="delete_certificate" value="1"> <?php echo $this->text('Delete'); ?> <i><?php echo $this->escape($certificate_file); ?></i>
        </div>
      </div>
      <?php } ?>
      <div class="form-group<?php echo $this->error('certificate_secret', ' has-error'); ?>">
        <label class="col-md-2 control-label"><?php echo $this->text('Certificate secret'); ?></label>
        <div class="col-md-4">
          <input type="password" name="settings[certificate_secret]" class="form-control" value="<?php echo $this->escape($settings['certificate_secret']); ?>">
          <div class="help-block">
            <?php echo $this->error('certificate_secret'); ?>
            <div class="text-muted">
              <?php echo $this->text('A secret word for the certificate file. If empty, default "notasecret" will be used'); ?>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group<?php echo $this->error('service_account_id', ' has-error'); ?>">
        <label class="col-md-2 control-label"><?php echo $this->text('Service account ID'); ?></label>
        <div class="col-md-4">
          <input name="settings[service_account_id]" class="form-control" value="<?php echo $this->escape($settings['service_account_id']); ?>">
          <div class="help-block">
            <?php echo $this->error('service_account_id'); ?>
            <div class="text-muted">
              <?php echo $this->text('A service account ID associated with the credentials'); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading"><?php echo $this->text('Results'); ?></div>
    <div class="panel-body">
      <div class="form-group<?php echo $this->error('limit', ' has-error'); ?>">
        <label class="col-md-2 control-label"><?php echo $this->text('Limit'); ?></label>
        <div class="col-md-4">
          <input name="settings[limit]" class="form-control" value="<?php echo $this->escape($settings['limit']); ?>">
          <div class="help-block">
            <?php echo $this->error('limit'); ?>
            <div class="text-muted">
              <?php echo $this->text('How many rows to fetch from Google Analytics API. Maximum allowed value - 100'); ?>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group<?php echo $this->error('start_date', ' has-error'); ?>">
        <label class="col-md-2 control-label"><?php echo $this->text('Start date'); ?></label>
        <div class="col-md-4">
          <input name="settings[start_date]" class="form-control" value="<?php echo $this->escape($settings['start_date']); ?>">
          <div class="help-block">
            <?php echo $this->error('start_date'); ?>
            <div class="text-muted">
                <?php echo $this->text('<a target="_blank" href="@url">Relative</a> to the current date, e.g @example', array(
                    '@url' => 'http://php.net/manual/en/datetime.formats.relative.php', '@example' => '-1 month')); ?>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group<?php echo $this->error('end_date', ' has-error'); ?>">
        <label class="col-md-2 control-label"><?php echo $this->text('End date'); ?></label>
        <div class="col-md-4">
          <input name="settings[end_date]" class="form-control" value="<?php echo $this->escape($settings['end_date']); ?>">
          <div class="help-block">
            <?php echo $this->error('end_date'); ?>
            <div class="text-muted">
              <?php echo $this->text('<a target="_blank" href="@url">Relative</a> to the current date, e.g @example', array(
                    '@url' => 'http://php.net/manual/en/datetime.formats.relative.php', '@example' => 'now'));?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading"><?php echo $this->text('Cache'); ?></div>
    <div class="panel-body">
      <div class="form-group<?php echo $this->error('cache', ' has-error'); ?>">
        <label class="col-md-2 control-label"><?php echo $this->text('Cache lifespan'); ?></label>
        <div class="col-md-4">
          <input name="settings[cache]" class="form-control" value="<?php echo $this->escape($settings['cache']); ?>">
          <div class="help-block">
            <?php echo $this->error('cache'); ?>
            <div class="text-muted">
              <?php echo $this->text('Cached Google Analytics data will not be automatically updated at least this much time has elapsed. Enter seconds. 0 disables cache at all'); ?>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="col-md-10 col-md-offset-2">
          <button name="clear_cache" class="btn btn-default" value="1"><?php echo $this->text('Clear cache'); ?></button>
        </div>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading"><?php echo $this->text('View'); ?></div>
    <div class="panel-body">
      <div class="form-group">
        <label class="col-md-2 control-label"><?php echo $this->text('Default store'); ?></label>
        <div class="col-md-4">
          <select name="settings[store_id]" class="form-control">
            <?php foreach ($stores as $id => $name) { ?>
              <option value="<?php echo $this->escape($id); ?>"<?php echo $settings['store_id'] == $id ? ' selected' : ''; ?>><?php echo $this->escape($name); ?></option>
            <?php } ?>
          </select>
          <div class="help-block"><?php echo $this->text('Show by default reports for the selected store'); ?></div>
        </div>
      </div>
      <div class="form-group">
        <label class="col-md-2 control-label"><?php echo $this->text('Panels on dashboard'); ?></label>
        <div class="col-md-4">
          <?php foreach ($handlers as $handler) { ?>
          <div class="checkbox">
            <label>
              <input type="checkbox" name="settings[dashboard][]" value="<?php echo $this->escape($handler['id']); ?>"<?php echo in_array($handler['id'], $settings['dashboard']) ? ' checked' : ''; ?>> <?php echo $this->escape($handler['name']); ?>
            </label>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading"><?php echo $this->text('Google Analytics profiles'); ?></div>
    <div class="panel-body">
      <?php foreach ($stores as $id => $store) { ?>
      <div class="form-group">
        <label class="col-md-2 control-label"><?php echo $this->escape($store['name']); ?></label>
        <div class="col-md-4">
          <input name="settings[ga_profile_id][<?php echo $this->escape($id); ?>]" class="form-control" value="<?php echo isset($settings['ga_profile_id'][$id]) ? $this->escape($settings['ga_profile_id'][$id]) : ''; ?>">
        </div>
      </div>
      <?php } ?>
      <div class="form-group">
        <div class="col-md-10 col-md-offset-2"><?php echo $this->text('Specify Google Analytics profile (view) ID for each existing store'); ?></div>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="form-group">
        <div class="col-md-4 col-md-offset-2">
          <div class="btn-toolbar">
            <a href="<?php echo $this->url('admin/module/list'); ?>" class="btn btn-default"><?php echo $this->text('Cancel'); ?></a>
            <button class="btn btn-default save" name="save" value="1"><?php echo $this->text("Save"); ?></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>