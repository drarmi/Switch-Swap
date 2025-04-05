<?php
/**
 * Home Page Class
 *
 * @package omnis_base
 * @since 4.4.0
 */

namespace Omnis\src\inc\classes\pages;

use Omnis\src\inc\classes\setup\Omnis_Theme;

/**
 * This class defines the functionality related to the home page, including
 *  handling AJAX requests and setting up any necessary actions.
 */
class Home_Page {

    /**
     * Holds the singleton instance of this class.
     *
     * @var Home_Page
     */
    private static Home_Page $instance;

    /**
     * Holds the singleton instance of "Omnis_Theme" class.
     *
     * @var Omnis_Theme
     */
    private Omnis_Theme $omnis_theme;

    /**
     * Constructor
     *
     * Initializes the class and sets up AJAX handlers.
     */
    public function __construct() {
        $this->setup_ajax_handlers();
        $this->omnis_theme = new Omnis_Theme();
    }

    /**
     * Get an instance of the Home_Page class.
     *
     * This method follows the singleton pattern to ensure that only
     * one instance of the class is created and used throughout the application.
     *
     * @return Home_Page The singleton instance of the Home_Page class.
     */
    public static function get_instance(): Home_Page {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register AJAX action hooks.
     *
     * This method registers the AJAX handlers for both logged-in and logged-out users.
     *
     * @param string $action The name of the AJAX action.
     *
     * @return void
     */
    public function wp_ajax_action( string $action ): void {
        $this->omnis_theme->add_action( 'wp_ajax_' . $action, array( $this, $action ) );
        $this->omnis_theme->add_action( 'wp_ajax_nopriv_' . $action, array( $this, $action ) );
    }

    /**
     * Setup AJAX handlers.
     *
     * This method is responsible for defining all the AJAX action handlers
     * for the home page. The actual handlers can be implemented in this method.
     *
     * @return void
     */
    public function setup_ajax_handlers() {
        // Implement AJAX handler setup here.
    }
}

// Instantiate the Home_Page class.
Home_Page::get_instance();
