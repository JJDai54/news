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
 * Created on 5 nov. 2006
 *
 * This page is used to display a maps of the topics (with articles count)
 *
 * @package News
 * @author Herve Thouzard
 * @copyright (c) Herve Thouzard - http://www.herve-thouzard.com
 */
include_once ('../../mainfile.php');
require_once XOOPS_ROOT_PATH . '/modules/news/class/class.newsstory.php';
require_once XOOPS_ROOT_PATH . '/modules/news/class/class.newstopic.php';
require_once XOOPS_ROOT_PATH . '/modules/news/class/utility.php';
require_once XOOPS_ROOT_PATH . '/modules/news/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/news/include/constantes.php';
//require_once XOOPS_ROOT_PATH . '/modules/news/class/class.sfiles.php';


require_once XOOPS_ROOT_PATH . '/modules/news/class/utility.php';






$GLOBALS['xoopsOption']['template_main'] = 'news_topics_directory.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

$myts = MyTextSanitizer::getInstance();

$newscountbytopic = $tbl_topics = [];
$perms            = '';
$xt               = new NewsTopic();
$restricted       = NewsUtility::getModuleOption('restrictindex');
if ($restricted) {
    global $xoopsUser;
    /** @var XoopsModuleHandler $moduleHandler */
    $moduleHandler = xoops_getHandler('module');
    $newsModule    = $moduleHandler->getByDirname('news');
    $groups        = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
    $gpermHandler  = xoops_getHandler('groupperm');
    $topics        = $gpermHandler->getItemIds('news_view', $groups, $newsModule->getVar('mid'));
    if (count($topics) > 0) {
        $topics = implode(',', $topics);
        $perms  = ' AND topic_id IN (' . $topics . ') ';
    } else {
        return '';
    }
}
$topics_arr       = $xt->getChildTreeArray(0, 'topic_title', $perms);
//echo "<hr><pre>" . print_r($topics_arr, true) . "</pre><hr>";
$newscountbytopic = $xt->getNewsCountByTopic();
if (is_array($topics_arr) && count($topics_arr)) {
    foreach ($topics_arr as $onetopic) {
        $count = 0;
        if (array_key_exists($onetopic['topic_id'], $newscountbytopic)) {
            $count = $newscountbytopic[$onetopic['topic_id']];
        }
        if (0 != $onetopic['topic_pid']) {
            $onetopic['prefix'] = str_replace('.', '-', $onetopic['prefix']) . '&nbsp;';
        } else {
            $onetopic['prefix'] = str_replace('.', '', $onetopic['prefix']);
        }

        $tbl_topics[] = [
            'id'          => $onetopic['topic_id'],
            'news_count'  => $count,
            'topic_color' => '#' . $onetopic['topic_color'],
            'color_set'   => $onetopic['topic_color_set'],
            'prefix'      => $onetopic['prefix'],
            'title'       => $myts->displayTarea($onetopic['topic_title']),
            'description' => NewsUtility::html2text($onetopic['topic_description']),
            'imgurl' =>  $onetopic['topic_imgurl']
        ];
    }
}
//echo "<hr><pre>" . print_r($tbl_topics, true) . "</pre><hr>";
$xoopsTpl->assign('topics', $tbl_topics);

$xoopsTpl->assign('advertisement', NewsUtility::getModuleOption('advertisement'));
news_load_css();
//$story['topic_color_set'] = news_get_color_set($article->topic_color_set);
//$xoopsTpl->assign('topic_color_set', $topic_color_set);

/**
 * Manage all the meta datas
 */
NewsUtility::createMetaDatas();

$xoopsTpl->assign('xoops_pagetitle', _AM_NEWS_TOPICS_DIRECTORY);
$meta_description = _AM_NEWS_TOPICS_DIRECTORY . ' - ' . $xoopsModule->name('s');
if (isset($xoTheme) && is_object($xoTheme)) {
    $xoTheme->addMeta('meta', 'description', $meta_description);
} else { // Compatibility for old Xoops versions
    $xoopsTpl->assign('xoops_meta_description', $meta_description);
}

require_once XOOPS_ROOT_PATH . '/footer.php';
