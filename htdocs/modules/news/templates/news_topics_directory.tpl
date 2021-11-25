<{if $smarty.const.NEWS_SHOW_TPL_NAME==1}>
<div style="text-align: center; background-color: black;"><span style="color: yellow;">Template : <{$smarty.template}></span></div>
<{/if}>

<style>
.cat_item{
    margin: 10px 30px 10px 30px;
    padding: 15px 30px 15px 30px;
    font-size: 1.5em;
}
.cat_description{
    font-size: 0.8em;
    font-style: italic;
}
</style>

<div class="news-topics-directory ">
    <h2><{$smarty.const._AM_NEWS_TOPICS_DIRECTORY}></h2>
<{foreach item=topic from=$topics}>

  <div class="cat_item <{$topic.color_set}>-itemBody item-text item-round-all">

    <li>
    <{if $topic.imgurl <> ""}><img src=<{$smarty.const.NEWS_URL_UPLOAD}>/<{$topic.imgurl}> ><{/if}>
    <a title="<{$topic.title}>"
        href="<{$xoops_url}>/modules/news/index.php?storytopic=<{$topic.id}>"><{$topic.title}></a>
         - <{$topic.news_count}> <{$smarty.const._MD_NEWS_ARTICLE_S}>
    </li>
    <span class="cat_description"><{$topic.description}></span>
   </div>
<{/foreach}>
</div>
