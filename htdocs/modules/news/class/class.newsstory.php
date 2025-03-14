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

// defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

require_once XOOPS_ROOT_PATH . '/modules/news/class/xoopsstory.php';
require_once XOOPS_ROOT_PATH . '/include/comment_constants.php';
require_once XOOPS_ROOT_PATH . '/modules/news/class/utility.php';

/**
 * Class NewsStory
 */
class NewsStory extends MyXoopsStory
{
    public $newstopic; // XoopsTopic object
    public $rating; // News rating
    public $votes; // Number of votes
    public $description; // META, desciption
    public $keywords; // META, keywords
    public $picture;
    public $topic_imgurl;
    public $topic_title;
    public $pictureinfo;

    /**
     * Constructor
     * @param int $storyid
     */
    public function __construct($storyid = -1)
    {
        $this->db          = XoopsDatabaseFactory::getDatabaseConnection();
        $this->table       = $this->db->prefix('news_stories');
        $this->topicstable = $this->db->prefix('news_topics');
        if (is_array($storyid)) {
            $this->makeStory($storyid);
        } elseif ($storyid != -1) {
            $this->getStory((int)$storyid);
        }
    }

    /**
     * Returns the number of stories published before a date
     * @param         $timestamp
     * @param         $expired
     * @param  string $topicslist
     * @return mixed
     */
    public function getCountStoriesPublishedBefore($timestamp, $expired, $topicslist = '')
    {
        $db  = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = 'SELECT count(*) AS cpt FROM ' . $db->prefix('news_stories') . ' WHERE published <=' . $timestamp;
        if ($expired) {
            $sql .= ' AND (expired>0 AND expired<=' . time() . ')';
        }
        if (strlen(trim($topicslist)) > 0) {
            $sql .= ' AND topicid IN (' . $topicslist . ')';
        }
        $result = $db->query($sql);
        list($count) = $db->fetchRow($result);

        return $count;
    }

    /**
     * Load the specified story from the database
     * @param $storyid
     */
    public function getStory($storyid)
    {
        $sql   = 'SELECT s.*, t.* FROM ' . $this->table . ' s, ' . $this->db->prefix('news_topics') . ' t WHERE (storyid=' . (int)$storyid . ') AND (s.topicid=t.topic_id)';
        $array = $this->db->fetchArray($this->db->query($sql));
        $this->makeStory($array);
    }

    /**
     * Delete stories that were published before a given date
     * @param         $timestamp
     * @param         $expired
     * @param  string $topicslist
     * @return bool
     */
    public function deleteBeforeDate($timestamp, $expired, $topicslist = '')
    {
        global $xoopsModule;
        $mid          = $xoopsModule->getVar('mid');
        $db           = XoopsDatabaseFactory::getDatabaseConnection();
        $prefix       = $db->prefix('news_stories');
        $vote_prefix  = $db->prefix('news_stories_votedata');
        $files_prefix = $db->prefix('news_stories_files');
        $sql          = 'SELECT storyid FROM  ' . $prefix . ' WHERE published <=' . $timestamp;
        if ($expired) {
            $sql .= ' (AND expired>0 AND expired<=' . time() . ')';
        }
        if (strlen(trim($topicslist)) > 0) {
            $sql .= ' AND topicid IN (' . $topicslist . ')';
        }
        $result = $db->query($sql);
        while ($myrow = $db->fetchArray($result)) {
            xoops_comment_delete($mid, $myrow['storyid']); // Delete comments
            xoops_notification_deletebyitem($mid, 'story', $myrow['storyid']); // Delete notifications
            $db->queryF('DELETE FROM ' . $vote_prefix . ' WHERE storyid=' . $myrow['storyid']); // Delete votes
            // Remove files and records related to the files
            $result2 = $db->query('SELECT * FROM ' . $files_prefix . ' WHERE storyid=' . $myrow['storyid']);
            while ($myrow2 = $db->fetchArray($result2)) {
                $name = XOOPS_ROOT_PATH . '/uploads/' . $myrow2['downloadname'];
                if (file_exists($name)) {
                    unlink($name);
                }
                $db->query('DELETE FROM ' . $files_prefix . ' WHERE fileid=' . $myrow2['fileid']);
            }
            $db->queryF('DELETE FROM ' . $prefix . ' WHERE storyid=' . $myrow['storyid']); // Delete the story
        }

        return true;
    }

    /**
     * @param      $storyid
     * @param bool $next
     * @param bool $checkRight
     *
     * @return array
     */
    public function _searchPreviousOrNextArticle($storyid, $next = true, $checkRight = false)
    {
        $db      = XoopsDatabaseFactory::getDatabaseConnection();
        $ret     = [];
        $storyid = (int)$storyid;
        if ($next) {
            $sql     = 'SELECT storyid, title FROM ' . $db->prefix('news_stories') . ' WHERE (published > 0 AND published <= ' . time() . ') AND (expired = 0 OR expired > ' . time() . ') AND storyid > ' . $storyid;
            $orderBy = ' ORDER BY storyid ASC';
        } else {
            $sql     = 'SELECT storyid, title FROM ' . $db->prefix('news_stories') . ' WHERE (published > 0 AND published <= ' . time() . ') AND (expired = 0 OR expired > ' . time() . ') AND storyid < ' . $storyid;
            $orderBy = ' ORDER BY storyid DESC';
        }
        if ($checkRight) {
            $topics = NewsUtility::getMyItemIds('news_view');
            if (count($topics) > 0) {
                $sql .= ' AND topicid IN (' . implode(',', $topics) . ')';
            } else {
                return null;
            }
        }
        $sql    .= $orderBy;
        $db     = XoopsDatabaseFactory::getDatabaseConnection();
        $result = $db->query($sql, 1);
        if ($result) {
            $myts = MyTextSanitizer::getInstance();
            while ($row = $db->fetchArray($result)) {
                $ret = ['storyid' => $row['storyid'], 'title' => $myts->htmlSpecialChars($row['title'])];
            }
        }

        return $ret;
    }

    /**
     * @param int  $storyid
     * @param bool $checkRight
     *
     * @return null|array
     */
    public function getNextArticle($storyid, $checkRight = false)
    {
        return $this->_searchPreviousOrNextArticle($storyid, true, $checkRight);
    }

    /**
     * @param      $storyid
     * @param bool $checkRight
     *
     * @return array
     */
    public function getPreviousArticle($storyid, $checkRight = false)
    {
        return $this->_searchPreviousOrNextArticle($storyid, false, $checkRight);
    }

