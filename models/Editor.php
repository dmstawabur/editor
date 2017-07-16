<?php

/**
 * @package Theme editor
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\editor\models;

use gplcart\core\Model;
use gplcart\core\models\Language as LanguageModel;
use gplcart\modules\backup\models\Backup as ModuleBackupModel;

/**
 * Manages basic behaviors and data related to Theme Editor module
 */
class Editor extends Model
{

    /**
     * Language model instance
     * @var \gplcart\core\models\Language $language
     */
    protected $language;

    /**
     * Backup model instance
     * @var \gplcart\modules\backup\models\Backup $backup
     */
    protected $backup;

    /**
     * @param LanguageModel $language
     * @param ModuleBackupModel $backup
     */
    public function __construct(LanguageModel $language,
            ModuleBackupModel $backup)
    {
        parent::__construct();

        $this->backup = $backup;
        $this->language = $language;
    }

    /**
     * Returns an array of editable files
     * @param array $module
     * @return array
     */
    public function getList(array $module)
    {
        $list = array();
        foreach (array('templates', 'css', 'js') as $folder) {
            $files = gplcart_file_scan_recursive("{$module['directory']}/$folder");
            sort($files);
            $list[$folder] = $files;
        }

        $this->hook->fire('module.editor.list', $list);
        return $list;
    }

    /**
     * Saves an edited file
     * @param array $data
     * @return boolean
     */
    public function save($data)
    {
        $this->hook->fire('module.editor.save.before', $data);

        if (empty($data)) {
            return false;
        }

        $has_backup = true;
        if (!$this->hasBackup($data['module'])) {
            $has_backup = $this->backup->backup('module', $data['module']);
        }

        if ($has_backup !== true) {
            return false;
        }

        $result = $this->write($data['content'], $data['path']);

        $this->hook->fire('module.editor.save.after', $data, $result);
        return $result;
    }

    /**
     * Writes a content to a file
     * @param string $content
     * @param string $file
     * @return boolean
     */
    protected function write($content, $file)
    {
        if (!file_exists($file)) {
            return false; // Do not create a new file
        }

        return file_put_contents($file, $content) !== false;
    }

    /**
     * Whether a module ID has a backup
     * @param array $module
     * @return boolean
     */
    public function hasBackup(array $module)
    {
        $conditions = array('module_id' => $module['id']);
        $existing = $this->backup->getList($conditions);
        return !empty($existing);
    }

}
