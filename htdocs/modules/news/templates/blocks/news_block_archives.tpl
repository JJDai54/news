<{if $smarty.const.NEWS_SHOW_TPL_NAME==1}>
<div style="text-align: center; background-color: black;"><span style="color: yellow;">Template : <{$smarty.template}></span></div>
<{/if}>

<div class="news-archives">
    <h2><{$smarty.const._MD_NEWS_NEWSARCHIVES}></h2>
    <ul>
        <{foreach item=onedate from=$block.archives}>
            <li>
                <a title="<{$onedate.formated_month}> <{$onedate.year}>"
                   href="<{$xoops_url}>/modules/news/archive.php?year=<{$onedate.year}>&amp;month=<{$onedate.month}>"><{$onedate.formated_month}> <{$onedate.year}></a>
            </li>
        <{/foreach}>
    </ul>
</div>
