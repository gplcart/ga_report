<?php
/**
 * @package Google Analytics Report
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */
?>
<?php if($this->access('ga_report')) { ?>
<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <span class="pull-left">
    <?php echo $this->e($content['data']['report']['handler']['name']); ?>
    </span>
    <span class="small pull-right">
      <?php if(isset($content['data']['report']['updated'])) { ?>
      <?php echo $this->text('Last updated: @date', array('@date' => $this->date($content['data']['report']['updated']))); ?>
      <?php } ?>
      <a href="<?php echo $this->url('', array('ga' => array('update' => array('handler_id' => $content['data']['report']['handler']['id'], 'store_id' => $content['data']['settings']['store_id'])))); ?>">
        <i class="fa fa-refresh" title="<?php echo $this->text('Update'); ?>"></i>
      </a>
    </span>
  </div>
  <div class="panel-body">
    <?php if (isset($content['data']['report']['error'])) { ?>
    <?php echo $this->e($content['data']['report']['error']); ?>
    <?php } else if (empty($content['data']['report']['data']['rows'])) { ?>
    <?php echo $this->text('No results'); ?>
    <?php } else { ?>
    <div data-chart-source="<?php echo $this->json($content['data']['report']['data']['rows']); ?>" data-chart-id="ga_<?php echo $this->e($content['data']['report']['handler']['id']); ?>">
      <table class="table table-condensed table-striped">
        <thead>
        <th><?php echo $this->text('Keyword'); ?></th>
        <th><?php echo $this->text('Visits'); ?></th>
        </thead>
        <tbody>
          <?php foreach ($content['data']['report']['data']['rows'] as $row) { ?>
          <tr>
            <td><?php echo $this->truncate($this->e($row[0]), 100); ?></td>
            <td><?php echo $this->e($row[1]); ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
    <?php } ?>
  </div>
</div>
<?php } ?>
