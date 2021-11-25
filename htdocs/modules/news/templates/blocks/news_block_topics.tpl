<{if $smarty.const.NEWS_SHOW_TPL_NAME==1}>
<div style="text-align: center; background-color: black;"><span style="color: yellow;">Template : <{$smarty.template}></span></div>
<{/if}>

<div class="center news-topics">
    <form name="newstopicform" action="<{$xoops_url}>/modules/news/index.php" method="get"><{$block.selectbox}></form>
</div>
