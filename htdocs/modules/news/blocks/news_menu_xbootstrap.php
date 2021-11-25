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
use Xmf\Module\Helper;
// defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

/**
 * @return array
 */
 include_once (dirname(__DIR__) . '/class/xoopstree.php');
 include_once (dirname(__DIR__) . '/class/xoopstopic.php');
 include_once (dirname(__DIR__) . '/class/class.newstopic.php');
 //include_once (dirname(__DIR__) . '/class/news_topics.php');


function b_news_menu_xbootstrap_show()
{
global $xoopsDB, $xoTheme;
    $block              = [];
    $moduleDirName = basename(dirname(__DIR__));
    $prefix = '<img src="'.XOOPS_URL.'/modules/'.$moduleDirName.'/assets/images/deco/arrow.gif">';

    /** @var \xos_opal_Theme $xoTheme */
    $xoTheme->addStylesheet(XOOPS_URL . '/modules/' . $moduleDirName . '/assets/css/blocks.css', null);


    $xt          = new NewsTopic($xoopsDB->prefix('news_topics'), 'topic_id', 'topic_pid');
    $topics_arr  = $xt->getChildTreeArray(0, 'topic_weight,topic_title');


// $tr = print_r($topics_arr, true);
// echo "<hr><pre>{$tr}</pre><hr>";

      $MenuItems = [];
      $url = XOOPS_URL . "/modules/" . $moduleDirName . "/index.php?storytopic=";
      $previousKey = "";

// --- Tous les articles
      $k=-1;
      $item = array('id'=>$k, 'lib'=>_MB_NEWS_ALL_STORIES, 'url' => $url);
      $MenuItems[$k] = $item;




      foreach ($topics_arr as $k=>$t){
        $title = block_news_parse_title($t['topic_title']);

        $item = array('id'=>$k, 'lib'=>$title, 'url' => $url . $t['topic_id']);
        //$item = array('id'=>$k, 'lib'=>$t['topic_title'], 'url' => $url . $t['topic_id']);
        if ($t['topic_pid'] > 0){
          $MenuItems[$previousKey]['submenu'][$k] = $item;
        }else{
          $MenuItems[$k] = $item;
          $previousKey = $k;
        }
      }

     $block['MenuCatItems'] = $MenuItems;

// $tr = print_r($MenuItems, true);
// echo "<hr><pre>{$tr}</pre><hr>";





    $block['module']['url'] = XOOPS_URL . "/modules/" . $moduleDirName ;
    $block['module']['lib'] = _MB_NEW_STORIES;


    $block['search']['url'] = XOOPS_URL . "/modules/" . $moduleDirName . "/index.php?storytopic=";
    $block['search']['lib'] = _MB_NEWS_ALL_STORIES;


//---------------------------------------------------------------------------------------

// --- Tous les articles
//     $block['main']['search']['url'] = XOOPS_URL . "/modules/" . $moduleDirName . "/index.php?storytopic=";
//     $block['main']['search']['lib'] = _MB_NEWS_ALL_STORIES;

// --- Soumettre un article
    $block['main']['submit']['url'] = XOOPS_URL . "/modules/" . $moduleDirName . "/submit.php";
    $block['main']['submit']['lib'] = _MB_NEWS_SUBMIT;

// --- Valider les articles les articles
    $block['main']['approve']['url'] = XOOPS_URL . "/modules/" . $moduleDirName . "/admin/index.php?op=newarticle";
    $block['main']['approve']['lib'] = _MB_NEWS_APPROVE_STORYES;

// --- Index des categories
    $block['main']['topicsIndex']['url'] = XOOPS_URL . "/modules/" . $moduleDirName . "/topics_directory.php";
    $block['main']['topicsIndex']['lib'] = _MB_NEWS_TOPICS_INDEX;

// --- Archives
    $block['main']['archives']['url'] = XOOPS_URL . "/modules/" . $moduleDirName . "/archive.php";
    $block['main']['archives']['lib'] = _MB_NEWS_ARCHIVES;

// --- Annuaire des auteurs
    $block['main']['whoswho']['url'] = XOOPS_URL . "/modules/" . $moduleDirName . "/whoswho.php";
    $block['main']['whoswho']['lib'] = _MB_NEWS_WHOS_WHO;


    $block['module']['nbMainMenu'] = count($block['main']);

    return $block;
}
/****************
 *
 ***************/
function block_news_parse_title($title, $sep='|') {
      $h = strpos($title, $sep);
      if (!($h === false)) $title = substr($title,$h+1);
      return $title;

}

/**
 * @param $options
 */
function b_news_menu_xbootstrap_edit($options)
{
}
