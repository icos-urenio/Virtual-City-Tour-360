<?php
/**
 * @version     2.0
 * @package     com_virtualcitytour360
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU Affero General Public License version 3 or later; see LICENSE.txt
 * @author      URENIO Research Unit
 */

// no direct access
defined('_JEXEC') or die;
?>
<div id="imc-wrapper" class="imc <?php echo $this->pageclass_sfx; ?>">			
	<?php if ($this->params->get('show_page_heading', 0)) : ?>			
	<h1 class="title">
		<?php if ($this->escape($this->params->get('page_heading'))) :?>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		<?php else : ?>
			<?php echo $this->escape($this->params->get('page_title')); ?>
		<?php endif; ?>				
	</h1>
	<?php endif; ?>				
	<div id="imc-header">
		<div id="imc-menu" class="issueslist">
			<!-- Filters -->
			<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
				<input type="hidden" name="status[0]" value="0" />
				<input type="hidden" name="cat[0]" value="0" />
				<input type="hidden" name="limitstart" value="" />
				<input type="hidden" name="limit" value="<?php echo  $this->state->get('list.limit');?>" />
				<input type="hidden" name="task" value="" />
				
				<!-- Mega Menu -->
				
				<ul id="mega-menu">
					<li id="drop-1"><a id="btn-1" href="javascript:void(0);" class="btn"><i class="icon-list-alt"></i> <?php echo JText::_('COM_VIRTUALCITYTOUR360_FILTER_SELECTION')?></a>
						<div class="megadrop dropdown_4columns">
							<div class="col_4">
								<h2><?php echo JText::_('COM_VIRTUALCITYTOUR360_CATEGORIES')?></h2>
							</div>
							
							<?php /*
							<?php foreach($this->arCat as $c){?>		
								<div class="col_2">
									<?php echo $c; ?>
								</div>					
							<?php }?>
							*/?>
							<?php echo $this->filters; 	?>

							<div class="col_4" style="text-align: center;">
								<button type="submit" class="btn btn-success" name="Submit" value="<?php echo JText::_('COM_VIRTUALCITYTOUR360_APPLY_FILTERS')?>"><i class="icon-ok icon-white"></i> <?php echo JText::_('COM_VIRTUALCITYTOUR360_APPLY_FILTERS')?></button>
							</div>
						</div>
					</li>
					<li id="drop-2"><a id="btn-2" href="javascript:void(0);" class="btn"><i class="icon-adjust"></i> <?php echo JText::_('COM_VIRTUALCITYTOUR360_VIEWS')?></a>
						<div class="megadrop dropdown_2columns">
							<div class="col_2">						
								<ul>
									<!-- dropdown menu links -->
									<li><a href="<?php echo Virtualcitytour360Helper::generateRouteLink('index.php?option=com_virtualcitytour360&view=virtualcitytour360');?>"><?php echo JText::_('COM_VIRTUALCITYTOUR360_VIEW1');?> <i class=" icon-ok"></i></a></li>
									<li><a href="<?php echo Virtualcitytour360Helper::generateRouteLink('index.php?option=com_virtualcitytour360&view=pois');?>"><?php echo JText::_('COM_VIRTUALCITYTOUR360_VIEW2');?></a></li>
								</ul>						
							</div>
						</div>
					</li>					
				</ul>
			</form>
	
			<!-- New Issue -->
			<div class="btn-group imc-right">
				<a class="btn btn-large btn-primary" href="<?php echo Virtualcitytour360Helper::generateRouteLink('index.php?option=com_virtualcitytour360&task=addPoi');?>"><i class="icon-plus icon-white"></i> <?php echo JText::_('REPORT_AN_ISSUE');?></a>
			</div>
				
		</div>
	</div>
	
	<div id="virtualcitytour360">
		<div id="searchPOI"><input type="text" id="searchTextMarker" name="searchTextMarker" class="inputbox" /><a id="searchMarker" href="javascript:void(0);"><?php echo JText::_('COM_VIRTUALCITYTOUR360_SEARCH');?></a></div>
		<div id="mapCanvas"><?php echo JText::_('COM_VIRTUALCITYTOUR360');?></div>
		
		<div id="wrapper-filters">
			<header>
				<h1 class="title">Φίλτρα</h1>
				<a class="filter-close" href="#"><img src="<?php echo JURI::base().'components/com_virtualcitytour360/images/close.png';?>" title="Κλείσιμο" alt="Κλείσιμο" width="18" height="18" /></a>
			</header>					
			<div id="content-filters">
				<?php echo $this->filters; 	?>
				<hr/>
				<div id="infobar">
					<ul>
						<?php
						$i = -1;
						foreach($this->items as $item){
							$i++;
							echo '<li><a href="javascript:markerclick(' . $i . ');">'.$item->title.'</a></li>';
						}
						?>
					</ul>
				</div>
			</div>
		</div>	
		
		<div id="wrapper-info">	
			<header>
				<h1 class="title">Πληροφορίες</h1>
				<a class="info-close" href="#"><img src="<?php echo JURI::base().'components/com_virtualcitytour360/images/close.png';?>" title="Κλείσιμο" alt="Κλείσιμο" width="18" height="18" /></a>
			</header>
			<div id="markerTitle" style="clear: both;"></div>
			<div id="content-info">
				<div id="panorama"></div>
				<div id="markerInfo"></div>		
			</div>
		</div>
	</div>

</div>