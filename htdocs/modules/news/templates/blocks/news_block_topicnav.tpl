<{if $smarty.const.NEWS_SHOW_TPL_NAME==1}>
<div style="text-align: center; background-color: black;"><span style="color: yellow;">Template : <{$smarty.template}></span></div>
<{/if}>

<div class="mainmenu news-mainmenu">
    <{foreach item=topic from=$block.topics}>
        <h2><a class="menuMain" title="<{$topic.title}>"
               href="<{$xoops_url}>/modules/news/index.php?storytopic=<{$topic.id}>"><{$topic.title}> <{$topic.news_count}></a><br>
        </h2>
    <{/foreach}>
</div>
