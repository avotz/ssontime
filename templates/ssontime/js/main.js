$(function() {

	$('#tours').on('click',function(){

		$('.descripcion-tours').toggle('fast');
		$('.descripcion-rates').hide();
		$('.descripcion-locations').hide();

	});
	$('#rates').on('click',function(){

		$('.descripcion-rates').toggle('fast');
		$('.descripcion-tours').hide();
		$('.descripcion-locations').hide();

	});
	$('#location').on('click',function(){

		$('.descripcion-locations').toggle('fast');
		$('.descripcion-rates').hide();
		$('.descripcion-tours').hide();

	});


});
