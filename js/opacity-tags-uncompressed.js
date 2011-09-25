jQuery(document).ready(function($){
	/*
	= here's the exact code from the -functions.js file, but uncompressed
		for people (like other developers) who might want to check out the code

 	= the otags_ prefix is not necessary in most cases, but when I
		removed the prefix and installed other scripts with similar names, it caused
		some errors with older versions of jQuery.

	= there are ways to not have to use `...a.get().reverse()).each(...)` for the
	  logic in the second half of the code, but I personally just find this easier to
	  understand
	*/
	var otags_a = $("#opacity-tags-list a"),    	 								// 1. get all the tag links from our generated tag cloud
			otags_number = otags_a.length,  				 	 								// 2. find how many links there are (let's say 30)
 	    otags_increment = 1 / otags_number, 			 								// 3. so if there are 30 links this would make _increment = 0.033333...
	    otags_opacity = ""; 											 								// 4. just so we can use _opacity down below

	$(otags_a.get().reverse()).each(function(i,el){
	    el.id = i + 1;                                           	// 5. each link will be given an id number based on number of links
			otags_opacity = el.id / otags_number - otags_increment;   // 6. so for the 15th link of a list of 30, 15/30 = 0.5 - 0.0333...
      $(this).css({ opacity: otags_opacity });                  // 7. continuing from above, its opacity would then be 0.46666...
	});
});