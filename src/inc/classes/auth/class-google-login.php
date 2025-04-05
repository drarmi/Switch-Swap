<?php
/**
 * Google Login Class
 *
 * @package omnis_base
 * @since 4.4.0
 */

namespace Omnis\src\inc\classes\auth;

use Omnis\src\inc\classes\setup\Omnis_Theme;
use Google\Client as Google_Client;
use Google\Service\Oauth2;

/**
 * This class handles the "Login with Google" functionality using Google_Client.
 */
class Google_Login {

    /**
     * Holds the singleton instance of this class.
     *
     * @var Google_Login
     */
    private static Google_Login $instance;

    /**
     * Holds the Omnis_Theme instance.
     *
     * @var Omnis_Theme
     */
    private Omnis_Theme $omnis_theme;

    /**
     * Google Client instance.
     *
     * @var Google_Client
     */
    private Google_Client $client;

    /**
     * Constructor
     *
     * Initializes the class, Google Client, and actions.
     */
    public function __construct() {
        $this->omnis_theme = new Omnis_Theme();

        // Initialize Google Client
        $this->client = new Google_Client();
        $this->client->setClientId( '996729890797-bj3ltbrt2341f1javkvstn4bfpbebjve.apps.googleusercontent.com' );
        $this->client->setClientSecret( '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' );
        $this->client->setRedirectUri( 'http://localhost/?auth=google_callback' ) ;
        $this->client->addScope( [ 'email', 'profile' ] );

        // Render Google login button
        $this->omnis_theme->add_action( 'woocommerce_login_form_end', [ $this, 'render_google_login_button' ] );

        // Handle Google callback
        $this->omnis_theme->add_action( 'init', [ $this, 'handle_google_callback' ] );
    }

    /**
     * Get an instance of the Google_Login class.
     *
     * @return Google_Login
     */
    public static function get_instance(): Google_Login {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Render the "Login with Google" button in WooCommerce login form.
     *
     * @return void
     */
    public function render_google_login_button() {
        $auth_url = $this->client->createAuthUrl();
        echo '<a href="' . esc_url( $auth_url ) . '" class="button google-login-button"><img src="' . get_theme_file_uri( 'assets/dist/images/login-google.svg' ) . '" height="44" alt="Login by Google"></a>';

    }

    /**
     * Handle Google OAuth callback to log in or register users.
     *
     * @return void
     */
    public function handle_google_callback() {
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'google_callback' && isset( $_GET['code'] ) ) {
            $code = sanitize_text_field( $_GET['code'] );

            try {
                // Exchange code for access token
                $token = $this->client->fetchAccessTokenWithAuthCode( $code );
                $this->client->setAccessToken( $token );

                // Fetch user information
                $oauth2      = new Oauth2( $this->client );
                $user_info   = $oauth2->userinfo->get();
                $email       = $user_info->email;
                $name        = $user_info->name;

                // Log in or register the user
                $this->login_or_register_user( $email, $name );
            } catch ( \Exception $e ) {
                wp_die( 'Error: ' . esc_html( $e->getMessage() ) );
            }
        }
    }

    /**
     * Log in or register the WooCommerce user based on Google account details.
     *
     * @param string $email
     * @param string $name
     *
     * @return void
     */
    private function login_or_register_user( string $email, string $name ) {
        $user = get_user_by( 'email', $email );

        if ( ! $user ) {
            // Register a new user
            $password = wp_generate_password();
            $user_id  = wp_insert_user( [
                'user_login' => $email,
                'user_email' => $email,
                'first_name' => $name,
                'user_pass'  => $password,
                'role'       => 'customer',
            ] );

            if ( is_wp_error( $user_id ) ) {
                wp_die( 'Error creating user account.' );
            }

            $user = get_user_by( 'id', $user_id );
        }

        // Log in the user
        wp_set_auth_cookie( $user->ID, true );
        wp_redirect( wc_get_page_permalink( 'myaccount' ) );
        exit;
    }
}

// Instantiate the Google_Login class.
Google_Login::get_instance();