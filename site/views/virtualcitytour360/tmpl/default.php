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
?>
<div id="system">	
	<div id="wrapper-virtualcitytour360">
		<article class="virtualcitytour360 <?php echo $this->pageclass_sfx; ?>">
			<header>
				<?php if ($this->params->get('show_page_heading', 1)) : ?>			
				<h1 class="title">
					<?php if ($this->escape($this->params->get('page_heading'))) :?>
						<?php echo $this->escape($this->params->get('page_heading')); ?>
					<?php else : ?>
						<?php echo $this->escape($this->params->get('page_title')); ?>
					<?php endif; ?>				
				</h1>
				<?php endif; ?>
				<a class="filter-open" href="#"><?php echo JText::_('COM_VIRTUALCITYTOUR360_FILTERS');?></a><br />
				<div id="toggleMap"><a id="toggleMapSize" href="#"><span id="map-size"><?php echo JText::_('COM_VIRTUALCITYTOUR360_BIG_MAP');?></span></a></div>
				<div id="loading"><img src="<?php echo JURI::base().'components/com_virtualcitytour360/images/ajax-loader.gif';?>" /></div>
			</header>
			
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

		</article>
	</div>
</div>