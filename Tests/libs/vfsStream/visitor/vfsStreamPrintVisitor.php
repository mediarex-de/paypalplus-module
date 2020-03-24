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
 * Visitor which traverses a content structure recursively to print it to an output stream.
 *
 * @package     bovigo_vfs
 * @subpackage  visitor
 */
/**
 * @ignore
 */
require_once dirname(__FILE__) . '/vfsStreamAbstractVisitor.php';
/**
 * Visitor which traverses a content structure recursively to print it to an output stream.
 *
 * @package     bovigo_vfs
 * @subpackage  visitor
 * @since       0.10.0
 * @see         https://github.com/mikey179/vfsStream/issues/10
 */
class vfsStreamPrintVisitor extends vfsStreamAbstractVisitor
{
    /**
     * target to write output to
     *
     * @var  resource
     */
    protected $out;
    /**
     * current depth in directory tree
     *
     * @var  int
     */
    protected $depth;

    /**
     * constructor
     *
     * If no file pointer given it will fall back to STDOUT.
     *
     * @param   resource  $out  optional
     * @throws  InvalidArgumentException
     */
    public function __construct($out = STDOUT)
    {
        if (is_resource($out) === false || get_resource_type($out) !== 'stream') {
            throw new InvalidArgumentException('Given filepointer is not a resource of type stream');
        }

        $this->out = $out;
    }

    /**
     * visit a file and process it
     *
     * @param   vfsStreamFile          $file
     * @return  vfsStreamPrintVisitor
     */
    public function visitFile(vfsStreamFile $file)
    {
        $this->printContent($file);
        return $this;
    }

    /**
     * visit a directory and process it
     *
     * @param   vfsStreamDirectory     $dir
     * @return  vfsStreamPrintVisitor
     */
    public function visitDirectory(vfsStreamDirectory $dir)
    {
        $this->printContent($dir);
        $this->depth++;
        foreach ($dir as $child) {
            $this->visit($child);
        }

        $this->depth--;
        return $this;
    }

    /**
     * helper method to print the content
     *
     * @param  vfsStreamContent  $content
     */
    protected function printContent(vfsStreamContent $content)
    {
        fwrite($this->out, str_repeat('  ', $this->depth) . '- ' . $content->getName() . "\n");
    }
}
?>
