<?php

defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

$html   = array();
$html[] = '<fieldset>';
$html[] = '<label id="batch_setposition-lbl" for="batch_setposition">' .
			Text::_('COM_HELLOWORLD_BATCH_SETPOSITION_LABEL') . '</label>';
$html[] = '<select name="batch[position][setposition]">' .
			'<option value="keepPosition" selected>' . Text::_('COM_HELLOWORLD_BATCH_KEEP_POSITION') . '</OPTION>' .
			'<option value="changePosition">' . Text::_('COM_HELLOWORLD_BATCH_CHANGE_POSITION') . '</OPTION>' .
		  '</select>';
$html[] = '<label id="batch_latitude-lbl" for="batch_latitude">' . 
				Text::_('COM_HELLOWORLD_HELLOWORLD_FIELD_LATITUDE_LABEL') . '</label>';
$html[] = '<input id="batch_latitude" name="batch[position][latitude]" class="inputbox" type="number" step=any min=-90.0 max=90.0 placeholder="0.0">';
$html[] = '<label id="batch_longitude-lbl" for="batch_longitude">' . 
				Text::_('COM_HELLOWORLD_HELLOWORLD_FIELD_LONGITUDE_LABEL') . '</label>';
$html[] = '<input id="batch_longitude" name="batch[position][longitude]" class="inputbox" type="number" step=any min=-180.0 max=180.0 placeholder="0.0">';
$html[] = '</fieldset>';

echo implode('', $html);