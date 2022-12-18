<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Robbie\Component\Helloworld\Site\Service;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Categories\CategoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

\defined('_JEXEC') or die;

class Router extends RouterView
{

    private $categoryFactory;

    private $categoryCache = [];

    private $db;

    /**
     * Component router constructor
     *
     * @param   SiteApplication           $app              The application object
     * @param   AbstractMenu              $menu             The menu object to work with
     * @param   CategoryFactoryInterface  $categoryFactory  The category object
     * @param   DatabaseInterface         $db               The database object
     */
    public function __construct(SiteApplication $app, AbstractMenu $menu, CategoryFactoryInterface $categoryFactory, DatabaseInterface $db)
    {
        $this->categoryFactory = $categoryFactory;
        $this->db              = $db;

        $category = new RouterViewConfiguration('category');
        $category->setKey('id')->setNestable();
        $this->registerView($category);
        
        $helloworld = new RouterViewConfiguration('helloworld');
        $helloworld->setKey('id')->setParent($category, 'catid');
        $this->registerView($helloworld);

        $form = new RouterViewConfiguration('form');
        $this->registerView($form);

        parent::__construct($app, $menu);

        $this->attachRule(new MenuRules($this));
        $this->attachRule(new StandardRules($this));
        $this->attachRule(new NomenuRules($this));
    }
    
    /**
     * Method to get the id for a helloworld item from the segment
     *
     * @param   string  $segment  Segment of the helloworld to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getHelloworldId($segment, $query)
    {
        return (int) $segment;
    }

    /**
     * Method to get the segment(s) for a helloworld item
     *
     * @param   string  $id     ID of the helloworld to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     */
    public function getHelloworldSegment($id, $query)
    {
        if (!strpos($id, ':')) {
            $id      = (int) $id;
            $dbquery = $this->db->getQuery(true);
            $dbquery->select($this->db->quoteName('alias'))
                ->from($this->db->quoteName('#__helloworld'))
                ->where($this->db->quoteName('id') . ' = :id')
                ->bind(':id', $id, ParameterType::INTEGER);
            $this->db->setQuery($dbquery);

            $id .= ':' . $this->db->loadResult();
        }

        return array((int) $id => $id);
    }

    /**
     * Method to get the id for a category
     *
     * @param   string  $segment  Segment to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getCategoryId($segment, $query)
    {
        if (isset($query['id'])) {
            $category = $this->getCategories(['access' => false])->get($query['id']);

            if ($category) {
                foreach ($category->getChildren() as $child) {
                    if ($child->id == (int) $segment) {
                        return $child->id;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Method to get the segment(s) for a category
     *
     * @param   string  $id     ID of the category to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     */
    public function getCategorySegment($id, $query)
    {
        $category = $this->getCategories(['access' => true])->get($id);

        if ($category) {
            $path = array_reverse($category->getPath(), true);
            $path[0] = '1:root';

            return $path;
        }

        return array();
    }

    /**
     * Method to get categories instance
     * The instance is stored in a cache to speed up subsequent invocations, keyed by the
     *   option of whether or not to take Access into consideration when analysing the categories
     *
     * @param   array  $options   The options for retrieving categories
     *
     * @return  CategoryInterface  The object containing categories
     *
     * @since   4.0.0
     */
    private function getCategories(array $options = []): CategoryInterface
    {
        $key = serialize($options);

        if (!isset($this->categoryCache[$key])) {
            $this->categoryCache[$key] = $this->categoryFactory->createCategory($options);
        }

        return $this->categoryCache[$key];
    }
}
