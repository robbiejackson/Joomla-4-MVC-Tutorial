<?php

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Categories\CategoryNode;

/**
 * Helloworld Component Helper file for generating the URL Routes
 *
 */
class HelloworldHelperRoute
{
    /**
	 * Helper function for generating the URL to a Helloworld page
	 * This is needed for the Tags functionality
	 */
	public static function getHelloworldRoute($id, $catid = 0, $language = 0)
	{
		// Create the link
		$link = 'index.php?option=com_helloworld&view=helloworld&id=' . $id;

		if ((int) $catid > 1)
		{
			$link .= '&catid=' . $catid;
		}

		if ($language && $language !== '*' && Multilanguage::isEnabled())
		{
			$link .= '&lang=' . $language;
		}

		return $link;
	}

	/**
	 * Helper function for generating the URL to a Helloworld Category page
	 * This is needed for the Tags functionality
	 */
	public static function getCategoryRoute($catid, $language = 0)
	{
		if ($catid instanceof CategoryNode)
		{
			$id = $catid->id;
		}
		else
		{
			$id = (int) $catid;
		}

		if ($id < 1)
		{
			$link = '';
		}
		else
		{
			$link = 'index.php?option=com_helloworld&view=category&id=' . $id;

			if ($language && $language !== '*' && Multilanguage::isEnabled())
			{
				$link .= '&lang=' . $language;
			}
		}

		return $link;
	}
}