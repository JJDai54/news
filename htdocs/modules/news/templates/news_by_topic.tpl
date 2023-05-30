<{if $smarty.const.NEWS_SHOW_TPL_NAME==1}>
<div style="text-align: center; background-color: black;"><span style="color: yellow;">Template : <{$smarty.template}></span></div>
<{/if}>

<div class="item">
    <table width='100%' border='0'>
        <tr>
            <{section name=i loop=$columns}>
                <td width="<{$columnwidth}>%" valign="top">
                    <{foreach item=topic from=$columns[i]}>
                        <div class="item-body">
                            <div class="item-info"><span class="item-text"><a title="<{$topic.title}>"
                                href="<{$xoops_url}>/modules/news/index.php?storytopic=<{$topic.id}>"><{$topic.title}></a></span>
                            </div>
                            <{counter start=0 print=false assign=storynum}>
                            <{foreach item=story from=$topic.stories}>
                            <{if $storynum == 0}>
                            <{include file="db:news_item.tpl" story=$story}><br>
                            <{else}>
                            <{if $storynum == 1}>
                            <ul>
                                <{/if}>
                                <li><a title="<{$story.title}>"
                                       href="<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>"><{$story.title}></a>
                                    (<{$story.posttime}>)
                                </li>
                                <{/if}>
                                <{counter}>
                                <{/foreach}>
                            </ul>
                            <{if $storynum > 1}>
                            <a title="<{$lang_morereleases}><{$topic.title}>"
                               href="<{$xoops_url}>/modules/news/index.php?storytopic=<{$topic.id}>"><{$lang_morereleases}><{$topic.title}></a>
                            <{/if}>
                        </div>
                    <{/foreach}>
                </td>
            <{/section}>
        </tr>
    </table>
</div>
<{include file='db:system_notification_select.tpl'}>
