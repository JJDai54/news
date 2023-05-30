<{if $smarty.const.NEWS_SHOW_TPL_NAME==1}>
<div style="text-align: center; background-color: black;"><span style="color: yellow;">Template : <{$smarty.template}></span></div>
<{/if}>

<{if false}>
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
<{* Mise en forme avec color_set *}>
<{else}> 
<div class="item-round-top default-item-head"><center><b>
    <a href='modules/news/index.php'><{$smarty.const._MB_NEWS_LAST_STORIES}></a>
    <{* <a href='modules/news/index.php'><{$block.options.title}></a> *}>
</b></center></div>

  <table class="outer" width="100%" style='border:none;'>
      <{foreach item=news from=$block.stories}>
          <tr class="<{cycle values="even, odd"}>">
              <td style='border:none;'>
<div class="item-round-none <{$news.topic_color_set}>-item-body" style='border:none;padding: 5px 12px 5px 12px;'>
                [<{$news.date}>] <a title="<{$news.title}>" href="<{$xoops_url}>/modules/news/article.php?storyid=<{$news.id}>"><{$news.title}></a>
</div>
              </td>
          </tr>
      <{/foreach}>
  </table>

<div class="item-round-bottom default-item-legend"><center>...</center></div>
<{/if}>