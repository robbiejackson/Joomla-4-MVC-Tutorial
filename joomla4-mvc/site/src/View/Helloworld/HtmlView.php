<?php

namespace Robbie\Component\Helloworld\Site\View\Helloworld;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Event\Event;

class HtmlView extends BaseHtmlView {
    

    public function display($template = null)
    {
        // Assign data to the view
		$this->item = $this->get('Item');
        $app = Factory::getApplication();
        $user = $app->getIdentity();
        
        // for custom fields
		PluginHelper::importPlugin('content');
		$item = $this->item;
		$item->text = null;
        $item->params   = $app->getParams();

		$this->dispatchEvent(new Event('onContentPrepare', array ('com_helloworld.helloworld', &$item, &$item->params, null)));

		$results = Factory::getApplication()->triggerEvent('onContentAfterTitle', array('com_helloworld.helloworld', &$item, &$item->params, null));
		$item->afterDisplayTitle = trim(implode("\n", $results));

		$results = Factory::getApplication()->triggerEvent('onContentBeforeDisplay', array('com_helloworld.helloworld', &$item, &$item->params, null));
		$item->beforeDisplayContent = trim(implode("\n", $results));

		$results = Factory::getApplication()->triggerEvent('onContentAfterDisplay', array('com_helloworld.helloworld', &$item, &$item->params, null));
		$item->afterDisplayContent = trim(implode("\n", $results));

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			Log::add(implode('<br />', $errors), Log::WARNING, 'jerror');

			return false;
		}
        
        // Take action based on whether the user has access to see the record or not
		$loggedIn = $user->get('guest') != 1;
		if (!$this->item->canAccess)
		{
			if ($loggedIn)
			{
				$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
				$app->setHeader('status', 403, true);
				return;
			}
			else
			{
				$return = base64_encode(Uri::getInstance());
				$login_url_with_return = Route::_('index.php?option=com_users&view=login&return=' . $return, false);
				$app->enqueueMessage(Text::_('COM_HELLOWORLD_MUST_LOGIN'), 'notice');
				$app->redirect($login_url_with_return, 403);
			}
		}
        
        $this->addMap();
        
        $tagsHelper = new TagsHelper;
		$this->item->tags = $tagsHelper->getItemTags('com_helloworld.helloworld' , $this->item->id);
        
        $model = $this->getModel();
		$this->parentItem = $model->getItem($this->item->parent_id);
		$this->children = $model->getChildren($this->item->id);
		// getChildren includes the record itself (as well as the children) so remove this record
		unset($this->children[0]);
        
        parent::display($template);
    }
    
    function addMap() 
	{
		// we need the Openlayers JS and CSS libraries
		//$this->document->addScript("https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.6.4/ol.js");
		//$this->document->addStyleSheet("https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.6.4/ol.css");

		// ... and our own JS and CSS
		//$this->document->addScript(Uri::root() . "media/com_helloworld/js/openstreetmap.js");
		//$this->document->addStyleSheet(Uri::root() . "media/com_helloworld/css/openstreetmap.css");
        $this->document->getWebAssetManager()->usePreset('com_helloworld.openstreetmap');


		// get the data to pass to our JS code
		$params = $this->get("mapParams");
		$this->document->addScriptOptions('params', $params);
	}

}