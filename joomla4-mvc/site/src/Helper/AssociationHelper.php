<?php
/**
 * Helper file for Helloworld Associations (on the site part)
 */
namespace Robbie\Component\Helloworld\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\Component\Categories\Administrator\Helper\CategoryAssociationHelper;

/**
 * Helloworld Component Association Helper
 *
 */
abstract class AssociationHelper extends CategoryAssociationHelper
{
	/**
	 * Method to get the associations for a given item
	 *
	 * @param   integer  $id    Id of the item (helloworld id or catid, depending on view)
	 * @param   string   $view  Name of the view ('helloworld' or 'category')
	 *
	 * @return  array   Array of associations for the item
	 */
	public static function getAssociations($id = 0, $view = null, $layout = null)
	{
		$input = Factory::getApplication()->input;
		$view = $view === null ? $input->get('view') : $view;
		$id = empty($id) ? $input->getInt('id') : $id;

		if ($view === 'helloworld')
		{
			if ($id)
			{
				$associations = Associations::getAssociations('com_helloworld', '#__helloworld', 'com_helloworld.item', $id);

				$return = array();

				foreach ($associations as $tag => $item)
				{
					$link = 'index.php?option=com_helloworld&view=helloworld&id=' . $item->id . '&catid=' . $item->catid;
					if ($item->language && $item->language !== '*' && Multilanguage::isEnabled())
					{
						$link .= '&lang=' . $item->language;
					}
					$return[$tag] = $link;
				}

				return $return;
			}
		}

		if ($view === 'category' || $view === 'categories')
		{
            \JLoader::registerAlias('HelloworldHelperRoute', '\\Robbie\\Component\\Helloworld\\Site\\Helper\\RouteHelper', '5.0');
			return self::getCategoryAssociations($id, 'com_helloworld');
		}

		return array();
	}
}