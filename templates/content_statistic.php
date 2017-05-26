<?php
/**
 * @package Google Analytics Report
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */
?>
<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <span class="pull-left">
    <?php echo $this->e($report['handler']['name']); ?>
    </span>
    <span class="small pull-right">
      <?php if(isset($report['updated'])) { ?>
      <?php echo $this->text('Last updated: @date', array('@date' => $this->date($report['updated']))); ?>
      <?php } ?>
      <a href="<?php echo $this->url('', array('ga' => array('update' => array('handler_id' => $report['handler']['id'], 'store_id' => $settings['store_id'])))); ?>">
        <i class="fa fa-refresh" title="<?php echo $this->text('Update'); ?>"></i>
      </a>
    </span>
  </div>
  <div class="panel-body">
    <?php if (isset($report['error'])) { ?>
    <?php echo $this->e($report['error']); ?>
    <?php } else if (empty($report['data']['rows'])) { ?>
    <?php echo $this->text('No results'); ?>
    <?php } else { ?>
    <div data-chart-source="<?php echo $this->json($report['data']['rows']); ?>" data-chart-id="ga_<?php echo $this->e($report['handler']['id']); ?>">
      <table class="table table-condensed table-striped">
        <thead>
        <th><?php echo $this->text('Views'); ?></th>
        <th><?php echo $this->text('Unique visits'); ?></th>
        </thead>
        <tbody>
          <?php foreach ($report['data']['rows'] as $row) { ?>
          <tr>
            <td><?php echo $this->e($row[0]); ?></td>
            <td><?php echo $this->e($row[1]); ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
    <?php } ?>
  </div>
</div>

