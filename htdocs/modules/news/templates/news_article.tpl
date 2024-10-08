<{if $smarty.const.NEWS_SHOW_TPL_NAME==1}>
<div style="text-align: center; background-color: black;"><span style="color: yellow;">Template : <{$smarty.template}></span></div>
<{/if}>

<{include file="db:news_topic_color_set.tpl"}><br><br>

<div class="news-article">

    <div class="marg2 pad2"><{include file="db:news_item.tpl" story=$story}>

        <{if $xoops_isadmin}>
            <a href="<{$xoops_url}>/modules/news/submit.php?op=edit&amp;storyid=<{$story.id}>">
              <img src="<{xoModuleIcons16}>/edit.png" title="<{$smarty.const._EDIT}>"></a>
            <a href="<{$xoops_url}>/modules/news/admin/index.php?op=delete&amp;storyid=<{$story.id}>"><img
                        src="<{xoModuleIcons16}>/delete.png" title="<{$smarty.const._DELETE}>"></a>
        <{/if}>

        <{if $showicons == true}>
            <a href="<{$xoops_url}>/modules/news/print.php?storyid=<{$story.id}>" rel="nofollow"
               title="<{$lang_printerpage}>"><img src="<{xoModuleIcons16}>/printer.png"
                                                  alt="<{$lang_printerpage}>"></a>
            <a target="_top" href="<{$mail_link}>" title="<{$lang_sendstory}>" rel="nofollow">
              <img src="<{xoModuleIcons16}>/mail_forward.png" alt="<{$lang_sendstory}>"></a>
        <{/if}>
        <{if $showPdfIcon == true}>
            <a target="_blank" href="<{$xoops_url}>/modules/news/makepdf.php?storyid=<{$story.id}>" rel="nofollow"
               title="<{$lang_pdfstory}>"><img src="<{xoModuleIcons16}>/pdf.png"
                                               alt="<{$lang_pdfstory}>"></a>
        <{/if}>

    </div>



    <{if $pagenav}>
        <div class="pagenav"><{$smarty.const._MD_NEWS_PAGE}> <{$pagenav}></div><{/if}>

    <{if $tags}>
        <div class="marg10 tagbar"><{include file="db:tag_bar.tpl"}></div>
    <{/if}>

    <div class="pad5 marg5">
        <{if $nav_links}>
            <{if $previous_story_id != -1}><a
                href='<{$xoops_url}>/modules/news/article.php?storyid=<{$previous_story_id}>'
                title="<{$previous_story_title}>"><{$lang_previous_story}></a> - <{/if}>
            <{if $next_story_id!= -1}><a href='<{$xoops_url}>/modules/news/article.php?storyid=<{$next_story_id}>'
                                         title="<{$next_story_title}>"><{$lang_next_story}></a><{/if}>
        <{/if}>

    </div>

    <{* ------------------------------------------------------------------------- *}>
    <{* JJD - Ajout de commentaire - plus logique de mettre directement suite a l'article *}>
    <div class="item-info item-round-all <{$story.topic_color_set}>-item-info"><{$smarty.const._COMMENTS}>
    <{if $fbcomments == true}>
        <div id="fb-root"></div>
        <script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
        <fb:comments href="<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>" num_posts="5"
                     width="500"></fb:comments>
    <{/if}>


    <div class="pad2 marg2 <{$story.topic_color_set}>-item-body-scoop">
        <{$commentsnav}>
        <{$lang_notice}>
    </div>


    <div class="pad2 marg2 <{$story.topic_color_set}>-item-body-scoop">
        <{if $comment_mode == "flat"}>
            <{include file="db:system_comments_flat.tpl"}>
        <{elseif $comment_mode == "thread"}>
            <{include file="db:system_comments_thread.tpl"}>
        <{elseif $comment_mode == "nest"}>
            <{include file="db:system_comments_nest.tpl"}>
        <{/if}>
    </div> <br><br>
    </div>
    <{* ------------------------------------------------------------------------- *}>

    <{* Liste des derniers articles parus *}>
    <{if $showsummary == true && $summary_count>0}>
        <div class="marg10">
            <table width='100%' cellspacing='0' cellpadding='1'>
                <tr>
                    <th><{$lang_other_story}></th>
                </tr>
                <{foreach item=onesummary from=$summary}>
                    <tr class="<{cycle values="even,odd"}>">
                        <td align='left'><{$onesummary.story_published}> -
                            <a href='<{$xoops_url}>/modules/news/article.php?storyid=<{$onesummary.story_id}>'<{$onesummary.htmltitle}>><{$onesummary.story_title}></a>
                        </td>
                    </tr>
                <{/foreach}>
            </table>
        </div>
    <{/if}>

    <{if $bookmarkme == true}>
        <div class="item-bookmarkme">
            <div class="head item-bookmarkme-title"><{$smarty.const._MD_NEWS_BOOKMARK_ME}></div>
            <div class="item-bookmarkme-items">
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_BLINKLIST}>"
                   href="http://www.blinklist.com/index.php?Action=Blink/addblink.php&Description=&Url=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>&Title=<{$story.news_title}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_BLINKLIST}>"
                            src="<{xoModuleIconsBookmarks}>/blinklist.gif"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_DELICIOUS}>"
                   href="http://del.icio.us/post?url=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>&title=<{$story.news_title}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_DELICIOUS}>"
                            src="<{xoModuleIconsBookmarks}>/delicious.gif"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_DIGG}>"
                   href="http://digg.com/submit?phase=2&url=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_DIGG}>"
                            src="<{xoModuleIconsBookmarks}>/diggman.gif"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_FARK}>"
                   href="http://cgi.fark.com/cgi/fark/edit.pl?new_url=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>&new_comment=<{$story.news_title}>&new_link_other=<{$story.news_title}>&linktype=Misc"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_FARK}>" src="<{xoModuleIconsBookmarks}>/fark.gif"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_FURL}>"
                   href="http://www.furl.net/storeIt.jsp?t=<{$story.news_title}>&u=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_FURL}>"
                            src="<{xoModuleIconsBookmarks}>/furl.gif"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_NEWSVINE}>"
                   href="http://www.nwvine.com/_tools/seed&save?u=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>&h=<{$story.news_title}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_NEWSVINE}>"
                            src="<{xoModuleIconsBookmarks}>/newsvine.gif"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_REDDIT}>"
                   href="http://reddit.com/submit?url=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>&title=<{$story.news_title}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_REDDIT}>"
                            src="<{xoModuleIconsBookmarks}>/reddit.gif"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_SIMPY}>"
                   href="http://www.simpy.com/simpy/LinkAdd.do?href=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>&title=<{$story.news_title}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_SIMPY}>"
                            src="<{xoModuleIconsBookmarks}>/simpy.png"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_SPURL}>"
                   href="http://www.spurl.net/spurl.php?title=<{$story.news_title}>&url=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_SPURL}>"
                            src="<{xoModuleIconsBookmarks}>/spurl.gif"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_YAHOO}>"
                   href="http://myweb2.search.yahoo.com/myresults/bookmarklet?t=<{$story.news_title}>&u=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_YAHOO}>"
                            src="<{xoModuleIconsBookmarks}>/yahoomyweb.gif"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_BALATARIN}>"
                   href="http://balatarin.com/links/submit?phase=2&amp;url=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_BALATARIN}>"
                            src="<{xoModuleIconsBookmarks}>/balatarin.png"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_FACEBOOK}>"
                   href="http://www.facebook.com/share.php?u=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_FACEBOOK}>"
                            src="<{xoModuleIconsBookmarks}>/facebook_share_icon.gif"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_TWITTER}>"
                   href="http://twitter.com/home?status=Browsing:%20<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_TWITTER}>"
                            src="<{xoModuleIconsBookmarks}>/twitter_share_icon.gif"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_SCRIPSTYLE}>"
                   href="http://scriptandstyle.com/submit?url=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_SCRIPSTYLE}>"
                            src="<{xoModuleIconsBookmarks}>/scriptandstyle.png"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_STUMBLE}>"
                   href="http://www.stumbleupon.com/submit?url=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_STUMBLE}>"
                            src="<{xoModuleIconsBookmarks}>/stumbleupon.png"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_TECHNORATI}>"
                   href="http://technorati.com/faves?add=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_TECHNORATI}>"
                            src="<{xoModuleIconsBookmarks}>/technorati.png"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_MIXX}>"
                   href="http://www.mixx.com/submit?page_url=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_MIXX}>"
                            src="<{xoModuleIconsBookmarks}>/mixx.png"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_MYSPACE}>"
                   href="http://www.myspace.com/Modules/PostTo/Pages/?u=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_MYSPACE}>"
                            src="<{xoModuleIconsBookmarks}>/myspace.jpg"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_DESIGNFLOAT}>"
                   href="http://www.designfloat.com/submit.php?url=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_DESIGNFLOAT}>"
                            src="<{xoModuleIconsBookmarks}>/designfloat.png"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_GOOGLEPLUS}>"
                   href="https://plusone.google.com/_/+1/confirm?hl=en&url=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_GOOGLEPLUS}>"
                            src="<{xoModuleIconsBookmarks}>/google_plus.png"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_GOOGLEREADER}>"
                   href="http://www.google.com/reader/link?url=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>&amp;title=<{$story.news_title}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_GOOGLEREADER}>"
                            src="<{xoModuleIconsBookmarks}>/google-reader-icon.png"></a>
                <a rel="external nofollow" target="_blank" title="<{$smarty.const._MD_NEWS_BOOKMARK_TO_GOOGLEBOOKMARKS}>"
                   href="https://www.google.com/bookmarks/mark?op=add&amp;bkmk=<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>&amp;title=<{$story.news_title}>"><img
                            alt="<{$smarty.const._MD_NEWS_BOOKMARK_TO_GOOGLEBOOKMARKS}>"
                            src="<{xoModuleIconsBookmarks}>/google-icon.png"></a>
            </div>
        </div>
    <{/if}>
    <{if $share == true}>
        <div class="item-bookmarkme-ftg">
            <ul>
                <li>
                    <div class="item-bookmarkme-facebook">
                        <script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
                        <fb:like href="<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>"
                                 layout="button_count" show_faces="false"></fb:like>
                    </div>
                </li>
                <li>
                    <div class="item-bookmarkme-twitter">
                        <script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>
                        <a href="http://twitter.com/share/<{$xoops_url}>/modules/news/article.php?storyid=<{$story.id}>"
                           class="twitter-share-button">Tweet</a></div>
                </li>
                <li>
                    <div class="item-bookmarkme-google1">
                        <script src="https://apis.google.com/js/plusone.js" type="text/javascript"></script>
                        <g:plusone size="medium" count="true"></g:plusone>
                    </div>
                </li>
            </ul>
            <br>
        </div>
    <{/if}>


    <{include file='db:system_notification_select.tpl'}>

</div>