    /**
     * Returns published stories according to some options
     * @param  int    $limit
     * @param  int    $start
     * @param  bool   $checkRight
     * @param  int    $topic
     * @param  int    $ihome
     * @param  bool   $asobject
     * @param  string $order
     * @param  bool   $topic_frontpage
     * @return array
     */
    public static function getAllPublished(
        $limit = 0,
        $start = 0,
        $checkRight = false,
        $topic = 0,
        $ihome = 0,
        $asobject = true,
        $order = 'published',
        $topic_frontpage = false
    ) {
        $db   = XoopsDatabaseFactory::getDatabaseConnection();
        $myts = MyTextSanitizer::getInstance();
        $ret  = [];
        $sql  = 'SELECT s.*, t.* FROM ' . $db->prefix('news_stories') . ' s, ' . $db->prefix('news_topics') . ' t WHERE (s.published > 0 AND s.published <= ' . time() . ') AND (s.expired = 0 OR s.expired > ' . time() . ') AND (s.topicid=t.topic_id) ';
        if (0 != $topic) {
            if (!is_array($topic)) {
                if ($checkRight) {
                    $topics = NewsUtility::getMyItemIds('news_view');
                    if (!in_array($topic, $topics)) {
                        return null;
                    } else {
                        $sql .= ' AND s.topicid=' . (int)$topic . ' AND (s.ihome=1 OR s.ihome=0)';
                    }
                } else {
                    $sql .= ' AND s.topicid=' . (int)$topic . ' AND (s.ihome=1 OR s.ihome=0)';
                }
            } else {
                if ($checkRight) {
                    $topics = NewsUtility::getMyItemIds('news_view');
                    $topic  = array_intersect($topic, $topics);
                }
                if (count($topic) > 0) {
                    $sql .= ' AND s.topicid IN (' . implode(',', $topic) . ')';
                } else {
                    return null;
                }
            }
        } else {
            if ($checkRight) {
                $topics = NewsUtility::getMyItemIds('news_view');
                if (count($topics) > 0) {
                    $topics = implode(',', $topics);
                    $sql    .= ' AND s.topicid IN (' . $topics . ')';
                } else {
                    return null;
                }
            }
            if (0 == (int)$ihome) {
                $sql .= ' AND s.ihome=0';
            }
        }
        if ($topic_frontpage) {
            $sql .= ' AND t.topic_frontpage=1';
        }
        $sql    .= " ORDER BY s.$order DESC";
        $result = $db->query($sql, (int)$limit, (int)$start);

        while ($myrow = $db->fetchArray($result)) {
            if ($asobject) {
                $ret[] = new NewsStory($myrow);
            } else {
                $ret[$myrow['storyid']] = $myts->htmlSpecialChars($myrow['title']);
            }
        }

        return $ret;
    }

    /**
     * Retourne la liste des articles aux archives (pour une p?riode donn?e)
     * @param             $publish_start
     * @param             $publish_end
     * @param  bool       $checkRight
     * @param  bool       $asobject
     * @param  string     $order
     * @return array|null
     */
    public function getArchive(
        $publish_start,
        $publish_end,
        $checkRight = false,
        $asobject = true,
        $order = 'published'
    ) {
        $db   = XoopsDatabaseFactory::getDatabaseConnection();
        $myts = MyTextSanitizer::getInstance();
        $ret  = [];
        $sql  = 'SELECT s.*, t.* FROM ' . $db->prefix('news_stories') . ' s, ' . $db->prefix('news_topics') . ' t WHERE (s.topicid=t.topic_id) AND (s.published > ' . $publish_start . ' AND s.published <= ' . $publish_end . ') AND (expired = 0 OR expired > ' . time() . ') ';

        if ($checkRight) {
            $topics = NewsUtility::getMyItemIds('news_view');
            if (count($topics) > 0) {
                $topics = implode(',', $topics);
                $sql    .= ' AND topicid IN (' . $topics . ')';
            } else {
                return null;
            }
        }
        $sql    .= " ORDER BY $order DESC";
        $result = $db->query($sql);
        while ($myrow = $db->fetchArray($result)) {
            if ($asobject) {
                $ret[] = new NewsStory($myrow);
            } else {
                $ret[$myrow['storyid']] = $myts->htmlSpecialChars($myrow['title']);
            }
        }

        return $ret;
    }

    /**
     * Get the today's most readed article
     *
     * @param int     $limit      records limit
     * @param int     $start      starting record
     * @param boolean $checkRight Do we need to check permissions (by topics) ?
     * @param int     $topic      limit the job to one topic
     * @param int     $ihome      Limit to articles published in home page only ?
     * @param boolean $asobject   Do we have to return an array of objects or a simple array ?
     * @param string  $order      Fields to sort on
     *
     * @return array
     */
    public function getBigStory(
        $limit = 0,
        $start = 0,
        $checkRight = false,
        $topic = 0,
        $ihome = 0,
        $asobject = true,
        $order = 'counter'
    ) {
        $db    = XoopsDatabaseFactory::getDatabaseConnection();
        $myts  = MyTextSanitizer::getInstance();
        $ret   = [];
        $tdate = mktime(0, 0, 0, date('n'), date('j'), date('Y'));
        $sql   = 'SELECT s.*, t.* FROM ' . $db->prefix('news_stories') . ' s, ' . $db->prefix('news_topics') . ' t WHERE (s.topicid=t.topic_id) AND (published > ' . $tdate . ' AND published < ' . time() . ') AND (expired > ' . time() . ' OR expired = 0) ';

        if (0 != (int)$topic) {
            if (!is_array($topic)) {
                $sql .= ' AND topicid=' . (int)$topic . ' AND (ihome=1 OR ihome=0)';
            } else {
                if (count($topic) > 0) {
                    $sql .= ' AND topicid IN (' . implode(',', $topic) . ')';
                } else {
                    return null;
                }
            }
        } else {
            if ($checkRight) {
                $topics = NewsUtility::getMyItemIds('news_view');
                if (count($topics) > 0) {
                    $topics = implode(',', $topics);
                    $sql    .= ' AND topicid IN (' . $topics . ')';
                } else {
                    return null;
                }
            }
            if (0 == (int)$ihome) {
                $sql .= ' AND ihome=0';
            }
        }
        $sql    .= " ORDER BY $order DESC";
        $result = $db->query($sql, (int)$limit, (int)$start);
        while ($myrow = $db->fetchArray($result)) {
            if ($asobject) {
                $ret[] = new NewsStory($myrow);
            } else {
                $ret[$myrow['storyid']] = $myts->htmlSpecialChars($myrow['title']);
            }
        }

        return $ret;
    }

