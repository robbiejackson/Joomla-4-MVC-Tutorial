<?php
/**
 * Layout file for the main body component of the modal showing the batch options
 * This layout displays the various html input elements relating to the batch processes
 */
defined('_JEXEC') or die;
use Joomla\CMS\Layout\LayoutHelper;

$published = $this->state->get('filter.published');
?>

<div class="p-3">

	<div class="row">

		<div class="form-group col-md-6">
			<div class="controls">
				<?php echo LayoutHelper::render('joomla.html.batch.item', array('extension' => 'com_helloworld')); ?>
			</div>
        </div>
        <div class="form-group col-md-6">
			<div class="controls">
				<?php echo LayoutHelper::render('helloworld.position', array()); ?>
			</div>
		</div>
        
    </div>
    
    <div class="row">

		<div class="form-group col-md-6">
			<div class="controls">
				<?php echo LayoutHelper::render('joomla.html.batch.language', array()); ?>
			</div>
        </div>
        <div class="form-group col-md-6">
			<div class="controls">
				<?php echo LayoutHelper::render('joomla.html.batch.access', array()); ?>
			</div>
        </div>
        <div class="form-group col-md-6">
			<div class="controls">
				<?php echo LayoutHelper::render('joomla.html.batch.tag', array()); ?>
			</div>
		</div>

	</div>
	
</div>