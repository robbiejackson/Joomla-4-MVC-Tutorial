<?php

namespace Robbie\Component\Helloworld\Administrator\View\Helloworld;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;


class HtmlView extends BaseHtmlView {
    
    protected $form;
	protected $item;
	protected $canDo;
       
    /**
     * Display the "Hello World" edit view
     */
    function display($tpl = null) {
        
        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');
        
        // What Access Permissions does this user have? What can (s)he do?
		$this->canDo = ContentHelper::getActions('com_helloworld', 'helloworld', $this->item->id);

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
        
        $this->addToolBar();
        
        $this->setupDocument();
        
        parent::display($tpl);
    }

    protected function addToolBar() {

        $input = Factory::getApplication()->input;

		// Hide Joomla Administrator Main menu
		$input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);

		ToolBarHelper::title($isNew ? Text::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_NEW')
		                            : Text::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT'), 'helloworld');
		// Build the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($this->canDo->get('core.create')) 
			{
				ToolbarHelper::apply('helloworld.apply', 'JTOOLBAR_APPLY');
				ToolbarHelper::save('helloworld.save', 'JTOOLBAR_SAVE');
				ToolbarHelper::custom('helloworld.save2new', 'save-new.png', 'save-new_f2.png',
				                       'JTOOLBAR_SAVE_AND_NEW', false);
			}
			ToolbarHelper::cancel('helloworld.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($this->canDo->get('core.edit'))
			{
				// We can save the new record
				ToolbarHelper::apply('helloworld.apply', 'JTOOLBAR_APPLY');
				ToolbarHelper::save('helloworld.save', 'JTOOLBAR_SAVE');
 
				// We can save this record, but check the create permission to see
				// if we can return to make a new one.
				if ($this->canDo->get('core.create')) 
				{
					ToolbarHelper::custom('helloworld.save2new', 'save-new.png', 'save-new_f2.png',
					                       'JTOOLBAR_SAVE_AND_NEW', false);
				}
                $save_history = Factory::getApplication()->get('save_history', true);
				if ($save_history) 
				{
					ToolbarHelper::versions('com_helloworld.helloworld', $this->item->id);
				}
			}
			if ($this->canDo->get('core.create')) 
			{
				ToolbarHelper::custom('helloworld.save2copy', 'save-copy.png', 'save-copy_f2.png',
				                       'JTOOLBAR_SAVE_AS_COPY', false);
			}
			ToolbarHelper::cancel('helloworld.cancel', 'JTOOLBAR_CLOSE');
		}
		
		ToolbarHelper::divider();
		ToolbarHelper::inlinehelp();
    }
        
    protected function setupDocument() {
		//HtmlHelper::_('behavior.framework');
		//HtmlHelper::_('behavior.formvalidator');

		$isNew = ($this->item->id < 1);
		$this->document->setTitle($isNew ? Text::_('COM_HELLOWORLD_HELLOWORLD_CREATING') :
                Text::_('COM_HELLOWORLD_HELLOWORLD_EDITING'));
	}
}