<?php
$tb_incricoes_nome = $wpdb->prefix . 'tb_incricoes';

// Criação da base de dados de inscrições
function register_eati_inscricoes_db() {
   	global $wpdb;
  	global $tb_incricoes_nome;
	if($wpdb->get_var("show tables like '$tb_incricoes_nome'") != $tb_incricoes_nome)
	{
		$sql = "CREATE TABLE " . $tb_incricoes_nome . " (
			`id` mediumint(9) NOT NULL AUTO_INCREMENT,
			`created_at` datetime NOT NULL,
			`user_id` integer NOT NULL,
			`user_name` varchar(64) NOT NULL,
			`evento_id` integer NOT NULL,
			`evento_name` varchar(64) NOT NULL,
			UNIQUE KEY id (id)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

}
add_action( 'init', 'register_eati_inscricoes_db' );

function destaque_cpt() {
    $slider = new Odin_Post_Type(
        'Slider', // Nome (Singular) do Post Type.
        'slider' // Slug do Post Type.
    );

    $slider->set_labels(
        array(
            'menu_name' => __( 'Slider Home', 'odin' )
        )
    );

    $slider->set_arguments(
        array(
            'supports' => array( 'title', 'thumbnail' ),
            'menu_position' => 5,
            'menu_icon' => 'dashicons-images-alt'
        )
    );
}

add_action( 'init', 'destaque_cpt', 1 );


function minicursos_cpt() {
    $minicurso = new Odin_Post_Type(
        'Minicurso', // Nome (Singular) do Post Type.
        'minicurso' // Slug do Post Type.
    );

    $minicurso->set_labels(
        array(
            'menu_name' => __( 'Minicursos', 'odin' )
        )
    );

    $minicurso->set_arguments(
        array(
            'supports' => array( 'title', 'editor', 'thumbnail' ),
            'menu_position' => 6,
            'menu_icon' => 'dashicons-welcome-learn-more'
        )
    );
}

add_action( 'init', 'minicursos_cpt', 1 );


function programacao_cpt() {
    $programacao = new Odin_Post_Type(
        'Evento', // Nome (Singular) do Post Type.
        'evento' // Slug do Post Type.
    );

    $programacao->set_labels(
        array(
            'menu_name' => __( 'Programação', 'odin' )
        )
    );

    $programacao->set_arguments(
        array(
            'supports' => array( 'title', 'thumbnail' ),
            'menu_position' => 7,
            'menu_icon' => 'dashicons-calendar-alt'
        )
    );
}

add_action( 'init', 'programacao_cpt', 1 );


function evento_taxonomy() {
    $taxonomy = new Odin_Taxonomy(
        'Tipo', // Nome (Singular) da nova Taxonomia.
        'tipo', // Slug do Taxonomia.
        'evento' // Nome do tipo de conteúdo que a taxonomia irá fazer parte.
    );

    $taxonomy->set_labels(
        array(
            'menu_name' => __( 'Tipos de eventos', 'odin' )
        )
    );

    $taxonomy->set_arguments(
        array(
            'hierarchical' => true,
        )
    );
}

add_action( 'init', 'evento_taxonomy', 1 );

/**
 *  Advanced Custom Fields - ACF
 */

// 1. customize ACF path
add_filter('acf/settings/path', 'my_acf_settings_path');

function my_acf_settings_path( $path ) {
    // update path
    $path = get_stylesheet_directory() . '/inc/acf/';

    // return
    return $path;

}


// 2. customize ACF dir
add_filter('acf/settings/dir', 'my_acf_settings_dir');

function my_acf_settings_dir( $dir ) {

    // update path
    $dir = get_stylesheet_directory_uri() . '/inc/acf/';

    // return
    return $dir;

}

// 3. Hide ACF field group menu item
//add_filter('acf/settings/show_admin', '__return_false');

// 4. Include ACF
include_once( get_stylesheet_directory() . '/inc/acf/acf.php' );