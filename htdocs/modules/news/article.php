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

/**
 * Article's page
 *
 * This page is used to see an article (or story) and is mainly called from
 * the module's index page.
 *
 * If no story Id has been placed on the URL or if the story is not yet published
 * then the page will redirect user to the module's index.
 * If the user does not have the permissions to see the article, he is also redirected
 * to the module's index page but with a error message saying :
 *     "Sorry, you don't have the permission to access this area"
 *
 * Each time a page is seen, and only if we are on the first page, its counter of hits is
 * updated
 *
 * Each file(s) attached to the article is visible at the bottom of the article and can
 * be downloaded
 *
 * Notes :
 * - To create more than one page in your story, use the tag [pagebreak]
 * - If you are a module's admin, you have the possibility to see two links at the bottom
 *   of the article, "Edit & Delete"
 *
 * @package                         News
 * @author                          Xoops Modules Dev Team
 * @copyright (c)                   XOOPS Project (https://xoops.org)
 *
 * Parameters received by this page :
 *
 * @param int storyid    Id of the story we want to see
 * @param int page        page's number (in the case where there are more than one page)
 *
 * @page_title                      Article's title - Topic's title - Module's name
 *
 * @template_name                   news_article.html wich will call news_item.html
 *
 * Template's variables :
 * @template_var                    string    pagenav    some links to navigate thru pages
 * @template_var                    array    story    Contains all the information about the story
 *                                    Structure :
 * @template_var                    int        id            Story's ID
 * @template_var                    string    posttime    Story's date of publication
 * @template_var                    string    title        A link to go and see all the articles in the same topic and the story's title
 * @template_var                    string    news_title    Just the news title
 * @template_var                    string    topic_title    Just the topic's title
 * @template_var                    string    text        Defined as "The scoop"
 * @template_var                    string    poster        A link to see the author's profil and his name or "Anonymous"
 * @template_var                    int        posterid    Author's uid (or 0 if it's an anonymous or a user wich does not exist any more)
 * @template_var                    string    morelink    Never used ???? May be it could be deleted
 * @template_var                    string    adminlink    A link to Edit or Delete the story or a blank string if you are not the module's admin
 * @template_var                    string    topicid        News topic's Id
 * @template_var                    string    topic_color    Topic's color
 * @template_var                    string    imglink        A link to go and see the topic of the story with the topic's picture (if it exists)
 * @template_var                    string    align        Topic's image alignement
 * @template_var                    int        hits        Story's counter of visits
 * @template_var                    string    mail_link    A link (with a mailto) to email the story's URL to someone
 * @template_var                    string    lang_printerpage    Used in the link and picture to have a "printable version" (fixed text)
 * @template_var                    string    lang_on        Fixed text "On" ("published on")
 * @template_var                    string    lang_postedby    Fixed text "Posted by"
 * @template_var                    string    lang_reads    Fixed text "Reads"
 * @template_var                    string    news_by_the_same_author_link    According the the module's option named "newsbythisauthor", it contains a link to see all the article's stories
 * @template_var                    int        summary_count    Number of stories really visibles in the summary table
 * @template_var                    boolean    showsummary    According to the module's option named "showsummarytable", this contains "True" of "False"
 * @template_var                    array    summary    Contains the required information to create a summary table at the bottom of the article. Note, we use the module's option "storyhome" to determine the maximum number of stories visibles in this summary table
 *                                    Structure :
 * @template_var                    int        story_id        Story's ID
 * @template_var                    string    story_title        Story's title
 * @template_var                    int        story_hits        Counter of hits
 * @template_var                    string    story_published    Story's date of creation
 * @template_var                    string    lang_attached_files    Fixed text "Attached Files:"
 * @template_var                    int        attached_files_count    Number of files attached to the story
 * @template_var                    array    attached_files    Contains the list of all the files attached to the story
 *                                    Structure :
 * @template_var                    int        file_id                File's ID
 * @template_var                    string    visitlink            Link to download the file
 * @template_var                    string    file_realname        Original filename (not the real one use to store the file but the one it have when it was on the user hard disk)
 * @template_var                    string    file_attacheddate    Date to wich the file was attached to the story (in general that's equal to the article's creation date)
 * @template_var                    string    file_mimetype        File's mime type
 * @template_var                    string    file_downloadname    Real name of the file on the webserver's disk (changed by the module)
 * @template_var                    boolean    nav_links    According to the module's option named "showprevnextlink" it contains "True" or "False" to know if we have to show two links to go to the previous and next article
 * @template_var                    int        previous_story_id    Id of the previous story (according to the published date and to the perms)
 * @template_var                    int        next_story_id        Id of the next story (according to the published date and to the perms)
 * @template_var                    string    previous_story_title    Title of the previous story
 * @template_var                    string    next_story_title        Title of the next story
 * @template_var                    string    lang_previous_story        Fixed text "Previous article"
 * @template_var                    string    lang_next_story            Fixed text "Next article"
 * @template_var                    string    lang_other_story        Fixed text "Other articles"
 * @template_var                    boolean    rates    To know if rating is enable or not
 * @template_var                    string    lang_ratingc    Fixed text "Rating: "
 * @template_var                    string    lang_ratethisnews    Fixed text "Rate this News"
 * @template_var                    float    rating    Article's rating
 * @template_var                    string    votes    "1 vote" or "X votes"
 * @template_var                    string    topic_path    A path from the root to the current topic (of the current news)
 */
