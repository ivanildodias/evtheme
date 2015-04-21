<?php
/**
 * Tags de modelo personalizadas para este tema
 * 
 * Eventualmente, algumas das funcionalidades aqui poderia ser substituída
 * por características do wordpress
 * 
 * @package Estúdio Viking
 * @since 1.0
 */


/**
 * Favicon personalizado
 * 
 * @since Estúdio Viking 1.0
 * ----------------------------------------------------------------------------
 */
function my_favicon(){
	$favicon 			= ICONS_URI . '/favicon.ico';
	$apple_icons 		= viking_readdir( ICONS_PATH, 'png' );
	$apple_icons_name 	= array_keys( $apple_icons );
	$apple_icons_count 	= count( $apple_icons_name );
	$apple_icons_size 	= str_replace( '-', '', $apple_icons_name);
	$apple_icons_size 	= str_replace( 'appletouchicon', '', $apple_icons_size);
	
	$favicons  = '<!-- Favicon IE 9 -->';
	$favicons .= '<!--[if lte IE 9]><link rel="icon" type="image/x-icon" href="' . $favicon . '" /> <![endif]-->';
	
	$favicons .= '<!-- Favicon Outros Navegadores -->';
	$favicons .= '<link rel="shortcut icon" type="image/png" href="' . $favicon . '" />';
	
	$favicons .='<!-- Favicon Apple -->';
	
	for ( $i = 0; $i < $apple_icons_count; $i++ ) :
		$size = ( $apple_icons_size[$i] == '' ) ? '' : ' sizes="' . $apple_icons_size[$i] . '"';
		
		$favicons .='<link rel="apple-touch-icon"' . $size . ' href="' . ICONS_URI . $apple_icons_name[$i] . '.png" />';
	endfor;
	
	echo $favicons;
}
add_action( 'wp_head', 'my_favicon' );
add_action( 'admin_head', 'my_favicon' );
add_action( 'login_head', 'my_favicon' );


/**
 * Ícone personalizado para a tela de login
 * 
 * @since Estúdio Viking 1.0
 * ----------------------------------------------------------------------------
 */
function viking_login_icon(){
	$login_icon_url    = IMAGES_URI . '/viking-logo.svg';
	$login_icon_width  = 100;
	$login_icon_height = 100;
	
	$output  = '
		<style id="viking_login_icon" type="text/css">
			.login h1 a {
				background-image: url( "' . $login_icon_url . '" );
				background-size: ' . $login_icon_width . 'px auto;
				width: ' . $login_icon_width . 'px;
				height: ' . $login_icon_height . 'px;
			}
		</style>
	';
	
	echo $output;
}
add_action( 'login_enqueue_scripts', 'viking_login_icon' );


/**
 * Remove 'text/css' dos links de folhas de estilo no head
 * 
 * @since Estúdio Viking 1.0
 * ----------------------------------------------------------------------------
 */
function my_style_remove( $tag ) {
	return preg_replace( '~\s+type=["\'][^"\']++["\']~', '', $tag );
}
add_filter( 'style_loader_tag', 'my_style_remove' );


/**
 * Título das páginas
 * 
 * @since Estúdio Viking 1.0
 * ----------------------------------------------------------------------------
 */
function my_wp_title( $title, $sep ) {
	$site_name = get_bloginfo( 'name', 'display' );
	$site_description = get_bloginfo( 'description', 'display' );
	
	if ( is_page() || is_archive() ) $title .= ' - ' . $site_description;
	
	return str_replace( "$site_name $sep $site_description", "$site_name - $site_description", $title );
}
add_filter( 'wp_title', 'my_wp_title', 10, 2 );


/**
 * Adiciona o atributo 'role' aos menus de navegação
 * 
 * @since Estúdio Viking 1.0
 * ----------------------------------------------------------------------------
 */
function add_role_navigation_to_nav_menu( $nav_menu, $args ) {
	if( 'nav' != $args->container ) return $nav_menu;
	
	return str_replace( '<'. $args->container, '<'. $args->container . ' role="navigation"', $nav_menu );
}
add_filter( 'wp_nav_menu', 'add_role_navigation_to_nav_menu', 10, 2 );


/**
 * Títulos personalizados para páginas arquivos
 * 
 * @since Estúdio Viking 1.0
 * ----------------------------------------------------------------------------
 */
function my_archive_title( $title ) {
	if ( is_category() ) :
		$title = sprintf( __( 'Posts in category: %s', 'viking-theme' ), single_cat_title( '', false ) );
	elseif ( is_tag() ) :
		$title = sprintf( __( 'Posts in tag: %s', 'viking-theme' ), single_tag_title( '', false ) );
	elseif ( is_author() ) :
		$title = sprintf( __( 'Posts of the author: %s', 'viking-theme' ), get_the_author() );
	elseif ( is_day() ) :
		$title = sprintf( __( 'Posts of the day: %s', 'viking-theme' ), get_the_date( get_option( 'date_format' ) ) );
	elseif ( is_month() ) :
		$title = sprintf( __( 'Posts of the month: %s', 'viking-theme' ), get_the_date( 'F\/Y' ) );
	elseif ( is_year() ) :
		$title = sprintf( __( 'Posts of the year: %s', 'viking-theme' ), get_the_date( 'Y' ) );
	endif;
	
	return $title;
}
add_filter( 'get_the_archive_title', 'my_archive_title' );


