<?php

/**
 * Arquivo que trata o processo de inscrições de usuários no site
 */

/**
 * Efetua persistência em banco de um registro de participação em evento/minicurso
 * @param  integer 		$user_id     	Identificação do usuário a ser cadastrado
 * @param  string 		$user_name   	Nome do usuário cadastrado
 * @param  integer 		$evento_id   	Identificação do envento a qual foi feita a inscrição
 * @param  string 		$evento_name 	nome do evento a qual foi feita a inscrição
 * @return integer 						Número de linhas inseridas ou NULL em caso de falha
 */
function inscreveUsuario( $user_id, $user_name, $evento_id, $evento_name ){
	global $wpdb;
  	global $tb_incricoes_nome;

	return $wpdb->insert(
		$tb_incricoes_nome,
		array(
			'created_at' => current_time( 'mysql' ),
			'user_id' => $user_id,
			'user_name' => $user_name,
			'evento_id' => $evento_id,
			'evento_name' => $evento_name,
		)
	);
}

/**
 * Efetua descadastramento em banco de um registro de participação em evento/minicurso
 * @param  integer 		$user_id     	Identificação do usuário a ser descadastrado
 * @param  integer 		$evento_id   	Identificação do envento
 * @return integer 						Número de linhas inseridas ou NULL em caso de falha
 */
function desinscreveUsuario( $user_id, $evento_id ){
	global $wpdb;
  	global $tb_incricoes_nome;

  	return $wpdb->delete(
  		$tb_incricoes_nome,
  		array(
  			'user_id' => $user_id,
			'evento_id' => $evento_id
  		)
  	);
}

/**
 * Função de callback da chamada Ajax de cadastro em minicursos/eventos
 */
function cadastrar_minicurso_callback() {
	global $current_user;
    get_currentuserinfo();

	$minicurso_id = intval( $_POST['minicurso'] );
	$user_id = $current_user->ID;
	$user_name = $current_user->first_name . ' ' . $current_user->last_time;
	$evento = get_post( $minicurso_id );

	if(isset($evento) && inscreveUsuario( $user_id, $user_name, $minicurso_id, $evento->post_title)){
		echo json_encode(array('status' => 'success', 'msg' => 'Inscrição realizada com sucesso!'));
	}else{
		echo json_encode(array('status' => 'error', 'msg' => 'Erro ao realizar cadastro, tente mais tarde!'));
	}
	wp_die();
}

add_action( 'wp_ajax_cadastrar_minicurso', 'cadastrar_minicurso_callback' );

/**
 * Função de callback da chamada Ajax de descadastro em minicursos/eventos
 */
function descadastrar_minicurso_callback() {
	global $current_user;
    get_currentuserinfo();

	$minicurso_id = intval( $_POST['minicurso'] );
	$user_id = $current_user->ID;

	$evento = get_post( $minicurso_id );
	if( isset($evento) ){
		if( desinscreveUsuario( $user_id, $minicurso_id ) ){
			echo json_encode(array('status' => 'success', 'msg' => 'Inscrição cancelada com sucesso!'));
		}else{
			echo json_encode(array('status' => 'error', 'msg' => 'Erro ao efetuar cancelamento, tente mais tarde!'));
		}
	}
	wp_die();
}

add_action( 'wp_ajax_descadastrar_minicurso', 'descadastrar_minicurso_callback' );

/**
 * Função de callback da chamada Ajax de cadastro em minicursos/eventos por usuário específico
 */
function descadastrar_minicurso_usuario_callback() {
	$minicurso_id = intval( $_POST['minicurso'] );
	$user_id = intval( $_POST['usuario'] );

	$evento = get_post( $minicurso_id );
	$usuario = get_user_by('id', $user_id);

	if( isset($evento) && isset($usuario) ){
		if( desinscreveUsuario( $user_id, $minicurso_id ) ){
			echo json_encode(array('status' => 'success', 'msg' => 'Inscrição removida com sucesso!'));
		}else{
			echo json_encode(array('status' => 'error', 'msg' => 'Erro ao efetuar cancelamento, tente mais tarde!'));
		}
	}else{
		echo json_encode(array('status' => 'error', 'msg' => 'Erro ao efetuar cancelamento, entidades não identificadas!'));
	}
	wp_die();
}

add_action( 'wp_ajax_descadastrar_minicurso_usuario', 'descadastrar_minicurso_usuario_callback' );

/**
 * Verifica se um o usuário logado está cadastrado em um dado minicurso/evento
 * @param  integer $id identificação do minicurso/evento
 * @return boolean     true se estiver cadastrado, false caso contrário
 */
function verificaCadastroMinicurso( $id ){
	global $wpdb;
	global $current_user;
	global $tb_incricoes_nome;
    get_currentuserinfo();

	$minicurso_id = intval( $id );
	$user_id = $current_user->ID;

	$busca = $wpdb->get_results(
		"SELECT *
		FROM $tb_incricoes_nome
		WHERE user_id = $user_id
		AND evento_id = $minicurso_id"
	);
	return count( $busca ) > 0;
}


/**
 * Retorna lista de cadastrados em um dado minicurso
 * @param  integer $id 	identificação do evento/minicurso
 * @return html         listagem de funcionários
 */
function getListaCadastrados( $id ){
	global $wpdb;
	global $tb_incricoes_nome;

	$minicurso_id = intval( $id );

	$busca = $wpdb->get_results(
		"SELECT *
		FROM $tb_incricoes_nome
		WHERE evento_id = $minicurso_id
		ORDER BY created_at"
	); ?>
	<table id="listagem-inscricoes" class="table table-striped">
		<tr>
			<th>ID</th>
			<th>Usuário</th>
			<th>Cadastrado em</th>
			<th></th>
		</tr>
		<?php foreach ($busca as $usuario) : ?>

		<tr>
			<td><?php echo $usuario->user_id; ?></td>
			<td><?php echo $usuario->user_name; ?></td>
			<td><?php echo date_format( date_create( $usuario->created_at ), 'd/m/Y H:i'); ?></td>
			<td><button data-minicurso="<?php echo $usuario->evento_id; ?>" data-usuario="<?php echo $usuario->user_id; ?>" class="btn btn-danger descadastrar-minicurso-usuario"> <span class="glyphicon glyphicon-trash"></span> </button></td>
		</tr>

		<?php endforeach; ?>
	</table>
	<?php return count( $busca ) > 0;
}

/**
 * Retorna a quantidade de usuários cadastrados
 */
function getTotalInscritos( $id ){
	global $wpdb;
	global $tb_incricoes_nome;

	$minicurso_id = intval( $id );

	$busca = $wpdb->get_results(
		"SELECT *
		FROM $tb_incricoes_nome
		WHERE evento_id = $minicurso_id"
	);
	if(isset($busca)){
		return count($busca) == 1 ? '1 inscrito' : count($busca).' inscritos';
	}else{
		return '0 inscritos';
	}
}

/**
 * Retorna lista de cadastrados em um dado minicurso em chamada AJAX
 * @param  integer $minicurso 	identificação do evento/minicurso
 */
function lista_cadastrados_callback(){
	$minicurso = $_POST['minicurso'];
	getListaCadastrados( $minicurso );
	wp_die();
}

add_action( 'wp_ajax_lista_cadastrados', 'lista_cadastrados_callback' );
