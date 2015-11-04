</body>
	
<script src="https://code.jquery.com/jquery-1.11.2.js"></script>

<script type="text/javascript" charset="utf-8">
$(document).ready(function(){
 
     var count_clicked;
     var arr=[];
 	 $('a[data-next-q-id]').click(function(e){
 	 
 	 var str_array='';
 	var clicked_text = $(this).data('text');
 	 
 	if ( count_clicked ==undefined) {
 		count_clicked = 1;
 	  
 	}
 	
 	else if (count_clicked  ==2) {
 		$('a.small').attr("disabled", "disabled").off('click');
 		count_clicked = count_clicked+1;
 
 		 
 	}
 	
 	else {
 		count_clicked = count_clicked+1;
 			 
 
 		 
 		
 	}
 	
 	
 	 
 	
 	
 	
 	
 	//console.log(clicked_text);
 	console.log(count_clicked);

  arr[count_clicked]=clicked_text;
  clicked_array = arr.toString();	
 	$(this).css('background-color','#ecd3a8');
 	
 
 while( clicked_array.charAt( 0 ) === ',' )
    clicked_array = clicked_array.slice( 1 );
 
 
 	//console.log(clicked_array);
 	
 	
 	var encodedString = btoa(clicked_array);
 	
 	
 $('input[name="answer"]').attr('value',encodedString);
 $('input[type="submit"]').removeAttr('disabled');
 
 	 });
 	 
 	 
 	 
 	 
 
 
});
</script>
	
<script>
function goBack(path) {
    window.location = path;
}
</script>	
	
</html>