/**
 * Paginação de Artigos
 * 
 * @since Estúdio Viking 1.0
 * ----------------------------------------------------------------------------
 */
function viking_post_pagination() {
	the_posts_pagination( array(
		'prev_text'          => '<i class="fa fa-arrow-left"></i> ' . '<span class="meta-nav screen-reader-text">' . __( 'Previous page', 'viking-theme' ) . ' </span>',
		'next_text'          => '<span class="meta-nav screen-reader-text">' . __( 'Next page', 'viking-theme' ) . ' </span>' . ' <i class="fa fa-arrow-right"></i>',
		'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'viking-theme' ) . ' </span>',
	) );
	
	echo '<!-- .pagination -->';
}


/**
 * Cria link Ver Artigo personalizado para a postagem
 * 
 * @since Estúdio Viking 1.0
 * ----------------------------------------------------------------------------
 */
function viking_view_article( $more ) {
	global $post;
	
	$tagmore  = '...<br />';
	$tagmore .= '<a class="button view-article" ';
	$tagmore .= 'href="' . get_permalink( $post->ID ) . '" ';
	$tagmore .= 'title ="Ver artigo: ' . get_the_title() . '">';
	$tagmore .= 'Ver artigo';
	$tagmore .= '</a>';
	
	return $tagmore;
}
add_filter( 'excerpt_more', 'viking_view_article' );


/**
 * Coleta informações da imagem destacada da postagem
 * 
 * @since Estúdio Viking 1.0
 * ----------------------------------------------------------------------------
 */
function viking_get_thumb_meta( $thumbnail_id, $meta ) {
	$thumb = get_post( $thumbnail_id );
	
	$thumb = array(
		'alt'			=> get_post_meta( $thumb->ID, '_wp_attachment_image_alt', true ),
		'caption'		=> $thumb->post_excerpt,
		'description'	=> $thumb->post_content,
		'href'			=> get_permalink( $thumb->ID ),
		'src'			=> $thumb->guid,
		'title'			=> $thumb->post_title
	);
	
	return $thumb[$meta];
}


/**
 * Miniaturas personalizadas para as postagens
 * 
 * @since Estúdio Viking 1.0
 * ----------------------------------------------------------------------------
 */
function viking_post_thumb( $size = 'post-size' ) {
	$thumb_id = get_post_thumbnail_id();
	
	$thumb_link_full = wp_get_attachment_image_src( $thumb_id, 'full' );
	$thumb_link_full = $thumb_link_full[0];
	
	$thumb_caption = viking_get_thumb_meta( $thumb_id, 'caption' );
	?>
	
	<figure class="post-thumb<?php if ( is_page() ) : echo ' col_4'; endif; ?>">
		<a class="link-thumb img-link"
		   href="<?php if ( is_single() ) : echo $thumb_link_full; else : the_permalink(); endif; ?>"
		   title="<?php the_title(); ?>"
		   <?php if ( is_single() ) : ?>data-lightbox="post-<?php the_ID(); ?>" data-title="<?php echo $thumb_caption; ?>"<?php endif; ?>>
			<?php the_post_thumbnail( $size, array( 'class' => 'img-thumb' ) ); ?>
		</a>
	</figure>
	<!-- .post thumbnail -->
	
	<?php
}


/**
 * Detalhes personalizadas para as postagens
 * 
 * @since Estúdio Viking 1.0
 * ----------------------------------------------------------------------------
 */
function viking_post_details() {
	$post = get_post();
	foreach ( ( array ) get_the_category( $post->ID ) as $categ ) :
		$categ_slug = sanitize_html_class( $categ->slug, $categ->term_id );
	endforeach;
	?>
	<section class="post-details">
		<?php viking_post_thumb(); ?>
		
		<span class="post-categ shadow categ-<?php echo $categ_slug; ?>"><?php the_category( ', ' ); ?></span>
		
		<?php if ( is_single() ) : ?>
			<div class="post-details-bar">
				<span class="post-author"><?php the_author_posts_link(); ?></span> | 
				<span class="post-date"><?php viking_date_link(); ?></span> | 
				<span class="post-comments"><?php viking_comment_link(); ?></span>
			</div>
		<?php endif; ?>
	</section>
	<!-- .post details -->
	<?php
		edit_post_link( __( 'Edit', 'viking-theme' ), '<span class="edit-link">', '</span>' );
}


