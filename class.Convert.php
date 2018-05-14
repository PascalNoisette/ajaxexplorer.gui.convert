<?php
/*
 *
 * Copyright (C) 2012 Pascal Noisette
 *
 * This file is part of gui.convert an Ajaxplorer plugin
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Library General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor Boston, MA 02110-1301,  USA
 *
 */
defined('AJXP_EXEC') or die( 'Access not allowed');
require_once("./plugins/gui.convert/class.Converter.php");
class GuiConvertPlugin extends AJXP_Plugin{
	public function retriveTargetFormatAction($action, $httpVars, $fileVars){
		$xPath = new DOMXPath($this->manifestDoc);
		$items = $xPath->query("//convertions/" . $httpVars['mime'] . '/*');
		echo '<select id="mime" name="mime">';
		foreach ($items as $target) {
			echo ('<option value="'.$target->tagName.'">'.$target->tagName.'</option>');
		}
		die ('</select>');
	}
	public function convertAction($action, $httpVars, $fileVars){
		$xPath = new DOMXPath($this->manifestDoc);
		$target = $xPath->query("//convertions/" . $httpVars['from'] . '/' . $httpVars['to'])->item(0);
		if ($target) {
			$filename = $target->getAttribute("filename");
			
			require_once($filename);
			$classname = $target->getAttribute("classname");
						
			$adapter = new $classname;
			$adapter->sourceMime = $httpVars['from'];
			$adapter->targetMime = $httpVars['to'];

			try {
				$res = $adapter->convert($this->_getRealFileName($httpVars['file']));
				$message = $this->_translateThatFileSuccessFullyCreated($res);
				
			} catch (Exception $e) {
				$message = $e->getMessage();
			}
			$this->_sendAjaxResponse($message);
		}
	}
	protected function _getRealFileName($filepath) {
		$repo = ConfService::getRepository();
                $repo->detectStreamWrapper();
                $wrapperData = $repo->streamData;
                $urlBase = $wrapperData["protocol"]."://".$repo->getId();
		$realFile = call_user_func(array($wrapperData["classname"], "getRealFSReference"), rtrim($urlBase, '/').   $filepath);
		return $realFile;
	}
	protected function _sendAjaxResponse($message){
		AJXP_XMLWriter::header();
		AJXP_XMLWriter::sendMessage($message, null);
		AJXP_XMLWriter::triggerBgAction("reload_node", array(), "Triggering DL ", true, 2);
		AJXP_XMLWriter::close();
		session_write_close();
		exit();
	}

	protected function _translateThatFileSuccessFullyCreated($filename){
		$translationTable = ConfService::getMessages();

		$message = sprintf($translationTable["gui_convert.5"], $filename);
		
		return $message;
	}

}

?>
