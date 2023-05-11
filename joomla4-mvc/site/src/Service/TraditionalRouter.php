<?php

namespace Robbie\Component\Helloworld\Site\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Language\Associations;

class TraditionalRouter implements RouterInterface
{
    private $application;
    
    private $menu;
    
    private $categoryFactory;

    private $db;
    
    private $categories;
    
    public function __construct($application, $menu, CategoryFactoryInterface $categoryFactory, DatabaseInterface $db)
    {
        $this->application = $application;
        $this->menu = $menu;
        $this->categoryFactory = $categoryFactory;
        $this->db = $db;
    }

	public function build(&$query)
	{
		$segments = array();

		if (!Multilanguage::isEnabled() || !isset($query['view']))
		{
			return $segments;
		}

		$app  = Factory::getApplication();
        $lang = $app->getLanguage()->getTag();
        
		// get the menu item that this call to build() relates to
		if (!isset($query['Itemid']))
		{
			return $segments;
		}
		$sitemenu = $app->getMenu();
		$thisMenuitem = $sitemenu->getItem($query['Itemid']);

		if ($thisMenuitem->language != $lang)
		{   // from language switcher
            unset($query['view']);
			return $segments;
		}
        
		if ($thisMenuitem->note == "Ajax")
		{   
			// We're on the /message menuitem. 
			// Check we've got the right parameters then set url segment = id : alias
			if ($query['view'] == "helloworld" && isset($query['id']))
			{
				// we'll support the passed id being in the form id:alias
				$segments[] = $query['id'];

				unset($query['id']);
				unset($query['catid']);
			}
		}
		else
		{
			if (($query['view'] == "category") && isset($query['id']))
			{
				// if the menuitem matches the id then just remove the id
                if (array_key_exists('id', $query) && array_key_exists('id', $thisMenuitem->query) && $thisMenuitem->query['id'] == (int)$query['id'])
                {
                    unset($query['id']);
                    if (array_key_exists('catid', $query)) {
                        unset($query['catid']);
                    }
                }
                else
                {
                    // set this part of the url to be of the form /subcat1/subcat2/...
                    $pathSegments = $this->getCategorySegments($query['id']);
                    if ($pathSegments)
                    {
                        $segments = $pathSegments;
                        unset($query['id']);
                    }
                }
			}
			elseif ($query['view'] == "helloworld")
			{
				// if the menuitem matches the id then just remove the id
                if (array_key_exists('id', $query) && array_key_exists('id', $thisMenuitem->query) && $thisMenuitem->query['id'] == (int)$query['id'])
                {
                    unset($query['id']);
                    if (array_key_exists('catid', $query)) {
                        unset($query['catid']);
                    }
                }
                elseif (isset($query['catid']) && isset($query['id']))
                {
                    // set this part of the url to be of the form /subcat1/subcat2/.../hello-world 
                    $pathSegments = $this->getCategorySegments($query['catid']);
                    if ($pathSegments)
                    {
                        $segments = $pathSegments;
                    }

                    $segments[] = $query['id'];

                    unset($query['id']);
                    unset($query['catid']);
                }
			}
            elseif ($query['view'] == "form")
            {
                // this must be a menuitem with a link to the helloworld form - just remove the 'layout' query parameter
                if (array_key_exists('layout', $query)) {
                    unset($query['layout']);
                }
            }
		}

		unset($query['view']);
		return $segments;
	}
    
    /*
	 * This function gets an instance of the Categories object, which is needed for finding CategoryNode records
     * It replaces Categories::getInstance() by using the $categoryFactory which is passed into the constructor
     * It saves the Categories object for returning in further invocations
	 */
     
	private function getCategories()
	{
        if (!isset($this->categories)) 
        {
            $this->categories = $this->categoryFactory->createCategory();
        }
        return $this->categories;
    }
    
