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
use Xmf\Module\Helper as xHelper;
//global $xoopsModuleConfig;
//define ("NEWS_SHOW_TPL_NAME", $xoopsModuleConfig['displayTemplateName']);
//$newsHelper = Xmf\Module\Helper::getHelper('news');

//$newsHelper = xHelper::getHelper("news");
if(isset($newsHelper)){
    define ("NEWS_SHOW_TPL_NAME", $newsHelper->getConfig('displayTemplateName') );
}else{
    define ("NEWS_SHOW_TPL_NAME", 0);
}

define ("NEWS_DIRNAME", 'news');
define ("NEWS_URL", XOOPS_URL . '/modules/' . NEWS_DIRNAME);
define ("NEWS_URL_UPLOAD", XOOPS_URL . '/uploads/' . NEWS_DIRNAME . '/image');

define ("NEWS_ITEM_IMG_HEIGHT", 200);
define ("NEWS_DEFAULT", 'default');
define ("NEWS_COLORSET_AUTHOR", 'purple2');


//JJDai - Ajout des selection sur l'expiration des articles
define ("NEWS_STORY_STATUS_ACTIFS", 0);
define ("NEWS_STORY_STATUS_PERMANENT", 1);
define ("NEWS_STORY_STATUS_NON_EXPIRED", 2);
define ("NEWS_STORY_STATUS_EXPIRED", 3);
define ("NEWS_STORY_STATUS_ALL", 4);

define ("NEWS_NB_LAST_CAR_TO_READ", 50);