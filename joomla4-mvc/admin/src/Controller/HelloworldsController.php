<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Robbie\Component\Helloworld\Administrator\Controller;

use Joomla\CMS\MVC\Controller\AdminController;

\defined('_JEXEC') or die;

class HelloworldsController extends AdminController
{

    public function getModel($name = 'Helloworld', $prefix = 'Administrator', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }
}
