/**
 * Created by Administrator on 14-3-10.
 */
function playSound(filename){
    document.getElementById("sound").innerHTML='<audio autoplay="autoplay"><source src="' + filename + '.wav" type="audio/mpeg" /><source src="' + filename + '.ogg" type="audio/ogg" /><embed hidden="true" autostart="true" loop="false" src="' + filename +'.wav" /></audio>';
}