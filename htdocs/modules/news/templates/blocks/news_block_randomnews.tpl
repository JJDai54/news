<{if $smarty.const.NEWS_SHOW_TPL_NAME==1}>
<div style="text-align: center; background-color: black;"><span style="color: yellow;">Template : <{$smarty.template}></span></div>
<{/if}>

<div class="news-random">
    <{foreach item=news from=$block.stories}>
        <div class="item">
            <h3>
           <span>
            <{if $block.sort=='counter'}>
                [<{$news.hits}>]
            <{elseif $block.sort=='published'}>
                [<{$news.date}>]
            <{else}>
                [<{$news.rating}>]
            <{/if}>
            </span>
                <{$news.topic_title}> - <a
                        href="<{$xoops_url}>/modules/news/article.php?storyid=<{$news.id}>" <{$news.infotips}>><{$news.title}></a>
            </h3>
            <{if $news.teaser}><p><{$news.teaser}></p><{/if}>
        </div>
    <{/foreach}>
</div>
