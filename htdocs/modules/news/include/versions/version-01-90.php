<?php
/**
 * News functions
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright   {@link https://xoops.org/ XOOPS Project}
 * @license     GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @author      Voltan
 * @package     News
 */


global $xoopsDB;
ALTER TABLE `x251_news_stories` ADD `authors` VARCHAR(255); 

   $sql = "ALTER TABLE " . $xoopsDB->prefix('news_stories')
        . " ADD authors VARCHAR(255) NOT NULL AFTER uid;";
  $xoopsDB->queryf($sql);
  //echo $sql . "<hr>";

    return $success;
