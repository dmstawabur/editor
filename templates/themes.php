<?php
/**
 * @package Theme editor
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */
?>
<?php echo $this->text('Select a theme to edit:'); ?>
<ul class="list-unstyled">
  <?php foreach ($themes as $module_id => $module) { ?>
  <li>
    <a href="<?php echo $this->url("admin/tool/editor/$module_id"); ?>">
      <?php echo $this->e($module['name']); ?>
    </a>
  </li>
  <?php } ?>
</ul>
