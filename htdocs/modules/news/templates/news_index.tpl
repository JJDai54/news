<{if $smarty.const.NEWS_SHOW_TPL_NAME==1}>
<div style="text-align: center; background-color: black;"><span style="color: yellow;">Template : <{$smarty.template}></span></div>
<{/if}>

<div class="news-index">
    <{if $topic_rssfeed_link != ""}>
        <div align='right'><{$topic_rssfeed_link}></div>
    <{/if}>

    <{if $displaynav == true}>
        <div style="text-align: center;">
            <{if false== true}>
              <form name="form1" action="<{$xoops_url}>/modules/news/index.php" method="get">

                  <{$topic_select}>
                  <select name="storynum"><{$storynum_options}></select> <{* nombre d'article par page *}>
                  <input type="submit" value="<{$lang_go}>" class="formButton">
              </form>
            <{/if}>


            <{include file="db:news_topic_color_set.tpl"}>

            <hr>
        </div>
    <{/if}>

    <{if $topic_description != '' }>
       <br> <div class="itemDescription <{$topic_color_set}>-item-head"><{$topic_description}></div>
    <{/if}>


<style>
hr.style-seven {
    overflow: visible; /* For IE */
    height: 30px;
    border-style: solid;
    border-color: black;
    border-width: 1px 0 0 0;
    border-radius: 20px;
}
hr.style-seven:before { /* Not really supposed to work, but does */
    display: block;
    content: "";
    height: 30px;
    margin-top: -31px;
    border-style: solid;
    border-color: black;
    border-width: 0 0 1px 0;
    border-radius: 20px;
}

/* pour compenser le décalage verticale provoqué par le style du <hr> */
.divHr{margin-top:-40px;}

.news_scoop{
  border:0px;
    border-radius: 20px;
    background-color: transparent;
    padding:8px;
}

</style>
<{* <{$onglets}> *}>

    <div style="margin: 10px;float:right;" ><{$pagenav}></div>
    <table width='100%' class="news_scoop">
        <tr>
            <{section name=i loop=$columns}>
                <{assign var="chrono" value=0}>
                <td width="<{$columnwidth}>%" valign="top" class="news_scoop">
                  <{foreach item=story from=$columns[i]}>
                  <{*
                    <{if $chrono > 0}><hr class="style-seven">
                    <{assign var='divHr' value='divHr'}>
                    <{else}>
                    <{assign var='divHr' value=''}>
                    <{/if}>

                  *}>

                    <{if $chrono > 0}><br><{/if}>


                    <{* JJDai  (chrono=<{$chrono}>|i=<{$i}>)  *}>
                    <{assign var='class_item_article' value=1}>
                    <{include file="db:news_item.tpl" story=$story}>

                  <{assign var="chrono" value=$chrono+1}>
                  <{/foreach}>
                </td>
            <{/section}>
        </tr>
    </table>

    <div class="pagenav"><{$pagenav}></div>
    <{include file='db:system_notification_select.tpl'}>
</div>
