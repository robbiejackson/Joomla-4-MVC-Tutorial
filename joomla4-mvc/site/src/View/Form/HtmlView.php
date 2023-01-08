<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Robbie\Component\Helloworld\Site\View\Form;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Language\Multilanguage;

/**
 * HelloWorld View
 * This is the site view presenting the user with the ability to add a new Helloworld record
 * 
 */
class HtmlView extends BaseHtmlView
{

	protected $form = null;
	protected $canDo;

	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the layout file to parse.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$app = Factory::getApplication(); 
        
        // Get the form to display
		$this->form = $this->get('Form');

		// Check that the user has permissions to create a new helloworld record
		$this->canDo = ContentHelper::getActions('com_helloworld');
		if (!($this->canDo->get('core.create'))) 
		{
			
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->setHeader('status', 403, true);
			return;
		}
        
        // Propose current language as default
		if (Multilanguage::isEnabled())
		{
			$lang = $app->getLanguage()->getTag();
			$this->form->setFieldAttribute('language', 'default', $lang);
		}
        
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode("\n", $errors), 500);
		}

		// Call the parent display to display the layout file
		parent::display($tpl);

		// Set properties of the html document
		$this->setDocument();
	}

	/**
	 * Method to set up the html document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
        $this->document->setTitle(Text::_('COM_HELLOWORLD_HELLOWORLD_CREATING'));
	}
}