include_once ('../../mainfile.php');
require_once XOOPS_ROOT_PATH . '/modules/news/class/class.newsstory.php';
require_once XOOPS_ROOT_PATH . '/modules/news/class/class.sfiles.php';
require_once XOOPS_ROOT_PATH . '/modules/news/class/tree.php';
require_once XOOPS_ROOT_PATH . '/modules/news/class/utility.php';
require_once XOOPS_ROOT_PATH . '/modules/news/class/class.newstopic.php';
require_once XOOPS_ROOT_PATH . '/modules/news/class/keyhighlighter.class.php';
require_once XOOPS_ROOT_PATH . '/modules/news/config.php';
require_once XOOPS_ROOT_PATH . '/modules/news/include/functions.php';


$storyid = isset($_GET['storyid']) ? (int)$_GET['storyid'] : 0;

if (empty($storyid)) {
    redirect_header(XOOPS_URL . '/modules/news/index.php', 2, _MD_NEWS_NOSTORY);
}

/***********************************************************************/

$myts = MyTextSanitizer::getInstance();

// Not yet published
$article = new NewsStory($storyid);
if (0 == $article->published() || $article->published() > time()) {
    redirect_header(XOOPS_URL . '/modules/news/index.php', 2, _MD_NEWS_NOTYETSTORY);
}
/* JJDai / Bin non, il faut pouvoir aceder aux articles expir�s
// Expired
if (0 != $article->expired() && $article->expired() < time()) {
    redirect_header(XOOPS_URL . '/modules/news/index.php', 2, _MD_NEWS_NOSTORY);
}
*/

$gpermHandler = xoops_getHandler('groupperm');
if (is_object($xoopsUser)) {
    $groups = $xoopsUser->getGroups();
} else {
    $groups = XOOPS_GROUP_ANONYMOUS;
}
if (!$gpermHandler->checkRight('news_view', $article->topicid(), $groups, $xoopsModule->getVar('mid'))) {
    redirect_header(XOOPS_URL . '/modules/news/index.php', 3, _NOPERM);
}

$storypage  = isset($_GET['page']) ? (int)$_GET['page'] : 0;
$dateformat = NewsUtility::getModuleOption('dateformat');
$hcontent   = '';

/**
 * update counter only when viewing top page and when you are not the author or an admin
 * 
 * 
 * 
         if (($xoopsUser->getVar('uid') == $article->uid())  
             || (NewsUtility::isAdminGroup() && $xoopsModuleConfig('countAdminRead')==0) ) {
             
            // nothing ! ;-)
        } else {
 
 */
 global $xoopsModuleConfig;
if (empty($_GET['com_id']) && 0 == $storypage) {
    if (is_object($xoopsUser)) {
         if (($xoopsUser->getVar('uid') == $article->uid())  
             || (NewsUtility::isAdminGroup() && $xoopsModuleConfig['countAdminRead']==0) ) {

             //echo "<hr>countAdminRead = "  . $xoopsModuleConfig['countAdminRead'] . "<hr>";
            // nothing ! ;-)
        } else {
            $article->updateCounter();
        }
    } else {
        $article->updateCounter();
    }
}
$GLOBALS['xoopsOption']['template_main'] = 'news_article.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

//date_default_timezone_set('UTC');
$story['id']          = $storyid;
$story['posttime']    = formatTimestamp($article->published(), $dateformat);
$story['news_title']  = addCrLf($article->title());
$story['authors']     = $article->authors();
$story['title']       = $article->textlink() . '&nbsp;:&nbsp;' . $article->title();
$story['subtitle']    = $article->subtitle();
$story['topic_title'] = $article->textlink();