    /**
     * Get all articles published by an author
     *
     * @param int     $uid        author's id
     * @param boolean $checkRight whether to check the user's rights to topics
     *
     * @param bool    $asobject
     * @param int     $expired - 0=non,1=oui,2=all
     * @return array
     */
    public function getAllPublishedByAuthor($uid, $checkRight = false, $asobject = true, $orderby = 0, $story_status = 0,$story_category=0)
    {
        $db        = XoopsDatabaseFactory::getDatabaseConnection();
        $myts      = MyTextSanitizer::getInstance();
        $ret       = [];
        $tblstory  = $db->prefix('news_stories');
        $tbltopics = $db->prefix('news_topics');

        //--------------------------------------
        $sql = "SELECT {$tblstory}.*, {$tbltopics}.topic_title, {$tbltopics}.topic_color, {$tbltopics}.topic_color_set";
        $sql .= " FROM {$tblstory}, {$tbltopics}";

        //--------------------------------------

        //JJDai - Ajout des selection sur l'expiration des articles
        $time = time();
        switch ($story_status){
            case NEWS_STORY_STATUS_PERMANENT:  //_MD_NEWS_PERMANENT
              $sql .= " WHERE ({$tblstory}.topicid = {$tbltopics}.topic_id)"
                    . " AND (published > 0 AND published <= {$time})"
                    . " AND (expired = 0)";
                break;

            case NEWS_STORY_STATUS_NON_EXPIRED:  //_MD_NEWS_NON_EXPIRED
              $sql .= " WHERE ({$tblstory}.topicid = {$tbltopics}.topic_id)"
                    . " AND (published > 0 AND published <= {$time})"
                    . " AND (expired != 0 AND expired >= {$time})";
                break;

            case NEWS_STORY_STATUS_EXPIRED: // _MD_NEWS_EXPIRED
              $sql .= " WHERE ({$tblstory}.topicid = {$tbltopics}.topic_id)"
                    . " AND (published > 0 AND published <= {$time})"
                    . " AND (expired != 0 AND expired < {$time})";
                break;

            case NEWS_STORY_STATUS_ALL:  //_MD_NEWS_ALL
              $sql .= " WHERE ({$tblstory}.topicid = {$tbltopics}.topic_id)"
                    . " AND (published > 0 AND published <= {$time})";
                break;

            case NEWS_STORY_STATUS_ACTIFS:  //_MD_NEWS_ACTIFS
            default:
              $sql .= " WHERE ({$tblstory}.topicid = {$tbltopics}.topic_id)"
                    . " AND (published > 0 AND published <= {$time})"
                    . " AND (expired = 0 OR expired > {$time})";
                break;
        }
        if ($story_category>0) $sql .= " AND {$tblstory}.topicid = {$story_category}";

//           $sql .= ' WHERE ('
//                  . $tblstory
//                  . '.topicid='
//                  . $tbltopics
//                  . '.topic_id) AND (published > 0 AND published <= '
//                  . time()
//                  . ') AND (expired = 0 OR expired > '
//                  . time()
//                  . ')';
//echo "<hr>{$sql}<hr>";


        if ($uid) $sql .= ' AND uid=' . (int)$uid;
        
        if ($checkRight) {
            $topics = NewsUtility::getMyItemIds('news_view');
            $topics = implode(',', $topics);
            if ('' !== xoops_trim($topics)) {
                $sql .= ' AND topicid IN (' . $topics . ')';
            }
        }
        //--------------------------------------
        
        switch ($orderby){   
        case NEWS_STORY_ORDER_BY_DATE_ASC: 
                $sqlOrder = "{$tblstory}.published ASC, {$tblstory}.title ASC"; 
                break;
        case NEWS_STORY_ORDER_BY_DATE_DESC: 
                $sqlOrder = "{$tblstory}.published DESC, {$tblstory}.title ASC"; 
                break;
        case NEWS_STORY_ORDER_BY_NB_VIEWS_ASC: 
                $sqlOrder = "{$tblstory}.counter ASC, {$tblstory}.published DESC" ; 
                break;
        case NEWS_STORY_ORDER_BY_NB_VIEWS_DESC:  
                $sqlOrder = "{$tblstory}.counter DESC, {$tblstory}.published DESC" ; 
                break;
        case NEWS_STORY_ORDER_BY_TITLE_DESC:  
                $sqlOrder = "{$tblstory}.title DESC, {$tblstory}.published DESC" ; 
                break;
        case NEWS_STORY_ORDER_BY_TITLE_ASC:  
        default: 
                $sqlOrder = "{$tblstory}.title ASC, {$tblstory}.published DESC" ; 
                break;
        }
        $sql .= " ORDER BY {$tbltopics}.topic_title ASC, {$sqlOrder}";
       // echo "<hr>sqlOrder = : {$orderby} - {$sqlOrder}<hr>";
       // echo "<hr>sql = :<br>{$sql}<hr>";
        
        $result = $db->query($sql);
        while ($myrow = $db->fetchArray($result)) {
            if ($asobject) {
                $ret[] = new NewsStory($myrow);
            } else {
                if ($myrow['nohtml']) {
                    $html = 0;
                } else {
                    $html = 1;
                }
                if ($myrow['nosmiley']) {
                    $smiley = 0;
                } else {
                    $smiley = 1;
                }
                $ret[$myrow['storyid']] = [
                    'title'       => $myts->displayTarea($myrow['title'], $html, $smiley, 1),
                    'subtitle'    => $myts->displayTarea($myrow['subtitle'], $html, $smiley, 1),
                    'topicid'     => (int)$myrow['topicid'],
                    'storyid'     => (int)$myrow['storyid'],
                    'hometext'    => $myts->displayTarea($myrow['hometext'], $html, $smiley, 1),
                    'counter'     => (int)$myrow['counter'],
                    'created'     => (int)$myrow['created'],
                    'topic_title' => $myts->displayTarea($myrow['topic_title'], $html, $smiley, 1),
                    'topic_color' => $myts->displayTarea($myrow['topic_color']),
                    'topic_color_set' => $myrow['topic_color_set'],
                    'published'   => (int)$myrow['published'],
                    'rating'      => (float)$myrow['rating'],
                    'votes'       => (int)$myrow['votes']
                ];
            }
        }

        return $ret;
    }

    /**
     * Get all expired stories
     * @param  int  $limit
     * @param  int  $start
     * @param  int  $topic
     * @param  int  $ihome
     * @param  bool $asobject
     * @return array
     */
    public static function getAllExpired($limit = 0, $start = 0, $topic = 0, $ihome = 0, $asobject = true)
    {
        $db   = XoopsDatabaseFactory::getDatabaseConnection();
        $myts = MyTextSanitizer::getInstance();
        $ret  = [];
        $sql  = 'SELECT * FROM ' . $db->prefix('news_stories') . ' WHERE expired <= ' . time() . ' AND expired > 0';
        if (!empty($topic)) {
            $sql .= ' AND topicid=' . (int)$topic . ' AND (ihome=1 OR ihome=0)';
        } else {
            if (0 == (int)$ihome) {
                $sql .= ' AND ihome=0';
            }
        }

        $sql    .= ' ORDER BY expired DESC';
        $result = $db->query($sql, (int)$limit, (int)$start);
        while ($myrow = $db->fetchArray($result)) {
            if ($asobject) {
                $ret[] = new NewsStory($myrow);
            } else {
                $ret[$myrow['storyid']] = $myts->htmlSpecialChars($myrow['title']);
            }
        }

        return $ret;
    }

    /**
     * Returns an array of object containing all the news to be automatically published.
     * @param  int  $limit
     * @param  bool $asobject
     * @param  int  $start
     * @return array
     */
    public static function getAllAutoStory($limit = 0, $asobject = true, $start = 0)
    {
        $db     = XoopsDatabaseFactory::getDatabaseConnection();
        $myts   = MyTextSanitizer::getInstance();
        $ret    = [];
        $sql    = 'SELECT * FROM ' . $db->prefix('news_stories') . ' WHERE published > ' . time() . ' ORDER BY published ASC';
        $result = $db->query($sql, (int)$limit, (int)$start);
        while ($myrow = $db->fetchArray($result)) {
            if ($asobject) {
                $ret[] = new NewsStory($myrow);
            } else {
                $ret[$myrow['storyid']] = $myts->htmlSpecialChars($myrow['title']);
            }
        }

        return $ret;
    }

    /**
     * Get all submitted stories awaiting approval
     *
     * @param int     $limit      Denotes where to start the query
     * @param boolean $asobject   true will returns the stories as an array of objects, false will return storyid => title
     * @param boolean $checkRight whether to check the user's rights to topics
     *
     * @param int     $start
     *
     * @return array
     */
    public static function getAllSubmitted($limit = 0, $asobject = true, $checkRight = false, $start = 0)
    {
        $db       = XoopsDatabaseFactory::getDatabaseConnection();
        $myts     = MyTextSanitizer::getInstance();
        $ret      = [];
        $criteria = new CriteriaCompo(new Criteria('published', 0));
        if ($checkRight) {
            global $xoopsUser;
            if (!is_object($xoopsUser)) {
                return $ret;
            }
            $allowedtopics = NewsUtility::getMyItemIds('news_approve');
            $criteria2     = new CriteriaCompo();
            foreach ($allowedtopics as $key => $topicid) {
                $criteria2->add(new Criteria('topicid', $topicid), 'OR');
            }
            $criteria->add($criteria2);
        }
        $sql    = 'SELECT s.*, t.* FROM ' . $db->prefix('news_stories') . ' s, ' . $db->prefix('news_topics') . ' t ';
        $sql    .= ' ' . $criteria->renderWhere() . ' AND (s.topicid=t.topic_id) ORDER BY created DESC';
        $result = $db->query($sql, (int)$limit, (int)$start);
        while ($myrow = $db->fetchArray($result)) {
            if ($asobject) {
                $ret[] = new NewsStory($myrow);
            } else {
                $ret[$myrow['storyid']] = $myts->htmlSpecialChars($myrow['title']);
            }
        }

        return $ret;
    }

