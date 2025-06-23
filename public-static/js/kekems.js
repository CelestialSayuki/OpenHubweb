/*
 Technical Support：Zgc.Seven
*/
$(document).ready(function() {
	$('.inactive').click(function(){
		if($(this).siblings('ul').css('display')=='none'){
			$(this).parent('li').siblings('li').removeClass('inactives');
			$(this).addClass('inactives');
			$(this).siblings('ul').slideDown(100).children('li');
			$(this).parents('li').siblings('li').children('ul').slideUp(100);
			$(this).parents('li').siblings('li').children('ul').parent('li').children('a').removeClass('inactives');
			/*$(this).parent('li').siblings('li').removeClass('inactives');
			$(this).addClass('inactives');
			$(this).siblings('ul').slideDown(100).children('li');
			if($(this).parents('li').siblings('li').children('ul').css('display')=='block'){
				$(this).parents('li').siblings('li').children('ul').parent('li').children('a').removeClass('inactives');
				$(this).parents('li').siblings('li').children('ul').slideUp(100);
			}*/
		}else{
			//控制自身变成+号
			$(this).removeClass('inactives');
			//控制自身菜单下子菜单隐藏
			$(this).siblings('ul').slideUp(100);
			//控制自身子菜单变成+号
			$(this).siblings('ul').children('li').children('ul').parent('li').children('a').addClass('inactives');
			//控制自身菜单下子菜单隐藏
			$(this).siblings('ul').children('li').children('ul').slideUp(100);
			//控制同级菜单只保持一个是展开的（-号显示）
			$(this).siblings('ul').children('li').children('a').removeClass('inactives');
		}
	})
});
$(document).ready(function(){
	//检测是否dark模式样式
	if(window.matchMedia('(prefers-color-scheme: dark)').matches){
		$('a').click(function(){
			$('a:not(this)').css('color','rgb(172, 172, 172)');
			$(this).css('color','rgb(245, 245, 245)');
		})
	}else{
		$('a').click(function(){
			$('a:not(this)').css('color','#808080');
			$(this).css('color','#19222B');
		})
	}
});