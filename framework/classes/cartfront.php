<?php
/**
 * Main class for the theme.
 *
 * @package cartfront
 */

namespace Niteo\WooCart\CartFront {

    use Niteo\WooCart\CartFront\Customizer\Rearrange;

    class Cartfront {

        /**
         * Constructor function.
         *
         * @access  public
         * @since   1.0.0
         */
        public function __construct() {
            /**
             * Setup theme.
             */
            $setup              = new Setup();

            add_action( 'wp_enqueue_scripts', array( &$this, 'scripts' ), PHP_INT_MAX );
            add_action( 'customize_preview_init', array( &$this, 'customize_preview_js' ) );

            // Hide the 'More' section in the customizer.
            add_filter( 'storefront_customizer_more', '__return_false' );

            /**
             * Initialize classes.
             */
            $footer_bar         = new Footer_Bar();
            $hamburger_menu     = new Hamburger_Menu();
            $blog_customiser    = new Blog_Customiser();
            $homepage_control   = new Homepage_Control();
            $simple_slider      = new Simple_Slider();
            $link_boxes         = new Link_Boxes();
            $layouts_presets    = new Layouts_Presets();
            $rearrange          = new Rearrange();
        }

        /**
         * Add styles & scripts for the theme.
         *
         * @access public
         */
        public function scripts() {
            global $theme_name, $theme_version, $cartfront_url;

            wp_enqueue_style( $theme_name . '-public', $cartfront_url . '/framework/css/public.css' );

            wp_enqueue_script( $theme_name . '-vendors', $cartfront_url . '/framework/js/vendors.js', array( 'jquery' ), $theme_version, true );
            wp_enqueue_script( $theme_name . '-public', $cartfront_url . '/framework/js/public.js', array( 'jquery' ), $theme_version, true );

            /**
             * Localization
             */
            $localization = array(
                'close'         => esc_html__( 'Close', 'cartfront' ),
                'ss_items_row'  => esc_html( get_theme_mod( 'cf_ss_items_row' ) )
            );

            wp_localize_script( $theme_name . '-public', 'cartfront_localize', $localization );
        }

        /**
         * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
         *
         * @access public
         */
        public function customize_preview_js() {
            global $theme_name, $theme_version, $cartfront_url;

            wp_enqueue_style( $theme_name . '-customizer-preview', $cartfront_url . '/framework/css/preview.css', '', $theme_version );

            wp_enqueue_script( $theme_name . '-customizer', $cartfront_url . '/framework/js/customizer.js', array( 'customize-preview' ), $theme_version, true );

            /**
             * Localization
             */
            $localization = array(
                'ajaxurl'   => admin_url( 'admin-ajax.php' ),
                'nonce'     => wp_create_nonce( 'cartfront_nonce' ),
                'theme_url' => $cartfront_url
            );

            wp_localize_script( $theme_name . '-customizer', 'cf_customizer', $localization );
        }

    }

}
