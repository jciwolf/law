<?php
$this->layout()->addJs('WeixinMenu.js');
?>
<?php XP_Lib_Partial::includes('Navigation'); ?>
    <h4 class="path-H">自定义菜单：</h4>
    <div class="x-list-li">
        <table width="1140" border="0" cellspacing="0" cellpadding="0" class="table_zdy">
            <tr>
                <th width="100">显示顺序</th>
                <th width="380">主菜单名称</th>
                <th width="340">触发关键词或url</th>
                <th>操作</th>
            </tr>
            <?php $i=0;?>
            <?php foreach ($menus->button AS $menu) {  $i+=1;?>
                <tr class="parentMenu" data-xpindex="<?= $i; ?>" data-subtotal="<?= count($menu->sub_button);?>">
                    <td><label><input type="text" class="inputstyle inpWidth40 ac" value="<?= $i; ?>"></label></td>
                    <td><span class="ar">
                            <label><input type="text" value="<?= $menu->name; ?>" class="inpWidth190 inputstyle" </label>
                            <label><input  type="button" value="添加子菜单" class="gray_but add_submenu"></label>
                        </span></td>
                    <?php if(count($menu->sub_button)>0) {?>
                        <td><label><input disabled="disabled" type="text" class="inpWidth310 inputstyle" value="<?= isset($menu->key)?$menu->key:$menu->url; ?>" title="url 必须以 http:// 开头"></label></td>
                      <?php } else{?>
                        <td><label><input type="text" class="inpWidth310 inputstyle" value="<?= isset($menu->key)?$menu->key:$menu->url; ?>"></label></td>
                    <?php }?>

                    <td><span class=""><a class="deleteMainMenu" class="delegateMenu" href="javascript:;">删除</a></span></td>
                </tr>

                <?php if(isset($menu->sub_button)){?>
                    <?php $j=0;?>
                 <?php foreach ($menu->sub_button AS $submenu) { $j++;?>
                    <tr class="submenu" data-xpindex="<?= $j; ?>">
                        <td><label><input type="text" class="inputstyle inpWidth40 ac" value="<?= $j; ?>"></label></td>
                        <td><span class="ar"><label class="chart_level">&nbsp;</label><label><input type="text" class="inpWidth190 inputstyle" value="<?= $submenu->name; ?>"></label></span></td>
                        <td><label><input type="text" class="inpWidth310 inputstyle" value="<?= isset($submenu->key)?$submenu->key:$submenu->url; ?>" title="url 必须以 http:// 开头"></label></td>
                        <td><span class=""><a class="deleteSubMenu" href="javascript:;">删除</a></span></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            <?php } ?>

            <tfoot>
            <tr>
                <td colspan="4"><input data-mainMenuTotal="<?= count($menus->button);?>" type="button" value="添加主菜单" class="gray_but add_mainmenu"></td>
            </tr>
            </tfoot>
        </table>
        <p class="graya5a">&nbsp;&nbsp;&nbsp;*温馨提示：最多可创建3个主菜单，每个主菜单下最多可以创建5个子菜单。</p>
        <div class="butbox13">
            <input id="edit" style="display: none" type="button" value="编　辑" class="x-green_but">
            <input id="savemenu" style="display:display" type="button" value="保　存" class="x-green_but">&nbsp;&nbsp;&nbsp;
            <input id="cancle" type="button" value="取　消" class="gray_but" style="display: none;">
            <span id="Result"></span>
        </div>
        <!--
        <div class="butbox pt20">
            <input id="edit" style="display: none" type="button" value="编 辑" class="x-green_but">&nbsp;&nbsp;&nbsp;
            <input type="button" value="上 传" class="gray_but">&nbsp;&nbsp;&nbsp;
            <input type="button" value="浏 览" class="gray_but">
        </div>
        -->
    </div>
    <!--
    <h4 class="path-H">浏览效果图：</h4>
    <div class="x-list-li">
        <img src="/images/phone.gif" width="239" height="507">
    </div>
    -->
    <div class="clear"></div>

<div class="mask-div" style="display: none;">
    <h3>批量操作</h3>
    <div class="warm">
        <img src="/images/warm.png"><span>确定要批量启动码？</span>
    </div>
    <div class="clear"></div>
    <div class="mask-input"><input type="button" value="确认" class="x-green_but"><input type="button" value="取消" class="gray_but"></div>
</div>