$story['text'] = $article->hometext();
$bodytext      = $article->bodytext();
//---------------------------
//JJDai - ajout dans a la fin du exte de readmore
$posReadMore = strripos($story['text'], NewsUtility::getModuleOption('readmore'));
$lgReadMore = strlen(_MD_NEWS_READMORE);
if ($posReadMore > ($introcount - NEWS_NB_LAST_CAR_TO_READ)){
$story['text'] = substr($story['text'],0,$posReadMore) . substr($story['text'], $posReadMore + $lgReadMore);
}
// pour des tests$story['text'] .= " ({$posReadMore})";
//-----------------------------

if ('' !== xoops_trim($bodytext)) {
    $articletext = [];
    if (NewsUtility::getModuleOption('enhanced_pagenav')) {
        $articletext             = preg_split('/(\[pagebreak:|\[pagebreak)(.*)(\])/iU', $bodytext);
        $arr_titles              = [];
        $auto_summary            = $article->auto_summary($bodytext, $arr_titles);
        $bodytext                = str_replace('[summary]', $auto_summary, $bodytext);
        $articletext[$storypage] = str_replace('[summary]', $auto_summary, $articletext[$storypage]);
        $story['text']           = str_replace('[summary]', $auto_summary, $story['text']);
        $articletext = explode('[pagebreak]', $bodytext);
    }

    $story_pages = count($articletext);

    if ($story_pages > 1) {
        require_once XOOPS_ROOT_PATH . '/modules/news/include/pagenav.php';
        $pagenav = new XoopsPageNav($story_pages, 1, $storypage, 'page', 'storyid=' . $storyid);
        if (NewsUtility::isBot()) { // A bot is reading the articles, we are going to show him all the links to the pages
            $xoopsTpl->assign('pagenav', $pagenav->renderNav($story_pages));
        } else {
            if (NewsUtility::getModuleOption('enhanced_pagenav')) {
                $xoopsTpl->assign('pagenav', $pagenav->renderEnhancedSelect(true, $arr_titles));
            } else {
                $xoopsTpl->assign('pagenav', $pagenav->renderNav());
            }
        }

        if (0 == $storypage) {
            $story['text'] = $story['text'] . '<br>' . NewsUtility::getModuleOption('advertisement') . '<br>' . $articletext[$storypage];
        } else {
            $story['text'] = $articletext[$storypage];
        }
    } else {
        $story['text'] = $story['text'] . '<br>' . NewsUtility::getModuleOption('advertisement') . '<br>' . $bodytext;
    }
}
// Publicit?
$xoopsTpl->assign('advertisement', NewsUtility::getModuleOption('advertisement'));

// ****************************************************************************************************************
/**
 * @param $matches
 *
 * @return string
 */
function my_highlighter($matches)
{
    $color = NewsUtility::getModuleOption('highlightcolor');
    if ('#' !== substr($color, 0, 1)) {
        $color = '#' . $color;
    }

    return '<span style="font-weight: bolder; background-color: ' . $color . ';">' . $matches[0] . '</span>';
}

$highlight = false;
$highlight = NewsUtility::getModuleOption('keywordshighlight');

if ($highlight && isset($_GET['keywords'])) {
    $keywords      = $myts->htmlSpecialChars(trim(urldecode($_GET['keywords'])));
    $h             = new keyhighlighter($keywords, true, 'my_highlighter');
    $story['text'] = $h->highlight($story['text']);
}
// ****************************************************************************************************************

$story['poster'] = $article->uname();
if ($story['poster']) {
    $story['posterid']         = $article->uid();
    $story['poster']           = '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $story['posterid'] . '">' . $story['poster'] . '</a>';
    $tmp_user                  = new XoopsUser($article->uid());
    $story['poster_avatar']    = XOOPS_UPLOAD_URL . '/' . $tmp_user->getVar('user_avatar');
    $story['poster_signature'] = $tmp_user->getVar('user_sig');
    $story['poster_email']     = $tmp_user->getVar('email');
    $story['poster_url']       = $tmp_user->getVar('url');
    $story['poster_from']      = $tmp_user->getVar('user_from');
    unset($tmp_user);
} else {
    $story['poster']           = '';
    $story['posterid']         = 0;
    $story['poster_avatar']    = '';
    $story['poster_signature'] = '';
    $story['poster_email']     = '';
    $story['poster_url']       = '';
    $story['poster_from']      = '';
    if (3 != NewsUtility::getModuleOption('displayname')) {
        $story['poster'] = $xoopsConfig['anonymous'];
    }
}
$story['morelink']  = '';
$story['adminlink'] = '';
unset($isadmin);

