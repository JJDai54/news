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

<{* ----------------- articles par categorie  --------------------------*}>


    <{foreach item=topic from=$topics}>

        <table width='100%' border='0px' class="this-author">
            <tr class="<{$topic.topic_color_set}>-itemHead">
                <{if $news_rating}>
                <th colspan='4' ><{else}>
                <th colspan='3' ><{/if}><{$topic.topic_link}></th>
            </tr>
        </table>

        <table id='news-<{$topic.topic_id}>' name='news-news-<{$topic.topic_id}>' width='100%' border='0px' class="this-author">
        <thead>
            <tr class="<{$topic.topic_color_set}>-itemHead" >
                <th width="25%"><{$lang_date}></th>
                <th><{$lang_title}></th>
                <th width="15%"><{$lang_hits}></th>
                <{if $news_rating}>
                    <th width="10%"><{$lang_rating}></th>
                <{/if}>
            </tr>
        </thead>
            <{* -------------- liste des articles -------------------------*}>
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
        </table>

        <table width='100%' border='0px' class="this-author">
            <tr>
                <td colspan='2' align='left'><{$topic.topic_count_articles}></td>
                <td width="15%" align='right'><{$topic.topic_count_reads}></td>
                <{if $news_rating}>
                    <td>&nbsp;</td>
                <{/if}>
            </tr>
        </table>
<script>
tth_set_value('last_asc', true);
tth_trierTableau('news-<{$topic.topic_id}>', 2);  
</script>

                <br>
                <br>
           
            <{/foreach}>

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