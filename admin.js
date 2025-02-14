jQuery(document).ready(function(){
	var $ = jQuery; // sure does make the code cleaner
	// Add Keyword Set
	$('a[href="#add-keyword-set"]').on('click',function(e){
		// Duplicate last, hidden element then select the first hidden one and make it visible
		$('table.keyword-sets tr:last-of-type').clone().appendTo('table.keyword-sets');
		$('table.keyword-sets tr:hidden:first').show().find('input[name="keyword_set-name[]"]').val('');
		// Don't add the pointless hash
		e.preventDefault();
	});
});
