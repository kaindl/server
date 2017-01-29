<?php
/**
 * @copyright Copyright (c) 2016, ownCloud, Inc.
 *
 * @author Lukas Reschke <lukas@statuscode.ch>
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 *
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OC\IntegrityCheck\Iterator;

/**
 * Class ExcludeFileByNameFilterIterator provides a custom iterator which excludes
 * entries with the specified file name from the file list.
 *
 * @package OC\Integritycheck\Iterator
 */
class ExcludeFileByNameFilterIterator extends \RecursiveFilterIterator {
	/**
	 * Array of excluded file names. Those are not scanned by the integrity checker.
	 * This is used to exclude files which administrators could upload by mistakes
	 * such as .DS_Store files. These file names are matched exactly.
	 *
	 * @var array
	 */
	private $excludedFilenames = [
		'.DS_Store', // Mac OS X
		'Thumbs.db', // Microsoft Windows
		'.directory', // Dolphin (KDE)
		'.webapp', // Gentoo/Funtoo & derivatives use a tool known as webapp-config to manage wep-apps.
	];
	/**
	 * Array of excluded file names. Those are not scanned by the integrity checker.
	 * This is used to exclude files which administrators could upload by mistakes
	 * such as .DS_Store files. These strings are submatched, so any file names
	 * containing these strings, are ignored.
	 *
	 * @var array
	 */
	private $excludedFilenamesSubMatch = [
		'.webapp-nextcloud-', // Gentoo/Funtoo & derivatives use a tool known as webapp-config to manage wep-apps.
	];

	/**
	 * @return bool
	 */
	public function accept() {
		if($this->isDir()) {
			return true;
		}

		if (in_array(
			$this->current()->getFilename(),
			$this->excludedFilenames,
			true
		)) {
			return false;
		}

		foreach ($this->excludedFilenamesSubMatch as $excludedFilename)
			if (strpos($this->current()->getFilename(),$excludedFilename) !== false) {
				return false;
			}

		return true;
	}
}