    /*
	 * This function take a category id and finds the path from that category to the root of the category tree
	 * The path returned from getPath() is an associative array of key = category id, value = id:alias
	 * If no valid category is found from the passed-in category id then null is returned. 
	 */
     
	private function getCategorySegments($catid)
	{
		$categories = $this->getCategories();
		$categoryNode = $categories->get($catid);
		if ($categoryNode)
		{
			$path = $categoryNode->getPath();

			return $path;
		}
		else
		{
			return null;
		}
	}
  
	public function parse(&$segments)
	{
		$vars = array();
		$nSegments = count($segments);
        
		$app  = Factory::getApplication();
		$sitemenu = $app->getMenu();
		$activeMenuitem = $sitemenu->getActive();
		if (!$activeMenuitem)
		{
			return $vars;
		}
        
		if ($activeMenuitem->note == "Ajax")
		{
			// Expect 1 segment of the form id:alias for the helloworld record
			if ($nSegments == 1)
			{
				$vars['id'] = $segments[0];
				$vars['view'] = 'helloworld';
			}
		}
		else
		{
			// Try to match the categories in the segments, starting at the root
			$categories = $this->getCategories();
			$matchingCategory = $categories->get('root');
            
			// Go through the category tree, try to get a match between each segment
			// and the id:alias of one of the children
			// The last segment may be a category id:alias or a helloworld record id:alias
			for ($i=0; $i < $nSegments; $i++)
			{
				$children = $matchingCategory->getChildren();
				$matchingCategory = $this->match($children, $segments[$i]);
				if ($matchingCategory)
				{
					$catid = $matchingCategory->id;
					if ($i == $nSegments - 1)    // we're done, all segments are categories
					{
						$vars['view'] = 'category';
						$vars['id'] = $catid;
					}
				}
				else
				{
					if ($i == $nSegments - 1)   // all but last segment are categories
					{
						$vars['id'] = $segments[$i];
						$vars['view'] = 'helloworld';
					}
					else   // something went wrong - didn't get a match at this level
					{
						break;
					}
				}
			}
		}
        
        for ($i=0; $i < $nSegments; $i++)
		{
            unset($segments[$i]);
        }

		return $vars;
	}
    
    /*
	 * This function takes an array of categoryNode elements and a url segment
	 * It goes through the categoryNodes looking for the one whose id:alias matches the passed-in segment
	 *   and returns the matching categoryNode, or null if not found
	 */
	private function match($categoryNodes, $segment)
	{
		foreach ($categoryNodes as $categoryNode)
		{
			if ($segment == $categoryNode->id . ':' . $categoryNode->alias)
			{
				return $categoryNode;
			}
		}
		return null;
	}
  
	public function preprocess($query)
	{
        $app  = Factory::getApplication();
        $sitemenu = $app->getMenu();

        if (!isset($query['Itemid']))
        {
            // No Itemid set, so try to find a helloworld menuitem which matches the query params
            // Firstly get all the helloworld menuitems, matching the language if set.
            // Note that if the lang is set in the query parameters then menuitems with language * are ignored
            // so this might need to be addressed in a genuine joomla extension
            if (Multilanguage::isEnabled() && isset($query['lang']))
            {
                $helloworldItems = $sitemenu->getItems(array('component','language'), array('com_helloworld',$query['lang']));
            } else
            {
                $helloworldItems = $sitemenu->getItems(array('component'), array('com_helloworld'));
            }
            foreach ($helloworldItems as $menuitem)
            {
                // look for a match with the view
                if (array_key_exists('view', $query) && array_key_exists('view', $menuitem->query) &&
                    ($menuitem->query['view'] == $query['view']))
                {
                    $query['Itemid'] = $menuitem->id;
                    // if there's an exact match with the id as well, then take that menuitem by preference
                    if (array_key_exists('id', $query) && array_key_exists('id', $menuitem->query) && ($menuitem->query['id'] == (int)$query['id']))
                    {
                        break;
                    }
                }
            }
        }
        return $query;
	}
}