<?php
/**
 * @version     1.0
 * @package     com_virtualcitytour360
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      URENIO Research Unit
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php foreach($this->items as $i => $item): ?>
	<tr class="row<?php echo $i % 2; ?>">
		<td>
			<?php echo $item->id; ?>
		</td>
		<td>
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>
		<td>
			<a href="<?php echo JRoute::_('index.php?option=com_virtualcitytour360&task=virtualcitytour360.edit&id=' . $item->id); ?>">
				<?php echo $item->title; ?>
			</a>
		</td>
		<td>
			<?php echo $item->category .($item->catid ? ' (' . $item->path . ')' : '');
			
			
			?>
			
		</td>
	</tr>
<?php endforeach; ?>

