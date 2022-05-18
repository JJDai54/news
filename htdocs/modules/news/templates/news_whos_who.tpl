<{if $smarty.const.NEWS_SHOW_TPL_NAME==1}>
<div style="text-align: center; background-color: black;"><span style="color: yellow;">Template : <{$smarty.template}></span></div>
<{/if}>

<div class="item-round-no">
  <div class="news-whoswho item  <{$colorset_author}>-itemHead item-round-top item-title">
       <span class="item-title"><{$smarty.const._AM_NEWS_WHOS_WHO}></span>
  </div>

  <div class="news-whoswho <{$colorset_author}>-itemInfo item-info">
      <{$smarty.const._MD_NEWS_NEWS_LIST_OF_AUTHORS}>
  </div>

  <div class="news-whoswho <{$colorset_author}>-itemBody item-text item-round-no">
      <ul style="margin:0px;">
          <{foreach item=who from=$whoswho}>
              <li>
                <a title="<{$who.name}>"
                href="<{$xoops_url}>/modules/news/newsbythisauthor.php?uid=<{$who.uid}>">
                <{if $who.name == $who.pseudo}>
                    <{$who.name}>
                <{else}>
                    <{$who.name}>  (<{$who.pseudo}>)
                <{/if}>
                </a>
              </li>
          <{/foreach}>
      </ul>
  </div>
  <div class="news-whoswho <{$colorset_author}>-itemHead item-round-bottom item-info">
        <a href="<{$smarty.const.XOOPS_URL}>/modules/news/index.php?storytopic="><{$smarty.const._MD_NEWS_ALL_STORIES}></a>
 </div>

</div>