<?php
/**
 * @version     1.0
 * @package     com_virtualcitytour360
 * @copyright   Copyright (C) 2011 - 2012 URENIO Research Unit. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      URENIO Research Unit
 */

// No direct access
defined('_JEXEC') or die;

class Virtualcitytour360Controller extends JController
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			$cachable	If true, the view output will be cached
	 * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		//require_once JPATH_COMPONENT.'/helpers/virtualcitytour360.php';

		// Load the submenu.
		Virtualcitytour360Helper::addSubmenu(JRequest::getCmd('view', 'items'));

		$view = JRequest::getCmd('view', 'items');
        JRequest::setVar('view', $view);

		parent::display();

		return $this;
	}
	
	//to be called by javascript async only as format=raw
	function deleteImage()
	{
		jimport('joomla.filesystem.file');
		

		//always use constants when making file paths, to avoid the possibilty of remote file inclusion
		$file = JPATH_SITE.DS.'images'.DS.'virtualcitytour360'.DS.JRequest::getInt('id').DS.'images'.DS.JRequest::getVar('filename');
		$thumbfile = JPATH_SITE.DS.'images'.DS.'virtualcitytour360'.DS.JRequest::getInt('id').DS.'images'.DS.'thumbs'.DS.JRequest::getVar('filename');
		
		if(!JFile::exists($file)){
			echo JText::_( 'CANNOT FIND FILE' );
			return;		
		}
		
		if(JFile::exists($thumbfile)){
			JFile::delete($thumbfile);
		}
		
		//$innerHTML = '';
		if(JFile::delete($file)){
			echo Virtualcitytour360Helper::getImages(JRequest::getVar('id'));			
		}
		else{
			echo JText::_( 'ERROR DELETING FILE' );
			return;				
		}

	}
	
	//to be called by javascript async only as format=raw
	function deletePanorama()
	{
		jimport('joomla.filesystem.file');
		

		//always use constants when making file paths, to avoid the possibilty of remote file inclusion
		$file = JPATH_SITE.DS.'images'.DS.'virtualcitytour360'.DS.JRequest::getInt('id').DS.'panoramas'.DS.JRequest::getVar('filename');
		$thumbfile = JPATH_SITE.DS.'images'.DS.'virtualcitytour360'.DS.JRequest::getInt('id').DS.'panoramas'.DS.'thumbs'.DS.JRequest::getVar('filename');
		
		if(!JFile::exists($file)){
			echo JText::_( 'CANNOT FIND FILE' );
			return;		
		}
		
		if(JFile::exists($thumbfile)){
			JFile::delete($thumbfile);
		}
		
		//$innerHTML = '';
		if(JFile::delete($file)){
			echo Virtualcitytour360Helper::getPanoramas(JRequest::getVar('id'));			
		}
		else{
			echo JText::_( 'ERROR DELETING FILE' );
			return;				
		}

	}	
	
	//to be called by javascript async only as format=raw
	function uploadImage()
	{
		//import joomlas filesystem functions, we will do all the filewriting with joomlas functions,
		//so if the ftp layer is on, joomla will write with that, not the apache user, which might
		//not have the correct permissions

		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		 
		//this is the name of the field in the html form, filedata is the default name for swfupload
		//so we will leave it as that
		$fieldName = 'Filedata';
		 
		//any errors the server registered on uploading
		$fileError = $_FILES[$fieldName]['error'];

		if ($fileError > 0) 
		{
				switch ($fileError) 
			{
				case 1:
				echo JText::_( 'FILE TO LARGE THAN PHP INI ALLOWS' );
				return;
		 
				case 2:
				echo JText::_( 'FILE TO LARGE THAN HTML FORM ALLOWS' );
				return;
		 
				case 3:
				echo JText::_( 'ERROR PARTIAL UPLOAD' );
				return;
		 
				case 4:
				echo JText::_( 'ERROR NO FILE' );
				return;
				}
		}
		 
		//check for filesize
		$fileSize = $_FILES[$fieldName]['size'];
		if($fileSize > 4000000)
		{
			echo JText::_( 'FILE BIGGER THAN 4MB' );
		}
		 
		//check the file extension is ok
		$fileName = $_FILES[$fieldName]['name'];
		$uploadedFileNameParts = explode('.',$fileName);
		$uploadedFileExtension = array_pop($uploadedFileNameParts);
		 
		$validFileExts = explode(',', 'jpeg,jpg,png,gif');
		 
		//assume the extension is false until we know its ok
		$extOk = false;
		 
		//go through every ok extension, if the ok extension matches the file extension (case insensitive)
		//then the file extension is ok
		foreach($validFileExts as $key => $value)
		{
			if( preg_match("/$value/i", $uploadedFileExtension ) )
			{
				$extOk = true;
			}
		}
		 
		if ($extOk == false) 
		{
			echo JText::_( 'INVALID EXTENSION' );
				return;
		}
		 
		//the name of the file in PHP's temp directory that we are going to move to our folder
		$fileTemp = $_FILES[$fieldName]['tmp_name'];
		 
		//for security purposes, we will also do a getimagesize on the temp file (before we have moved it 
		//to the folder) to check the MIME type of the file, and whether it has a width and height
		$imageinfo = getimagesize($fileTemp);
		 
		//we are going to define what file extensions/MIMEs are ok, and only let these ones in (whitelisting), rather than try to scan for bad
		//types, where we might miss one (whitelisting is always better than blacklisting) 
		$okMIMETypes = 'image/jpeg,image/pjpeg,image/png,image/x-png,image/gif';
		$validFileTypes = explode(",", $okMIMETypes);		
		 
		//if the temp file does not have a width or a height, or it has a non ok MIME, return
		if( !is_int($imageinfo[0]) || !is_int($imageinfo[1]) ||  !in_array($imageinfo['mime'], $validFileTypes) )
		{
			echo JText::_( 'INVALID FILETYPE' );
			return;
		}
		 
		//lose any special characters in the filename
		///$fileName = ereg_replace("[^A-Za-z0-9.]", "-", $fileName);
		
		$type = JRequest::getVar('uploadtype');
		switch($type){
			case "image" :
				//RESIZE IMAGE HERE 
				include_once(JPATH_COMPONENT.'/helpers/simpleimage.php');
				
				$image = new SimpleImage();
				$image->load($fileTemp);
				
				$width = $image->getWidth();
				$height = $image->getHeight();
				$new_height = $height;
				$new_width = $width;
				
				$target_width = 1024;	//TODO: GET FROM PARAMETERS
				$target_height = 768;	//TODO: GET FROM PARAMETERS

				$target_ratio = $target_width / $target_height;
				$img_ratio = $width / $height;
				
				if ($target_ratio > $img_ratio) {
					$new_height = $target_height;
					$new_width = $img_ratio * $target_height;
				} else {
					$new_height = $target_width / $img_ratio;
					$new_width = $target_width;
				}

				if ($new_height > $target_height) {
					$new_height = $target_height;
				}
				if ($new_width > $target_width) {
					$new_height = $target_width;
				}		
				
				if($width > $target_width && $height > $target_height){
					if($new_height != $height || $new_width != $width){
						$image->resize($new_width,$new_height);
						$image->save($fileTemp);		
					}
				}
				 
				//always use constants when making file paths, to avoid the possibilty of remote file inclusion
				$uploadPath = JPATH_SITE.DS.'images'.DS.'virtualcitytour360'.DS.JRequest::getInt('id').DS.'images'.DS.$fileName;
				if(!JFile::upload($fileTemp, $uploadPath)) 
				{
					echo JText::_( 'ERROR MOVING FILE' );
						return;
				}
				else
				{
					// success, exit with code 0 for Mac users, otherwise they receive an IO Error
					JPath::setPermissions($uploadPath);
					//CREATE THUMBNAIL HERE
					$image->load($uploadPath);
					$image->resize(100,80);
					$pathToThumb = JPATH_SITE.DS.'images'.DS.'virtualcitytour360'.DS.JRequest::getInt('id').DS.'images'.DS.'thumbs';
					if (!JFolder::exists($pathToThumb)){
						JFolder::create($pathToThumb);
					}
						
					$thumb = $pathToThumb.DS.$fileName;
					$image->save($thumb);

					exit(0);
				}					
				
			break;
			case "panorama" :
				//RESIZE IMAGE HERE 
				include_once(JPATH_COMPONENT.'/helpers/simpleimage.php');
				
				$image = new SimpleImage();
				
				// -- for panoramas will also need the original image
				
				$pathToOriginal = JPATH_SITE.DS.'images'.DS.'virtualcitytour360'.DS.JRequest::getInt('id').DS.'panoramas'.DS.'original';
				if (!JFolder::exists($pathToOriginal)){
					JFolder::create($pathToOriginal);
				}
				$original = $pathToOriginal.DS.$fileName;

				if(!JFile::upload($fileTemp, $original)) 
				{
					echo JText::_( 'ERROR MOVING FILE' );
						return;
				}
								
				// -- end saving original

				
				$image->load($original);
				$width = $image->getWidth();
				$height = $image->getHeight();
				$new_height = $height;
				$new_width = $width;
				
				$target_width = 1024;	//TODO: GET FROM PARAMETERS
				$target_height = 768;	//TODO: GET FROM PARAMETERS

				$target_ratio = $target_width / $target_height;
				$img_ratio = $width / $height;
				
				if ($target_ratio > $img_ratio) {
					$new_height = $target_height;
					$new_width = $img_ratio * $target_height;
				} else {
					$new_height = $target_width / $img_ratio;
					$new_width = $target_width;
				}

				if ($new_height > $target_height) {
					$new_height = $target_height;
				}
				if ($new_width > $target_width) {
					$new_height = $target_width;
				}		

				//always use constants when making file paths, to avoid the possibilty of remote file inclusion
				$uploadPath = JPATH_SITE.DS.'images'.DS.'virtualcitytour360'.DS.JRequest::getInt('id').DS.'panoramas'.DS.$fileName;
				
				if($new_height != $height || $new_width != $width){
					$image->resize($new_width,$new_height);
					$image->save($uploadPath);		
				}
				 

				JPath::setPermissions($uploadPath);
				//CREATE THUMBNAIL HERE
				$image->load($uploadPath);
				$image->resize(100,80);
				$pathToThumb = JPATH_SITE.DS.'images'.DS.'virtualcitytour360'.DS.JRequest::getInt('id').DS.'panoramas'.DS.'thumbs';
				if (!JFolder::exists($pathToThumb)){
					JFolder::create($pathToThumb);
				}
					
				$thumb = $pathToThumb.DS.$fileName;
				$image->save($thumb);

				exit(0);
								
							
			break;
		}
	
	}
	
	//to be called by javascript async only as format=raw
	function prepareImages(){

		echo Virtualcitytour360Helper::getImages(JRequest::getVar('id'));
		return ;
	}
	
	//to be called by javascript async only as format=raw
	function preparePanoramas(){
		echo Virtualcitytour360Helper::getPanoramas(JRequest::getVar('id'));
		return ;
	}
	
}
