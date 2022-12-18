<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Language\Multilanguage; 
use Joomla\CMS\Session\Session;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

defined('_JEXEC') or die('Restricted Access');

$lang = Factory::getLanguage()->getTag();
if (Multilanguage::isEnabled() && $lang)
{
    $query_lang = "&lang={$lang}";
}
else
{
    $query_lang = "";
}
?>
<h1><?php echo $this->item->greeting.(($this->item->category and $this->item->params->get('show_category'))
                                      ? (' ('.$this->item->category.')') : ''); ?>
</h1>
<?php
    echo $this->item->description;
    $tagLayout = new FileLayout('joomla.content.tags');
    echo $tagLayout->render($this->item->tags);
    $src = $this->item->imageDetails['image'];
    if ($src)
    {
        $html = '<figure>
                    <img src="%s" alt="%s" >
                    <figcaption>%s</figcaption>
                </figure>';
        $alt = $this->item->imageDetails['alt'];
        $caption = $this->item->imageDetails['caption'];
        echo sprintf($html, $src, $alt, $caption);
    } ?>

<?php if ($this->parentItem->id > 1) : ?>
	<h1><?php echo Text::_('COM_HELLOWORLD_PARENT') ?>
	</h1>
	<h3>
		<?php $url = Route::_('index.php?option=com_helloworld&view=helloworld&id=' . $this->parentItem->id . ':' . $this->parentItem->alias . '&catid=' . $this->parentItem->catid . $query_lang); ?>
		<a href="<?php echo $url; ?>"><?php echo $this->parentItem->greeting; ?></a>
	</h3>
<?php endif; ?>

<?php if ($this->children) : 
		$baseLevel = $this->item->level; ?>
		<h1><?php echo Text::_('COM_HELLOWORLD_CHILDREN') ?>
		</h1>
		<?php foreach ($this->children as $i => $child) : ?>
			<h3>
				<?php $prefix = LayoutHelper::render('joomla.html.treeprefix', array('level' => $child->level - $baseLevel)); ?>
				<?php echo $prefix; ?>
				<?php $url = Route::_('index.php?option=com_helloworld&view=helloworld&id=' . $child->id . ':' . $child->alias . '&catid=' . $child->catid . $query_lang); ?>
				<a href="<?php echo $url; ?>"><?php echo $child->greeting; ?></a>
			</h3>
	<?php endforeach; ?>
<?php endif; ?>

<?php
	echo "<h3>After display title:</h3>";
	echo $this->item->afterDisplayTitle;
	echo "<h3>Before display content:</h3>";
	echo $this->item->beforeDisplayContent;
	echo "<h3>After display content:</h3>";
	echo $this->item->afterDisplayContent;
	
    $fields = FieldsHelper::getFields('com_helloworld.helloworld', $this->item, true);
	echo "<h3>Fields set to not display automatically:</h3>";
	foreach ($fields as $field)
	{
		if ($field->params->get("display") == "0")
		{
			echo FieldsHelper::render($field->context, 'field.render', array('field' => $field)); 
			echo "<br>";
		}
	}
?>

<div id="map" class="map"></div>
<div class="map-callout map-callout-bottom" id="greeting-container"></div>
<div id="searchmap">
    <?php echo '<input id="token" type="hidden" name="' . Session::getFormToken() . '" value="1" />'; ?>
    <button type="button" class="btn btn-primary" onclick="searchHere();">
        <?php echo Text::_('COM_HELLOWORLD_SEARCH_HERE_BUTTON') ?>
    </button>
    <div id="searchresults">
    </div>
</div>