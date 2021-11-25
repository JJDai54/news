<{if $smarty.const.NEWS_SHOW_TPL_NAME==1}>
<div style="text-align: center; background-color: black;"><span style="color: yellow;">Template : <{$smarty.template}></span></div>
<{/if}>

<{*
<{include file="db:news_topic_color_set.tpl"}><br><br>
*}>

<style>

table.this-author tr td,th{
    padding: 0px 12px 0px 12px; //Marge à l'intérieur des cellules, équivalent de cellpadding
}
td.separation{
    background-color: #FFFFFF;
    border: 0px solid;
}
tr.separation{
    background-color: #FFFFFF;
    border: 0px solid;
}



#this-author-title  {
    border: 0px solid red;
    background-color: #FFFFFF;  /*  */
    border-collapse: collapse;
}

.this-author-title {
    padding: 0px 12px 0px 12px;  /*Marge à l'intérieur des cellules, équivalent de cellpadding*/
    border: 0px solid red;
    /*  background-color: #888888;  */
}

</style>

<div class="news-author">
<{*
    <img src='<{$user_avatarurl}>' border='0' alt='' style="display: inline;">
    <h2 style="display: inline;"><{$lang_news_by_this_author}> <{$author_name_with_link}></h2>
    <br><br>
*}>
    <table width='100%' id="this-author-title" >
        <tr>
            <td class="this-author-title"><img src='<{$user_avatarurl}>' border='0' alt=''></td>
            <td class="this-author-title"><h2><{$lang_news_by_this_author}> <{$author_name_with_link}></h2>
            <br><form id='form_story_status'  name='form_story_status' method='post' action='newsbythisauthor.php?uid=<{$uid}>'><{$smarty.const._MD_NEWS_ARTICLES}> : <{$selStories}></form>
           </td>
        </tr>
    </table>
    <br><br>


    <table width='100%' border='0px' class="this-author">
        <{foreach item=topic from=$topics}>
            <tr class="<{$topic.topic_color_set}>-itemHead">
                <{if $news_rating}>
                <th colspan='4' ><{else}>
                <th colspan='3' ><{/if}><{$topic.topic_link}></th>
            </tr>
            <tr class="<{$topic.topic_color_set}>-itemHead" >
                <td width="25%"><{$lang_date}></td>
                <td><{$lang_title}></td>
                <td width="10%"><{$lang_hits}></td>
                <{if $news_rating}>
                    <td width="10%"><{$lang_rating}></td>
                <{/if}>
            </tr>
            <{foreach item=article from=$topic.news}>
                <tr class="<{$topic.topic_color_set}>-itemBody">
                    <td><{$article.published}></td>
                    <td><{$article.article_link}></td>
                    <td align='right'><{$article.hits}></td>
                    <{if $news_rating}>
                        <td align='right'><{$article.rating}></td>
                    <{/if}>
                </tr>
            <{/foreach}>
            <tr>
                <td colspan='2' align='left'><{$topic.topic_count_articles}></td>
                <td align='right'><{$topic.topic_count_reads}></td>
                <{if $news_rating}>
                    <td>&nbsp;</td>
                <{/if}>
            </tr>
            <{* ====================================================== *}>
            <{*
            <tr class='separation'><{if $news_rating}>
                <td colspan='4' class='separation'><{else}>
                <td colspan='3' class='separation'><{/if}>&nbsp;</td>
            </tr>
             *}>
                </table>
                <br>
                <table width='100%' border='0px' class="this-author">
        <{/foreach}>
    </table>
</div>

            <{* =================== Liste des auteurs ======================= *}>


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
                    <{$who.name}>)
                <{else}>
                    <{$who.name}>  (<{$who.pseudo}>)
                <{/if}>
                </a>
              </li>
          <{/foreach}>
      </ul>
  </div>
  <div class="news-whoswho item  <{$colorset_author}>-itemHead item-round-bottom item-info">
        <a href="<{$smarty.const.XOOPS_URL}>/modules/news/index.php?storytopic="><{$smarty.const._MD_NEWS_ALL_STORIES}></a>
 </div>

</div>