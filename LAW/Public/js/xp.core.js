/**
 * Created with JetBrains PhpStorm.
 * User: kevin
 * Date: 13-11-12
 * Time: 下午4:08
 * To change this template use File | Settings | File Templates.
 */

$(function () {
    BindDataClick();
})

function BindDataClick(){
    if ($('#navigation_bar')) {
        $('#navigation-bar li').on('click', function () {
            location.href = $(this).data()['url'];
        });
        $('.php-dashboard').on('click', function () {
            location.href = $(this).data()['url'];
        });
        $('.php-publicaccount-nav dd').on('click', function () {
            location.href = $(this).data()['url'];
        });
        $('#php-goto-publicaccount').on('click', function () {
            location.href = $(this).data()['url'];
        });
    }
}
//
//var a = "I Love {0}, and You Love {1},Where are {0}! {4}";
//alert(String.format(a, "You","Me"));
//alert(a.format("You","Me"));
String.format = function () {
    if (arguments.length == 0)
        return null;

    var str = arguments[0];
    for (var i = 1; i < arguments.length; i++) {
        var re = new RegExp('\\{' + (i - 1) + '\\}', 'gm');
        str = str.replace(re, arguments[i]);
    }
    return str;
}

