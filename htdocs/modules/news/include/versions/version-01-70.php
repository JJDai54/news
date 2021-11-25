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

function xoops_module_update_news_106()
{
    require_once XOOPS_ROOT_PATH . '/modules/news/class/utility.php';
    global $xoopsDB;
    $errors = 0;

    //0) Rename all tables

    if (NewsUtility::existTable($xoopsDB->prefix('stories_files'))) {
        $sql    = sprintf('ALTER TABLE ' . $xoopsDB->prefix('stories_files') . ' RENAME ' . $xoopsDB->prefix('news_stories_files'));
        $result = $xoopsDB->queryF($sql);
        if (!$result) {
            echo '<br>' . _AM_NEWS_UPGRADEFAILED . ' ' . _AM_NEWS_UPGRADEFAILED2;
            ++$errors;
        }
    } else {

        // 1) Create, if it does not exists, the stories_files table
        if (!NewsUtility::existTable($xoopsDB->prefix('news_stories_files'))) {
            $sql = 'CREATE TABLE ' . $xoopsDB->prefix('news_stories_files') . " (
              fileid INT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
              filerealname VARCHAR(255) NOT NULL DEFAULT '',
              storyid INT(8) UNSIGNED NOT NULL DEFAULT '0',
              date INT(10) NOT NULL DEFAULT '0',
              mimetype VARCHAR(64) NOT NULL DEFAULT '',
              downloadname VARCHAR(255) NOT NULL DEFAULT '',
              counter INT(8) UNSIGNED NOT NULL DEFAULT '0',
              PRIMARY KEY  (fileid),
              KEY storyid (storyid)
            ) ENGINE=MyISAM;";
            if (!$xoopsDB->queryF($sql)) {
                echo '<br>' . _AM_NEWS_UPGRADEFAILED . ' ' . _AM_NEWS_UPGRADEFAILED1;
                ++$errors;
            }
        }
    }

    if (NewsUtility::existTable($xoopsDB->prefix('stories'))) {
        $sql    = sprintf('ALTER TABLE ' . $xoopsDB->prefix('stories') . ' RENAME ' . $xoopsDB->prefix('news_stories'));
        $result = $xoopsDB->queryF($sql);
        if (!$result) {
            echo '<br>' . _AM_NEWS_UPGRADEFAILED . ' ' . _AM_NEWS_UPGRADEFAILED2;
            ++$errors;
        }
    }

    if (NewsUtility::existTable($xoopsDB->prefix('topics'))) {
        $sql    = sprintf('ALTER TABLE ' . $xoopsDB->prefix('topics') . ' RENAME ' . $xoopsDB->prefix('news_topics'));
        $result = $xoopsDB->queryF($sql);
        if (!$result) {
            echo '<br>' . _AM_NEWS_UPGRADEFAILED . ' ' . _AM_NEWS_UPGRADEFAILED2;
            ++$errors;
        }
    }

    if (NewsUtility::existTable($xoopsDB->prefix('stories_files'))) {
        $sql    = sprintf('ALTER TABLE ' . $xoopsDB->prefix('stories_files') . ' RENAME ' . $xoopsDB->prefix('news_stories_files'));
        $result = $xoopsDB->queryF($sql);
        if (!$result) {
            echo '<br>' . _AM_NEWS_UPGRADEFAILED . ' ' . _AM_NEWS_UPGRADEFAILED2;
            ++$errors;
        }
    }

    // 2) Change the topic title's length, in the topics table
    $sql    = sprintf('ALTER TABLE ' . $xoopsDB->prefix('news_topics') . ' CHANGE topic_title topic_title VARCHAR( 255 ) NOT NULL;');
    $result = $xoopsDB->queryF($sql);
    if (!$result) {
        echo '<br>' . _AM_NEWS_UPGRADEFAILED . ' ' . _AM_NEWS_UPGRADEFAILED2;
        ++$errors;
    }

    // 2.1) Add the new fields to the topic table
    if (!NewsUtility::existField('menu', $xoopsDB->prefix('news_topics'))) {
        NewsUtility::addField("menu TINYINT( 1 ) DEFAULT '0' NOT NULL", $xoopsDB->prefix('news_topics'));
    }
    if (!NewsUtility::existField('topic_frontpage', $xoopsDB->prefix('news_topics'))) {
        NewsUtility::addField("topic_frontpage TINYINT( 1 ) DEFAULT '1' NOT NULL", $xoopsDB->prefix('news_topics'));
    }
    if (!NewsUtility::existField('topic_rssurl', $xoopsDB->prefix('news_topics'))) {
        NewsUtility::addField('topic_rssurl VARCHAR( 255 ) NOT NULL', $xoopsDB->prefix('news_topics'));
    }
    if (!NewsUtility::existField('topic_description', $xoopsDB->prefix('news_topics'))) {
        NewsUtility::addField('topic_description TEXT NOT NULL', $xoopsDB->prefix('news_topics'));
    }
    if (!NewsUtility::existField('topic_color', $xoopsDB->prefix('news_topics'))) {
        NewsUtility::addField("topic_color varchar(6) NOT NULL default '000000'", $xoopsDB->prefix('news_topics'));
    }

    // 3) If it does not exists, create the table stories_votedata
    if (!NewsUtility::existTable($xoopsDB->prefix('news_stories_votedata'))) {
        $sql = 'CREATE TABLE ' . $xoopsDB->prefix('news_stories_votedata') . " (
              ratingid INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              storyid INT(8) UNSIGNED NOT NULL DEFAULT '0',
              ratinguser INT(11) NOT NULL DEFAULT '0',
              rating TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
              ratinghostname VARCHAR(60) NOT NULL DEFAULT '',
              ratingtimestamp INT(10) NOT NULL DEFAULT '0',
              PRIMARY KEY  (ratingid),
              KEY ratinguser (ratinguser),
              KEY ratinghostname (ratinghostname),
              KEY storyid (storyid)
            ) ENGINE=MyISAM;";
        if (!$xoopsDB->queryF($sql)) {
            echo '<br>' . _AM_NEWS_UPGRADEFAILED . ' ' . _AM_NEWS_UPGRADEFAILED3;
            ++$errors;
        }
    }

    // 4) Create the four new fields for the votes in the story table
    if (!NewsUtility::existField('rating', $xoopsDB->prefix('news_stories'))) {
        NewsUtility::addField("rating DOUBLE( 6, 4 ) DEFAULT '0.0000' NOT NULL", $xoopsDB->prefix('news_stories'));
    }
    if (!NewsUtility::existField('votes', $xoopsDB->prefix('news_stories'))) {
        NewsUtility::addField("votes INT( 11 ) UNSIGNED DEFAULT '0' NOT NULL", $xoopsDB->prefix('news_stories'));
    }
    if (!NewsUtility::existField('keywords', $xoopsDB->prefix('news_stories'))) {
        NewsUtility::addField('keywords VARCHAR(255) NOT NULL', $xoopsDB->prefix('news_stories'));
    }
    if (!NewsUtility::existField('description', $xoopsDB->prefix('news_stories'))) {
        NewsUtility::addField('description VARCHAR(255) NOT NULL', $xoopsDB->prefix('news_stories'));
    }
    if (!NewsUtility::existField('pictureinfo', $xoopsDB->prefix('news_stories'))) {
        NewsUtility::addField('pictureinfo VARCHAR(255) NOT NULL', $xoopsDB->prefix('news_stories'));
    }
    if (!NewsUtility::existField('subtitle', $xoopsDB->prefix('news_stories'))) {
        NewsUtility::addField('subtitle VARCHAR(255) NOT NULL', $xoopsDB->prefix('news_stories'));
    }

    // 5) Add some indexes to the topics table
    $sql    = sprintf('ALTER TABLE ' . $xoopsDB->prefix('news_topics') . ' ADD INDEX ( `topic_title` );');
    $result = $xoopsDB->queryF($sql);
    $sql    = sprintf('ALTER TABLE ' . $xoopsDB->prefix('news_topics') . ' ADD INDEX ( `menu` );');
    $result = $xoopsDB->queryF($sql);

    // 6) Make files and folders
    $dir = XOOPS_ROOT_PATH . '/uploads/news';
    if (!@mkdir($dir) && !is_dir($dir)) {
        throw new \RuntimeException('The directory ' . $dir . ' could not be created.');
    }
    if (!is_writable($dir)) {
        chmod($dir, 0777);
    }

    $dir = XOOPS_ROOT_PATH . '/uploads/news/file';
    if (!@mkdir($dir) && !is_dir($dir)) {
        throw new \RuntimeException('The directory ' . $dir . ' could not be created.');
    }
    if (!is_writable($dir)) {
        chmod($dir, 0777);
    }

    $dir = XOOPS_ROOT_PATH . '/uploads/news/image';
    if (!@mkdir($dir) && !is_dir($dir)) {
        throw new \RuntimeException('The directory ' . $dir . ' could not be created.');
    }
    if (!is_writable($dir)) {
        chmod($dir, 0777);
    }

    // Copy index.html files on uploads folders
    $indexFile = XOOPS_ROOT_PATH . '/modules/news/include/index.html';
    copy($indexFile, XOOPS_ROOT_PATH . '/uploads/news/index.html');
    copy($indexFile, XOOPS_ROOT_PATH . '/uploads/news/file/index.html');
    copy($indexFile, XOOPS_ROOT_PATH . '/uploads/news/image/index.html');

    return true;
}
xoops_module_update_news_106();