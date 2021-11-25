<{if $smarty.const.NEWS_SHOW_TPL_NAME==1}>
<div style="text-align: center; background-color: black;"><span style="color: yellow;">Template : <{$smarty.template}></span></div>
<{/if}>

<div class="news-bigstory">
    <p><{$block.message}></p>
    <{if $block.story_id != ""}>
        <h2>
            <a href="<{$xoops_url}>/modules/news/article.php?storyid=<{$block.story_id}>"<{$block.htmltitle}>><{$block.story_title}></a>
        </h2>
    <{/if}>
</div>
