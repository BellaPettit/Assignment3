<?php
/**
 * Di eCommerce Engine. This file is responsible for theme setup, scripts, styles, sidebar registration.
 *
 * @package Di eCommerce
 */

if( ! class_exists( 'Di_eCommerce_Engine' ) ) {
	/**
	 * Class main : responsible for theme setup, scripts, styles, sidebar registration.
	 */
	class Di_eCommerce_Engine {

		/**
		 * Instance object.
		 *
		 * @var instance
		 */
		public static $instance;

		/**
		 * Get_instance method.
		 *
		 * @return instance instance of the class.
		 */
		public static function get_instance() {
			if ( empty( self::$instance ) ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 * Construct method.
		 */
		public function __construct() {
			$this->set_constants();
			add_action( 'after_setup_theme', array( $this, 'setup' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'scripts_and_styles' ) );
			add_action( 'customize_preview_init', array( $this, 'customizer_scripts_and_styles' ) );
			add_action( 'widgets_init', array( $this, 'sidebar_registration' ) );
		}

		/**
		 *  Set constants.
		 */
		public function set_constants() {
			define( 'DI_ECOMMERCE_VERSION', wp_get_theme( 'di-ecommerce' )->get( 'Version' ) );
		}

		/**
		 * Theme setup.
		 */
		public function setup() {

			global $content_width;
			if ( ! isset( $content_width ) ) {
				$content_width = 730;
			}

			load_theme_textdomain( 'di-ecommerce', get_template_directory() . '/languages' );

			add_theme_support( 'automatic-feed-links' );

			add_theme_support( 'title-tag' );

			add_theme_support( 'align-wide' );

			add_theme_support( 'wp-block-styles' );
			add_theme_support( 'responsive-embeds' );
			add_theme_support('custom-spacing');
			remove_theme_support( 'widgets-block-editor' );

			add_theme_support( 'post-thumbnails' );

			add_theme_support( 'customize-selective-refresh-widgets' );

			add_theme_support( 'html5', array( 'gallery', 'caption' ) );

			add_theme_support( 'post-formats', array( 'quote' ) );

			add_theme_support( 'custom-background', array(
				'default-color'      => '#fcfcfc',
				'default-attachment' => 'fixed',
			) );

			add_theme_support( 'custom-header', array(
				'width'         => 1350,
				'height'        => 260,
				'flex-width'    => true,
				'flex-height'   => true,
				'uploads'       => true,
				'header-text'	=> false,
			) );

			add_theme_support( 'custom-logo', array(
				'height'		=> '100',
				'width'			=> '360',
				'flex-height'	=> true,
				'flex-width'	=> true,
			) );

			set_post_thumbnail_size( 1138, 400, true );
			add_image_size( 'di-ecommerce-recentpostthumb', 90, 90, true );

			// This theme uses wp_nav_menu() at one location.
			register_nav_menus( array(
				'primary'	=> __( 'Top Main Menu', 'di-ecommerce' ),
			) );

			add_editor_style( array( '//fonts.googleapis.com/css?family=Raleway', get_template_directory_uri() . '/assets/css/style.css', get_template_directory_uri() . '/assets/css/editor-style.css' ) );

		}

		/**
		 * Scripts_and_styles of theme.
		 */
		public function scripts_and_styles() {

			// Load bootstrap css.
			wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.css', array(), '4.0.0', 'all' );

			// Load font-awesome file.
			wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.css', array(), '4.7.0', 'all' );

			// Load default css file.
			wp_enqueue_style( 'di-ecommerce-style-default', get_stylesheet_uri(), array( 'bootstrap', 'font-awesome' ), DI_ECOMMERCE_VERSION, 'all' );

			// Load css/style.css file.
			wp_enqueue_style( 'di-ecommerce-style-core', get_template_directory_uri() . '/assets/css/style.css', array( 'bootstrap', 'font-awesome' ), DI_ECOMMERCE_VERSION, 'all' );

			// Load woo, perfect-scrollbar css file if WooCommerce plugin is active.
			if( class_exists( 'WooCommerce' ) ) {

				wp_enqueue_style( 'di-ecommerce-style-woo', get_template_directory_uri() . '/assets/css/woo.css', array( 'bootstrap', 'font-awesome' ), DI_ECOMMERCE_VERSION, 'all' );

				// perfect-scrollbar css load if enabled sb_cart_onoff and do not load on template-landing-page.php
				if ( get_theme_mod( 'sb_cart_onoff', '0' ) == 1 && ! is_page_template( 'template-landing-page.php' ) ) {
					wp_enqueue_style( 'perfect-scrollbar', get_template_directory_uri() . '/assets/css/perfect-scrollbar.css', array( 'bootstrap', 'font-awesome' ), '1.4.0', 'all' );
				}
			}

			// Load bootstrap js.
			wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.js', array( 'jquery' ), '4.0.0', true );

			// Load script file.
			wp_enqueue_script( 'di-ecommerce-script', get_template_directory_uri() . '/assets/js/script.js', array( 'jquery' ), DI_ECOMMERCE_VERSION, true );

			// Load html5shiv.
			wp_enqueue_script( 'html5shiv', get_template_directory_uri() . '/assets/js/html5shiv.js', array(), '3.7.3', false );
			wp_script_add_data( 'html5shiv', 'conditional', 'lt IE 9' );

			// Load respond js.
			wp_enqueue_script( 'respond', get_template_directory_uri() . '/assets/js/respond.js', array(), DI_ECOMMERCE_VERSION, false );
			wp_script_add_data( 'respond', 'conditional', 'lt IE 9' );

			// load comment-reply js.
			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}

			// Load stickymenu js depends on jquery and if enabled by customizer.
			if ( get_theme_mod( 'stickymenu_setting', '0' ) == 1 && ! is_page_template( 'template-landing-page.php' ) ) {
				wp_enqueue_script( 'di-ecommerce-stickymenu', get_template_directory_uri() . '/assets/js/stickymenu.js', array( 'jquery' ), '', 'true' );
			}

			// Load back to top js depends on jquery and if enabled by customizer.
			if ( get_theme_mod( 'back_to_top', '1' ) == 1 ) {
				wp_enqueue_script( 'di-ecommerce-backtotop', get_template_directory_uri() . '/assets/js/backtotop.js', array( 'jquery' ), DI_ECOMMERCE_VERSION, true );
			}

			// Preloader icon js depends on jquery and if enabled by customizer.
			if ( get_theme_mod( 'loading_icon', '0' ) == 1 ) {
				wp_enqueue_script( 'di-ecommerce-loadicon', get_template_directory_uri() . '/assets/js/loadicon.js', array( 'jquery' ), DI_ECOMMERCE_VERSION, true );
			}

			// Load sidecart, perfect-scrollbar js file if WooCommerce plugin is active.
			if( class_exists( 'WooCommerce' ) ) {
				// Side bar cart js depends on jquery and if enabled by customizer and not on landing page.
				if ( get_theme_mod( 'sb_cart_onoff', '0' ) == 1 && ! is_page_template( 'template-landing-page.php' ) ) {

					// mini cart widget does not work on cart and checkout page so do not load perfect-scrollbar.js + sidebarcart.js
					if( ! is_cart() && ! is_checkout() ) {

						wp_enqueue_script( 'perfect-scrollbar', get_template_directory_uri() . '/assets/js/perfect-scrollbar.js', array( 'jquery' ), '1.4.0', true );

						wp_enqueue_script( 'di-ecommerce-sidecart', get_template_directory_uri() . '/assets/js/sidebarcart.js', array( 'jquery', 'perfect-scrollbar' ), DI_ECOMMERCE_VERSION, true );
					}
				}
			}

			// CSP Search js depends on jquery and if enabled by customizer and not on landing page.
			if ( get_theme_mod( 'top_bar_seach_icon', '1' ) == 1 && get_theme_mod( 'display_top_bar', '1' ) == 1 && ! is_page_template( 'template-landing-page.php' ) ) {
				wp_enqueue_script( 'di-ecommerce-csp-search', get_template_directory_uri() . '/assets/js/scpsearch.js', array( 'jquery' ), DI_ECOMMERCE_VERSION, true );
			}

			// Load cust masonry js theme code, masonry, imagesloaded IF enabled in customize.
			if ( get_theme_mod( 'blog_list_grid', 'list' ) == 'grid2c' || get_theme_mod( 'blog_list_grid', 'list' ) == 'grid3c' ) {
				wp_enqueue_script( 'di-ecommerce-masonry', get_template_directory_uri() . '/assets/js/masonry.js', array( 'jquery', 'imagesloaded', 'masonry' ), DI_ECOMMERCE_VERSION, true );
			}
			
			if( ! is_page_template( 'template-landing-page.php' ) ) {
				wp_enqueue_script( 'di-ecommerce-nav-menu', get_template_directory_uri() . '/assets/js/nav-menu.js', array( 'jquery' ), DI_ECOMMERCE_VERSION, true );
			}

		}

		/**
		 * [customizer_scripts_and_styles description]
		 * @return [type] [description]
		 */
		public function customizer_scripts_and_styles() {

			wp_enqueue_style( 'di-ecommerce-customize-preview', get_template_directory_uri() . '/assets/css/customizer.css', array( 'customize-preview' ), DI_ECOMMERCE_VERSION, 'all' );

			wp_enqueue_script( 'di-ecommerce-customize-preview', get_template_directory_uri() . '/assets/js/customizer.js', array( 'customize-preview' ), DI_ECOMMERCE_VERSION, true );

		}

		/**
		 * Sidebar_registration.
		 */
		public function sidebar_registration() {
			register_sidebar( array(
				'name'			=> __( 'Blog Sidebar', 'di-ecommerce' ),
				'id'			=> 'sidebar-1',
				'description'	=> __( 'Widgets for Blog sidebar. If you are using Full Width Layout in customize, then this sidebar will not display.', 'di-ecommerce' ),
				'before_widget'	=> '<div id="%1$s" class="widget_sidebar_main clearfix %2$s">',
				'after_widget'	=> '</div>',
				'before_title'	=> '<h3 class="right-widget-title">',
				'after_title'	=> '</h3>',
			) );

			register_sidebar( array(
				'name'			=> __( 'Page Sidebar', 'di-ecommerce' ),
				'id'			=> 'sidebar_page',
				'description'	=> __( 'Widgets for Page sidebar. Choose Sidebar Template to display it. Page edit screen > Page Attributes > Template.', 'di-ecommerce' ),
				'before_widget'	=> '<div id="%1$s" class="widget_sidebar_main clearfix %2$s">',
				'after_widget'	=> '</div>',
				'before_title'	=> '<h3 class="right-widget-title">',
				'after_title'	=> '</h3>',
			) );

			if ( class_exists( 'WooCommerce' ) ) {
				register_sidebar( array(
					'name'			=> __( 'WooCommerce Sidebar', 'di-ecommerce' ),
					'id'			=> 'sidebar_woo',
					'description'	=> __( 'Widgets for WooCommerce Pages (For:- Product Loop, Product Search and Product Single Page). If you are using Full Width Layout in customize, then this sidebar will not display.', 'di-ecommerce' ),
					'before_widget'	=> '<div id="%1$s" class="widget_sidebar_main clearfix %2$s">',
					'after_widget'	=> '</div>',
					'before_title'	=> '<h3 class="right-widget-title">',
					'after_title'	=> '</h3>',
				) );
			}

			register_sidebar( array(
				'name'			=> __( 'Top Header right', 'di-ecommerce' ),
				'id'			=> 'sidebar_header',
				'description'	=> __( 'Widgets for top header right. You can select header layout accordingly here: Dashboard > Appearance > Customize > Di eCommerce Options > Header Layout Options.', 'di-ecommerce' ),
				'before_widget'	=> '<div id="%1$s" class="widgets_header fl_right_header_spsl clearboth %2$s">',
				'after_widget'	=> '</div>',
				'before_title'	=> '',
				'after_title'	=> '',
			) );

			register_sidebar( array(
				'name'			=> __( 'Top Header Left', 'di-ecommerce' ),
				'id'			=> 'sidebar_header_left',
				'description'	=> __( 'Widgets for top header left. You can select header layout accordingly here: Dashboard > Appearance > Customize > Di eCommerce Options > Header Layout Options.', 'di-ecommerce' ),
				'before_widget'	=> '<div id="%1$s" class="widgets_header_left clearboth %2$s">',
				'after_widget'	=> '</div>',
				'before_title'	=> '',
				'after_title'	=> '',
			) );

			// Footer widget register base on settings.
			$enordis = absint( get_theme_mod( 'endis_ftr_wdgt', '0' ) );
			$layout = absint( get_theme_mod( 'ftr_wdget_lyot', '3' ) );
			if ( $enordis != 0 ) {
				if ( $layout == 48 || $layout == 84 ) {
					register_sidebar( array(
						'name'			=> __( 'Footer Widget 1', 'di-ecommerce' ),
						'id'			=> 'footer_1',
						'description'	=> __( 'Widgets for footer 1', 'di-ecommerce' ),
						'before_widget'	=> '<div id="%1$s" class="widgets_footer clearfix %2$s">',
						'after_widget'	=> '</div>',
						'before_title'	=> '<h3 class="widgets_footer_title">',
						'after_title'	=> '</h3>',
					) );

					register_sidebar( array(
						'name'			=> __( 'Footer Widget 2', 'di-ecommerce' ),
						'id'			=> 'footer_2',
						'description'	=> __( 'Widgets for footer 2', 'di-ecommerce' ),
						'before_widget'	=> '<div id="%1$s" class="widgets_footer clearfix %2$s">',
						'after_widget'	=> '</div>',
						'before_title'	=> '<h3 class="widgets_footer_title">',
						'after_title'	=> '</h3>',
					) );
				} else {
					for ( $i = 1; $i <= $layout; $i++ ) {
						register_sidebar( array(
							'name'			=> __( 'Footer Widget ', 'di-ecommerce' ) . $i,
							'id'			=> 'footer_' . $i,
							'description'	=> __( 'Widgets for footer ', 'di-ecommerce' ) . $i,
							'before_widget'	=> '<div id="%1$s" class="widgets_footer clearfix %2$s">',
							'after_widget'	=> '</div>',
							'before_title'	=> '<h3 class="widgets_footer_title">',
							'after_title'	=> '</h3>',
						) );
					}
				}
			}
		}
	}
}
Di_eCommerce_Engine::get_instance();
