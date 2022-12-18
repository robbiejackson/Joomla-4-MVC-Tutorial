<?php
/**
 * Class associated with displaying an input field to capture the parent of a helloworld record
 */
namespace Robbie\Component\Helloworld\Administrator\Field;

use Joomla\CMS\Form\Field\ListField;

\defined('_JEXEC') or die;

class HelloworldParentField extends ListField
{
	protected $type = 'HelloworldParent';

	/**
	 * Method to return the field options for the parent
	 *
	 */
	protected function getOptions()
	{
		$options = array();

		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('DISTINCT(a.id) AS value, a.greeting AS text, a.level, a.lft')
			->from('#__helloworld AS a');
		
		// Prevent parenting to children of this record, or to itself
		// If this record has lft = x and rgt = y, then its children have lft > x and rgt < y
		if ($id = $this->form->getValue('id'))
		{
			$query->join('LEFT', $db->quoteName('#__helloworld') . ' AS h ON h.id = ' . (int) $id)
				->where('NOT(a.lft >= h.lft AND a.rgt <= h.rgt)');
		}
		
		$query->order('a.lft ASC');
		
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (\RuntimeException $e)
		{
            throw new \RuntimeException(implode("\n", $e->getMessage()), 500);
		}
		
		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 0; $i < count($options); $i++)
		{
			$options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;

	}
}