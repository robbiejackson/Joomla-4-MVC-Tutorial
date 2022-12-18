<?php

namespace Robbie\Component\Helloworld\Administrator\Table;

use Joomla\CMS\Table\Nested;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\Access\Rules;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\CMS\Tag\TaggableTableInterface;
use Joomla\CMS\Tag\TaggableTableTrait;

\defined('_JEXEC') or die;

/**
 * Helloworld Table class.
 *
 * @since  1.0
 */
class HelloworldTable extends Nested implements VersionableTableInterface, TaggableTableInterface
{
    use TaggableTableTrait;
    
     public function __construct(DatabaseDriver $db)
    {
        $this->typeAlias = 'com_helloworld.helloworld';

        parent::__construct('#__helloworld', 'id', $db);
        
        // In functions such as generateTitle() Joomla looks for the 'title' field ...
        $this->setColumnAlias('title', 'greeting');
    }
    
    public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params']))
		{
			// Convert the params field to a string.
			$parameter = new Registry;
			$parameter->loadArray($array['params']);
			$array['params'] = (string)$parameter;
		}
        
        if (isset($array['imageinfo']) && is_array($array['imageinfo']))
		{
			// Convert the imageinfo array to a string.
			$parameter = new Registry;
			$parameter->loadArray($array['imageinfo']);
			$array['image'] = (string)$parameter;
		}
        
        // Bind the rules.
        if (isset($array['rules']) && \is_array($array['rules'])) {
            $rules = new Rules($array['rules']);
            $this->setRules($rules);
        }
        
        if (isset($array['parent_id']))
		{
			if (!isset($array['id']) || $array['id'] == 0)
			{   // new record
				$this->setLocation($array['parent_id'], 'last-child');
			}
			elseif (isset($array['helloworldordering']))
			{
				// when saving a record load() is called before bind() so the table instance will have properties which are the existing field values
				if ($this->parent_id == $array['parent_id'])
				{
					// If first is chosen make the item the first child of the selected parent.
					if ($array['helloworldordering'] == -1)
					{
						$this->setLocation($array['parent_id'], 'first-child');
					}
					// If last is chosen make it the last child of the selected parent.
					elseif ($array['helloworldordering'] == -2)
					{
						$this->setLocation($array['parent_id'], 'last-child');
					}
					// Don't try to put an item after itself. All other ones put after the selected item.
					elseif ($array['helloworldordering'] && $this->id != $array['helloworldordering'])
					{
						$this->setLocation($array['helloworldordering'], 'after');
					}
					// Just leave it where it is if no change is made.
					elseif ($array['helloworldordering'] && $this->id == $array['helloworldordering'])
					{
						unset($array['helloworldordering']);
					}
				}
				// Set the new parent id if parent id not matched and put in last position
				else
				{
					$this->setLocation($array['parent_id'], 'last-child');
				}
			}
		}

		return parent::bind($array, $ignore);
	}
    
    public function store($updateNulls = true)
    {
        // add the 'created by' and 'created' date fields if it's a new record
        // and these fields aren't already set
        $date = date('Y-m-d h:i:s');
        $userid = Factory::getApplication()->getIdentity()->get('id');
        if (!$this->id) {
            // new record
            if (empty($this->created_by)) {
                $this->created_by = $userid;
                $this->created    = $date;
            }
        }

        return parent::store();
    }
    
    /**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return	string
	 * @since	2.5
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_helloworld.helloworld.'.(int) $this->$k;
	}
	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return	string
	 * @since	2.5
	 */
	protected function _getAssetTitle()
	{
		return $this->greeting;
	}
	/**
	 * Method to get the asset-parent-id of the item
	 *
	 * @return	int
	 */
	protected function _getAssetParentId(Table $table = NULL, $id = NULL)
	{
		// We will retrieve the parent-asset from the Asset-table
		$assetParent = Table::getInstance('Asset');
		// Default: if no asset-parent can be found we take the global asset
		$assetParentId = $assetParent->getRootId();

		// Find the parent-asset
		if (($this->catid)&& !empty($this->catid))
		{
			// The item has a category as asset-parent
			$assetParent->loadByName('com_helloworld.category.' . (int) $this->catid);
		}
		else
		{
			// The item has the component as asset-parent
			$assetParent->loadByName('com_helloworld');
		}

		// Return the found asset-parent-id
		if ($assetParent->id)
		{
			$assetParentId=$assetParent->id;
		}
		return $assetParentId;
	}
    
    public function check()
	{
		$this->alias = trim($this->alias);
		if (empty($this->alias))
		{
			$this->alias = $this->greeting;
		}
		$this->alias = OutputFilter::stringURLSafe($this->alias);
		return true;
	}
    
    public function delete($pk = null, $children = false)
	{
		return parent::delete($pk, $children);
	}
    
    /**
     * typeAlias is the key used to find the content_types record
     * needed for creating the history record
     */
    public function getTypeAlias()
    {
        return $this->typeAlias;
    }
}
