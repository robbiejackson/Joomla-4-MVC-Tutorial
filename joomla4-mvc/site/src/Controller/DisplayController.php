<?php

namespace Robbie\Component\Helloworld\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;

class DisplayController extends BaseController {
    
    public function display($cachable = false, $urlparams = array())
    {        
        $viewName = $this->input->get('view', '');
		$cachable = true;
		if ($viewName == 'form' || Factory::getApplication()->getIdentity()->get('id'))
		{
			$cachable = false;
		}
		
		$safeurlparams = array(
			'id'               => 'ARRAY',
			'catid'            => 'ARRAY',
			'list'             => 'ARRAY',
			'limitstart'       => 'UINT',
			'Itemid'           => 'INT',
			'view'             => 'CMD',
			'lang'             => 'CMD',
		);
		
		parent::display($cachable, $safeurlparams);

        return $this;
    }
    
    public function mapsearch()
    {
//		if (!Session::checkToken('get')) 
//		{
//			echo new JsonResponse(null, JText::_('JINVALID_TOKEN'), true);
//		}
//		else 
//		{
			parent::display();
//		}
    }
}