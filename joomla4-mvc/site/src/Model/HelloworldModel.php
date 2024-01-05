<?php

namespace Robbie\Component\Helloworld\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\Registry\Registry;
use Robbie\Component\Helloworld\Site\Helper\HelloworldHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;

/**
 * Hello World Message Model
 * @since 0.0.5
 */
class HelloworldModel extends ItemModel {

    /**
	 * @var object item
	 */
	protected $item;

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	2.5
	 */
	protected function populateState()
	{
		// Get the message id
		$jinput = Factory::getApplication()->input;
		$id     = $jinput->get('id', 1, 'INT');
		$this->setState('message.id', $id);

		// Load the parameters.
		$this->setState('params', Factory::getApplication()->getParams());
		parent::populateState();
	}
    
    /**
	 * Get the message
	 * @return object The message to be displayed to the user
	 */
	public function getItem($pk = NULL)
	{
		if (!isset($this->item) || !is_null($pk)) 
		{
			$id    = $pk ?: $this->getState('message.id');
			$db    = $this->getDatabase();
			$query = $db->getQuery(true);
			$query->select('h.greeting, h.params, h.image as image, c.title as category, c.access as catAccess, 
                        h.latitude as latitude, h.longitude as longitude, h.access as access,
						h.id as id, h.alias as alias, h.catid as catid, h.parent_id as parent_id, h.level as level, h.description as description')
				  ->from('#__helloworld as h')
				  ->leftJoin('#__categories as c ON h.catid=c.id')
				  ->where('h.id=' . (int)$id);
			
            if (Multilanguage::isEnabled())
			{
				$lang = Factory::getLanguage()->getTag();
				$query->where('h.language IN ("*","' . $lang . '")');
			}
            
            
            $db->setQuery((string)$query);
		
			if ($this->item = $db->loadObject()) 
			{
				// Load the JSON string
				$params = new Registry;
				$params->loadString($this->item->params, 'JSON');
				$this->item->params = $params;

				// Merge global params with item params
				$params = clone $this->getState('params');
				$params->merge($this->item->params);
				$this->item->params = $params;
                
                // Convert the JSON-encoded image info into an array
				$image = new Registry;
				$image->loadString($this->item->image, 'JSON');
				$this->item->imageDetails = $image;
                
                // Check if the user can access this record (and category)
				$user = Factory::getApplication()->getIdentity();
				$userAccessLevels = $user->getAuthorisedViewLevels();
				if ($user->authorise('core.admin')) // ie superuser
				{
					$this->item->canAccess = true;
				}
				else
				{
					if ($this->item->catid == 0)
					{
						$this->item->canAccess = in_array($this->item->access, $userAccessLevels);
					}
					else
					{
						$this->item->canAccess = in_array($this->item->access, $userAccessLevels) && in_array($this->item->catAccess, $userAccessLevels);
					}
				}
			}
		}
		return $this->item;
	}
    
    public function getMapParams()
	{
		if ($this->item) 
		{
            $url = HelloworldHelper::getAjaxURL();
			$this->mapParams = array(
				'latitude' => $this->item->latitude,
				'longitude' => $this->item->longitude,
				'zoom' => 10,
				'greeting' => $this->item->greeting,
				'ajaxurl' => $url
			);
			return $this->mapParams; 
		}
		else
		{
			throw new \Exception('No helloworld details available for map', 500);
		}
	}
    
    public function getMapSearchResults($mapbounds)
	{
        $app = Factory::getApplication();
		if ($app->get('caching') >= 1)
		{
			// Build a cache ID based on the conditions for the SQL where clause
			$groups = implode(',', $app->getIdentity()->getAuthorisedViewLevels());
			$cacheId = $groups . '.' . $mapbounds['minlat'] . '.' . $mapbounds['maxlat'] . '.' . 
										$mapbounds['minlng'] . '.' . $mapbounds['maxlng'];
			if (Multilanguage::isEnabled())
			{
				$lang = $app->getLanguage()->getTag();
				$cacheId .= $lang;
			}
			$cache = Factory::getCache('com_helloworld', 'callback');
            $cacheFactory = Factory::getContainer()->get(CacheControllerFactoryInterface::class);
            $cache = $cacheFactory->createCacheController('callback');
			$results = $cache->get(array($this, '_getMapSearchResults'), array($mapbounds), md5($cacheId), false);
			return $results;
		}
		else
		{
			return $this->_getMapSearchResults($mapbounds);
		}
	}
    
    public function _getMapSearchResults($mapbounds)
	{
		$app = Factory::getApplication();
        
        try 
		{
			$db    = $this->getDatabase();
			$query = $db->getQuery(true);
			$query->select('h.id, h.alias, h.catid, h.greeting, h.latitude, h.longitude, h.access')
			   ->from('#__helloworld as h')
			   ->where('h.latitude > ' . $mapbounds['minlat'] . 
				' AND h.latitude < ' . $mapbounds['maxlat'] .
				' AND h.longitude > ' . $mapbounds['minlng'] .
				' AND h.longitude < ' . $mapbounds['maxlng']);
			
            if (Multilanguage::isEnabled())
			{
				$lang = $app->getLanguage()->getTag();
				$query->where('h.language IN ("*","' . $lang . '")');
			}
            
            $user = Factory::getApplication()->getIdentity();
			$loggedIn = $user->get('guest') != 1;
			if ($loggedIn && !$user->authorise('core.admin'))
			{
				$userAccessLevels = $user->getAuthorisedViewLevels();
				$query->where('h.access IN (' . implode(",", $userAccessLevels) . ')');
				$query->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON c.id = h.catid');
				$query->where('(c.access IN (' . implode(",", $userAccessLevels) . ') OR h.catid = 0)');
			}
            
            $db->setQuery($query);
			$results = $db->loadObjectList(); 
		}
		catch (\Exception $e)
		{
			$msg = $e->getMessage();
			$app->enqueueMessage($msg, 'error'); 
			$results = null;
		}
        
        if (Multilanguage::isEnabled())
		{
			$query_lang = "&lang={$lang}";
		}
		else
		{
			$query_lang = "";
		}
        
        // add on the itemid associated where the Note field is "Ajax" - for the legacy router on joomla 4
        $sitemenu = $app->getMenu();
        
        if (Multilanguage::isEnabled())
		{
			$mainmenuItems = $sitemenu->getItems(array('note','language'), array('Ajax',$app->getLanguage()->getTag()));
		}
		else
		{
			$mainmenuItems = $sitemenu->getItems(array('note'), array('Ajax'));
		}
        if (count($mainmenuItems) == 1)
        {
            $query_Itemid = '&Itemid=' . $mainmenuItems[0]->id;
        }
        else
        {
            $query_Itemid = '';
        }
            
        for ($i = 0; $i < count($results); $i++) 
		{
			$results[$i]->url = Route::_('index.php?option=com_helloworld&view=helloworld&id=' . $results[$i]->id . 
				":" . $results[$i]->alias . '&catid=' . $results[$i]->catid . $query_lang . $query_Itemid);
		}

		return $results; 
	}
    
    public function getChildren($id)
	{
		$table = $this->getTable();
		$children = $table->getTree($id);
		return $children;
	}
}