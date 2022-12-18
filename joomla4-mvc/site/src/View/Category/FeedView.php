<?php
/**
 * view file associated with the Syndicated Feed for a helloworld category
 */

namespace Robbie\Component\Helloworld\Site\View\Category;

defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\CategoryFeedView;
use Joomla\Registry\Registry;

class FeedView extends CategoryFeedView
{
	// required so that the parent class can find the helloworld content-type record containing the field mapping details
	protected $viewName = 'helloworld';

	/**
	 * Function overriding the parent reconcileNames() method. 
	 * We use this to insert an html link to the helloworld image into the description
	 * 
	 * The input parameter is the helloworld item as extracted from the database, passed by reference
	 *
	 * The result of the method is that the helloworld item passed as a parameter gets its description property changed
	 */
	protected function reconcileNames($item)
	{ 
		$description = '';
		
		if (!empty($item->image))
		{
			// Convert the JSON-encoded image info into an array
			$imageDetails = new Registry;
			$imageDetails->loadString($item->image, 'JSON');
			$src = $imageDetails->get('image','');
			if (!empty($src))
			{
				$description .= '<p><img src="' . $src . '" /></p>';
			}
		}
		$item->description =  $description . $item->description;
	}
}