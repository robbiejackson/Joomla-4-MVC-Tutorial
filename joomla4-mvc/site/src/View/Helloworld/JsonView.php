<?php
/**
 * View file for responding to Ajax request for performing Search Here on the map
 * 
 */
namespace Robbie\Component\Helloworld\Site\View\Helloworld;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\JsonView as BaseJsonView;
use Joomla\CMS\Factory;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Language\Text;

class JsonView extends BaseJsonView
{
	/**
	 * This display function returns in json format the Helloworld greetings
	 *   found within the latitude and longitude boundaries of the map.
	 * These bounds are provided in the parameters
	 *   minlat, minlng, maxlat, maxlng
	 */

	function display($tpl = null)
	{
		$input = Factory::getApplication()->input;
		$mapbounds = $input->get('mapBounds', array(), 'ARRAY');
		$model = $this->getModel();
		if ($mapbounds)
		{
			$records = $model->getMapSearchResults($mapbounds);
			if ($records) 
			{
				echo new JsonResponse($records);
			}
			else
			{
				echo new JsonResponse(null, Text::_('COM_HELLOWORLD_ERROR_NO_RECORDS'), true);
			}
		}
		else 
		{
			$records = array();
			echo new JsonResponse(null, Text::_('COM_HELLOWORLD_ERROR_NO_MAP_BOUNDS'), true);
		}
	}
}