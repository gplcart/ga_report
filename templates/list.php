<?php
/**
 * @package Google Analytics Report
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */
?>
<div class="panel panel-default">
  <div class="panel-body">
    <form class="form-horizontal">
      <div class="form-group">
        <label class="col-md-2 control-label"><?php echo $this->text('Store'); ?></label>
        <div class="col-md-4">
          <select name="ga[update][store_id]" class="form-control" onchange="$(this).closest('form').submit();">
            <?php foreach ($stores as $id => $store) { ?>
            <option value="<?php echo $this->escape($id); ?>"<?php echo $ga_store_id == $id ? ' selected' : ''; ?>><?php echo $this->escape($store['name']); ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="row">
  <?php foreach ($panels as $columns) { ?>
  <div class="col-md-4">
    <?php foreach ($columns as $panel) { ?>
    <?php echo $panel['rendered']; ?>
    <?php } ?>
  </div>
  <?php } ?>
</div>
