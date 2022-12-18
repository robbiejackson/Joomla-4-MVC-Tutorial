<?php

namespace Robbie\Component\Helloworld\Administrator\Rule;

use Joomla\CMS\Form\FormRule;


defined('_JEXEC') or die('Restricted access');
 
/**
 * Form Rule class for the Joomla Framework.
 */
class GreetingRule extends FormRule
{
	/**
	 * The regular expression.
	 */
	protected $regex = '^[^\*]+$';
}