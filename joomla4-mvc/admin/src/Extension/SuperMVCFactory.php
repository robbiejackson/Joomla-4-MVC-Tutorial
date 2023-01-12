<?php

namespace Robbie\Component\Helloworld\Administrator\Extension;

use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\CMS\Categories\CategoryServiceTrait;

class SuperMVCFactory extends MVCFactory
{
    use CategoryServiceTrait;
}