    /**
     * Used in the module's admin to know the number of expired, automated or pubilshed news
     *
     * @param int  $storytype  1=Expired, 2=Automated, 3=New submissions, 4=Last published stories
     * @param bool $checkRight verify permissions or not ?
     *
     * @return int
     */
    public static function getAllStoriesCount($storytype = 1, $checkRight = false)
    {
        $db  = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = 'SELECT count(*) AS cpt FROM ' . $db->prefix('news_stories') . ' WHERE ';
        switch ($storytype) {
            case 1: // Expired
                $sql .= '(expired <= ' . time() . ' AND expired >0)';
                break;
            case 2: // Automated
                $sql .= '(published > ' . time() . ')';
                break;
            case 3: // New submissions
                $sql .= '(published = 0)';
                break;
            case 4: // Last published stories
                $sql .= '(published > 0 AND published <= ' . time() . ') AND (expired = 0 OR expired > ' . time() . ')';
                break;
        }
        if ($checkRight) {
            $topics = NewsUtility::getMyItemIds('news_view');
            if (count($topics) > 0) {
                $topics = implode(',', $topics);
                $sql    .= ' AND topicid IN (' . $topics . ')';
            } else {
                return 0;
            }
        }
        $result = $db->query($sql);
        $myrow  = $db->fetchArray($result);

        return $myrow['cpt'];
    }

    /**
     * Get a list of stories (as objects) related to a specific topic
     * @param        $topicid
     * @param  int   $limit
     * @return array
     */
    public static function getByTopic($topicid, $limit = 0)
    {
        $ret    = [];
        $db     = XoopsDatabaseFactory::getDatabaseConnection();
        $sql    = 'SELECT * FROM ' . $db->prefix('news_stories') . ' WHERE topicid=' . (int)$topicid . ' ORDER BY published DESC';
        $result = $db->query($sql, (int)$limit, 0);
        while ($myrow = $db->fetchArray($result)) {
            $ret[] = new NewsStory($myrow);
        }

        return $ret;
    }

    /**
     * Count the number of news published for a specific topic
     * @param  int  $topicid
     * @param  bool $checkRight
     * @return null
     */
    public static function countPublishedByTopic($topicid = 0, $checkRight = false)
    {
        $db  = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = 'SELECT COUNT(*) FROM ' . $db->prefix('news_stories') . ' WHERE published > 0 AND published <= ' . time() . ' AND (expired = 0 OR expired > ' . time() . ')';
        if (!empty($topicid)) {
            $sql .= ' AND topicid=' . (int)$topicid;
        } else {
            $sql .= ' AND ihome=0';
            if ($checkRight) {
                $topics = NewsUtility::getMyItemIds('news_view');
                if (count($topics) > 0) {
                    $topics = implode(',', $topics);
                    $sql    .= ' AND topicid IN (' . $topics . ')';
                } else {
                    return null;
                }
            }
        }
        $result = $db->query($sql);
        list($count) = $db->fetchRow($result);

        return $count;
    }

