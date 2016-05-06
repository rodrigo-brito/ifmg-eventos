jQuery(document).ready(function($) {
	// fitVids.
	$( '.entry-content' ).fitVids();

	// Responsive wp_video_shortcode().
	$( '.wp-video-shortcode' ).parent( 'div' ).css( 'width', 'auto' );

	/**
	 * Odin Core shortcodes
	 */

	// Tabs.
	$( '.odin-tabs a' ).click(function(e) {
		e.preventDefault();
		$(this).tab( 'show' );
	});

	// Tooltip.
	$( '.odin-tooltip' ).tooltip();

	//Ajar Minicurso
	$('.cadastrar-minicurso').click(function(e) {
		e.preventDefault();
		var botao = $(this);
		var url = ajax.url;
		var id_minicurso = $(this).data('minicurso');
		var valorAnterior = botao.html();
		botao.html('Salvando...');
		$.post(url, {action: 'cadastrar_minicurso', minicurso: id_minicurso}, function(data) {
			if(data.status === 'success'){
				botao.hide();
				$('.descadastrar-minicurso').show();
				$('.msg-success').html(data.msg);
			}else{
				$('.msg-danger').html(data.msg);
			}
			botao.html(valorAnterior);
			atualizaListagem();
		}, 'json');
	});
	$('.descadastrar-minicurso').click(function(e) {
		e.preventDefault();
		var botao = $(this);
		var url = ajax.url;
		var id_minicurso = $(this).data('minicurso');
		var valorAnterior = botao.html();
		botao.html('Cancelando...');
		$.post(url, {action: 'descadastrar_minicurso', minicurso: id_minicurso}, function(data) {
			if(data.status === 'success'){
				botao.hide();
				$('.msg-success').html(data.msg);
				$('.cadastrar-minicurso').show();
			}else{
				$('.msg-danger').html(data.msg);
			}
			botao.html(valorAnterior);
			atualizaListagem();
		}, 'json');
	});
	$('#listagem-inscricoes').on('click', '.descadastrar-minicurso-usuario', function(e) {
		e.preventDefault();
		var botao = $(this);
		var id_minicurso = $(this).data('minicurso');
		var id_usuario = $(this).data('usuario');
		console.log(id_minicurso+' - '+id_usuario);
		var valorAnterior = botao.html();
		botao.html('Removendo...');
		$.post(ajax.url, {action: 'descadastrar_minicurso_usuario', minicurso: id_minicurso, usuario: id_usuario}, function(data) {
			if(data.status === 'success'){
				$('.msg-success').html(data.msg);
			}else{
				$('.msg-danger').html(data.msg);
			}
			botao.html(valorAnterior);
			atualizaListagem();
		}, 'json');
	});

	var atualizaListagem = function(){
		$.ajax({
			url: ajax.url,
			type: 'POST',
			dataType: 'html',
			data: {action: 'lista_cadastrados', minicurso: $('#minicurso-id').val()}
		})
		.done(function(data) {
			$('#listagem-inscricoes').html(data).fadeIn('fast');
		})
		.fail(function() {
			$('#listagem-inscricoes').html('Erro ao recuperar dados da listagem').fadeIn('fast');
		});
	};
});
