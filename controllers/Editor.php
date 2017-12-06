<?php

/**
 * @package Theme editor
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\editor\controllers;

use gplcart\modules\editor\models\Editor as EditorModuleModel;
use gplcart\core\controllers\backend\Controller as BackendController;

/**
 * Handles incoming requests and outputs data related to Theme editor module
 */
class Editor extends BackendController
{

    /**
     * Editor model instance
     * @var \gplcart\modules\editor\models\Editor $editor
     */
    protected $editor;

    /**
     * The current module
     * @var array
     */
    protected $data_module = array();

    /**
     * The current module file
     * @var string
     */
    protected $data_file;

    /**
     * The current file extension
     * @var string
     */
    protected $data_extension;

    /**
     * @param EditorModuleModel $editor
     */
    public function __construct(EditorModuleModel $editor)
    {
        parent::__construct();

        $this->editor = $editor;
    }

    /**
     * Route callback to display the select theme page
     */
    public function themeEditor()
    {
        $this->setTitleThemeEditor();
        $this->setBreadcrumbThemeEditor();

        $this->setData('themes', $this->module->getByType('theme'));

        $this->outputThemeEditor();
    }

    /**
     * Set title on the select theme page
     */
    protected function setTitleThemeEditor()
    {
        $this->setTitle($this->text('Themes'));
    }

    /**
     * Set breadcrumbs on the select theme page
     */
    protected function setBreadcrumbThemeEditor()
    {
        $breadcrumbs = array();

        $breadcrumbs[] = array(
            'url' => $this->url('admin'),
            'text' => $this->text('Dashboard')
        );

        $breadcrumbs[] = array(
            'url' => $this->url('admin/module/list'),
            'text' => $this->text('Modules')
        );

        $this->setBreadcrumbs($breadcrumbs);
    }

    /**
     * Output rendered templates on the select theme page
     */
    protected function outputThemeEditor()
    {
        $this->output('editor|themes');
    }

    /**
     * Displays the module file overview page
     * @param integer $module_id
     */
    public function listEditor($module_id)
    {
        $this->setModuleEditor($module_id);

        $this->setTitleListEditor();
        $this->setBreadcrumbListEditor();

        $this->setData('module', $this->data_module);
        $this->setData('files', $this->getFilesEditor());

        $this->outputListEditor();
    }

    /**
     * Returns an array of module data
     * @param string $module_id
     */
    protected function setModuleEditor($module_id)
    {
        $this->data_module = $this->module->get($module_id);

        if (empty($this->data_module)) {
            $this->outputHttpStatus(404);
        }

        if ($this->data_module['type'] !== 'theme') {
            $this->outputHttpStatus(403);
        }
    }

    /**
     * Returns an array of files to edit
     * @return array
     */
    protected function getFilesEditor()
    {
        $data = $this->editor->getList($this->data_module);
        return $this->prepareFilesEditor($data);
    }

    /**
     * Prepares an array of files to be edited
     * @param array $data
     * @return array
     */
    protected function prepareFilesEditor(array $data)
    {
        $prepared = array();
        foreach ($data as $folder => $files) {
            foreach ($files as $file) {

                $path = trim(str_replace($this->data_module['directory'], '', $file), '/');
                $depth = substr_count(str_replace('\\', '/', $path), '/'); // WIN compatibility

                $pathinfo = pathinfo($path);
                $directory = is_dir($file);
                $parent = $directory ? $path : $pathinfo['dirname'];

                $prepared[$folder][$parent][] = array(
                    'file' => $file,
                    'path' => $path,
                    'depth' => $depth,
                    'directory' => $directory,
                    'name' => $pathinfo['basename'],
                    'id' => gplcart_string_encode($path),
                    'indentation' => str_repeat('<span class="indentation"></span>', $depth)
                );
            }

            ksort($prepared[$folder]);
        }
        return $prepared;
    }

    /**
     * Sets title on theme files overview page
     */
    protected function setTitleListEditor()
    {
        $text = $this->text('Edit theme %name', array('%name' => $this->data_module['name']));
        $this->setTitle($text);
    }

    /**
     * Sets breadcrumbs on theme files overview page
     */
    protected function setBreadcrumbListEditor()
    {
        $breadcrumbs = array();

        $breadcrumbs[] = array(
            'url' => $this->url('admin'),
            'text' => $this->text('Dashboard')
        );

        $breadcrumbs[] = array(
            'url' => $this->url('admin/module/list'),
            'text' => $this->text('Modules')
        );

        $this->setBreadcrumbs($breadcrumbs);
    }

    /**
     * Renders templates of theme files overview page
     */
    protected function outputListEditor()
    {
        $this->output('editor|list');
    }

    /**
     * Displays the file edit page
     * @param string $module_id
     * @param string $file_id
     */
    public function editEditor($module_id, $file_id)
    {
        $this->setModuleEditor($module_id);
        $this->setFilePathEditor($file_id);

        $this->setTitleEditEditor();
        $this->setBreadcrumbEditEditor();
        $this->setMessageEditEditor();

        $this->setData('module', $this->data_module);
        $this->setData('can_save', $this->canSaveEditor());
        $this->setData('lines', $this->getFileTotalLinesEditor());
        $this->setData('editor.content', $this->getFileContentEditor());

        $this->submitEditor();

        $this->setJsSettingsEditor();
        $this->outputEditEditor();
    }

