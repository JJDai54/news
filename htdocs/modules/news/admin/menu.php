<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright      {@link https://xoops.org/ XOOPS Project}
 * @license        {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package
 * @since
 * @author         XOOPS Development Team
 */

$moduleDirName = basename(dirname(__DIR__));

if (false !== ($newsHelper = Xmf\Module\Helper::getHelper($moduleDirName))) {
} else {
    $newsHelper = Xmf\Module\Helper::getHelper('system');
}


$pathIcon32 = \Xmf\Module\Admin::menuIconPath('');
//$pathModIcon32 = $newsHelper->getModule()->getInfo('modicons32');

$newsHelper->loadLanguage('modinfo');

$adminObject = [];
$adminmenu[] = [
    'title' => _MI_NEWS_HOME,
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . '/home.png'
];

$adminmenu[] = [
    'title' => _MI_NEWS_ADMENU2,
    'link'  => 'admin/index.php?op=topicsmanager',
    'icon'  => $pathIcon32 . '/category.png'
];

$adminmenu[] = [
    'title' => _MI_NEWS_ADMENU3,
    'link'  => 'admin/index.php?op=newarticle',
    'icon'  => $pathIcon32 . '/content.png'
];

$adminmenu[] = [
    'title' => _MI_NEWS_ADMENU_MERGE,
    'link'  => 'admin/index.php?op=topics_merge_form',
    'icon'  => $pathIcon32 . '/groupmod.png'
];

$adminmenu[] = [
    'title' => _MI_NEWS_GROUPPERMS,
    'link'  => 'admin/groupperms.php',
    'icon'  => $pathIcon32 . '/permissions.png'
];

$adminmenu[] = [
    'title' => _MI_NEWS_PRUNENEWS,
    'link'  => 'admin/index.php?op=prune',
    'icon'  => $pathIcon32 . '/prune.png'
];

$adminmenu[] = [
    'title' => _MI_NEWS_EXPORT,
    'link'  => 'admin/index.php?op=export',
    'icon'  => $pathIcon32 . '/export.png'
];

$adminmenu[] = [
    'title' => _MI_NEWS_NEWSLETTER,
    'link'  => 'admin/index.php?op=configurenewsletter',
    'icon'  => $pathIcon32 . '/newsletter.png'
];

$adminmenu[] = [
    'title' => _MI_NEWS_STATS,
    'link'  => 'admin/index.php?op=stats',
    'icon'  => $pathIcon32 . '/stats.png'
];

if (isset($xoopsModule) && 167 != $xoopsModule->getVar('version')) {
    $adminmenu[] = [
        'title' => _MI_NEWS_UPGRADE,
        'link'  => 'admin/upgrade.php',
        'icon'  => $pathIcon32 . '/update.png'
    ];
}

$adminmenu[] = [
    'title' => _MI_NEWS_METAGEN,
    'link'  => 'admin/index.php?op=metagen',
    'icon'  => $pathIcon32 . '/metagen.png'
];

$adminmenu[] = [
    'title' => _MI_NEWS_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png'
];
