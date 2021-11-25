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

function xoops_module_update_news(\XoopsModule $module, $prev_version)
{
    /* @var \XoopsModules\Xforms\Utility $utility */
//     $utility = new Utility();
//
//     $success = true;
//     $success = $utility::checkVerXoops($module);
//     $success = $utility::checkVerPHP($module);
//     if (!$success) {
//         return false;
//     }

echo "version = {$prev_version}<br>";
    $success = true;

    /* @var \XoopsModules\Xforms\Helper $helper */
//     $helper = xHelper::getInstance();
//     $helper->loadLanguage('modinfo');

    //require_once $helper->path('include/common.php');
    //$fldVersions = XOOPS_ROOT_PATH . 'modules/xforms/include/versions/';
    $fldVersions = 'versions/';
    //---------------------------------------------------------------------
    if ($prev_version < 106) {
      $success = include_once ($fldVersions . 'version-01-70.php');
    }
    //---------------------------------------------------------------------
    if ($prev_version < 180) {
      $success = include_once ($fldVersions . 'version-01-80.php');
    }
    //---------------------------------------------------------------------


     return $success;
}

