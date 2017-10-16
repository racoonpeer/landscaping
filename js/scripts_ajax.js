$(document).ready(function(){ 
    resizeHS();
});
function getSize(){
        var scnWid,scnHei;
	if (self.innerHeight){ // all except Explorer
            scnWid = self.innerWidth;
            scnHei = self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight){ // Explorer 6 Strict Mode
            scnWid = document.documentElement.clientWidth;
            scnHei = document.documentElement.clientHeight;
	} else if (document.body) { // other Explorers
            scnWid = document.body.clientWidth;
            scnHei = document.body.clientHeight;
        }
	return {width:scnWid, height:scnHei};
}
            
function resizeHS(id){
    if(this.parent.length){
        var hs = parent.window.hs;
        //var objSize = getSize();
        if(hs.getExpander(id)){
            var exp = hs.getExpander(id);
            //exp.iframe.style.height = objSize.height;
            exp.reflow();
            $('body').css('overflow', 'hidden');
        }
    }
}
function closeHS(){
    if(this.parent.length) parent.window.hs.close();
    else location='/';
}
function parentRedirect(obj){
    if(this.parent.length){
        parent.window.hs.close();
        parent.location=obj.href;
    } else location=obj.href;
}
function localRedirect(obj){
    if(this.parent.length){
        var hs = parent.window.hs;
        var exp = hs.getExpander();
        location=obj.href;
        if(exp!==undefined) 
            exp.heading.innerHTML = obj.title;
    } else location=obj.href;
}