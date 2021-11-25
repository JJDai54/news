<{if $smarty.const.NEWS_SHOW_TPL_NAME==1}>
<div style="text-align: center; background-color: black;"><span style="color: yellow;">Template : <{$smarty.template}></span></div>
<{/if}>

                  <{*
<div class="item <{$divHr}>" style="padding:8px;"></div>
                  *}>

    <div class="itemHead item-round-top <{$story.topic_color_set}>-itemHead">
        <span >
           <{if $displaytopictitle == true}> <{$story.topic_title}><{/if}>
           <h3 style="margin-top:0px;"><{$story.news_title}></h3>
        </span>
    <{if $story.subtitle}><h6><i><{$story.subtitle}></i></h6><{/if}>
    </div>


    <div class="itemInfo <{$story.topic_color_set}>-itemInfo" >
        <{if $story.files_attached}><{$story.attached_link}>&nbsp;<{/if}>
        <{if $story.poster != ''}><span class="itemPoster <{$story.topic_color_set}>-itemPoster"><{$lang_postedby}> <{$story.poster}></span><{/if}>
        <span class="itemPostDate"><{$lang_on}> <{$story.posttime}></span>
        <br>(<span class="itemStats"><{$story.hits}> <{$lang_reads}></span>)

        <br><{$story.news_by_the_same_author_link}>  <{* <{$news_by_the_same_author_link}> Lien sur les autres articles du même auteur *}>
        <!--<span class="itemTopic"><{$lang_topic}> <{$story.topic_title}></span>-->
    </div>
<{if $class_item_article == 1}>
    <div class="itemBody_scoop <{$story.topic_color_set}>-itemBody_scoop">
<{else}>
    <div class="itemBody <{$story.topic_color_set}>-itemBody">
<{/if}>
        <{if $story.picture != ''}>

            <a id="news-<{$story.id}>" class="highslide" onclick="return hs.expand(this, { maxWidth: 1200 });" href="<{$story.picture}>" >

                <img class="left"  style="border:1px solid #FFFFFF;margin-right:6px;max-width:200px"
                     src="<{$story.picture}>" alt="<{$story.pictureinfo}>">
            </a>
        <{else}>
            <{$story.imglink}>
        <{/if}>
        <div class="itemText <{$story.topic_color_set}>-itemText"><{$story.text}></div>
        <div class="clear"></div>
</div>
    <{if $attached_files_count>0}>
        <div class="itemInfo <{$story.topic_color_set}>-item-legend"><img src="<{$pathIcon16}>/attach.png" title="<{$smarty.const._MD_NEWS_ATTACHEDLIB}>"> <b><{$lang_attached_files}></b>
            <{foreach item=onefile from=$attached_files}>
                <br><a href='<{$onefile.visitlink}>' target='_blank'><{$onefile.file_realname}></a>
                &nbsp;
            <{/foreach}>
        </div>
    <{/if}>

    <div class="itemFoot item-round-bottom <{$story.topic_color_set}>-itemFoot">
        <{* <hr class="<{$story.topic_color_set}>-hr-style-two" >  *}>
        <{if $story.adminlink <> ""}><span class="itemAdminLink"><{$story.adminlink}></span><{/if}>
        <{if $rates}><b><{$lang_ratingc}></b> <{$story.rating}> (<{$story.votes}>) -
            <a title="<{$lang_ratethisnews}>" href="<{$xoops_url}>/modules/news/ratenews.php?storyid=<{$story.id}>"
               rel="nofollow"><{$lang_ratethisnews}></a>
            - <{/if}>
        <{if $story.morelink <>""}><span class="itemPermaLink"><{$story.morelink}></span><{/if}>
        <br><span class="itemPermaLink"><{$news_by_the_same_author_link}></span>
    </div>

