<?php

/**
 * @package Theme editor
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\editor\models;

use gplcart\core\Config,
    gplcart\core\Hook;
use gplcart\core\models\Language as LanguageModel;

/**
 * Manages basic behaviors and data related to Theme Editor module
 */
class Editor
{

    /**
     * Hook class instance
     * @var \gplcart\core\Hook $hook
     */
    protected $hook;

    /**
     * Config class instance
     * @var \gplcart\core\Config $config
     */
    protected $config;

    /**
     * Language model instance
     * @var \gplcart\core\models\Language $language
     */
    protected $language;

    /**
     * @param Hook $hook
     * @param Config $config
     * @param LanguageModel $language
     */
    public function __construct(Hook $hook, Config $config, LanguageModel $language)
    {
        $this->hook = $hook;
        $this->config = $config;
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
        foreach ($this->getScanFolders() as $folder) {
            $files = gplcart_file_scan_recursive("{$module['directory']}/$folder");
            sort($files);
            $list[$folder] = $files;
        }

        $this->hook->attach('module.editor.list', $list);
        return $list;
    }

    /**
     * Returns an array of folder names to scan
     * @return array
     */
    protected function getScanFolders()
    {
        return array('templates', 'css', 'js');
    }

    /**
     * Saves an edited file
     * @param array $data
     * @return boolean
     */
    public function save($data)
    {
        $result = null;
        $this->hook->attach('module.editor.save.before', $data, $result);

        if (isset($result)) {
            return $result;
        }

        if (!$this->backup($data)) {
            return false;
        }

        $result = $this->write($data['content'], $data['path']);
        $this->hook->attach('module.editor.save.after', $data, $result);
        return $result;
    }

    /**
     * Backup a module
     * @param array $data
     * @return bool
     */
    protected function backup(array $data)
    {
        $has_backup = true;

        try {
            /* @var $backup \gplcart\modules\backup\Backup */
            $backup = $this->config->getModuleInstance('backup');
            if (!$backup->exists($data['module']['id'])) {
                $has_backup = $backup->backup('module', $data['module']) === true;
            }
        } catch (\Exception $ex) {
            trigger_error($ex->getMessage());
            $has_backup = false;
        }

        return $has_backup;
    }

    /**
     * Writes a content to a file
     * @param string $content
     * @param string $file
     * @return boolean
     */
    protected function write($content, $file)
    {
        if (file_exists($file)) { // Do not create a new file
            return file_put_contents($file, $content) !== false;
        }

        return false;
    }

    /**
     * Tries to validate syntax of a PHP file
     * @param string $file
     * @return mixed
     */
    public function validatePhpFile($file)
    {
        if (!$this->canValidatePhpCode()) {
            return null;
        }

        $output = shell_exec('php -l ' . escapeshellarg($file));

        $count = 0;
        $error = preg_replace('/Errors parsing.*$/', '', $output, -1, $count);
        return $count > 0 ? trim("$error") : true;
    }

    /**
     * Whether it's possible to validate a PHP code
     * @return bool
     */
    public function canValidatePhpCode()
    {
        return function_exists('shell_exec') && !in_array('shell_exec', explode(',', ini_get('disable_functions')));
    }

    /**
     * Tries to validate a PHP code
     * @param string $code
     * @return mixed
     */
    public function validatePhpCode($code)
    {
        $temp = tmpfile();
        fwrite($temp, $code);
        $meta_data = stream_get_meta_data($temp);

        $result = $this->validatePhpFile($meta_data['uri']);
        fclose($temp);
        return $result;
    }

}
