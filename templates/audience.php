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
    <?php echo $this->escape($report['handler']['name']); ?>
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
    <?php echo $this->escape($report['error']); ?>
    <?php } else if (empty($report['data']['rows'])) { ?>
    <?php echo $this->text('No results'); ?>
    <?php } else { ?>
    <div data-chart-settings="<?php echo $this->json($report['data']['rows']); ?>" data-chart-id="ga-<?php echo $this->escape($report['handler']['id']); ?>">
      <table class="table table-condensed table-striped">
        <tbody>
          <tr>
            <th><?php echo $this->text('Visitors'); ?></th>
            <td><?php echo $this->escape($report['data']['rows'][0][0]); ?></td>
          </tr>
          <tr>
            <th><?php echo $this->text('New visits'); ?></th>
            <td><?php echo $this->escape($report['data']['rows'][0][1]); ?></td>
          </tr>
          <tr>
            <th><?php echo $this->text('New visits'); ?> %</th>
            <td><?php echo $this->escape(round($report['data']['rows'][0][2], 1)); ?></td>
          </tr>
          <tr>
            <th><?php echo $this->text('Total visits'); ?></th>
            <td><?php echo $this->escape($report['data']['rows'][0][3]); ?></td>
          </tr>
          <tr>
            <th><?php echo $this->text('Page views'); ?></th>
            <td><?php echo $this->escape($report['data']['rows'][0][4]); ?></td>
          </tr>
          <tr>
            <th><?php echo $this->text('Bounces'); ?></th>
            <td><?php echo $this->escape($report['data']['rows'][0][5]); ?></td>
          </tr>
          <tr>
            <th><?php echo $this->text('Bounce rate'); ?></th>
            <td><?php echo $this->escape(round($report['data']['rows'][0][6], 1)); ?></td>
          </tr>
          <tr>
            <th><?php echo $this->text('Time on site'); ?></th>
            <td><?php echo $this->escape(round($report['data']['rows'][0][7], 1)); ?></td>
          </tr>
          <tr>
            <th><?php echo $this->text('Averate time on site'); ?></th>
            <td><?php echo $this->escape(round($report['data']['rows'][0][8], 1)); ?></td>
          </tr>
        </tbody>
      </table>
    </div>
    <?php } ?>
  </div>
</div>

