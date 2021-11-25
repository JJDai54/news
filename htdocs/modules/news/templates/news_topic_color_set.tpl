<{if $smarty.const.NEWS_SHOW_TPL_NAME==1}>
<div style="text-align: center; background-color: black;"><span style="color: yellow;">Template : <{$smarty.template}></span></div>
<{/if}>

<style>
.div_color_set{
  width:30px;
  height:30px;
}
</style>

<div>
    <div class="item-legend">
      <a href="<{$smarty.const.XOOPS_URL}>/modules/news/index.php" >
        <{$smarty.const._ALL}>
      </a>

    </div>
    <{foreach item=topic_color_set from=$topics_color_set}>

          <div class="item-legend <{$topic_color_set.color_set}>-item-legend" >
            <a href="<{$smarty.const.XOOPS_URL}>/modules/news/index.php?storytopic=<{$topic_color_set.id}>" >
              <{$topic_color_set.title}>
            </a>

          </div>

    <{/foreach}>

</div><hr>
