    /**
     * Internal function
     */
    public function adminlink()
    {
        global $xoopsModule;
        $dirname = basename(dirname(__DIR__));
        /** @var XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $module        = $moduleHandler->getByDirname($dirname);
        $pathIcon16    = \Xmf\Module\Admin::iconUrl('', 16);

        $ret = '&nbsp; <a href="'
               . XOOPS_URL
               . '/modules/news/submit.php?op=edit&amp;storyid='
               . $this->storyid()
               . '"><img src="'
               . $pathIcon16
               . '/edit.png" title="'
               . _MD_NEWS_EDIT
               . '"></a>'
               . '<a href="'
               . XOOPS_URL
               . '/modules/news/admin/index.php?op=delete&amp;storyid='
               . $this->storyid()
               . '"><img src="'
               . $pathIcon16
               . '/delete.png" title="'
               . _MD_NEWS_DELETE . ""
               . '"></a> &nbsp;';

        return $ret;
    }

    /**
     * Get the topic image url
     * @param string $format
     * @return
     */
    public function topic_imgurl($format = 'S')
    {
        if ('' === trim($this->topic_imgurl)) {
            $this->topic_imgurl = 'blank.png';
        }
        $myts = MyTextSanitizer::getInstance();
        switch ($format) {
            case 'S':
                $imgurl = $myts->htmlSpecialChars($this->topic_imgurl);
                break;
            case 'E':
                $imgurl = $myts->htmlSpecialChars($this->topic_imgurl);
                break;
            case 'P':
                $imgurl = $myts->stripSlashesGPC($this->topic_imgurl);
                $imgurl = $myts->htmlSpecialChars($imgurl);
                break;
            case 'F':
                $imgurl = $myts->stripSlashesGPC($this->topic_imgurl);
                $imgurl = $myts->htmlSpecialChars($imgurl);
                break;
        }

        return $imgurl;
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function topic_title($format = 'S')
    {
        $myts = MyTextSanitizer::getInstance();
        switch ($format) {
            case 'S':
                $title = $myts->htmlSpecialChars($this->topic_title);
                break;
            case 'E':
                $title = $myts->htmlSpecialChars($this->topic_title);
                break;
            case 'P':
                $title = $myts->stripSlashesGPC($this->topic_title);
                $title = $myts->htmlSpecialChars($title);
                break;
            case 'F':
                $title = $myts->stripSlashesGPC($this->topic_title);
                $title = $myts->htmlSpecialChars($title);
                break;
        }

        return $title;
    }

    /**
     * @return string
     */
    public function imglink()
    {
        $ret = '';
        if ('' !== $this->topic_imgurl()
            && file_exists(XOOPS_ROOT_PATH . '/uploads/news/image/' . $this->topic_imgurl())) {
            $ret = "<a href='" . XOOPS_URL . '/modules/news/index.php?storytopic=' . $this->topicid() 
                 . "'><img src='" . XOOPS_URL . '/uploads/news/image/' . $this->topic_imgurl() 
                 . "' alt='" . $this->topic_title() . "' style='max-width:150px;' hspace='10' vspace='10' align='" 
                 . $this->topicalign() . "'></a>";
        }

        return $ret;
    }

    /**
     * @return string
     */
    public function textlink()
    {
        $ret = '<a title=' . $this->topic_title() . " href='" . XOOPS_URL . '/modules/news/index.php?storytopic=' . $this->topicid() . "'>" . $this->topic_title() . '</a>';

        return $ret;
    }

    /**
     * Function used to prepare an article to be showned
     * @param $filescount
     * @return array
     */
    public function prepare2show($filescount)
    {
        require_once XOOPS_ROOT_PATH . '/modules/news/class/utility.php';
        global $xoopsUser, $xoopsConfig, $xoopsModuleConfig, $xoopsModule;

        $dirname = basename(dirname(__DIR__));
        /** @var XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $module        = $moduleHandler->getByDirname($dirname);
        $pathIcon16    = \Xmf\Module\Admin::iconUrl('', 16);

        $myts                 = MyTextSanitizer::getInstance();
        $infotips             = NewsUtility::getModuleOption('infotips');
        $story                = [];
        $story['id']          = $this->storyid();
        $story['poster']      = $this->uname();
        $story['author_name'] = $this->uname();
        $story['author_uid']  = $this->uid();
        if (false !== $story['poster']) {
            $story['poster'] = "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $this->uid() . "'>" . $story['poster'] . '</a>';
        } else {
            if (3 != $xoopsModuleConfig['displayname']) {
                $story['poster'] = $xoopsConfig['anonymous'];
            }
        }
        if ($xoopsModuleConfig['ratenews']) {
            $story['rating'] = number_format($this->rating(), 2);
            if (1 == $this->votes) {
                $story['votes'] = _MD_NEWS_ONEVOTE;
            } else {
                $story['votes'] = sprintf(_MD_NEWS_NUMVOTES, $this->votes);
            }
        }
        $story['posttimestamp']     = $this->published();
        $story['posttime']          = formatTimestamp($story['posttimestamp'], NewsUtility::getModuleOption('dateformat'));
        $story['topic_description'] = $myts->displayTarea($this->topic_description);

        $auto_summary = '';
        $tmp          = '';
        $auto_summary = $this->auto_summary($this->bodytext(), $tmp);

        $story['text'] = $this->hometext();
        $story['text'] = str_replace('[summary]', $auto_summary, $story['text']);
        //$story['picture'] = XOOPS_URL.'/uploads/news/image/'.$this->picture();
        if ('' !== $this->picture()) {
            $story['picture'] = XOOPS_URL . '/uploads/news/image/' . $this->picture();
        } else {
            $story['picture'] = '';
        }
        $story['pictureinfo'] = $this->pictureinfo();

        $introcount = strlen($story['text']);
        $fullcount  = strlen($this->bodytext());
        $totalcount = $introcount + $fullcount;

        //-JJDai - Simplification du code         ----------------------------
        $linkStory = XOOPS_URL . '/modules/news/article.php?storyid=' . $this->storyid();
        $morelink = '';

        //si body n'est pas vide on ajoute un lien sur l'article complet
        if ($fullcount > 1) {
            $morelink = "<a href='{$linkStory}'><b>" . _MD_NEWS_READMORE . "</b><img src='{$pathIcon16}/forward.png'>" .  "</a>";
            $morelink2 = "<a href='{$linkStory}'>" . " <img src='{$pathIcon16}/forward.png'> <b>" . _MD_NEWS_READMORE .  "</b></a>";
            //---------------------------
            //JJDai - ajout dans a la fin du exte de readmore
            $posReadMore = strripos($story['text'], NewsUtility::getModuleOption('readmore'));
            $lgReadMore = strlen(_MD_NEWS_READMORE);
            if ($posReadMore > ($introcount - NEWS_NB_LAST_CAR_TO_READ)){
            $story['text'] = substr($story['text'],0,$posReadMore) . $morelink2 . substr($story['text'], $posReadMore + $lgReadMore);
            }
            // pour des tests$story['text'] .= " ({$posReadMore})";
            //-----------------------------

            //JJDai inutile d'ajouter le nombre d'octet qui n'est pas uneinformation pertinente
            //$morelink .= ' | ' . sprintf(_MD_NEWS_BYTESMORE, $totalcount);
        }

        if (XOOPS_COMMENT_APPROVENONE != $xoopsModuleConfig['com_rule']) {
            $ccount    = $this->comments();
            if ($ccount == 0){
                $libComment = _MD_NEWS_COMMENTS;
            }elseif ($ccount == 1){
                $libComment = _MD_NEWS_ONECOMMENT;
            }else{
                $libComment = sprintf(_MD_NEWS_NUMCOMMENTS, $ccount);
            }
            $morelink .= "<br><a href='{$linkStory}'>" . $libComment . "</a>";
        }
        //--------------------------------------------

        $story['morelink']  = $morelink;
        $story['adminlink'] = '';

        $approveprivilege = 0;
        if (NewsUtility::isAdminGroup()) {
            $approveprivilege = 1;
        }

        if (1 == $xoopsModuleConfig['authoredit']
            && (is_object($xoopsUser)
                && $xoopsUser->getVar('uid') == $this->uid())) {
            $approveprivilege = 1;
        }
        if ($approveprivilege) {
            $story['adminlink'] = $this->adminlink();
        }
        $story['mail_link'] = 'mailto:?subject=' . sprintf(_MD_NEWS_INTARTICLE, $xoopsConfig['sitename']) . '&amp;body=' . sprintf(_MD_NEWS_INTARTFOUND, $xoopsConfig['sitename']) . ':  ' . XOOPS_URL . '/modules/news/article.php?storyid=' . $this->storyid();
        $story['imglink']   = '';
        $story['align']     = '';
        if ($this->topicdisplay()) {
            $story['imglink'] = $this->imglink();
            $story['align']   = $this->topicalign();
        }
        if ($infotips > 0) {
            $story['infotips'] = ' title="' . NewsUtility::makeInfotips($this->hometext()) . '"';
        } else {
            $story['infotips'] = ' title="' . $this->title() . '"';
        }
        //$story['title'] = $this->title(); //JJDai
        $story['url_to_story'] = XOOPS_URL . '/modules/news/article.php?storyid=' . $this->storyid();
        $story['title'] = "<a href='" . $story['url_to_story'] . "'" . $story['infotips'] . '>' . $this->title() . '</a>';
        $story['title_url'] = "<a href='" . $story['url_to_story'] . "'" . $story['infotips'] . '>' . $this->title() . '</a>';
        //$story['subtitle'] = $this->subtitle();

        $story['hits'] = $this->counter();
        if ($filescount > 0) {
            $story['files_attached'] = true;
            $story['attached_link']  = "<a href='" . $story['url_to_story'] . "' title='" . _MD_NEWS_ATTACHEDLIB . "'><img src=\"" . $pathIcon16 . '/attach.png" title="' . _MD_NEWS_ATTACHEDLIB . '"></a>';
        } else {
            $story['files_attached'] = false;
            $story['attached_link']  = '';
        }

        return $story;
    }

    /**
     * Returns the user's name of the current story according to the module's option "displayname"
     * @param  int $uid
     * @return null|string
     */
    public function uname($uid = 0)
    {
        global $xoopsConfig;
        require_once XOOPS_ROOT_PATH . '/modules/news/class/utility.php';
        static $tblusers = [];
        $option = -1;
        if (0 == $uid) {
            $uid = $this->uid();
        }

        if (is_array($tblusers) && array_key_exists($uid, $tblusers)) {
            return $tblusers[$uid];
        }

        $option = NewsUtility::getModuleOption('displayname');
        if (!$option) {
            $option = 1;
        }

        switch ($option) {
            case 1: // Username
                $tblusers[$uid] = XoopsUser::getUnameFromId($uid);

                return $tblusers[$uid];

            case 2: // Display full name (if it is not empty)
                $memberHandler = xoops_getHandler('member');
                $thisuser      = $memberHandler->getUser($uid);
                if (is_object($thisuser)) {
                    $return = $thisuser->getVar('name');
                    if ('' === $return) {
                        $return = $thisuser->getVar('uname');
                    }
                } else {
                    $return = $xoopsConfig['anonymous'];
                }
                $tblusers[$uid] = $return;

                return $return;

            case 3: // Nothing
                $tblusers[$uid] = '';

                return '';
        }

        return null;
    }

    /**
     * Function used to export news (in xml) and eventually the topics definitions
     * Warning, permissions are not exported !
     *
     * @param int      $fromdate     Starting date
     * @param int      $todate       Ending date
     * @param string   $topicslist
     * @param bool|int $usetopicsdef Should we also export topics definitions ?
     * @param          $tbltopics
     * @param boolean  $asobject     Return values as an object or not ?
     *
     * @param string   $order
     *
     * @internal param string $topiclist If not empty, a list of topics to limit to
     * @return array
     */
    public function exportNews(
        $fromdate,
        $todate,
        $topicslist = '',
        $usetopicsdef = 0,
        &$tbltopics = null,
        $asobject = true,
        $order = 'published'
    ) {
        $ret  = [];
        $myts = MyTextSanitizer::getInstance();
        if ($usetopicsdef) { // We firt begin by exporting topics definitions
            // Before all we must know wich topics to export
            $sql = 'SELECT DISTINCT topicid FROM ' . $this->db->prefix('news_stories') . ' WHERE (published >=' . $fromdate . ' AND published <= ' . $todate . ')';
            if (strlen(trim($topicslist)) > 0) {
                $sql .= ' AND topicid IN (' . $topicslist . ')';
            }
            $result = $this->db->query($sql);
            while ($myrow = $this->db->fetchArray($result)) {
                $tbltopics[] = $myrow['topicid'];
            }
        }

        // Now we can search for the stories
        $sql = 'SELECT s.*, t.* FROM ' . $this->table . ' s, ' . $this->db->prefix('news_topics') . ' t WHERE (s.topicid=t.topic_id) AND (s.published >=' . $fromdate . ' AND s.published <= ' . $todate . ')';
        if (strlen(trim($topicslist)) > 0) {
            $sql .= ' AND topicid IN (' . $topicslist . ')';
        }
        $sql    .= " ORDER BY $order DESC";
        $result = $this->db->query($sql);
        while ($myrow = $this->db->fetchArray($result)) {
            if ($asobject) {
                $ret[] = new NewsStory($myrow);
            } else {
                $ret[$myrow['storyid']] = $myts->htmlSpecialChars($myrow['title']);
            }
        }

        return $ret;
    }

    /**
     * Create or update an article
     * @param  bool $approved
     * @return bool|int
     */
    public function store($approved = false)
    {
        $myts        = MyTextSanitizer::getInstance();
        $counter     = isset($this->counter) ? $this->counter : 0;
        $title       = $myts->addSlashes($myts->censorString($this->title));
        $subtitle    = $myts->addSlashes($myts->censorString($this->subtitle));
        $hostname    = $myts->addSlashes($this->hostname);
        $type        = $myts->addSlashes($this->type);
        $hometext    = $myts->addSlashes($myts->censorString($this->hometext));
        $bodytext    = $myts->addSlashes($myts->censorString($this->bodytext));
        $description = $myts->addSlashes($myts->censorString($this->description));
        $keywords    = $myts->addSlashes($myts->censorString($this->keywords));
        $picture     = $myts->addSlashes($this->picture);
        $pictureinfo = $myts->addSlashes($myts->censorString($this->pictureinfo));
        $votes       = (int)$this->votes;
        $rating      = (float)$this->rating;
        if (!isset($this->nohtml) || 1 != $this->nohtml) {
            $this->nohtml = 0;
        }
        if (!isset($this->nosmiley) || 1 != $this->nosmiley) {
            $this->nosmiley = 0;
        }
        if (!isset($this->notifypub) || 1 != $this->notifypub) {
            $this->notifypub = 0;
        }
        if (!isset($this->topicdisplay) || 0 != $this->topicdisplay) {
            $this->topicdisplay = 1;
        }
        $expired = !empty($this->expired) ? $this->expired : 0;
        if (!isset($this->storyid)) {
            //$newpost = 1;
            $newstoryid = $this->db->genId($this->table . '_storyid_seq');
            $created    = time();
            $published  = $this->approved ? (int)$this->published : 0;
            $sql        = sprintf(
                "INSERT INTO %s (storyid, uid, title, created, published, expired, hostname, nohtml, nosmiley, hometext, bodytext, counter, topicid, ihome, notifypub, story_type, topicdisplay, topicalign, comments, rating, votes, description, keywords, picture, pictureinfo, subtitle) VALUES (%u, %u, '%s', %u, %u, %u, '%s', %u, %u, '%s', '%s', %u, %u, %u, %u, '%s', %u, '%s', %u, %u, %u, '%s', '%s', '%s', '%s', '%s')",
                                  $this->table,
                $newstoryid,
                (int)$this->uid(),
                $title,
                $created,
                $published,
                $expired,
                $hostname,
                (int)$this->nohtml(),
                (int)$this->nosmiley(),
                $hometext,
                $bodytext,
                $counter,
                (int)$this->topicid(),
                (int)$this->ihome(),
                (int)$this->notifypub(),
                $type,
                                  (int)$this->topicdisplay(),
                $this->topicalign,
                (int)$this->comments(),
                $rating,
                $votes,
                $description,
                $keywords,
                $picture,
                $pictureinfo,
                $subtitle
            );
        } else {
            $sql        = sprintf(
                "UPDATE %s SET title='%s', published=%u, expired=%u, nohtml=%u, nosmiley=%u, hometext='%s', bodytext='%s', topicid=%u, ihome=%u, topicdisplay=%u, topicalign='%s', comments=%u, rating=%u, votes=%u, uid=%u, description='%s', keywords='%s', picture='%s' , pictureinfo='%s' , subtitle='%s' WHERE storyid = %u",
                                  $this->table,
                $title,
                (int)$this->published(),
                $expired,
                (int)$this->nohtml(),
                (int)$this->nosmiley(),
                $hometext,
                $bodytext,
                (int)$this->topicid(),
                (int)$this->ihome(),
                (int)$this->topicdisplay(),
                $this->topicalign,
                (int)$this->comments(),
                $rating,
                $votes,
                                  (int)$this->uid(),
                $description,
                $keywords,
                $picture,
                $pictureinfo,
                $subtitle,
                (int)$this->storyid()
            );
            $newstoryid = (int)$this->storyid();
        }
        if (!$this->db->queryF($sql)) {
            return false;
        }
        if (empty($newstoryid)) {
            $newstoryid    = $this->db->getInsertId();
            $this->storyid = $newstoryid;
        }

        return $newstoryid;
    }

    /**
     * @return mixed
     */
    public function picture()
    {
        return $this->picture;
    }

    /**
     * @return mixed
     */
    public function pictureinfo()
    {
        return $this->pictureinfo;
    }

//     /**
//      * @return mixed
//      */
//     public function subtitle()
//     {
//         return $this->subtitle;
//     }

    /**
     * @return mixed
     */
    public function rating()
    {
        return $this->rating;
    }

    /**
     * @return mixed
     */
    public function votes()
    {
        return $this->votes;
    }

    /**
     * @param $data
     */
    public function setPicture($data)
    {
        $this->picture = $data;
    }

    /**
     * @param $data
     */
    public function setPictureinfo($data)
    {
        $this->pictureinfo = $data;
    }

    /**
     * @param $data
     */
    public function setSubtitle($data)
    {
        $this->subtitle = $data;
    }

    /**
     * @param $data
     */
    public function setDescription($data)
    {
        $this->description = $data;
    }

    /**
     * @param $data
     */
    public function setKeywords($data)
    {
        $this->keywords = $data;
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function description($format = 'S')
    {
        $myts = MyTextSanitizer::getInstance();
        switch (strtoupper($format)) {
            case 'S':
                $description = $myts->htmlSpecialChars($this->description);
                break;
            case 'P':
            case 'F':
                $description = $myts->htmlSpecialChars($myts->stripSlashesGPC($this->description));
                break;
            case 'E':
                $description = $myts->htmlSpecialChars($this->description);
                break;
        }

        return $description;
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function keywords($format = 'S')
    {
        $myts = MyTextSanitizer::getInstance();
        switch (strtoupper($format)) {
            case 'S':
                $keywords = $myts->htmlSpecialChars($this->keywords);
                break;
            case 'P':
            case 'F':
                $keywords = $myts->htmlSpecialChars($myts->stripSlashesGPC($this->keywords));
                break;
            case 'E':
                $keywords = $myts->htmlSpecialChars($this->keywords);
                break;
        }

        return $keywords;
    }

    /**
     * Returns a random number of news
     * @param  int    $limit
     * @param  int    $start
     * @param  bool   $checkRight
     * @param  int    $topic
     * @param  int    $ihome
     * @param  string $order
     * @param  bool   $topic_frontpage
     * @return array
     */
    public function getRandomNews(
        $limit = 0,
        $start = 0,
        $checkRight = false,
        $topic = 0,
        $ihome = 0,
        $order = 'published',
        $topic_frontpage = false
    ) {
        $db  = XoopsDatabaseFactory::getDatabaseConnection();
        $ret = $rand_keys = $ret3 = [];
        $sql = 'SELECT storyid FROM ' . $db->prefix('news_stories') . ' WHERE (published > 0 AND published <= ' . time() . ') AND (expired = 0 OR expired > ' . time() . ')';
        if (0 != $topic) {
            if (!is_array($topic)) {
                if ($checkRight) {
                    $topics = NewsUtility::getMyItemIds('news_view');
                    if (!in_array($topic, $topics)) {
                        return null;
                    } else {
                        $sql .= ' AND topicid=' . (int)$topic . ' AND (ihome=1 OR ihome=0)';
                    }
                } else {
                    $sql .= ' AND topicid=' . (int)$topic . ' AND (ihome=1 OR ihome=0)';
                }
            } else {
                if (count($topic) > 0) {
                    $sql .= ' AND topicid IN (' . implode(',', $topic) . ')';
                } else {
                    return null;
                }
            }
        } else {
            if ($checkRight) {
                $topics = NewsUtility::getMyItemIds('news_view');
                if (count($topics) > 0) {
                    $topics = implode(',', $topics);
                    $sql    .= ' AND topicid IN (' . $topics . ')';
                } else {
                    return null;
                }
            }
            if (0 == (int)$ihome) {
                $sql .= ' AND ihome=0';
            }
        }
        if ($topic_frontpage) {
            $sql .= ' AND t.topic_frontpage=1';
        }
        $sql    .= " ORDER BY $order DESC";
        $result = $db->query($sql);

        while ($myrow = $db->fetchArray($result)) {
            $ret[] = $myrow['storyid'];
        }
        $cnt = count($ret);
        if ($cnt) {
            mt_srand((double)microtime() * 10000000);
            if ($limit > $cnt) {
                $limit = $cnt;
            }
            $rand_keys = array_rand($ret, $limit);
            if ($limit > 1) {
                for ($i = 0; $i < $limit; ++$i) {
                    $onestory = $ret[$rand_keys[$i]];
                    $ret3[]   = new NewsStory($onestory);
                }
            } else {
                $ret3[] = new NewsStory($ret[$rand_keys]);
            }
        }

        return $ret3;
    }

    /**
     * Returns statistics about the stories and topics
     * @param $limit
     * @return array
     */
    public function getStats($limit)
    {
        $ret  = [];
        $db   = XoopsDatabaseFactory::getDatabaseConnection();
        $tbls = $db->prefix('news_stories');
        $tblt = $db->prefix('news_topics');
        $tblf = $db->prefix('news_stories_files');

        $db = XoopsDatabaseFactory::getDatabaseConnection();
        // Number of stories per topic, including expired and non published stories
        $ret2   = [];
        $sql    = "SELECT count(s.storyid) as cpt, s.topicid, t.topic_title FROM $tbls s, $tblt t WHERE s.topicid=t.topic_id GROUP BY s.topicid ORDER BY t.topic_title";
        $result = $db->query($sql);
        while ($myrow = $db->fetchArray($result)) {
            $ret2[$myrow['topicid']] = $myrow;
        }
        $ret['storiespertopic'] = $ret2;
        unset($ret2);

        // Total of reads per topic
        $ret2   = [];
        $sql    = "SELECT Sum(counter) as cpt, topicid FROM $tbls GROUP BY topicid ORDER BY topicid";
        $result = $db->query($sql);
        while ($myrow = $db->fetchArray($result)) {
            $ret2[$myrow['topicid']] = $myrow['cpt'];
        }
        $ret['readspertopic'] = $ret2;
        unset($ret2);

        // Attached files per topic
        $ret2   = [];
        $sql    = "SELECT Count(*) as cpt, s.topicid FROM $tblf f, $tbls s WHERE f.storyid=s.storyid GROUP BY s.topicid ORDER BY s.topicid";
        $result = $db->query($sql);
        while ($myrow = $db->fetchArray($result)) {
            $ret2[$myrow['topicid']] = $myrow['cpt'];
        }
        $ret['filespertopic'] = $ret2;
        unset($ret2);

        // Expired articles per topic
        $ret2   = [];
        $sql    = "SELECT Count(storyid) as cpt, topicid FROM $tbls WHERE expired>0 AND expired<=" . time() . ' GROUP BY topicid ORDER BY topicid';
        $result = $db->query($sql);
        while ($myrow = $db->fetchArray($result)) {
            $ret2[$myrow['topicid']] = $myrow['cpt'];
        }
        $ret['expiredpertopic'] = $ret2;
        unset($ret2);

        // Number of unique authors per topic
        $ret2   = [];
        $sql    = "SELECT Count(Distinct(uid)) as cpt, topicid FROM $tbls GROUP BY topicid ORDER BY topicid";
        $result = $db->query($sql);
        while ($myrow = $db->fetchArray($result)) {
            $ret2[$myrow['topicid']] = $myrow['cpt'];
        }
        $ret['authorspertopic'] = $ret2;
        unset($ret2);

        // Most readed articles
        $ret2   = [];
        $sql    = "SELECT s.storyid, s.uid, s.title, s.counter, s.topicid, t.topic_title  FROM $tbls s, $tblt t WHERE s.topicid=t.topic_id ORDER BY s.counter DESC";
        $result = $db->query($sql, (int)$limit);
        while ($myrow = $db->fetchArray($result)) {
            $ret2[$myrow['storyid']] = $myrow;
        }
        $ret['mostreadednews'] = $ret2;
        unset($ret2);

        // Less readed articles
        $ret2   = [];
        $sql    = "SELECT s.storyid, s.uid, s.title, s.counter, s.topicid, t.topic_title  FROM $tbls s, $tblt t WHERE s.topicid=t.topic_id ORDER BY s.counter";
        $result = $db->query($sql, (int)$limit);
        while ($myrow = $db->fetchArray($result)) {
            $ret2[$myrow['storyid']] = $myrow;
        }
        $ret['lessreadednews'] = $ret2;
        unset($ret2);

        // Best rated articles
        $ret2   = [];
        $sql    = "SELECT s.storyid, s.uid, s.title, s.rating, s.topicid, t.topic_title  FROM $tbls s, $tblt t WHERE s.topicid=t.topic_id ORDER BY s.rating DESC";
        $result = $db->query($sql, (int)$limit);
        while ($myrow = $db->fetchArray($result)) {
            $ret2[$myrow['storyid']] = $myrow;
        }
        $ret['besratednews'] = $ret2;
        unset($ret2);

        // Most readed authors
        $ret2   = [];
        $sql    = "SELECT Sum(counter) as cpt, uid FROM $tbls GROUP BY uid ORDER BY cpt DESC";
        $result = $db->query($sql, (int)$limit);
        while ($myrow = $db->fetchArray($result)) {
            $ret2[$myrow['uid']] = $myrow['cpt'];
        }
        $ret['mostreadedauthors'] = $ret2;
        unset($ret2);

        // Best rated authors
        $ret2   = [];
        $sql    = "SELECT Avg(rating) as cpt, uid FROM $tbls WHERE votes > 0 GROUP BY uid ORDER BY cpt DESC";
        $result = $db->query($sql, (int)$limit);
        while ($myrow = $db->fetchArray($result)) {
            $ret2[$myrow['uid']] = $myrow['cpt'];
        }
        $ret['bestratedauthors'] = $ret2;
        unset($ret2);

        // Biggest contributors
        $ret2   = [];
        $sql    = "SELECT Count(*) as cpt, uid FROM $tbls GROUP BY uid ORDER BY cpt DESC";
        $result = $db->query($sql, (int)$limit);
        while ($myrow = $db->fetchArray($result)) {
            $ret2[$myrow['uid']] = $myrow['cpt'];
        }
        $ret['biggestcontributors'] = $ret2;
        unset($ret2);

        return $ret;
    }

    /**
     * Get the date of the older and most recent news
     * @param $older
     * @param $recent
     */
    public function getOlderRecentNews(&$older, &$recent)
    {
        $db     = XoopsDatabaseFactory::getDatabaseConnection();
        $sql    = 'SELECT min(published) AS minpublish, max(published) AS maxpublish FROM ' . $db->prefix('news_stories');
        $result = $db->query($sql);
        if (!$result) {
            $older = $recent = 0;
        } else {
            list($older, $recent) = $this->db->fetchRow($result);
        }
    }

    /*
     * Returns the author's IDs for the Who's who page
     */
    /**
     * @param bool $checkRight
     * @param int  $limit
     * @param int  $start
     *
     * @return array|null
     */
    public function getWhosWho($checkRight = false, $limit = 0, $start = 0)
    {
        $db  = XoopsDatabaseFactory::getDatabaseConnection();
        $ret = [];
        $sql = 'SELECT DISTINCT(uid) AS uid FROM ' . $db->prefix('news_stories') . ' WHERE (published > 0 AND published <= ' . time() . ') AND (expired = 0 OR expired > ' . time() . ')';
        if ($checkRight) {
            $topics = NewsUtility::getMyItemIds('news_view');
            if (count($topics) > 0) {
                $topics = implode(',', $topics);
                $sql    .= ' AND topicid IN (' . $topics . ')';
            } else {
                return null;
            }
        }
        $sql    .= ' ORDER BY uid';
        $result = $db->query($sql);
        while ($myrow = $db->fetchArray($result)) {
            $ret[] = $myrow['uid'];
        }

        return $ret;
    }

    /**
     * Returns the content of the summary and the titles requires for the list selector
     * @param $text
     * @param $titles
     * @return string
     */
    public function auto_summary($text, &$titles)
    {
        $auto_summary = '';
        if (NewsUtility::getModuleOption('enhanced_pagenav')) {
            $expr_matches = [];
            $posdeb       = preg_match_all('/(\[pagebreak:|\[pagebreak).*\]/iU', $text, $expr_matches);
            if (count($expr_matches) > 0) {
                $delimiters  = $expr_matches[0];
                $arr_search  = ['[pagebreak:', '[pagebreak', ']'];
                $arr_replace = ['', '', ''];
                $cpt         = 1;
                if (isset($titles) && is_array($titles)) {
                    $titles[] = strip_tags(sprintf(_MD_NEWS_PAGE_AUTO_SUMMARY, 1, $this->title()));
                }
                $item         = "<a href='" . XOOPS_URL . '/modules/news/article.php?storyid=' . $this->storyid() . "&page=0'>" . sprintf(_MD_NEWS_PAGE_AUTO_SUMMARY, 1, $this->title()) . '</a><br>';
                $auto_summary .= $item;

                foreach ($delimiters as $item) {
                    ++$cpt;
                    $item = str_replace($arr_search, $arr_replace, $item);
                    if ('' == xoops_trim($item)) {
                        $item = $cpt;
                    }
                    $titles[]     = strip_tags(sprintf(_MD_NEWS_PAGE_AUTO_SUMMARY, $cpt, $item));
                    $item         = "<a href='" . XOOPS_URL . '/modules/news/article.php?storyid=' . $this->storyid() . '&page=' . ($cpt - 1) . "'>" . sprintf(_MD_NEWS_PAGE_AUTO_SUMMARY, $cpt, $item) . '</a><br>';
                    $auto_summary .= $item;
                }
            }
        }

        return $auto_summary;
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function hometext($format = 'Show')
    {
        $myts = MyTextSanitizer::getInstance();
        $html = $smiley = $xcodes = 1;
        if ($this->nohtml()) {
            $html = 0;
        }
        if ($this->nosmiley()) {
            $smiley = 0;
        }
        switch ($format) {
            case 'Show':
                $hometext     = $myts->displayTarea($this->hometext, $html, $smiley, $xcodes);
                $tmp          = '';
                $auto_summary = $this->auto_summary($this->bodytext('Show'), $tmp);
                $hometext     = str_replace('[summary]', $auto_summary, $hometext);
                break;
            case 'Edit':
                $hometext = $myts->htmlSpecialChars($this->hometext);
                break;
            case 'Preview':
                $hometext = $myts->previewTarea($this->hometext, $html, $smiley, $xcodes);
                break;
            case 'InForm':
                $hometext = $myts->stripSlashesGPC($this->hometext);
                $hometext = $myts->htmlSpecialChars($hometext);
                break;
        }

        return $hometext;
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function bodytext($format = 'Show')
    {
        $myts   = MyTextSanitizer::getInstance();
        $html   = 1;
        $smiley = 1;
        $xcodes = 1;
        if ($this->nohtml()) {
            $html = 0;
        }
        if ($this->nosmiley()) {
            $smiley = 0;
        }
        switch ($format) {
            case 'Show':
                $bodytext     = $myts->displayTarea($this->bodytext, $html, $smiley, $xcodes);
                $tmp          = '';
                $auto_summary = $this->auto_summary($bodytext, $tmp);
                $bodytext     = str_replace('[summary]', $auto_summary, $bodytext);
                break;
            case 'Edit':
                $bodytext = $myts->htmlSpecialChars($this->bodytext);
                break;
            case 'Preview':
                $bodytext = $myts->previewTarea($this->bodytext, $html, $smiley, $xcodes);
                break;
            case 'InForm':
                $bodytext = $myts->stripSlashesGPC($this->bodytext);
                $bodytext = $myts->htmlSpecialChars($bodytext);
                break;
        }

        return $bodytext;
    }

    /**
     * Returns stories by Ids
     * @param             $ids
     * @param  bool       $checkRight
     * @param  bool       $asobject
     * @param  string     $order
     * @param  bool       $onlyOnline
     * @return array|null
     */
    public function getStoriesByIds(
        $ids,
        $checkRight = true,
        $asobject = true,
        $order = 'published',
        $onlyOnline = true
    ) {
        $limit = $start = 0;
        $db    = XoopsDatabaseFactory::getDatabaseConnection();
        $myts  = MyTextSanitizer::getInstance();
        $ret   = [];
        $sql   = 'SELECT s.*, t.* FROM ' . $db->prefix('news_stories') . ' s, ' . $db->prefix('news_topics') . ' t WHERE ';
        if (is_array($ids) && count($ids) > 0) {
            array_walk($ids, 'intval');
        }
        $sql .= ' s.storyid IN (' . implode(',', $ids) . ') ';

        if ($onlyOnline) {
            $sql .= ' AND (s.published > 0 AND s.published <= ' . time() . ') AND (s.expired = 0 OR s.expired > ' . time() . ') ';
        }
        $sql .= ' AND (s.topicid=t.topic_id) ';
        if ($checkRight) {
            $topics = NewsUtility::getMyItemIds('news_view');
            if (count($topics) > 0) {
                $topics = implode(',', $topics);
                $sql    .= ' AND s.topicid IN (' . $topics . ')';
            } else {
                return null;
            }
        }
        $sql    .= " ORDER BY s.$order DESC";
        $result = $db->query($sql, $limit, $start);

        while ($myrow = $db->fetchArray($result)) {
            if ($asobject) {
                $ret[$myrow['storyid']] = new NewsStory($myrow);
            } else {
                $ret[$myrow['storyid']] = $myts->htmlSpecialChars($myrow['title']);
            }
        }

        return $ret;
    }
}

/**
 * Class news_NewsStoryHandler
 */
class news_NewsStoryHandler extends XoopsPersistableObjectHandler
{
    /**
     * @param null|XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'news_stories', 'stories', 'storieid', 'title');
    }
}
