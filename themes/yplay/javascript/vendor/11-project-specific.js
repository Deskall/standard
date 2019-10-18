$(document).ready(function(){
	if ($(".sidebar-menu").length > 0){
		var sidebarWidth = Math.ceil($(".sidebar-menu").width()) / 4;
		console.log(sidebarWidth);
		$(".sidebar-menu").css("transform-origin", 'calc(50% + '+sidebarWidth+'px + 3px) 0');
		$(".sidebar-menu").show();
	}
});