if (is_object($xoopsUser)) {
    if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))
        || (NewsUtility::getModuleOption('authoredit')
            && $article->uid() == $xoopsUser->getVar('uid'))) {
        $isadmin = true;
        //      $story['adminlink'] = $article->adminlink();
    }
}
$story['topicid']     = $article->topicid();
$story['topic_color'] = '#' . $myts->displayTarea($article->topic_color);
$story['topic_color_set'] = news_get_color_set($article->topic_color_set);

$story['imglink'] = '';
$story['align']   = '';
if ($article->topicdisplay()) {
    $story['imglink'] = $article->imglink();
    $story['align']   = $article->topicalign();
}
$story['hits']      = $article->counter();
$story['mail_link'] = 'mailto:?subject=' . sprintf(_MD_NEWS_INTARTICLE, $xoopsConfig['sitename']) . '&amp;body=' . sprintf(_MD_NEWS_INTARTFOUND, $xoopsConfig['sitename']) . ':  ' . XOOPS_URL . '/modules/news/article.php?storyid=' . $article->storyid();
$xoopsTpl->assign('lang_printerpage', _MD_NEWS_PRINTERFRIENDLY);
$xoopsTpl->assign('lang_sendstory', _MD_NEWS_SENDSTORY);
$xoopsTpl->assign('lang_pdfstory', _MD_NEWS_MAKEPDF);
$xoopsTpl->assign('lang_on', _ON);
$xoopsTpl->assign('lang_postedby', _POSTEDBY);
$xoopsTpl->assign('lang_authors', _MD_NEWS_AUTHORS);
$xoopsTpl->assign('lang_reads', _MD_NEWS_VIEWS);
$xoopsTpl->assign('mail_link', 'mailto:?subject=' . sprintf(_MD_NEWS_INTARTICLE, $xoopsConfig['sitename']) . '&amp;body=' . sprintf(_MD_NEWS_INTARTFOUND, $xoopsConfig['sitename']) . ':  ' . XOOPS_URL . '/modules/news/article.php?storyid=' . $article->storyid());
$xoopsTpl->assign('topics_color_set', news_get_topics_color_set(null));
if ('' !== xoops_trim($article->picture())) {
    $story['picture']     = XOOPS_URL . '/uploads/news/image/' . $article->picture();
    $story['pictureinfo'] = $article->pictureinfo();
} else {
    $story['picture']     = '';
    $story['pictureinfo'] = '';
}

$xoopsTpl->assign('lang_attached_files', _MD_NEWS_ATTACHEDFILES);
$sfiles     = new sFiles();
$filesarr   = $newsfiles = [];
$filesarr   = $sfiles->getAllbyStory($storyid);
$filescount = count($filesarr);
$xoopsTpl->assign('attached_files_count', (isset($filescount)?$filescount:0));

if ($filescount > 0) {
    foreach ($filesarr as $onefile) {
        $newsfiles[] = [
            'file_id'           => $onefile->getFileid(),
            'visitlink'         => XOOPS_URL . '/modules/news/visit.php?fileid=' . $onefile->getFileid(),
            'file_realname'     => $onefile->getFileRealName(),
            'file_attacheddate' => formatTimestamp($onefile->getDate(), $dateformat),
            'file_mimetype'     => $onefile->getMimetype(),
            'file_downloadname' => XOOPS_UPLOAD_URL . '/' . $onefile->getDownloadname()
        ];
    }
    $xoopsTpl->assign('attached_files', $newsfiles);
}

/**
 * Create page's title
 */
$complement = '';
if (NewsUtility::getModuleOption('enhanced_pagenav')
    && (isset($arr_titles) && is_array($arr_titles)
        && isset($arr_titles, $storypage)
        && $storypage > 0)) {
    $complement = ' - ' . $arr_titles[$storypage];
}
$xoopsTpl->assign('xoops_pagetitle', $article->title() . $complement . ' - ' . $article->topic_title() . ' - ' . $xoopsModule->name('s'));

if (NewsUtility::getModuleOption('newsbythisauthor')) {
    $xoopsTpl->assign('news_by_the_same_author_link', sprintf("<a href='%s?uid=%d'>%s</a>", XOOPS_URL . '/modules/news/newsbythisauthor.php', $article->uid(), _MD_NEWS_NEWSSAMEAUTHORLINK));
}

