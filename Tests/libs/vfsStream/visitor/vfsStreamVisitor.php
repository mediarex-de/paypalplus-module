<?php
/**
 * This file is part of OXID eSales PayPal Plus module.
 *
 * OXID eSales PayPal Plus module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal Plus module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales PayPal Plus module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2015
 */

/**
 * Interface for a visitor to work on a vfsStream content structure.
 *
 * @package     bovigo_vfs
 * @subpackage  visitor
 */
/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../vfsStreamDirectory.php';
require_once dirname(__FILE__) . '/../vfsStreamFile.php';
/**
 * Interface for a visitor to work on a vfsStream content structure.
 *
 * @package     bovigo_vfs
 * @subpackage  visitor
 * @since       0.10.0
 * @see         https://github.com/mikey179/vfsStream/issues/10
 */
interface vfsStreamVisitor
{
    /**
     * visit a content and process it
     *
     * @param   vfsStreamContent  $content
     * @return  vfsStreamVisitor
     */
    public function visit(vfsStreamContent $content);

    /**
     * visit a file and process it
     *
     * @param   vfsStreamFile  $file
     * @return  vfsStreamVisitor
     */
    public function visitFile(vfsStreamFile $file);

    /**
     * visit a directory and process it
     *
     * @param   vfsStreamDirectory  $dir
     * @return  vfsStreamVisitor
     */
    public function visitDirectory(vfsStreamDirectory $dir);
}
?>
