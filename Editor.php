<?php

/**
 * @package Theme editor
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\editor;

use gplcart\core\Module;

/**
 * Main class for Theme editor module
 */
class Editor
{

    /**
     * Module class instance
     * @var \gplcart\core\Module $module
     */
    protected $module;

    /**
     * @param Module $module
     */
    public function __construct(Module $module)
    {
        $this->module = $module;
    }

    /**
     * Implements hook "route.list"
     * @param array $routes
     */
    public function hookRouteList(array &$routes)
    {
        $routes['admin/tool/editor'] = array(
            'access' => 'editor',
            'menu' => array('admin' => /* @text */'Theme editor'),
            'handlers' => array(
                'controller' => array('gplcart\\modules\\editor\\controllers\\Editor', 'themeEditor')
            )
        );

        $routes['admin/tool/editor/(\w+)'] = array(
            'access' => 'editor',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\editor\\controllers\\Editor', 'listEditor')
            )
        );

        $routes['admin/tool/editor/(\w+)/([^/]+)'] = array(
            'access' => 'editor_content',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\editor\\controllers\\Editor', 'editEditor')
            )
        );
    }

    /**
     * Implements hook "construct.controller.backend"
     * @param \gplcart\core\controllers\backend\Controller $controller
     */
    public function hookConstructControllerBackend($controller)
    {
        $this->setModuleAssets($controller);
    }

    /**
     * Implements hook "user.role.permissions"
     * @param array $permissions
     */
    public function hookUserRolePermissions(array &$permissions)
    {
        $permissions['editor'] = /* @text */'Theme editor: access';
        $permissions['editor_edit'] = /* @text */'Theme editor: edit file';
        $permissions['editor_content'] = /* @text */'Theme editor: access file content';
    }

    /**
     * Sets module specific assets
     * @param \gplcart\core\controllers\backend\Controller $controller
     */
    protected function setModuleAssets($controller)
    {
        if ($controller->path('^admin/tool/editor') && $this->module->isEnabled('codemirror')) {
            $this->getCodemirrorModule()->addLibrary($controller);
            $controller->setJs('system/modules/editor/js/common.js', array('aggregate' => false));
        }
    }

    /**
     * Returns CodeMirror module instance
     * @return \gplcart\modules\codemirror\Codemirror
     */
    protected function getCodemirrorModule()
    {
        return $this->module->getInstance('codemirror');
    }

}
