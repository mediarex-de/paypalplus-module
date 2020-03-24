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
 * Visitor which traverses a content structure recursively to create an array structure from it.
 *
 * @package     bovigo_vfs
 * @subpackage  visitor
 */
/**
 * @ignore
 */
require_once dirname(__FILE__) . '/vfsStreamAbstractVisitor.php';
/**
 * Visitor which traverses a content structure recursively to create an array structure from it.
 *
 * @package     bovigo_vfs
 * @subpackage  visitor
 * @since       0.10.0
 * @see         https://github.com/mikey179/vfsStream/issues/10
 */
class vfsStreamStructureVisitor extends vfsStreamAbstractVisitor
{
    /**
     * collected structure
     *
     * @var  array<string,array|string>
     */
    protected $structure = array();
    /**
     * poiting to currently iterated directory
     *
     * @var  array<string,array|string>
     */
    protected $current;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * visit a file and process it
     *
     * @param   vfsStreamFile              $file
     * @return  vfsStreamStructureVisitor
     */
    public function visitFile(vfsStreamFile $file)
    {
        $this->current[$file->getName()] = $file->getContent();
        return $this;
    }

    /**
     * visit a directory and process it
     *
     * @param   vfsStreamDirectory         $dir
     * @return  vfsStreamStructureVisitor
     */
    public function visitDirectory(vfsStreamDirectory $dir)
    {
        $this->current[$dir->getName()] = array();
        $tmp           =& $this->current;
        $this->current =& $tmp[$dir->getName()];
        foreach ($dir as $child) {
            $this->visit($child);
        }

        $this->current =& $tmp;
        return $this;
    }

    /**
     * returns structure of visited contents
     *
     * @return  array<string,array|string>
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * resets structure so visitor could be reused
     *
     * @return  vfsStreamStructureVisitor
     */
    public function reset()
    {
        $this->structure = array();
        $this->current   =& $this->structure;
        return $this;
    }
}
?>
