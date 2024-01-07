<?php

namespace Robbie\Component\Helloworld\Administrator\Extension;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Association\AssociationServiceInterface;
use Joomla\CMS\Association\AssociationServiceTrait;
use Joomla\CMS\Categories\CategoryServiceInterface;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Psr\Container\ContainerInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Robbie\Component\Helloworld\Administrator\Service\HTML\AdministratorService;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Fields\FieldsServiceInterface;
use Joomla\Database\DatabaseAwareTrait;
    
class HelloworldComponent extends MVCComponent implements 
    CategoryServiceInterface, RouterServiceInterface, BootableExtensionInterface, AssociationServiceInterface, FieldsServiceInterface
{
	use CategoryServiceTrait;
    use RouterServiceTrait;
	use HTMLRegistryAwareTrait;
    use AssociationServiceTrait;
    use DatabaseAwareTrait;

	/**
	 * Booting the extension. This is the function to set up the environment of the extension like
	 * registering new class loaders, etc.
	 *
	 * We use this to register the helper file class which contains the html for displaying associations
	 */
	public function boot(ContainerInterface $container)
	{
		$this->getRegistry()->register('helloworldadministrator', new AdministratorService);
	}


	/**
	 * Returns the table name for the count items function for the given section of the category table
	 *
	 */
	protected function getTableNameForSection(string $section = null)
	{
		return 'helloworld';
	}
    
    /**
	 * Returns the name of the published state column in the table
     * for use by the count items function
	 *
	 */
    protected function getStateColumnForSection(string $section = null)
    {
        return 'published';
    }
    
    /**
	 * This is used by com_fields in the admin menu
     * It uses it to create the little Helloworld Items / Categories dropdown
	 *
	 */
    public function getContexts(): array
	{
		Factory::getApplication()->getLanguage()->load('com_helloworld', JPATH_ADMINISTRATOR);

		$contexts = array(
			'com_helloworld.helloworld' => Text::_('COM_HELLOWORLD_ITEMS'),
			'com_helloworld.categories' => Text::_('JCATEGORY')
		);

		return $contexts;
	}
	
    /**
	 * This is used by com_fields
     * It indicates to com_fields to use the 'helloworld' context 
     * eg when using the front end form (called 'form').
	 *
	 */
    public function validateSection($section, $item = null)
    {
        if (Factory::getApplication()->isClient('site') && $section == 'form')
        {
            return 'helloworld';
        }
        if ($section != 'helloworld' && $section != 'form')
        {
            return null;
        }

        return $section;
    }
}
