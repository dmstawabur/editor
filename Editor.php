<?php

/**
 * @package Theme editor
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\editor;

use gplcart\core\Module,
    gplcart\core\Config;

/**
 * Main class for Theme editor module
 */
class Editor extends Module
{

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        parent::__construct($config);
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
        if ($controller->path('^admin/tool/editor') && $this->config->isEnabledModule('codemirror')) {

            /* @var $module \gplcart\modules\codemirror\Codemirror */
            $module = $this->getInstance('Codemirror');

            $module->addLibrary($controller);
            $controller->setJs('system/modules/editor/js/common.js', array('aggregate' => false));
        }
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

}
