<?php
/**
 * Apple Login Class
 *
 * @package omnis_base
 * @since 4.4.0
 */

namespace Omnis\src\inc\classes\auth;

use Omnis\src\inc\classes\setup\Omnis_Theme;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * This class handles the "Login with Apple" functionality.
 */
class Apple_Login {

    /**
     * Holds the singleton instance of this class.
     *
     * @var Apple_Login
     */
    private static Apple_Login $instance;

    /**
     * Holds the Omnis_Theme instance.
     *
     * @var Omnis_Theme
     */
    private Omnis_Theme $omnis_theme;

    /**
     * Constructor
     *
     * Initializes the class and actions.
     */
    public function __construct() {
        $this->omnis_theme = new Omnis_Theme();

        // Render Apple login button
        $this->omnis_theme->add_action( 'woocommerce_login_form_end', [ $this, 'render_apple_login_button' ] );

        // Handle Apple callback
        $this->omnis_theme->add_action( 'init', [ $this, 'handle_apple_callback' ] );
    }

    /**
     * Get an instance of the Apple_Login class.
     *
     * @return Apple_Login
     */
    public static function get_instance(): Apple_Login {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Render the "Login with Apple" button in WooCommerce login form.
     *
     * @return void
     */
    public function render_apple_login_button() {
        $client_id = 'com.example.app'; // Replace with your Apple Service ID
        $redirect_uri = 'http://localhost/?auth=apple_callback';
        $auth_url = "https://appleid.apple.com/auth/authorize?response_type=code&client_id=$client_id&redirect_uri=$redirect_uri&scope=name%20email";
        
        echo '<a href="' . esc_url( $auth_url ) . '" class="button apple-login-button"><img src="' . get_theme_file_uri( 'assets/dist/images/login-apple.svg' ) . '" height="44" alt="Login by Apple"></a>';
    }

    /**
     * Handle Apple OAuth callback to log in or register users.
     *
     * @return void
     */
    public function handle_apple_callback() {
        if ( isset( $_GET['auth'] ) && $_GET['auth'] === 'apple_callback' && isset( $_POST['id_token'] ) ) {
            $id_token = sanitize_text_field( $_POST['id_token'] );

            try {
                // Decode and verify the ID token
                $client_id = 'com.example.app'; // Replace with your Apple Service ID
                $key_file_path = '/path/to/apple/key.p8'; // Replace with the path to your private key file
                $key_id = 'YOUR_KEY_ID'; // Replace with your key ID
                $team_id = 'YOUR_TEAM_ID'; // Replace with your team ID

                $decoded_token = JWT::decode($id_token, new Key($this->get_apple_public_key(), 'RS256'));

                // Fetch user information from token
                $email = $decoded_token->email;
                $name = $decoded_token->name ?? '';

                // Log in or register the user
                $this->login_or_register_user( $email, $name );

            } catch ( \Exception $e ) {
                wp_die( 'Error: ' . esc_html( $e->getMessage() ) );
            }
        }
    }

    /**
     * Log in or register the WooCommerce user based on Apple account details.
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

    /**
     * Fetch the public key from Apple to verify the ID token.
     *
     * @return string
     */
    private function get_apple_public_key(): string {
        // This should fetch and cache Apple's public key for validating ID tokens.
        $response = wp_remote_get( 'https://appleid.apple.com/auth/keys' );

        if ( is_wp_error( $response ) ) {
            wp_die( 'Unable to fetch Apple public key.' );
        }

        $keys = json_decode( wp_remote_retrieve_body( $response ), true );

        return $keys['keys'][0]['n'] ?? ''; // Adjust as needed to extract the correct key.
    }
}

// Instantiate the Apple_Login class.
Apple_Login::get_instance();
