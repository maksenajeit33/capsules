var code = document.getElementById('code');

code.onclick = function () {
    this.select(true);
    document.execCommand("copy");
    document.getElementById('copy').style.display = 'inline';
    document.getElementById('copy').style.opacity = '.9';
    setTimeout(function(){
    	document.getElementById('copy').style.display = 'none';
    }, 1000);
}