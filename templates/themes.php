<?php

/**
 * @package Theme editor
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */
?>
<div class="panel panel-default">
  <div class="panel-body">
    <div class="form-group">
      <div class="col-md-12">
        <?php foreach ($themes as $module_id => $module) { ?>
          <a class="btn btn-default" href="<?php echo $this->url("admin/tool/editor/$module_id"); ?>"><?php echo $this->e($module['name']); ?></a>
        <?php } ?>
      </div>
    </div>
  </div>
</div>