/**
 * Create a clickable path from the root to the current topic (if we are viewing a topic)
 * Actually this is not used in the default's templates but you can use it as you want
 * Uncomment the code to be able to use it
 */
if ($cfg['create_clickable_path']) {
    $mytree    = new MyXoopsObjectTree($xoopsDB->prefix('news_topics'), 'topic_id', 'topic_pid');
    $topicpath = $mytree->getNicePathFromId($article->topicid(), 'topic_title', 'index.php?op=1');
    $xoopsTpl->assign('topic_path', $topicpath);
    unset($mytree);
}

/**
 * Summary table
 *
 * When you are viewing an article, you can see a summary table containing
 * the first n links to the last published news.
 * This summary table is visible according to a module's option (showsummarytable)
 * The number of items is equal to the module's option "storyhome" ("Select the number
 * of news items to display on top page")
 * We also use the module's option "restrictindex" ("Restrict Topics on Index Page"), like
 * this you (the webmaster) select if users can see restricted stories or not.
 */
if (NewsUtility::getModuleOption('showsummarytable')) {
    $xoopsTpl->assign('showsummary', true);
    $xoopsTpl->assign('lang_other_story', _MD_NEWS_OTHER_ARTICLES);
    $count      = 0;
    $tmparticle = new NewsStory();
    $infotips   = NewsUtility::getModuleOption('infotips');
    $sarray     = NewsStory::getAllPublished($cfg['article_summary_items_count'], 0, $xoopsModuleConfig['restrictindex']);
    if (count($sarray) > 0) {
        foreach ($sarray as $onearticle) {
            ++$count;
            $htmltitle = '';
            $tooltips  = '';
            $htmltitle = '';
            if ($infotips > 0) {
                $tooltips  = NewsUtility::makeInfotips($onearticle->hometext());
                $htmltitle = ' title="' . $tooltips . '"';
            }
            $xoopsTpl->append('summary', [
                'story_id'        => $onearticle->storyid(),
                'htmltitle'       => $htmltitle,
                'infotips'        => $tooltips,
                'story_title'     => $onearticle->title(),
                'story_hits'      => $onearticle->counter(),
                'story_published' => formatTimestamp($onearticle->published, $dateformat)
            ]);
        }
    }
    $xoopsTpl->assign('summary_count', $count);
    unset($tmparticle);
} else {
    $xoopsTpl->assign('showsummary', false);
}

/**
 * Show a link to go to the previous article and to the next article
 *
 * According to a module's option "showprevnextlink" ("Show Previous and Next link?")
 * you can display, at the bottom of each article, two links used to navigate thru stories.
 * This feature uses the module's option "restrictindex" so that we can, or can't see
 * restricted stories
 */
if (NewsUtility::getModuleOption('showprevnextlink')) {
    $xoopsTpl->assign('nav_links', true);
    $tmparticle    = new NewsStory();
    $nextId        = $previousId = -1;
    $next          = $previous = [];
    $previousTitle = $nextTitle = '';

    $next = $tmparticle->getNextArticle($storyid, $xoopsModuleConfig['restrictindex']);
    if (count($next) > 0) {
        $nextId    = $next['storyid'];
        $nextTitle = $next['title'];
    }

    $previous = $tmparticle->getPreviousArticle($storyid, $xoopsModuleConfig['restrictindex']);
    if (count($previous) > 0) {
        $previousId    = $previous['storyid'];
        $previousTitle = $previous['title'];
    }

    $xoopsTpl->assign('previous_story_id', $previousId);
    $xoopsTpl->assign('next_story_id', $nextId);
    if ($previousId > 0) {
        $xoopsTpl->assign('previous_story_title', $previousTitle);
        $hcontent .= sprintf("<link rel=\"Prev\" title=\"%s\" href=\"%s/\">\n", $previousTitle, XOOPS_URL . '/modules/news/article.php?storyid=' . $previousId);
    }

    if ($nextId > 0) {
        $xoopsTpl->assign('next_story_title', $nextTitle);
        $hcontent .= sprintf("<link rel=\"Next\" title=\"%s\" href=\"%s/\">\n", $nextTitle, XOOPS_URL . '/modules/news/article.php?storyid=' . $nextId);
    }
    $xoopsTpl->assign('lang_previous_story', _MD_NEWS_PREVIOUS_ARTICLE);
    $xoopsTpl->assign('lang_next_story', _MD_NEWS_NEXT_ARTICLE);
    unset($tmparticle);
} else {
    $xoopsTpl->assign('nav_links', false);
}

