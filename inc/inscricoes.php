<?php

/**
 * Arquivo que trata o processo de inscrições de usuários no site
 */

function inscreveUsuario( $user_id, $user_name, $evento_id, $evento_name ){
	global $wpdb;
  	global $tb_incricoes_nome;
	$wpdb->insert(
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

function cadastrar_minicurso_callback() {
	global $current_user;
    get_currentuserinfo();

	$minicurso_id = intval( $_POST['minicurso'] );
	$user_id = $current_user->ID;

	$participantes = get_field('participantes', $minicurso_id);
	$participantes_ids = [];
	if( $participantes ){
		foreach ($participantes as $participante) {
			if($participante['ID'] == $user_id){ //já cadastrado
				echo 'sucesso';
				wp_die();
			}
			$participantes_ids[] = $participante['ID'];
		}
	}
	$participantes_ids[] = $user_id;
	update_field('field_5614724c1c8d1', $participantes_ids, $minicurso_id);
    if( verificaCadastroMinicurso( $minicurso_id ) ){
    	echo 'sucesso';
    }else{
    	echo 'erro';
    }
	wp_die();
}

add_action( 'wp_ajax_cadastrar_minicurso', 'cadastrar_minicurso_callback' );

function descadastrar_minicurso_callback() {
	global $current_user;
    get_currentuserinfo();

	$minicurso_id = intval( $_POST['minicurso'] );
	$user_id = $current_user->ID;

	$participantes = get_field('participantes', $minicurso_id);
	$participantes_ids = [];
	if( $participantes ){
		foreach ($participantes as $participante) {
			if($participante['ID'] != $user_id){
				$participantes_ids[] = $participante['ID'];
			}
		}
	}
	update_field('field_5614724c1c8d1', $participantes_ids, $minicurso_id);
    if( verificaCadastroMinicurso( $minicurso_id ) ){
    	echo 'erro';
    }else{
    	echo 'sucesso';
    }
	wp_die();
}

add_action( 'wp_ajax_descadastrar_minicurso', 'descadastrar_minicurso_callback' );

function descadastrar_minicurso_usuario_callback() {
	$minicurso_id = intval( $_POST['minicurso'] );
	$user_id = intval( $_POST['usuario'] );

	$participantes = get_field('participantes', $minicurso_id);
	$participantes_ids = [];
	if( $participantes ){
		foreach ($participantes as $participante) {
			if($participante['ID'] != $user_id){
				$participantes_ids[] = $participante['ID'];
			}
		}
	}
	update_field('field_5614724c1c8d1', $participantes_ids, $minicurso_id);
    if( verificaCadastroMinicurso( $minicurso_id ) ){
    	echo 'erro';
    }else{
    	echo 'sucesso';
    }
	wp_die();
}

add_action( 'wp_ajax_descadastrar_minicurso_usuario', 'descadastrar_minicurso_usuario_callback' );

function verificaCadastroMinicurso( $minicurso_id ){
	global $current_user;
    get_currentuserinfo();
	$user_id = $current_user->ID;

	$participantes = get_field('participantes', $minicurso_id);
	if( $participantes ){
		foreach ($participantes as $participante) {
			if($participante['ID'] == $user_id){
				return true;
			}
		}
	}
	return false;
}