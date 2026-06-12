<?php
if (!defined('ABSPATH')) exit;

/**
 * Evoke ONE — Główna strona ustawień
 * Router/dispatcher — ładuje odpowiedni plik zakładki.
 */

// =========================================================================
// ENQUEUE
// =========================================================================

add_action('admin_enqueue_scripts', function (string $hook) {
    if ($hook !== 'settings_page_evoke-one') return;

    wp_enqueue_style('evoke-one-admin',
        EVOKE_ONE_URL . 'assets/admin/admin.css', [], EVOKE_ONE_VERSION);

    wp_enqueue_script('evoke-one-admin',
        EVOKE_ONE_URL . 'assets/admin/admin.js',
        ['jquery'], EVOKE_ONE_VERSION, true);

    // Sitemap
    wp_localize_script('evoke-one-admin', 'evoSitemapAjax', [
        'url'   => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('tl_ajax_nonce'),
    ]);

    // IO
    wp_localize_script('evoke-one-admin', 'evoIoAjax', [
        'url'     => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('tl_ajax_nonce'),
        'modules' => evoke_one_get_io_modules(),
    ]);

    // Cursor
    $cursor_settings = EVK_Cursor::get_instance()->get_settings();
    wp_localize_script('evoke-one-admin', 'evoOneCursorData', [
        'rowStart' => count($cursor_settings['elements'] ?? []) + 100,
    ]);

    // SEO
    wp_localize_script('evoke-one-admin', 'evoSeoAjax', [
        'url'   => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('evoke_seo_nonce'),
    ]);

    // Toggle AJAX
    wp_localize_script('evoke-one-admin', 'evkToggle', [
        'url'   => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('evk-toggle-nonce'),
    ]);

    wp_enqueue_media();

    wp_enqueue_script('sortablejs',
        'https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js',
        [], '1.15.2', true);
});

// =========================================================================
// HELPER — moduły IO
// =========================================================================

function evoke_one_get_io_modules(): array {
    return [
        // System tłumaczeń
        'tl_translations'     => 'Tłumaczenia',
        'tl_languages'        => 'Języki i ustawienia TL',
        'tl_images'           => 'Obrazy wielojęzyczne',
        'tl_url_slugs'        => 'Slugi URL',
        'tl_sitemap_settings' => 'Mapa strony TL',
        'tl_dd_keys'          => 'Klucze Dynamic Data',
        // Frontend
        'evk_darkmode'        => 'Dark Mode',
        'evk_cursor'          => 'Kursor',
        'evk_lenis'           => 'Smooth Scroll (Lenis)',
        'evk_parallax'        => 'Parallax',
        'evk_a11y'            => 'Dostępność',
        // SEO & OG
        'evk_schema'          => 'Schema.org',
        'evk_og'              => 'OpenGraph',
        // Admin
        'evk_white_label'     => 'White Label',
        'evk_security'        => 'Bezpieczeństwo',
        'evk_smtp'            => 'SMTP',
        'evk_maintenance'     => 'Tryb konserwacji',
        'evk_redirects'       => 'Przekierowania 301',
        'evk_logs404'         => 'Ustawienia logów 404',
        'evk_dashboard'       => 'Kokpit Bricks',
        'evk_snippets'        => 'Snippety PHP',
        'evk_other'           => 'Inne ustawienia',
        'evk_newsletter'      => 'Newsletter',
    ];
}

// =========================================================================
// MENU
// =========================================================================

add_action('admin_menu', function () {
    add_options_page(
        'Evoke ONE',
        'Evoke ONE',
        'manage_options',
        'evoke-one',
        'evoke_one_render_settings'
    );
});

// =========================================================================
// RENDER — główna funkcja (router)
// =========================================================================

function evoke_one_render_settings(): void {
    if (!current_user_can('manage_options')) return;

    $tab  = sanitize_key($_GET['tab'] ?? 'wydajnosc');
    $base = admin_url('options-general.php?page=evoke-one');

    $tabs = [
        'wydajnosc'      => ['label' => 'Wydajność',      'icon' => 'dashicons-performance'],
        'strona'         => ['label' => 'Strona',          'icon' => 'dashicons-admin-site-alt3'],
        'bezpieczenstwo' => ['label' => 'Bezpieczeństwo',  'icon' => 'dashicons-shield'],
        'narzedzia'      => ['label' => 'Narzędzia',       'icon' => 'dashicons-admin-tools'],
        'admin_panel'    => ['label' => 'Admin',           'icon' => 'dashicons-admin-settings'],
        'newsletter'     => ['label' => 'Newsletter',      'icon' => 'dashicons-email-alt'],
    ];

    if (!array_key_exists($tab, $tabs)) $tab = 'wydajnosc';

    // Mapowanie zakładki → plik
    $tab_files = [
        'wydajnosc'      => 'tab-wydajnosc.php',
        'strona'         => 'tab-strona.php',
        'bezpieczenstwo' => 'tab-bezpieczenstwo.php',
        'narzedzia'      => 'tab-narzedzia.php',
        'admin_panel'    => 'tab-admin.php',
        'newsletter'     => 'tab-newsletter.php',
    ];

    ?>
    <div class="wrap evo-wrap">
        <div class="evo-layout" id="evo-layout">

            <!-- SIDEBAR -->
            <aside class="evo-sidebar">
                <div class="evo-sidebar-brand">
                    <span class="evo-brand-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 2L17.32 6.5V15.5L10 20L2.68 15.5V6.5L10 2Z" fill="currentColor"/>
                        </svg>
                    </span>
                    <div class="evo-brand-text">
                        <span class="evo-brand-name">Evoke ONE</span>
                        <span class="evo-brand-ver">v<?php echo esc_html(EVOKE_ONE_VERSION); ?></span>
                    </div>
                </div>

                <nav class="evo-nav">
                    <?php foreach ($tabs as $key => $t): ?>
                    <a href="<?php echo esc_url(add_query_arg('tab', $key, $base)); ?>"
                       class="evo-nav-item <?php echo $tab === $key ? 'is-active' : ''; ?>">
                        <span class="evo-nav-icon dashicons <?php echo esc_attr($t['icon']); ?>"></span>
                        <span class="evo-nav-label"><?php echo esc_html($t['label']); ?></span>
                        <?php if ($tab === $key): ?><span class="evo-nav-pip"></span><?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </nav>

                <div class="evo-sidebar-footer">
                    <a href="https://evoke.pl" target="_blank" class="evo-sidebar-link">evoke.pl</a>
                </div>
            </aside>

            <!-- MAIN CONTENT -->
            <main class="evo-main">
                <header class="evo-main-header">
                    <div class="evo-main-header-title">
                        <span class="dashicons <?php echo esc_attr($tabs[$tab]['icon']); ?> evo-main-header-icon"></span>
                        <h1><?php echo esc_html($tabs[$tab]['label']); ?></h1>
                    </div>
                </header>

                <div class="evo-content">
                    <?php
                    $tab_file = EVOKE_ONE_DIR . 'includes/admin/' . ($tab_files[$tab] ?? '');
                    if ($tab_file && file_exists($tab_file)) {
                        require $tab_file;
                    } else {
                        echo '<p style="color:#dc2626;">Błąd: plik zakładki nie istnieje.</p>';
                    }
                    ?>
                </div>
            </main>

        </div>
    </div>
    <?php
}
