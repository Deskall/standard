UIkit.icon(element).svg.then(function(svg) { 
	var size = svg.parent().css("font-size"); 
	svg.height = size;
	svg.width = size;
});
