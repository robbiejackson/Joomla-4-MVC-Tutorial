<?php
/**
 * Helper file for outputting html associated with the helloworld administrator functionality
 */

namespace Robbie\Component\Helloworld\Administrator\Service\HTML;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Associations;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\HTMLHelper;

class AdministratorService
{
	/**
	 * Render the list of associated items
	 *
	 * @param   integer  $id  The id of the helloworld record
	 *
	 * @return  string  The language HTML
	 *
	 * @throws  Exception
	 */
	public static function association($id)
	{
		// Defaults
		$html = '';

		// Get the associations
		if ($associations = Associations::getAssociations('com_helloworld', '#__helloworld', 'com_helloworld.item', (int)$id))
		{
			foreach ($associations as $tag => $associated)
			{
				$associations[$tag] = (int) $associated->id;
			}

			// get the relevant category titles and languages, for the tooltip
			$db = Factory::getDbo();
			$query = $db->getQuery(true)
				->select('h.*')
				->select('l.sef as lang_sef')
				->select('l.lang_code')
				->from('#__helloworld as h')
				->select('cat.title as category_title')
				->join('LEFT', '#__categories as cat ON cat.id=h.catid')
				->where('h.id IN (' . implode(',', array_values($associations)) . ')')
				->join('LEFT', '#__languages as l ON h.language=l.lang_code')
				->select('l.image')
				->select('l.title as language_title');
			$db->setQuery($query);

			try
			{
				$items = $db->loadObjectList('id');
			}
			catch (\RuntimeException $e)
			{
				throw new \Exception($e->getMessage(), 500, $e);
			}

			if ($items)
			{
				foreach ($items as &$item)
				{
					$text    = $item->lang_sef ? strtoupper($item->lang_sef) : 'XX';
					$url     = Route::_('index.php?option=com_helloworld&task=helloworld.edit&id=' . (int) $item->id);

					$tooltip = htmlspecialchars($item->greeting, ENT_QUOTES, 'UTF-8') . '<br />' . Text::sprintf('JCATEGORY_SPRINTF', $item->category_title);
					$classes = 'hasPopover label label-association label-' . $item->lang_sef;

					$item->link = '<a href="' . $url . '" title="' . $item->language_title . '" class="' . $classes
						. '" data-content="' . $tooltip . '" data-placement="top">'
						. $text . '</a>';
				}
			}

			HTMLHelper::_('bootstrap.popover');

			$html = LayoutHelper::render('joomla.content.associations', $items);
		}

		return $html;
	}
}