// JavaScript


$(function () {

});
function radio(title) {
    var labels = document.getElementById(title).getElementsByTagName('label');
    var radios = document.getElementById(title).getElementsByTagName('input');
    for (i = 0, j = labels.length; i < j; i++) {
        labels[i].onclick = function () {
            if (this.className == '') {
                for (k = 0, l = labels.length; k < l; k++) {
                    labels[k].className = '';
                    radios[k].checked = false;
                }
                this.className = 'checked';
                try {
                    document.getElementById($(this).attr("name")).checked = true;
                } catch (e) {
                }
            }
        }
    }

}