/**
 * Remove as dimensões de largura e altura das miniaturas
 * que evitam imagens fluidas em the_thumbnail
 * 
 * @since Estúdio Viking 1.0
 * ----------------------------------------------------------------------------
 */
function remove_thumbnail_dimensions( $html ) {
	$html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
	return $html;
}
add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10 );
add_filter( 'image_send_to_editor', 'remove_thumbnail_dimensions', 10 );


/**
 * Remove valores invalidos do atributo "rel" na lista de categorias
 * 
 * @since Estúdio Viking 1.0
 * ----------------------------------------------------------------------------
 */
function remove_category_rel_from_category_list( $thelist ) {
    return str_replace( 'rel="category tag"', 'rel="tag"', $thelist );
}
add_filter( 'the_category', 'remove_category_rel_from_category_list' );


/**
 * Cria datas como links
 * 
 * @since Estúdio Viking 1.0
 * ----------------------------------------------------------------------------
 */
function viking_date_link() {
	$ano		= get_the_time( 'Y' );
	
	$mes		= get_the_time( 'm' );
	$mes_nome	= get_the_time( 'F' );
	$data_mes	= get_the_time( 'F \d\e Y' );
	
	$dia		= get_the_time( 'd' );
	$data_dia	= get_the_time( 'd \d\e F \d\e Y' );
	
	$data_title	= get_the_time( 'l, d \d\e F \d\e Y, h:i a' );
	$data_full	= esc_attr( get_the_date( 'c' ) );
	
	$link_dia	= get_day_link( $ano, $mes, $dia );
	$link_mes	= get_month_link( $ano, $mes );
	$link_ano	= get_year_link( $ano );
	
	$data  = '<time class="date" title="'. $data_title .'" datetime="' . $data_full . '">';
	$data .= '<a href="' . $link_dia . '" title="Arquivos de ' . $data_dia . '">' . $dia . '</a> de ';
	$data .= '<a href="' . $link_mes . '" title="Arquivos de ' . $data_mes . '">' . $mes_nome . '</a> de ';
	$data .= '<a href="' . $link_ano . '" title="Arquivos de ' . $ano . '">' . $ano . '</a>';
	$data .= '</time>';
	
	echo $data;
}


/**
 * Link para os comentários
 * 
 * @since Estúdio Viking 1.0
 * ----------------------------------------------------------------------------
 */
function viking_comment_link() {
	if ( comments_open( get_the_ID() ) )
		comments_popup_link(
			__( 'Leave your thoughts', 'viking-theme' ),
			__( '1 comment', 'viking-theme' ),
			__( '% comments', 'viking-theme' )
		);
}


/**
 * Criar resumos personalizados
 * 
 * @since Estúdio Viking 1.0
 * ----------------------------------------------------------------------------
 */
function viking_excerpt( $length_callback = '', $more_callback = '' ) {
	global $post;
	
    if ( function_exists( $length_callback ) ) {
    	add_filter( 'excerpt_length', $length_callback );
	}
	
	if ( function_exists( $more_callback ) ) {
		add_filter( 'excerpt_more', $more_callback );
	}
	
	$output = get_the_excerpt();
	$output = apply_filters( 'wptexturize', $output );
	$output = apply_filters( 'convert_chars', $output );
	$output = '<section class="post-content clear"><p>' . $output . '</p></section>';
	
	echo $output;
}


/**
 * Tamanho em palavras para os resumos personalizados.
 * Uso: viking_excerpt( 'viking_index' );
 * 
 * @since Estúdio Viking 1.0
 * ----------------------------------------------------------------------------
 */
function viking_index( $length ) {
	return 50;
}


/**
 * Tamanho em palavras para os resumos personalizados do slider.
 * Uso: viking_excerpt( 'viking_length_slider' );
 * 
 * @since Estúdio Viking 1.0
 * ----------------------------------------------------------------------------
 */
function viking_length_slider( $lenght ) {
	return 10;
}


/**
 * Navegação dos comentários
 * 
 * @since Estúdio Viking 1.0
 * ----------------------------------------------------------------------------
 */
function viking_comment_nav() {
	// Há comentários para navegação?
	if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
		<nav class="nav comment-nav" role="navigation">
			<h2 class="screen-reader-text"><?php _e( 'Comment navigation', 'viking-theme' ); ?></h2>
			<div class="nav-links">
				<?php
					if ( $prev_link = get_previous_comments_link( __( 'Older comments', 'viking-theme' ) ) ) :
						printf( '<div class="nav-previous">%s</div>', $prev_link );
					endif;
	
					if ( $next_link = get_next_comments_link( __( 'Newer comments', 'viking-theme' ) ) ) :
						printf( '<div class="nav-next">%s</div>', $next_link );
					endif;
				?>
			</div><!-- .nav-links -->
		</nav><!-- .comment-nav -->
	<?php endif;
}