/**
 * Manage all the meta datas
 */
NewsUtility::createMetaDatas($article);

/**
 * Show a "Bookmark this article at these sites" block ?
 */
if (NewsUtility::getModuleOption('bookmarkme')) {
    $xoopsTpl->assign('bookmarkme', true);
    $xoopsTpl->assign('encoded_title', rawurlencode($article->title()));
} else {
    $xoopsTpl->assign('bookmarkme', false);
}

/**
 * Use Facebook Comments Box?
 */
if (NewsUtility::getModuleOption('fbcomments')) {
    $xoopsTpl->assign('fbcomments', true);
} else {
    $xoopsTpl->assign('fbcomments', false);
}

/**
 * Enable users to vote
 *
 * According to a module's option, "ratenews", you can display a link to rate the current news
 * The actual rate in showed (and the number of votes)
 * Possible modification, restrict votes to registred users
 */
$other_test = true;
if ($cfg['config_rating_registred_only']) {
    if (isset($xoopsUser) && is_object($xoopsUser)) {
        $other_test = true;
    } else {
        $other_test = false;
    }
}

if (NewsUtility::getModuleOption('ratenews') && $other_test) {
    $xoopsTpl->assign('rates', true);
    $xoopsTpl->assign('lang_ratingc', _MD_NEWS_RATINGC);
    $xoopsTpl->assign('lang_ratethisnews', _MD_NEWS_RATETHISNEWS);
    $story['rating'] = number_format($article->rating(), 2);
    if (1 == $article->votes) {
        $story['votes'] = _MD_NEWS_ONEVOTE;
    } else {
        $story['votes'] = sprintf(_MD_NEWS_NUMVOTES, $article->votes);
    }
} else {
    $xoopsTpl->assign('rates', false);
}
if (NewsUtility::getModuleOption('newsbythisauthor')) {
   $story['news_by_the_same_author_link'] = sprintf("<a href='%s?uid=%d'>%s</a>", XOOPS_URL . '/modules/news/newsbythisauthor.php', $article->uid(), _MD_NEWS_NEWSSAMEAUTHORLINK);
   //$story['news_by_the_same_author_link'] ="zzzzzzzzzzzzzzz";
}

$xoopsTpl->assign('story', $story);

// Added in version 1.63, TAGS
if (xoops_isActiveModule('tag') && NewsUtility::getModuleOption('tags')) {
    require_once XOOPS_ROOT_PATH . '/modules/tag/include/tagbar.php';
    $xoopsTpl->assign('tags', true);
    $xoopsTpl->assign('tagbar', tagBar($storyid, 0));
} else {
    $xoopsTpl->assign('tags', false);
}

$xoopsTpl->assign('share', $xoopsModuleConfig['share']);
$xoopsTpl->assign('showicons', $xoopsModuleConfig['showicons']);

$canPdf = 1;
if (!is_object($GLOBALS['xoopsUser']) && 0 == $xoopsModuleConfig['show_pdficon']) {
    $canPdf = 0;
}
$xoopsTpl->assign('showPdfIcon', $canPdf);


if (1 == NewsUtility::getModuleOption('displaytopictitle')) {
    $xoopsTpl->assign('displaytopictitle', true);
} else {
    $xoopsTpl->assign('displaytopictitle', false);
}

$pathIcon16 = \Xmf\Module\Admin::iconUrl('', 16);
$pathIcon32 = \Xmf\Module\Admin::iconUrl('', 32);

$xoopsTpl->assign('pathIcon16', $pathIcon16);
//Add style css
$xoTheme->addStylesheet('modules/news/assets/css/style.css');
//$highslide = 'highslide'; //4113
$highslide = 'highslide';
$xoTheme->addScript(XOOPS_URL . "/Frameworks/{$highslide}/highslide.js");
//$xoTheme->addScript(XOOPS_URL . "/Frameworks/{$highslide}/xoops_highslide.js");
$xoTheme->addScript(XOOPS_URL . "/modules/news/assets/js/config_highslide.js");
$xoTheme->addStylesheet(XOOPS_URL ."/Frameworks/{$highslide}/highslide.css");
news_load_css();

require_once XOOPS_ROOT_PATH . '/include/comment_view.php';
require_once XOOPS_ROOT_PATH . '/footer.php';




