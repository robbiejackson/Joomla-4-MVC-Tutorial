<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * This layout file is for displaying the front end form for capturing a new helloworld message
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

$this->document->getWebAssetManager()->useScript('com_helloworld.validate-greeting');

$fieldsets = $this->form->getFieldsets('com_fields');

?>
<form action="<?php echo Route::_('index.php?option=com_helloworld&view=form&layout=edit'); ?>"
    method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

	<div class="form-horizontal">
		<fieldset class="adminform">
			<legend><?php echo Text::_('COM_HELLOWORLD_LEGEND_DETAILS') ?></legend>
			<div class="row-fluid">
				<div class="span6">
					<?php echo $this->form->renderFieldset('details');  ?>
				</div>
			</div>
		</fieldset>
	</div>
    <?php 
		foreach($fieldsets as $fieldset) 
		{
			echo $this->form->renderFieldset($fieldset->name);
		}
		?>
	<div class="btn-toolbar">
		<div class="btn-group">
			<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('helloworld.save')">
				<span class="icon-ok"></span><?php echo Text::_('JSAVE') ?>
			</button>
		</div>
		<div class="btn-group">
			<button type="button" class="btn" onclick="Joomla.submitbutton('helloworld.cancel')">
				<span class="icon-cancel"></span><?php echo Text::_('JCANCEL') ?>
			</button>
		</div>
	</div>

	<input type="hidden" name="task" />
    <input type="hidden" name="modelname" value="form"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>