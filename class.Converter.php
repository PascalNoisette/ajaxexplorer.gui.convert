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

abstract class GuiAdapterConverter {
	abstract protected function _convert($from, $to);
	public $sourceMime;
	public $targetMime;

	public function convert($absoluteFilepathSource) {
		$absoluteFilepathTarget = $absoluteFilepathSource;
		if (strpos($absoluteFilepathTarget, $this->sourceMime) != -1) {
			$absoluteFilepathTarget = substr($absoluteFilepathTarget, 0, strlen($absoluteFilepathTarget)-strlen($this->sourceMime));
		}
		$absoluteFilepathTarget .= $this->targetMime;

		$this->_convert($absoluteFilepathSource, $absoluteFilepathTarget);
		
		return basename($absoluteFilepathTarget);
	}
}

?>
