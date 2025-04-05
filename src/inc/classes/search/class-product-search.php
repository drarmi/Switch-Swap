<?php
namespace Omnis\src\inc\classes\search;

use WP_Query;
use WP_Error;

class Product_Search {
    private static $instance = null;

    public static function get_instance() {
        if ( self::$instance === null ) {
            self::$instance = new self();
            self::$instance->setup_ajax_handlers();
        }
        return self::$instance;
    }

    public function setup_ajax_handlers() {
        $this->wp_ajax_action('product_search');
    }

    private function wp_ajax_action(string $action): void {
        add_action('wp_ajax_' . $action, [$this, $action]);
        add_action('wp_ajax_nopriv_' . $action, [$this, $action]);
    }

    public function product_search() {
        check_ajax_referer('search_nonce', 'security');

        $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';
        if ( strlen( $search_term ) < 3 ) {
            wp_send_json_error(['message' => 'Please enter at least 3 characters']);
        }

        $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

        $query_args = [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            'paged'          => $paged,
            's'              => $search_term 
        ];

        // Get products using Dokan instead of WP_Query
        $query   = dokan()->product->all( $query_args );
        $results = $query->get_posts();

        if ( ! empty( $results ) ) {
            // Save the search query to history and update popularity if products are found
            $this->update_search_history($search_term);
            $this->update_popular_search($search_term);
        }

        ob_start();
        if ( ! empty( $results ) ) {
            echo '<div class="products-inner">';
            foreach ( $results as $post_object ) {
                global $post;
                $post = $post_object;
                setup_postdata( $post );
                get_template_part('template-parts/products/product-item');
            }
            echo '</div>';
            wp_reset_postdata();
        }
        $html = ob_get_clean();

        // Get pagination info; if not available, use count
        $found_posts = isset( $query->found_posts ) ? $query->found_posts : count( $results );
        $max_pages   = isset( $query->max_num_pages ) ? $query->max_num_pages : 1;
        $has_more    = $paged < $max_pages;

        wp_send_json_success([
            'html'      => $html,
            'found'     => $found_posts,
            'term'      => $search_term,
            'has_more'  => $has_more,
            'paged'     => $paged,
            'max_pages' => $max_pages
        ]);
    }

    private function update_search_history($search_term) {
        if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            $history = get_user_meta( $user_id, 'search_history', true );
            if ( ! is_array( $history ) ) {
                $history = [];
            }
            // Remove duplicate if the search query already exists
            if (($key = array_search($search_term, $history)) !== false) {
                unset($history[$key]);
            }
            array_unshift( $history, $search_term );
            $history = array_values($history);
            $history = array_slice( $history, 0, 4 );
            update_user_meta( $user_id, 'search_history', $history );
        } else {
            if ( isset( $_COOKIE['omnis_search_history'] ) ) {
                $decoded = json_decode( stripslashes( $_COOKIE['omnis_search_history'] ), true );
                if ( ! is_array( $decoded ) ) {
                    $decoded = [];
                }
            } else {
                $decoded = [];
            }
            if (($key = array_search($search_term, $decoded)) !== false) {
                unset($decoded[$key]);
            }
            array_unshift( $decoded, $search_term );
            $decoded = array_values($decoded);
            $decoded = array_slice( $decoded, 0, 4 );
            setcookie( 'omnis_search_history', json_encode( $decoded ), time() + 3600 * 24 * 30, '/' );
        }
    }

    // Function to update popular searches in the database
    private function update_popular_search($search_term) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'search_popular';

        // Check if a record exists
        $existing = $wpdb->get_var( $wpdb->prepare( "SELECT count FROM $table_name WHERE search_term = %s", $search_term ) );
        if ( $existing !== null ) {
            $wpdb->update(
                $table_name,
                ['count' => intval($existing) + 1],
                ['search_term' => $search_term],
                ['%d'],
                ['%s']
            );
        } else {
            $wpdb->insert(
                $table_name,
                ['search_term' => $search_term, 'count' => 1],
                ['%s', '%d']
            );
        }
    }
}

Product_Search::get_instance();
