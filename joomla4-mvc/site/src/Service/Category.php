<?php

namespace Robbie\Component\Helloworld\Site\Service;

use Joomla\CMS\Categories\Categories;

\defined('_JEXEC') or die;

class Category extends Categories
{

    public function __construct($options = array())
    {
        $options['table']     = '#__helloworld';
        $options['extension'] = 'com_helloworld';

        parent::__construct($options);
    }
}
