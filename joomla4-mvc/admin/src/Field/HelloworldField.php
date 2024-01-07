<?php

namespace Robbie\Component\Helloworld\Administrator\Field;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;

\defined('_JEXEC') or die;

class HelloworldField extends ListField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.6
     */
    protected $type = 'Helloworld';

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     *
     * @since   1.6
     */
    public function getOptions()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
		$query->select('#__helloworld.id as id,greeting,#__categories.title as category,catid');
		$query->from('#__helloworld');
		$query->leftJoin('#__categories on catid=#__categories.id');
		// Retrieve only published items
		$query->where('#__helloworld.published = 1');
		$db->setQuery((string) $query);
		$messages = $db->loadObjectList();
		$options  = array();

		if ($messages)
		{
			foreach ($messages as $message)
			{
				$options[] = HTMLHelper::_('select.option', $message->id, $message->greeting .
				                      ($message->catid ? ' (' . $message->category . ')' : ''));
			}
		}

		$options = array_merge(parent::getOptions(), $options);

		return $options;

    }
}