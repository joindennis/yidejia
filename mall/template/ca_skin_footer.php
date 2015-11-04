<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1254890388'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s95.cnzz.com/z_stat.php%3Fid%3D1254890388' type='text/javascript'%3E%3C/script%3E"));</script>


</body>
	
<script src="https://code.jquery.com/jquery-1.11.2.js"></script>

<script type="text/javascript" charset="utf-8">
$(document).ready(function(){
 
 	$('a[data-id]').click(function(e){
 	
 	 
 	var clicked_text = $(this).data('text');
 	
 	
 	
 	$('a[data-id]').css('background-color','#b6a189'); 	 // regular colour
 	$(this).css('background-color','#ecd3a8'); 			 // clicked colour
 	
 	$(this).addClass('on'); 
 	
	var encodedString = btoa(clicked_text);
	//console.log("encoded string= "+encodedString);
 	
	$('input[name="answer"]').attr('value',encodedString);
	$('input[type="submit"]').removeAttr('disabled');
 	
// console.log($('input[name="answer"]').val('answer'));
 //console.log(clicked_text);
 	 });
 	 
 
 
 

});
</script>
	
<script>
function goBack(number) {
   console.log(number);
   
   $('input[name="question"]').attr('value', number);
   document.getElementById("new_quiz_user_response").submit();
}
</script>	





<script language='JavaScript' type='text/javascript'>
function refreshCaptcha()
{
	var img = document.images['captchaimg'];
	img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
}

function refreshCaptcha0()
{
	var img = document.images['captchaimg0'];
	img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
}

</script> 


<script type="text/javascript">
var addthis_share = {
   url: "https://www.yidejia.ca/skin",
   title: "Find your Skin Type"
}

</script>


<script type="text/javascript">
    var _maq = _maq || [];
    _maq.push(['_setAccount', '网站标识']);

    (function() {
        var ma = document.createElement('script'); ma.type = 'text/javascript'; ma.async = true;
        ma.src = ('https:' == document.location.protocol ? 'https://jzstatic' : 'http://jzstatic') + '.qiniudn.com/Js/ydj/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ma, s);
    })();
</script>
	
</html>