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
 * @author         Herve Thouzard  URL: http://www.herve-thouzard.com
 */

/**
 * @param $items
 *
 * @return bool|null
 */
function news_tag_iteminfo(&$items)
{
    if (empty($items) || !is_array($items)) {
        return false;
    }

    $items_id = [];
    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            $items_id[] = (int)$item_id;
        }
    }
    require_once XOOPS_ROOT_PATH . '/modules/news/class/class.newsstory.php';
    $tempNews  = new NewsStory();
    $items_obj = $tempNews->getStoriesByIds($items_id);

    foreach (array_keys($items) as $cat_id) {
        foreach (array_keys($items[$cat_id]) as $item_id) {
            if (isset($items_obj[$item_id])) {
                $item_obj                 =& $items_obj[$item_id];
                $items[$cat_id][$item_id] = [
                    'title'   => $item_obj->title(),
                    'uid'     => $item_obj->uid(),
                    'link'    => "article.php?storyid={$item_id}",
                    'time'    => $item_obj->published(),
                    'tags'    => '', // tag_parse_tag($item_obj->getVar("item_tags", "n")), // optional
                    'content' => ''
                ];
            }
        }
    }
    unset($items_obj);

    return null;
}

/**
 * @param $mid
 */
function news_tag_synchronization($mid)
{
    global $xoopsDB;
    $itemHandler_keyName = 'storyid';
    $itemHandler_table   = $xoopsDB->prefix('news_stories');
    $linkHandler         = xoops_getModuleHandler('link', 'tag');
    $where               = "($itemHandler_table.published > 0 AND $itemHandler_table.published <= " . time() . ") AND ($itemHandler_table.expired = 0 OR $itemHandler_table.expired > " . time() . ')';

    /* clear tag-item links */
    if ($linkHandler->mysql_major_version() >= 4):
        $sql = "    DELETE FROM {$linkHandler->table}"
               . ' WHERE '
               . "     tag_modid = {$mid}"
               . '     AND '
               . '       ( tag_itemid NOT IN '
               . "           ( SELECT DISTINCT {$itemHandler_keyName} "
               . "             FROM {$itemHandler_table} "
               . "                WHERE $where"
               . '           ) '
               . '     )'; else:
        $sql = "   DELETE {$linkHandler->table} FROM {$linkHandler->table}"
               . "  LEFT JOIN {$itemHandler_table} AS aa ON {$linkHandler->table}.tag_itemid = aa.{$itemHandler_keyName} "
               . '   WHERE '
               . "     tag_modid = {$mid}"
               . '     AND '
               . "       ( aa.{$itemHandler_keyName} IS NULL"
               . '           OR '
               . '       (aa.published > 0 AND aa.published <= '
               . time()
               . ') AND (aa.expired = 0 OR aa.expired > '
               . time()
               . ')'
               . '       )';

    endif;
    if (!$result = $linkHandler->db->queryF($sql)) {
        //xoops_error($linkHandler->db->error());
    }
}
