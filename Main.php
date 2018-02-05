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
class Main
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
            'menu' => array(
                'admin' => 'Theme editor' // @text
            ),
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
        $permissions['editor'] = 'Theme editor: access'; // @text
        $permissions['editor_edit'] = 'Theme editor: edit file'; // @text
        $permissions['editor_content'] = 'Theme editor: access file content'; // @text
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
     * @return \gplcart\modules\codemirror\Main
     */
    protected function getCodemirrorModule()
    {
        /** @var \gplcart\modules\codemirror\Main $instance */
        $instance = $this->module->getInstance('codemirror');
        return $instance;
    }

}
