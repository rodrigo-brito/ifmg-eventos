<?php

/**
 * Arquivo que trata o processo de inscrições de usuários no site
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

function descadastrar_minicurso_usuario_callback() {
	global $current_user;
    get_currentuserinfo();

	$minicurso_id = intval( $_POST['minicurso'] );
	$user_id = intval( $_POST['usuario'] );

	$evento = get_post( $minicurso_id );
	$usuario = get_user_by('id', $user_id);

	if( isset($evento) && isset($usuario) ){
		if( desinscreveUsuario( $user_id, $minicurso_id ) ){
			echo json_encode(array('status' => 'success', 'msg' => 'Inscrição cancelada com sucesso!'));
		}else{
			echo json_encode(array('status' => 'error', 'msg' => 'Erro ao efetuar cancelamento, tente mais tarde!'));
		}
	}
	wp_die();
}

add_action( 'wp_ajax_descadastrar_minicurso_usuario', 'descadastrar_minicurso_callback' );

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