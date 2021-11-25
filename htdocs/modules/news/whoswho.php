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

/*
 * Created on 28 oct. 2006
 *
 * This page will display a list of the authors of the site
 *
 * @package News
 * @author Herve Thouzard
 * @copyright (c) Herve Thouzard (http://www.herve-thouzard.com)
 */
///include_once __DIR__ . '/../../mainfile.php';
include_once 'header.php';
require_once XOOPS_ROOT_PATH . '/modules/news/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/news/include/constantes.php';
require_once XOOPS_ROOT_PATH . '/modules/news/class/class.newsstory.php';
require_once XOOPS_ROOT_PATH . '/modules/news/class/class.newstopic.php';
require_once XOOPS_ROOT_PATH . '/modules/news/class/class.sfiles.php';
require_once XOOPS_ROOT_PATH . '/modules/news/class/utility.php';

if (!NewsUtility::getModuleOption('newsbythisauthor')) {
    redirect_header('index.php', 2, _ERRORS);
}

$GLOBALS['xoopsOption']['template_main'] = 'news_whos_who.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

$option  = NewsUtility::getModuleOption('displayname');
$article = new NewsStory();
$uid_ids = [];
$uid_ids = $article->getWhosWho(NewsUtility::getModuleOption('restrictindex'));
if (count($uid_ids) > 0) {
    $lst_uid       = implode(',', $uid_ids);
    $memberHandler = xoops_getHandler('member');
    $critere       = new Criteria('uid', '(' . $lst_uid . ')', 'IN');
    $tbl_users     = $memberHandler->getUsers($critere);
    foreach ($tbl_users as $one_user) {
        $uname = '';
        switch ($option) {
            case 1: // Username
                $uname = $one_user->getVar('uname');
                break;

            case 2: // Display full name (if it is not empty)
                if ('' !== xoops_trim($one_user->getVar('name'))) {
                    $uname = $one_user->getVar('name');
                } else {
                    $uname = $one_user->getVar('uname');
                }
                break;
        }
        $xoopsTpl->append('whoswho', [
            'uid'            => $one_user->getVar('uid'),
            'name'           => $uname,
            'user_avatarurl' => XOOPS_URL . '/uploads/' . $one_user->getVar('user_avatar')
        ]);
    }
}

$xoopsTpl->assign('advertisement', NewsUtility::getModuleOption('advertisement'));
news_load_css();
$xoopsTpl->assign('colorset_author', NEWS_COLORSET_AUTHOR);

/**
 * Manage all the meta datas
 */
NewsUtility::createMetaDatas($article);

$xoopsTpl->assign('xoops_pagetitle', _AM_NEWS_WHOS_WHO);
$myts             = MyTextSanitizer::getInstance();
$meta_description = _AM_NEWS_WHOS_WHO . ' - ' . $xoopsModule->name('s');
if (isset($xoTheme) && is_object($xoTheme)) {
    $xoTheme->addMeta('meta', 'description', $meta_description);
} else { // Compatibility for old Xoops versions
    $xoopsTpl->assign('xoops_meta_description', $meta_description);
}

require_once XOOPS_ROOT_PATH . '/footer.php';
