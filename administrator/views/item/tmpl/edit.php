<?php
/**
 * @version     1.0
 * @package     com_virtualcitytour360
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      URENIO Research Unit
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.modal');
$params = $this->form->getFieldsets('params');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'item.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_virtualcitytour360&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">
	<div class="width-40 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_VIRTUALCITYTOUR360_VIRTUALCITYTOUR360_DETAILS' ); ?></legend>
			<?php echo JHtml::_('sliders.start', 'virtualcitytour360-slider1'); ?>
			<?php echo JHtml::_('sliders.panel', JText::_('COM_VIRTUALCITYTOUR360_VIRTUALCITYTOUR360_MAIN_FIELDS'), 'main');?>
			<div style="margin: 10px 20px;">
				<ul class="adminformlist">
					<?php foreach($this->form->getFieldset('details') as $field): ?>
						
						<li>
						<?php 
						echo $field->label;
						
						if ($field->type == 'Editor'){
							echo '<div style="float:left;">'.$field->input . '</div>';
						}else{
							echo $field->input;
						}
						?>
						</li>
					<?php endforeach; ?>            
				</ul>
			</div>
		</fieldset>

			<?php echo JHtml::_('sliders.panel', JText::_('COM_VIRTUALCITYTOUR360_VIRTUALCITYTOUR360_PHOTOS'), 'photo');?>
			<div style="margin: 10px 20px;">
				<?php 
				if((int)JRequest::getVar('id') == 0){
					echo '<div style="color: red;font-weight: bold;">Please Save item first to add images</div>';
				}else{
				?>
					<div style="display: block; border: solid 1px #7FAAFF; background-color: #C5D9FF; padding: 2px; height: 18px;">
						<span id="spanButtonPlaceholder"></span>
					</div>
					<input id="btnCancel" type="button" value="Ακύρωση όλων" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 0px; font-size: 8pt; height: 20px;" />
					<div class="fieldset flash" id="fsUploadProgress">
					<br />
					</div>
					<div id="divFileProgressContainer"></div>
					
					<div id="divStatus"></div>
					
					<div id="thumbnails" style="height: auto;">
						<?php 
							echo $this->displayImages;
						?>
					</div>
				<?php }?>
				
			</div>

			<?php echo JHtml::_('sliders.panel', JText::_('COM_VIRTUALCITYTOUR360_VIRTUALCITYTOUR360_PANORAMAS'), 'panorama');?>
			<div style="margin: 10px 20px;">
				<?php 
				if((int)JRequest::getVar('id') == 0){
					echo '<div style="color: red;font-weight: bold;">'.JText::_('COM_VIRTUALCITYTOUR360_VIRTUALCITYTOUR360_PANORAMAS_SAVE_FIRST').'</div>';
				}else{
				?>			
				
					<div style="display: block; border: solid 1px #7FAAFF; background-color: #C5D9FF; padding: 2px; height: 18px;">
						<span id="spanButtonPlaceholder2"></span>
					</div>
					<input id="btnCancel2" type="button" value="Ακύρωση όλων" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 0px; font-size: 8pt; height: 20px;" />
					<div class="fieldset flash" id="fsUploadProgress2">
					<br />
					</div>
					<div id="divFileProgressContainer2"></div>
					
					<div id="divStatus2"></div>
					
					<div id="thumbnails2" style="height: auto;">
						<?php 
							echo $this->displayPanoramas;
						?>
					</div>
				<?php }?>
				
			</div>
			
			
			<?php echo JHtml::_('sliders.end'); ?>

	</div>
	
	<div class="width-60 fltrt">
		<?php echo JHtml::_('sliders.start', 'virtualcitytour360-slider2'); ?>
		<?php echo JHtml::_('sliders.panel', JText::_('COM_VIRTUALCITYTOUR360_VIRTUALCITYTOUR360_MAP'), 'map');?>
			<div style="width: 100%;height: 400px;" id="mapCanvas"><?php echo JText::_('COM_VIRTUALCITYTOUR360_VIRTUALCITYTOUR360_MAP');?></div>				
			<div id="infoPanel" style="margin: 15px;">
			<b><?php echo JText::_('COM_VIRTUALCITYTOUR360_VIRTUALCITYTOUR360_GEOLOCATION');?></b>
			<div id="info"></div>
			<b><?php echo JText::_('COM_VIRTUALCITYTOUR360_VIRTUALCITYTOUR360_CLOSEST_ADDRESS');?></b>
			<div id="near_address"></div>
			<div id="geolocation">
				<input id="address" type="textbox" size="75" value="Θέρμη">
				<input type="button" value="<?php echo JText::_('COM_VIRTUALCITYTOUR360_VIRTUALCITYTOUR360_LOCATE');?>" onclick="codeAddress()">
			</div>	
			</div>	
				
		<?php foreach ($params as $name => $fieldset): ?>
				<?php echo JHtml::_('sliders.panel', JText::_($fieldset->label), $name.'-params');?>
			<?php if (isset($fieldset->description) && trim($fieldset->description)): ?>
				<p class="tip"><?php echo $this->escape(JText::_($fieldset->description));?></p>
			<?php endif;?>
				<fieldset class="panelform" >
					<ul class="adminformlist">
			<?php foreach ($this->form->getFieldset($name) as $field) : ?>
						<li><?php echo $field->label; ?><?php echo $field->input; ?></li>
			<?php endforeach; ?>
					</ul>
				</fieldset>
		<?php endforeach; ?>

	
		<?php echo JHtml::_('sliders.end'); ?>
	</div>


	<div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	<div class="clr"></div>
</form>