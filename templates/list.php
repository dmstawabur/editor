<?php
/**
 * @package Theme Editor
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */
?>
<div class="panel panel-default">
  <div class="panel-body">
    <?php $access_content = $this->access('editor_content'); ?>
    <?php foreach ($files as $folder => $sections) { ?>
    <ul class="list-unstyled">
      <li><?php echo $folder; ?> <i class="fa fa-folder-open-o"></i></li>
      <?php foreach ($sections as $section) { ?>
      <?php foreach ($section as $file) { ?>
      <?php if (empty($file['directory'])) { ?>
      <li>
        <?php echo $file['indentation']; ?>
        <?php if($access_content) { ?>
        <a title="<?php echo $file['path']; ?>" href="<?php echo $this->url("admin/tool/editor/{$module['id']}/{$file['id']}"); ?>">
          <?php echo $this->e($file['name']); ?>
        </a>
        <?php } else { ?>
        <?php echo $this->e($file['name']); ?>
        <?php } ?>
      </li>
      <?php } else { ?>
      <li title="<?php echo $file['path']; ?>">
        <?php echo $file['indentation']; ?> <?php echo $this->e($file['name']); ?> <i class="fa fa-folder-open-o"></i>
      </li>
      <?php } ?>
      <?php } ?>
      <?php } ?>
    </ul>
    <?php } ?>
  </div>
</div>