    /**
     * Sets messages on the file edit page
     */
    protected function setMessageEditEditor()
    {
        if ($this->current_theme['id'] == $this->data_module['id']) {
            $message = $this->text('You cannot edit the current theme');
            $this->setMessage($message, 'warning');
        } else if ($this->canSaveEditor() && $this->data_extension === 'php') {
            if (!$this->editor->canValidatePhpCode()) {
                $message = $this->text('Warning! PHP syntax validation is disabled due to your environment settings. It means that nothing stops you from saving invalid PHP code!');
                $this->setMessage($message, 'warning');
            }
            $this->setMessage($this->text('Be careful! Invalid PHP code can break down all your site!'), 'danger');
        }
    }

    /**
     * Sets JavaScript settings on the file edit page
     */
    protected function setJsSettingsEditor()
    {
        $settings = array(
            'readonly' => !$this->canSaveEditor(),
            'file_extension' => $this->data_extension
        );

        $this->setJsSettings('editor', $settings);
    }

    /**
     * Saves an array of submitted data
     */
    protected function submitEditor()
    {
        if ($this->isPosted('save') && $this->validateEditor()) {
            $this->saveEditor();
        }
    }

    /**
     * Validates a submitted data when editing a theme file
     * @return bool
     */
    protected function validateEditor()
    {
        $this->setSubmitted('editor', null, false);

        $this->setSubmitted('user_id', $this->uid);
        $this->setSubmitted('path', $this->data_file);
        $this->setSubmitted('module', $this->data_module);

        $this->validateSyntaxEditor();

        return !$this->hasErrors();
    }

    /**
     * Validate syntax of a submitted file
     */
    protected function validateSyntaxEditor()
    {
        $code = $this->getSubmitted('content');

        if (empty($code) || $this->data_extension !== 'php') {
            return null;
        }

        $result = $this->editor->validatePhpCode($code);

        if (!isset($result) || $result === true) {
            return null;
        }

        $error = empty($result) ? $this->text('There is a syntax error in your file') : $result;
        $this->setError('content', $error);
    }

    /**
     * Writes a submitted content to a theme file
     */
    protected function saveEditor()
    {
        $this->controlAccessSaveEditor();

        $submitted = $this->getSubmitted();
        $result = $this->editor->save($submitted);

        if ($result === true) {
            $message = $this->text('File has been saved');
            $this->redirect("admin/tool/editor/{$submitted['module']['id']}", $message, 'success');
        }

        $this->redirect('', $this->text('An error occurred'), 'warning');
    }

    /**
     * Whether the current user can save the file
     */
    protected function canSaveEditor()
    {
        return $this->access('editor_edit')//
                && $this->current_theme['id'] != $this->data_module['id'];
    }

    /**
     * Controls permissions to save a theme file for the current user
     */
    protected function controlAccessSaveEditor()
    {
        if (!$this->canSaveEditor()) {
            $this->outputHttpStatus(403);
        }
    }

    /**
     * Sets titles on the file edit page
     */
    protected function setTitleEditEditor()
    {
        $vars = array('%name' => str_replace('\\', '/', gplcart_path_relative($this->data_file)));
        $this->setTitle($this->text('Edit %name', $vars));
    }

    /**
     * Sets breadcrumbs on the file edit page
     */
    protected function setBreadcrumbEditEditor()
    {
        $breadcrumbs = array();

        $breadcrumbs[] = array(
            'url' => $this->url('admin'),
            'text' => $this->text('Dashboard')
        );

        $breadcrumbs[] = array(
            'url' => $this->url('admin/module/list'),
            'text' => $this->text('Modules')
        );

        $breadcrumbs[] = array(
            'url' => $this->url('admin/tool/editor'),
            'text' => $this->text('Themes')
        );

        $breadcrumbs[] = array(
            'url' => $this->url("admin/tool/editor/{$this->data_module['id']}"),
            'text' => $this->text('Edit theme %name', array('%name' => $this->data_module['name']))
        );

        $this->setBreadcrumbs($breadcrumbs);
    }

    /**
     * Renders the file edit page
     */
    protected function outputEditEditor()
    {
        $this->output('editor|edit');
    }

    /**
     * Returns a path to the file to be edited
     * @param string $encoded_filename URL encoded base64 hash
     */
    protected function setFilePathEditor($encoded_filename)
    {
        $filepath = gplcart_string_decode($encoded_filename);
        $this->data_file = "{$this->data_module['directory']}/$filepath";

        if (!is_file($this->data_file) || !is_readable($this->data_file)) {
            $this->outputHttpStatus(404);
        }

        $this->data_extension = pathinfo($this->data_file, PATHINFO_EXTENSION);
    }

    /**
     * Returns a content of the file
     * @return string
     */
    protected function getFileContentEditor()
    {
        return file_get_contents($this->data_file);
    }

    /**
     * Returns the total number of lines in the file
     * @return integer
     */
    protected function getFileTotalLinesEditor()
    {
        return count(file($this->data_file));
    }

}
