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
 * Interface for stream contents.
 *
 * @package  bovigo_vfs
 */
/**
 * @ignore
 */
require_once dirname(__FILE__) . '/vfsStreamContainer.php';
/**
 * Interface for stream contents.
 *
 * @package  bovigo_vfs
 */
interface vfsStreamContent
{
    /**
     * stream content type: file
     *
     * @see  getType()
     */
    const TYPE_FILE = 0100000;
    /**
     * stream content type: directory
     *
     * @see  getType()
     */
    const TYPE_DIR  = 0040000;
    /**
     * stream content type: symbolic link
     *
     * @see  getType();
     */
    #const TYPE_LINK = 0120000;

    /**
     * returns the file name of the content
     *
     * @return  string
     */
    public function getName();

    /**
     * renames the content
     *
     * @param  string  $newName
     */
    public function rename($newName);

    /**
     * checks whether the container can be applied to given name
     *
     * @param   string  $name
     * @return  bool
     */
    public function appliesTo($name);

    /**
     * returns the type of the container
     *
     * @return  int
     */
    public function getType();

    /**
     * returns size of content
     *
     * @return  int
     */
    public function size();

    /**
     * sets the last modification time of the stream content
     *
     * @param   int               $filemtime
     * @return  vfsStreamContent
     */
    public function lastModified($filemtime);

    /**
     * returns the last modification time of the stream content
     *
     * @return  int
     */
    public function filemtime();

    /**
     * adds content to given container
     *
     * @param   vfsStreamContainer  $container
     * @return  vfsStreamContent
     */
    public function at(vfsStreamContainer $container);

    /**
     * change file mode to given permissions
     *
     * @param   int               $permissions
     * @return  vfsStreamContent
     */
    public function chmod($permissions);

    /**
     * returns permissions
     *
     * @return  int
     */
    public function getPermissions();

    /**
     * checks whether content is readable
     *
     * @param   int   $user   id of user to check for
     * @param   int   $group  id of group to check for
     * @return  bool
     */
    public function isReadable($user, $group);

    /**
     * checks whether content is writable
     *
     * @param   int   $user   id of user to check for
     * @param   int   $group  id of group to check for
     * @return  bool
     */
    public function isWritable($user, $group);

    /**
     * checks whether content is executable
     *
     * @param   int   $user   id of user to check for
     * @param   int   $group  id of group to check for
     * @return  bool
     */
    public function isExecutable($user, $group);

    /**
     * change owner of file to given user
     *
     * @param   int               $user
     * @return  vfsStreamContent
     */
    public function chown($user);

    /**
     * checks whether file is owned by given user
     *
     * @param   int  $user
     * @return  bool
     */
    public function isOwnedByUser($user);

    /**
     * returns owner of file
     *
     * @return  int
     */
    public function getUser();

    /**
     * change owner group of file to given group
     *
     * @param   int               $group
     * @return  vfsStreamContent
     */
    public function chgrp($group);

    /**
     * checks whether file is owned by group
     *
     * @param   int   $group
     * @return  bool
     */
    public function isOwnedByGroup($group);

    /**
     * returns owner group of file
     *
     * @return  int
     */
    public function getGroup();
}
?>
