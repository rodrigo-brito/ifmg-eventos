<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Odin
 * @since 2.2.0
 */

get_header(); ?>
	<input type="hidden" id="minicurso-id" value="<?php the_ID(); ?>">
	<div id="primary" class="<?php echo odin_classes_page_sidebar(); ?>">
		<main id="main-content" class="site-main" role="main">
			<?php
				// Start the Loop.
				while ( have_posts() ) : the_post();
					$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'large' ); ?>
					<div class="col-lg-12 post-type">
						<div class="thumbnail">
							<img src="<?php echo $thumbnail['0']; ?>" alt="<?php the_title(); ?>">
							<div class="caption">
								<h3><?php the_title(); ?></h3>
								<?php
									$vagas = get_field('qtde_vagas');
									$instrutor  = get_field('instrutor');
									$duracao = get_field('duracao');
								?>
								<?php if($duracao): ?>
								<p><strong>Duracao: </strong><?php echo $duracao; ?></p>
								<?php endif; ?>
								<?php if($vagas): ?>
								<p><strong>Vagas: </strong><?php echo $vagas; ?> (<?php echo getTotalInscritos(get_the_ID()); ?>)</p>
								<?php endif; ?>
								<?php if($instrutor): ?>
								<p><strong>Instrutor: </strong><?php echo $instrutor; ?></p>
								<?php endif; ?>
								<p><?php the_content(); ?></p>
								<?php if ( is_user_logged_in() ): ?>
								<p>
									<?php if( verificaCadastroMinicurso( get_the_ID() ) ): ?>
									<button href="#" data-minicurso="<?php echo get_the_ID(); ?>" data-url="<?php echo admin_url('admin-ajax.php'); ?>" class="cadastrar-minicurso btn btn-primary" role="button" style="display: none;">Inscreva-se</button>
									<button href="#" data-minicurso="<?php echo get_the_ID(); ?>" data-url="<?php echo admin_url('admin-ajax.php'); ?>" class="descadastrar-minicurso btn btn-danger" role="button">Cancelar Inscrição</button>
									<?php else: ?>
									<button href="#" data-minicurso="<?php echo get_the_ID(); ?>" class="cadastrar-minicurso btn btn-primary" role="button">Inscreva-se</button>
									<button href="#" data-minicurso="<?php echo get_the_ID(); ?>" class="descadastrar-minicurso btn btn-danger" role="button" style="display: none;">Cancelar Inscrição</button>
									<?php endif; ?>
									<span class="msg-ajax">
										<span class="alert-success msg-success"></span>
										<span class="alert-danger msg-danger"></span>
									</span>
								</p>
								<?php else: ?>
									<div class="alert alert-success" role="alert">
										<p>Para se inscrever em minicursos é preciso estar logado no sistema.</p>
									</div>
									<a href="<?php echo wp_registration_url(); ?>" class="btn btn-primary" role="button">Cadastre-se</a>
									<a href="<?php echo wp_login_url( $_SERVER['REQUEST_URI'] ); ?>" class="btn btn-default" role="button">Faça login</a>
								<?php endif; ?>
							</div>
						</div>
						<?php if( current_user_can( 'manage_options' ) ) : ?>
							<?php getListaCadastrados( get_the_ID() ); ?>
						<?php endif; ?>
					</div>
					<?php
					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;
				endwhile;
			?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
