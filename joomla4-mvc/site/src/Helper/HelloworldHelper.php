<?php

namespace Robbie\Component\Helloworld\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Categories\CategoryNode;

/**
 * Helloworld Component Helper file for generating the URL Routes
 *
 */
class HelloworldHelper
{
	/**
	 * When the Helloworld message is displayed then there is also shown a map with a Search Here button.
	 * This function generates the URL which the Ajax call will use to perform the search. 
	 * 
	 */
	public static function getAjaxURL()
	{
		if (!Multilanguage::isEnabled())
		{
			return null;
		}
        
		$app  = Factory::getApplication();
        $lang = $app->getLanguage()->getTag();
		$sitemenu= $app->getMenu();
		$thismenuitem = $sitemenu->getActive();

		// if we haven't got an active menuitem, or we're currently on a menuitem 
		// with view=category or note = "Ajax", then just stay on it
		if (!$thismenuitem || strpos($thismenuitem->link, "view=category") !== false || $thismenuitem->note == "Ajax")
		{
			return null;
		}

		// look for a menuitem with the right language, and a note field of "Ajax"
		$menuitem = $sitemenu->getItems(array('language','note'), array($lang, "Ajax"));
		if ($menuitem)
		{
			$itemid = $menuitem[0]->id; 
			$url = Route::_("index.php?Itemid=$itemid&view=helloworld&format=json");
			return $url;
		}
		else
		{
			return null;
		}
	}
    
}