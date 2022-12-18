<?php

namespace Robbie\Component\Helloworld\Administrator\Field\Modal;

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

/**
 * Supports a modal for selecting a helloworld record
 *
 */
class HelloworldField extends FormField
{
	/**
	 * Method to get the html for the input field.
	 *
	 * @return  string  The field input html.
	 */
	protected function getInput()
	{
		// Load language
		Factory::getLanguage()->load('com_helloworld', JPATH_ADMINISTRATOR);

		// $this->value is set if there's a default id specified in the xml file
		$value = (int) $this->value > 0 ? (int) $this->value : '';
        
		// $this->id will be jform_request_xxx where xxx is the name of the field in the xml file
		$modalId = 'Helloworld_' . $this->id;
        
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        
        // Need the modal field script
        $wa->useScript('field.modal-fields');

		// our callback function from the modal to the main window:
		$wa->addInlineScript("
			function jSelectHelloworld_" . $this->id . "(id, title, catid, object, url, language) {
				window.processModalSelect('Helloworld', '" . $this->id . "', id, title, catid, object, url, language);
			}
			");

		// if a default id is set, then get the corresponding greeting to display it
		if ($value)
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('greeting'))
				->from($db->quoteName('#__helloworld'))
				->where($db->quoteName('id') . ' = ' . (int) $value);
			$db->setQuery($query);

			try
			{
				$title = $db->loadResult();
			}
			catch (\RuntimeException $e)
			{
                Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}
        
		// display the default greeting or "Select" if no default specified
		$title = empty($title) ? Text::_('COM_HELLOWORLD_MENUITEM_SELECT_HELLOWORLD') : htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
		$html  = '<span class="input-group">';
		$html .= '<input class="form-control" id="' . $this->id . '_name" type="text" value="' . $title . '" readonly size="35" />';

		// html for the Select button
		$html .= '<button'
			. ' class="btn btn-primary' . ($value ? ' hidden' : '') . '"'
			. ' id="' . $this->id . '_select"'
			. ' data-bs-toggle="modal"'
			. ' type="button"'
			. ' data-bs-target="#ModalSelect' . $modalId . '"'
			. ' title="' . HTMLHelper::tooltipText('COM_HELLOWORLD_MENUITEM_SELECT_BUTTON_TOOLTIP') . '">'
			. '<span class="icon-file" aria-hidden="true"></span> ' . Text::_('JSELECT')
			. '</button>';

		// html for the Clear button
		$html .= '<button'
			. ' class="btn btn-secondary' . ($value ? '' : ' hidden') . '"'
			. ' id="' . $this->id . '_clear"'
			. ' type="button"'
			. ' onclick="window.processModalParent(\'' . $this->id . '\'); return false;">'
			. '<span class="icon-times" aria-hidden="true"></span> ' . Text::_('JCLEAR')
			. '</button>';

		$html .= '</span>';

		// url for the iframe
		$linkHelloworlds = 'index.php?option=com_helloworld&amp;view=helloworlds&amp;layout=modal&amp;tmpl=component&amp;' . Session::getFormToken() . '=1';
		$urlSelect = $linkHelloworlds . '&amp;function=jSelectHelloworld_' . $this->id;
        
		// title to go in the modal header
		$modalTitle    = Text::_('COM_HELLOWORLD_MENUITEM_SELECT_MODAL_TITLE');
        
        // if the form definition has a 'language' field then it's for the association
		// add the forcedLanguage parameter to the URL, and add the language to the modal title
		if (isset($this->element['language']))
		{
			$urlSelect .= '&amp;forcedLanguage=' . $this->element['language'];
			$modalTitle .= ' &#8212; ' . $this->element['label'];
		}
        
		// html to set up the modal iframe
		$html .= HTMLHelper::_(
			'bootstrap.renderModal',
			'ModalSelect' . $modalId,
			array(
				'title'       => $modalTitle,
				'url'         => $urlSelect,
				'height'      => '400px',
				'width'       => '800px',
				'bodyHeight'  => '70',
				'modalWidth'  => '80',
				'footer'      => '<a role="button" class="btn" data-bs-dismiss="modal" aria-hidden="true">' . Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>',
			)
		);

		// class='required' for client side validation.
		$class = $this->required ? ' class="required modal-value"' : '';

		// hidden input field to store the helloworld record id
		$html .= '<input type="hidden" id="' . $this->id . '_id" ' . $class 
			. ' data-required="' . (int) $this->required . '" name="' . $this->name
			. '" data-text="' . htmlspecialchars(Text::_('COM_HELLOWORLD_MENUITEM_SELECT_HELLOWORLD', true), ENT_COMPAT, 'UTF-8') 
			. '" value="' . $value . '" />';

		return $html;
	}

	/**
	 * Method to get the html for the label field.
	 *
	 * @return  string  The field label html.
	 */
	protected function getLabel()
	{
        return str_replace($this->id, $this->id . '_name', parent::getLabel());
	}
}