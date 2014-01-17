
<h3 class="channel-path"><!--<span>当前位置：</span>管理中心 <span>></span> 真功夫连锁 > 关键词管理 > 关键词列表111 --></h3>
<script type="text/javascript" language="javascript">
    $(document).ready(function () {
        var nav={};
        nav.first={name:'管理中心',url:'/'};
        var id=GetPublicAccountId();
        if(id=='') {
            var list=jQuery.parseJSON('<?=json_encode($_SESSION['permission'])?>');
            nav=NavFix(list,nav)
            Navigation(nav);
        }
        else {
            nav.id=id;
            ConsumeObject('/publicAccount/info', { Id: id}, NavPublicAccountResult,nav);
        }
    });
    function NavPublicAccountResult(r) {
        if (r.errcode == '0') {
            var nav= r.i;
            nav.public={name: '公共号管理',url:'/publicAccount/index/'};
            var id=GetPublicAccountId(),type=2;
            ConsumeObject('/login/permission', { accountId: id, type: type }, NavPermissionResult,nav);
        }
    }
    function NavPermissionResult(r) {
        if (r.errcode == '0') {
            var nav= r.i;
            nav=NavFix(r.data,nav)
            Navigation(nav);
        }
    }
    function NavFix(list,nav) {
        $.each(list,function(index,item) {
            if(location.pathname.toLowerCase()==item.url.toLowerCase()) {
                nav.current={name:item.alias==''?item.name:item.alias,url: GetUrlByPid(item.url,nav.id)};
                nav.parentId=item.parentId;
                return false;
            }
        });
        if(nav.parentId!=null)
            $.each(list,function(index,item) {
                if(nav.parentId==item.id) {
                    nav.parent={name:item.alias==''?item.name:item.alias,url: GetUrlByPid(item.url,nav.id)};
                    return false;
                }
            });
        return nav;
    }
    function Navigation(nav) {
        var span,a;
        $('.channel-path').prop('innerHTML','');
        span=$('<span/>').prop({'innerHTML':'当前位置：'}).appendTo($('.channel-path'));
        a=$('<a/>').prop({'innerHTML':nav.first.name,'href':nav.first.url}).appendTo($('.channel-path'));
        span=$('<span/>').prop({'innerHTML':'&nbsp;>&nbsp;'}).appendTo($('.channel-path'));
        if(nav.public!=null) {
            a=$('<a/>').prop({'innerHTML':nav.public.name,'href':nav.public.url}).appendTo($('.channel-path'));
            span=$('<span/>').prop({'innerHTML':'&nbsp;>&nbsp;'}).appendTo($('.channel-path'));
        }
        if(nav.parent!=null) {
            a=$('<a/>').prop({'innerHTML':nav.parent.name,'href':nav.parent.url}).appendTo($('.channel-path'));
            span=$('<span/>').prop({'innerHTML':'&nbsp;>&nbsp;'}).appendTo($('.channel-path'));
        }
        if(nav.current!=null) {
            a=$('<a/>').prop({'innerHTML':nav.current.name,'href':nav.current.url}).appendTo($('.channel-path'));
            //span=$('<span/>').prop({'innerHTML':'&nbsp;>&nbsp;'}).appendTo($('.channel-path'));
        }
    }
</script>