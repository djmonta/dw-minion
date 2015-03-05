<?php
if ( !isset( $content_width ) )
	$content_width = 620;

if ( !function_exists( 'dw_minion_setup' ) ) {
	function dw_minion_setup() {
		load_theme_textdomain( 'dw-minion', get_template_directory() . '/languages' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-formats', array( 'gallery', 'video', 'quote', 'link' ) );
		add_theme_support( 'post-thumbnails' );

		// Added by monta
		add_theme_support( 'title-tag' );
		
		add_editor_style();
	}
}
add_action( 'after_setup_theme', 'dw_minion_setup' );

function dw_minion_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Main Sidebar', 'dw-minion' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => __( 'Secondary Sidebar', 'dw-minion' ),
		'id'            => 'sidebar-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
        'name' => __( 'Top Sidebar', 'dw-minion' ),
        'id' => 'top-sidebar',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );
}
add_action( 'widgets_init', 'dw_minion_widgets_init' );

function dw_minion_scripts() {
	wp_enqueue_style('dw-minion-main', get_template_directory_uri() . '/assets/css/main.css' ); // green
	wp_enqueue_style( 'dw-minion-style', get_stylesheet_uri() );
	wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/assets/js/modernizr-2.6.2.min.js' );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'dw-minion-main-script', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), false, true );
	wp_enqueue_script( 'bootstrap-transition', get_template_directory_uri() . '/assets/js/bootstrap-transition.js', array('jquery'), false, true );
	wp_enqueue_script( 'bootstrap-carousel', get_template_directory_uri() . '/assets/js/bootstrap-carousel.js', array('jquery'), false, true );
	wp_enqueue_script( 'bootstrap-collapse', get_template_directory_uri() . '/assets/js/bootstrap-collapse.js', array('jquery'), false, true );
	wp_enqueue_script( 'bootstrap-tab', get_template_directory_uri() . '/assets/js/bootstrap-tab.js', array('jquery'), false, true );
}
add_action( 'wp_enqueue_scripts', 'dw_minion_scripts' );

require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/extras.php';
require get_template_directory() . '/inc/widgets.php';
require get_template_directory() . '/inc/customizer.php';

// features image on social share
add_action('wp_head', 'minion_features_image_as_og_image');
function minion_features_image_as_og_image() {
	global $post;
	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'medium'); 
	?><meta property="og:image" content="<?php echo $thumb[0] ?>" /><?php
}

// load style for dw qa plugin
if( !function_exists('dwqa_minion_scripts') ){
	function dwqa_minion_scripts(){
	    wp_enqueue_style( 'dw-minion-qa', get_stylesheet_directory_uri() . '/dwqa-templates/style.css' );
	}
	add_action( 'wp_enqueue_scripts', 'dwqa_minion_scripts' );
}

// Top sidebar
add_action( 'dw_minion_top_sidebar', 'dw_minion_top_sidebar' );
function dw_minion_top_sidebar() {
    ?><div class="top-sidebar"><?php dynamic_sidebar( 'top-sidebar' ); ?></div><?php
}

// TGM plugin activation
require_once get_template_directory() . '/inc/class-tgm-plugin-activation.php';
function alx_plugins() {
	$plugins = array(
		array(
			'name' 				=> 'DW Question & Answer',
			'slug' 				=> 'dw-question-answer',
			'source'			=> false,
			'required'			=> false,
			'force_activation' 	=> false,
			'force_deactivation'=> false,
		),
		array(
			'name' 				=> 'Contact Form 7',
			'slug' 				=> 'contact-form-7',
			'required'			=> false,
			'force_activation' 	=> false,
			'force_deactivation'=> false,
		)
	);	
	tgmpa( $plugins );
}
add_action( 'tgmpa_register', 'alx_plugins' );

/**
 * hentry クラスを除くフィルタ
 * added by monta 2014.09.19
 */
function remove_hentry( $classes ) {
 
	$classes = array_diff($classes, array('hentry'));	
	return $classes;
}

 
/** autopagerize 対応
 *  added by monta 2015.3.5
 */
function custom_next_posts_link_attributes($args = null) {
	return 'rel="next"';
}
function custom_previous_posts_link_attributes($args = null) {
	return 'rel="previous"';
}
 
add_filter('next_posts_link_attributes', 'custom_next_posts_link_attributes');
add_filter('previous_posts_link_attributes', 'custom_previous_posts_link_attributes');


/*
 * JS, CSS からバージョン番号を非表示にする
 * http://kwski.net/wordpress/1058/
 */
// remove wp version param from any enqueued scripts
function vc_remove_wp_ver_css_js( $src ) {
    if ( strpos( $src, 'ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}
add_filter( 'style_loader_src', 'vc_remove_wp_ver_css_js', 9999 );
add_filter( 'script_loader_src', 'vc_remove_wp_ver_css_js', 9999 );

/*
 * アイキャッチに登録した画像をRSSにも含める。
 * http://kachibito.net/wp-code/show-post-thumbnails-in-feeds
 */
function post_thumbnail_in_feeds($content) {
	global $post;
	if(has_post_thumbnail($post->ID)) {
		$content = '<div>' . get_the_post_thumbnail($post->ID) . '</div>' . $content;
	}
	return $content;
}
add_filter('the_excerpt_rss', 'post_thumbnail_in_feeds');
add_filter('the_content_feed', 'post_thumbnail_in_feeds');
