<?php if (!defined('ABSPATH')) exit;
/**
 * Evoke ONE — Admin: White Label
 */
$wl        = evk_wl_get();
$bar_items = evk_wl_bar_items_get();
$nonce_bar = wp_create_nonce('evoke-one-wl-bar');

$bar_nodes = [
    'wp-logo'     => 'Logo WordPress',
    'site-name'   => 'Nazwa witryny',
    'updates'     => 'Aktualizacje',
    'comments'    => 'Komentarze',
    'new-content' => '+ Dodaj nowy',
    'my-account'  => 'Moje konto',
    'search'      => 'Szukaj',
    'customize'   => 'Dostosuj',
    'edit'        => 'Edytuj stronę',
];

$sidebar_items = [
    'index.php'               => 'Kokpit',
    'edit.php'                => 'Wpisy',
    'upload.php'              => 'Media',
    'edit.php?post_type=page' => 'Strony',
    'edit-comments.php'       => 'Komentarze',
    'themes.php'              => 'Wygląd',
    'plugins.php'             => 'Wtyczki',
    'users.php'               => 'Użytkownicy',
    'tools.php'               => 'Narzędzia',
    'options-general.php'     => 'Ustawienia',
];

$bar_order = $wl['bar_nodes_order'] ?? [];
?>

<style>
.evk-drag-handle { cursor: grab !important; color: #bbb; transition: color .15s; }
.evk-drag-handle:hover { color: #2271b1 !important; }
.evk-drag-chosen { box-shadow: 0 3px 16px rgba(0,0,0,.18) !important; z-index: 999; }
.evk-drag-ghost  { opacity: .35; background: #e8f0fe !important; }
.evk-bar-row     { transition: background .15s; }
.evk-order-grid  { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px,1fr)); gap: 8px; }
.evk-order-row   { display: flex; align-items: center; gap: 8px; padding: 6px 10px;
                   background: var(--evo-surface,#f8f8f8); border-radius: 4px; font-size: 13px; }
.evk-order-row input[type=number] { width: 56px; text-align: center; }
</style>

<!-- JEDNA FORMA dla całego modułu White Label -->
<form method="post" action="options.php" id="evk-wl-form">
<?php settings_fields('evk_white_label_settings'); ?>
<input type="hidden" name="evk_white_label[_resets]" id="evk-wl-resets" value="[]">
<input type="hidden" name="evk_white_label[_sentinel]" value="1">
<!-- Sentinele dla tablic — zapewniają klucz w POST gdy żaden checkbox niezaznaczony -->
<input type="hidden" name="evk_white_label[bar_nodes_hidden][]" value="">
<input type="hidden" name="evk_white_label[sidebar_hidden][]"   value="">
<!-- Serializowane dane dynamicznych sekcji — aktualizowane przez JS przed submitem -->
<input type="hidden" name="evk_white_label[sidebar_menu_order_json]" id="evk-wl-menu-order-json" value="">
<input type="hidden" name="evk_white_label[bar_items_json]"          id="evk-wl-bar-items-json"  value="">

<!-- STATUS -->
<div class="evo-status-card">
    <div class="evo-status-icon <?php echo !empty($wl['enabled']) ? 'on' : 'off'; ?>">
        <span class="dashicons dashicons-admin-customizer" style="font-size:24px;width:24px;height:24px;line-height:1;"></span>
    </div>
    <div class="evo-status-text">
        <h3>White Label: <?php echo !empty($wl['enabled']) ? 'WŁĄCZONY' : 'WYŁĄCZONY'; ?></h3>
        <p>Personalizacja panelu admina — logo, kolory, pasek górny, menu boczne.</p>
    </div>
    <div class="evo-status-actions">
        <label class="evo-toggle">
            <input type="checkbox" name="evk_white_label[enabled]" data-option="evk_white_label" data-field="enabled" value="1" <?php checked(1, $wl['enabled']); ?>>
            <span class="evo-slider"></span>
        </label>
    </div>
</div>

<!-- GRID 2-col -->
<div class="evk-grid-2col" style="margin-top:24px;">

<div>
    <p class="evo-section-title">Logo</p>
    <div class="evo-field">
        <label>URL logo (PNG/SVG)</label>
        <div style="display:flex;gap:8px;align-items:center;">
            <input type="url" name="evk_white_label[logo_url]" value="<?php echo esc_attr($wl['logo_url']); ?>" id="evk-wl-logo-url" style="flex:1;" placeholder="https://...">
            <button type="button" class="button" id="evk-wl-logo-pick">Wybierz</button>
        </div>
        <?php if (!empty($wl['logo_url'])): ?>
        <img src="<?php echo esc_url($wl['logo_url']); ?>" style="max-height:60px;max-width:200px;margin-top:8px;border-radius:4px;">
        <?php endif; ?>
    </div>
    <div class="evo-field">
        <label>Wymiary logo (px)</label>
        <div style="display:flex;gap:12px;align-items:center;">
            <label style="font-size:12px;color:#666;margin:0;">szer.</label>
            <input type="number" name="evk_white_label[logo_width]" value="<?php echo esc_attr($wl['logo_width']); ?>" min="40" max="400" style="width:80px;" placeholder="160">
            <label style="font-size:12px;color:#666;margin:0;">wys.</label>
            <input type="number" name="evk_white_label[logo_height]" value="<?php echo esc_attr($wl['logo_height'] ?? 60); ?>" min="20" max="200" style="width:80px;" placeholder="60">
        </div>
    </div>

    <p class="evo-section-title">Branding</p>
    <div class="evo-field">
        <label>Własna nazwa (zastępuje "WordPress")</label>
        <input type="text" name="evk_white_label[site_name]" value="<?php echo esc_attr($wl['site_name']); ?>" placeholder="np. CMS">
    </div>
    <div class="evo-field">
        <label>Tekst w stopce admina</label>
        <input type="text" name="evk_white_label[footer_text]" value="<?php echo esc_attr($wl['footer_text']); ?>" placeholder="Wykonano z ❤ przez Evoke Design">
    </div>

    <p class="evo-section-title">Czcionka admina</p>
    <div class="evo-field">
        <label>Nazwa czcionki (font-family)</label>
        <input type="text" name="evk_white_label[admin_font_family]" value="<?php echo esc_attr($wl['admin_font_family']); ?>" placeholder="Inter">
        <p class="evo-desc" style="margin:4px 0 0;">Wpisz dokładną nazwę czcionki zarejestrowanej w Bricks (lub systemowej). Czcionka musi być już załadowana przez motyw.</p>
    </div>

    <p class="evo-section-title">Pasek górny — wygląd</p>
    <div class="evo-field">
        <label>Tytuł w pasku (zastępuje nazwę witryny)</label>
        <input type="text" name="evk_white_label[admin_bar_title]" value="<?php echo esc_attr($wl['admin_bar_title']); ?>" placeholder="Moja Witryna">
    </div>
    <div class="evo-field">
        <label>Kolor tła paska górnego</label>
        <input type="color" data-field="admin_bar_color" data-saved="<?php echo esc_attr($wl['admin_bar_color'] ?? ''); ?>" name="evk_white_label[admin_bar_color]" value="<?php echo esc_attr($wl['admin_bar_color'] ?: '#23282d'); ?>">
    </div>
    <div class="evo-field">
        <label>Kolor linków/ikon paska (hover &amp; focus)</label>
        <input type="color" data-field="color_admin_bar_link" data-saved="<?php echo esc_attr($wl['color_admin_bar_link'] ?? ''); ?>" name="evk_white_label[color_admin_bar_link]" value="<?php echo esc_attr($wl['color_admin_bar_link'] ?: '#00b9eb'); ?>">
    </div>

    <p class="evo-section-title">Ogólne — ukryj elementy</p>
    <?php foreach ([
        'hide_wp_logo'     => 'Logo WordPress w pasku górnym',
        'hide_help_tab'    => 'Zakładka Pomoc',
        'hide_screen_opts' => 'Opcje ekranu',
        'hide_footer_wp'   => 'Informacja o WP w stopce',
    ] as $key => $lbl): ?>
    <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;margin-bottom:10px;">
        <input type="checkbox" name="evk_white_label[<?php echo $key; ?>]" value="1" <?php checked(1, $wl[$key] ?? 0); ?>>
        <?php echo esc_html($lbl); ?>
    </label>
    <?php endforeach; ?>
</div>

<div>
    <p class="evo-section-title">Kolory — menu boczne</p>
    <div class="evk-grid-colors">

        <div class="evo-field" style="margin:0;">
            <label style="font-size:12px;">Tło sidebara</label>
            <input type="color" data-field="color_menu_bg" data-saved="<?php echo esc_attr($wl['color_menu_bg'] ?? ''); ?>" name="evk_white_label[color_menu_bg]"
                   value="<?php echo esc_attr($wl['color_menu_bg'] ?: '#1d2327'); ?>">
        </div>
        <div class="evo-field" style="margin:0;">
            <label style="font-size:12px;">Tekst pozycji</label>
            <input type="color" data-field="color_menu_text" data-saved="<?php echo esc_attr($wl['color_menu_text'] ?? ''); ?>" name="evk_white_label[color_menu_text]"
                   value="<?php echo esc_attr($wl['color_menu_text'] ?: '#a7aaad'); ?>">
        </div>
        <div class="evo-field" style="margin:0;">
            <label style="font-size:12px;">Ikony</label>
            <input type="color" data-field="color_menu_icon" data-saved="<?php echo esc_attr($wl['color_menu_icon'] ?? ''); ?>" name="evk_white_label[color_menu_icon]"
                   value="<?php echo esc_attr($wl['color_menu_icon'] ?: '#a7aaad'); ?>">
        </div>

        <div style="grid-column:1/-1;height:1px;background:var(--evo-border,#e0e0e0);margin:2px 0;"></div>

        <div class="evo-field" style="margin:0;">
            <label style="font-size:12px;">Tło hover (pozycja główna)</label>
            <input type="color" data-field="color_menu_hover" data-saved="<?php echo esc_attr($wl['color_menu_hover'] ?? ''); ?>" name="evk_white_label[color_menu_hover]"
                   value="<?php echo esc_attr($wl['color_menu_hover'] ?: '#2271b1'); ?>">
        </div>
        <div class="evo-field" style="margin:0;">
            <label style="font-size:12px;">Tekst hover</label>
            <input type="color" data-field="color_menu_hover_text" data-saved="<?php echo esc_attr($wl['color_menu_hover_text'] ?? ''); ?>" name="evk_white_label[color_menu_hover_text]"
                   value="<?php echo esc_attr($wl['color_menu_hover_text'] ?: '#ffffff'); ?>">
        </div>

        <div style="grid-column:1/-1;height:1px;background:var(--evo-border,#e0e0e0);margin:2px 0;"></div>

        <div class="evo-field" style="margin:0;">
            <label style="font-size:12px;">Tło aktywnej pozycji</label>
            <input type="color" data-field="color_menu_active" data-saved="<?php echo esc_attr($wl['color_menu_active'] ?? ''); ?>" name="evk_white_label[color_menu_active]"
                   value="<?php echo esc_attr($wl['color_menu_active'] ?: '#2271b1'); ?>">
        </div>
        <div class="evo-field" style="margin:0;">
            <label style="font-size:12px;">Tekst aktywnej pozycji</label>
            <input type="color" data-field="color_menu_active_text" data-saved="<?php echo esc_attr($wl['color_menu_active_text'] ?? ''); ?>" name="evk_white_label[color_menu_active_text]"
                   value="<?php echo esc_attr($wl['color_menu_active_text'] ?: '#ffffff'); ?>">
        </div>

        <div style="grid-column:1/-1;height:1px;background:var(--evo-border,#e0e0e0);margin:2px 0;"></div>

        <div class="evo-field" style="margin:0;">
            <label style="font-size:12px;">Badge (kółko licznika)</label>
            <input type="color" data-field="color_menu_badge" data-saved="<?php echo esc_attr($wl['color_menu_badge'] ?? ''); ?>" name="evk_white_label[color_menu_badge]"
                   value="<?php echo esc_attr($wl['color_menu_badge'] ?: ($wl['color_primary'] ?: '#2271b1')); ?>">
        </div>
        <div class="evo-field" style="margin:0;">
            <label style="font-size:12px;">Tekst badge</label>
            <input type="color" data-field="color_menu_badge_text" data-saved="<?php echo esc_attr($wl['color_menu_badge_text'] ?? ''); ?>" name="evk_white_label[color_menu_badge_text]"
                   value="<?php echo esc_attr($wl['color_menu_badge_text'] ?: '#ffffff'); ?>">
        </div>

    </div>

    <p class="evo-section-title" style="margin-top:16px;">Kolory — aktywna pozycja podmenu</p>
    <div class="evk-grid-colors">
        <div class="evo-field" style="margin:0;"><label style="font-size:12px;">Tło aktywnej poz. podmenu</label>
            <input type="color" data-field="color_submenu_current_bg" data-saved="<?php echo esc_attr($wl['color_submenu_current_bg'] ?? ''); ?>" name="evk_white_label[color_submenu_current_bg]" value="<?php echo esc_attr($wl['color_submenu_current_bg'] ?: '#2271b1'); ?>">
        </div>
        <div class="evo-field" style="margin:0;"><label style="font-size:12px;">Tekst aktywnej poz. podmenu</label>
            <input type="color" data-field="color_submenu_current_tx" data-saved="<?php echo esc_attr($wl['color_submenu_current_tx'] ?? ''); ?>" name="evk_white_label[color_submenu_current_tx]" value="<?php echo esc_attr($wl['color_submenu_current_tx'] ?: '#ffffff'); ?>">
        </div>
    </div>

    <p class="evo-section-title" style="margin-top:16px;">Kolory — sekcja główna</p>
    <div class="evk-grid-colors">
        <div class="evo-field" style="margin:0;"><label>Tło</label>
            <input type="color" data-field="color_content_bg" data-saved="<?php echo esc_attr($wl['color_content_bg'] ?? ''); ?>" name="evk_white_label[color_content_bg]"   value="<?php echo esc_attr($wl['color_content_bg']   ?: '#f0f0f1'); ?>"></div>
        <div class="evo-field" style="margin:0;"><label>Tekst</label>
            <input type="color" data-field="color_content_text" data-saved="<?php echo esc_attr($wl['color_content_text'] ?? ''); ?>" name="evk_white_label[color_content_text]" value="<?php echo esc_attr($wl['color_content_text'] ?: '#1d2327'); ?>"></div>
        <div class="evo-field" style="margin:0;"><label>Linki</label>
            <input type="color" data-field="color_link" data-saved="<?php echo esc_attr($wl['color_link'] ?? ''); ?>" name="evk_white_label[color_link]"         value="<?php echo esc_attr($wl['color_link']         ?: '#2271b1'); ?>"></div>
        <div class="evo-field" style="margin:0;"><label>Przyciski</label>
            <input type="color" data-field="color_primary" data-saved="<?php echo esc_attr($wl['color_primary'] ?? ''); ?>" name="evk_white_label[color_primary]"      value="<?php echo esc_attr($wl['color_primary']      ?: '#2563eb'); ?>"></div>
        <div class="evo-field" style="margin:0;grid-column:1/-1;"><label>Tło powiadomień</label>
            <input type="color" data-field="color_notice_bg" data-saved="<?php echo esc_attr($wl['color_notice_bg'] ?? ''); ?>" name="evk_white_label[color_notice_bg]"    value="<?php echo esc_attr($wl['color_notice_bg']    ?: '#ffffff'); ?>"></div>
    </div>

    <p class="evo-section-title">Własny CSS admina</p>
    <div class="evo-field">
        <textarea name="evk_white_label[custom_css_admin]" rows="5" style="width:100%;font-family:monospace;font-size:12px;" placeholder="/* własne style CSS dla /wp-admin/ */"><?php echo esc_textarea($wl['custom_css_admin']); ?></textarea>
    </div>
</div>
</div><!-- /grid -->

<!-- WĘZŁY — widoczność -->
<div style="margin-top:28px;">
    <p class="evo-section-title">Pasek górny — widoczność węzłów</p>
    <p class="evo-desc" style="margin-bottom:12px;">Zaznaczone węzły będą <strong>ukryte</strong> dla wszystkich użytkowników.</p>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:8px;">
    <?php foreach ($bar_nodes as $node_id => $node_label):
        $checked = in_array($node_id, (array)($wl['bar_nodes_hidden'] ?? []), true);
    ?>
    <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;padding:7px 10px;background:var(--evo-surface,#f8f8f8);border-radius:4px;border:1px solid <?php echo $checked ? '#2271b1' : 'transparent'; ?>">
        <input type="checkbox" name="evk_white_label[bar_nodes_hidden][]" value="<?php echo esc_attr($node_id); ?>" <?php checked($checked); ?>>
        <span style="flex:1;"><?php echo esc_html($node_label); ?></span>
        <code style="font-size:10px;opacity:.4;"><?php echo esc_html($node_id); ?></code>
    </label>
    <?php endforeach; ?>
    </div>
</div>

<!-- WĘZŁY — kolejność (CSS order / flexbox) -->
<div style="margin-top:24px;">
    <p class="evo-section-title">Pasek górny — kolejność węzłów</p>
    <p class="evo-desc" style="margin-bottom:12px;">
        Ustaw kolejność węzłów WordPressa w pasku. Niższa liczba = bliżej lewej strony.
        Zostaw <strong>0</strong> dla domyślnej kolejności. Działa dla obu stref paska (lewa i prawa).
    </p>
    <div class="evk-order-grid">
    <?php
    foreach ($bar_nodes as $node_id => $node_label):
        $order_val = isset($bar_order[$node_id]) ? (int)$bar_order[$node_id] : 0;
    ?>
    <div class="evk-order-row">
        <span style="flex:1;"><?php echo esc_html($node_label); ?></span>
        <input type="number" name="evk_white_label[bar_nodes_order][<?php echo esc_attr($node_id); ?>]"
               value="<?php echo $order_val; ?>" min="0" max="99" step="1" placeholder="0">
    </div>
    <?php endforeach; ?>
    </div>
    <p style="font-size:12px;color:#888;margin-top:8px;">
        <span class="dashicons dashicons-info-outline" style="font-size:14px;vertical-align:middle;"></span>
        Technicznie: ustawia CSS <code>order</code> na flexbox admin bara. Wartości 0 nie generują CSS.
    </p>
</div>

<!-- MENU BOCZNE — widoczność -->
<div style="margin-top:28px;padding-top:24px;border-top:1px solid var(--evo-border,#e0e0e0);">
    <p class="evo-section-title">Menu boczne — ukryj pozycje</p>
    <p class="evo-desc" style="margin-bottom:12px;">Ukryte dla użytkowników <strong>innych niż administrator</strong>.</p>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:8px;">
    <?php foreach ($sidebar_items as $slug => $label):
        $checked = in_array($slug, (array)($wl['sidebar_hidden'] ?? []), true);
    ?>
    <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;padding:7px 10px;background:var(--evo-surface,#f8f8f8);border-radius:4px;border:1px solid <?php echo $checked ? '#2271b1' : 'transparent'; ?>">
        <input type="checkbox" name="evk_white_label[sidebar_hidden][]" value="<?php echo esc_attr($slug); ?>" <?php checked($checked); ?>>
        <?php echo esc_html($label); ?>
    </label>
    <?php endforeach; ?>
    </div>
    <p style="font-size:12px;color:#666;margin-top:8px;">
        <span class="dashicons dashicons-info-outline" style="font-size:14px;vertical-align:middle;"></span>
        Administratorzy zawsze widzą wszystkie pozycje.
    </p>
</div>

    <hr class="evo-divider" style="margin-top:24px;">
    <p class="evo-section-title">Menu boczne — kolejność pozycji</p>
    <p class="evo-desc" style="margin-bottom:12px;">
        Przeciągaj pozycje aby zmienić kolejność. Możesz dodawać separatory.
        Zapis razem z całym formularzem poniżej.
    </p>

    <div id="evk-menu-order-wrap">
        <div id="evk-menu-order-list" style="max-width:480px;">
            <div style="color:#666;font-size:13px;padding:12px 0;" id="evk-menu-order-loading">
                <span class="dashicons dashicons-update" style="animation:rotation 1s linear infinite;display:inline-block;"></span>
                Ładuję pozycje menu…
            </div>
        </div>
        <div style="margin-top:12px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <button type="button" class="button" id="evk-menu-order-add-sep">+ Dodaj separator</button>
            <button type="button" class="button" id="evk-menu-order-reset" style="color:#b32d2e;">Resetuj do domyślnej</button>
        </div>
    </div>

    <style>
        .evk-mo-row {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 12px; margin-bottom: 6px;
            background: var(--evo-surface, #f8f8f8);
            border: 1px solid var(--evo-border, #e0e0e0);
            border-radius: 6px; font-size: 13px; cursor: default;
        }
        .evk-mo-row.is-sep {
            background: #f0f4ff; border-style: dashed;
            color: #6b7280; font-style: italic;
        }
        .evk-mo-handle { cursor: grab; color: #bbb; flex-shrink: 0; }
        .evk-mo-handle:hover { color: #2271b1; }
        .evk-mo-label { flex: 1; }
        .evk-mo-remove { background: none; border: none; color: #ef4444; cursor: pointer; padding: 0; font-size: 16px; line-height: 1; }
        .evk-mo-remove:hover { color: #b91c1c; }
        @keyframes rotation { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>

    <script>
    (function($){
        var $list  = $('#evk-menu-order-list');
        var sepCount = 0;

        function buildRow(item) {
            var isSep = item.sep || item.slug.indexOf('separator') === 0;
            var label = isSep ? '— separator —' : (item.label || item.slug);
            var $row = $(
                '<div class="evk-mo-row' + (isSep ? ' is-sep' : '') + '" data-slug="' + item.slug + '">' +
                    '<span class="evk-mo-handle dashicons dashicons-menu"></span>' +
                    '<span class="evk-mo-label">' + $('<span>').text(label).html() + '</span>' +
                    (isSep ? '<button type="button" class="evk-mo-remove" title="Usuń">×</button>' : '') +
                '</div>'
            );
            $row.find('.evk-mo-remove').on('click', function(){ $row.remove(); });
            return $row;
        }

        function renderList(allItems, savedOrder) {
            $list.empty();
            var rendered = [];
            if (savedOrder && savedOrder.length) {
                savedOrder.forEach(function(slug) {
                    var found = allItems.find(function(i){ return i.slug === slug; });
                    if (found) { $list.append(buildRow(found)); rendered.push(slug); }
                    else if (slug.indexOf('separator') === 0) {
                        $list.append(buildRow({ slug: slug, label: '— separator —', sep: true }));
                        rendered.push(slug);
                    }
                });
                allItems.forEach(function(item) {
                    if (rendered.indexOf(item.slug) === -1) $list.append(buildRow(item));
                });
            } else {
                allItems.forEach(function(item) { $list.append(buildRow(item)); });
            }
        }

        function initSortable() {
            if (typeof Sortable !== 'undefined') {
                Sortable.create($list[0], {
                    handle: '.evk-mo-handle',
                    animation: 150,
                    ghostClass: 'evk-drag-ghost',
                    chosenClass: 'evk-drag-chosen',
                });
            }
        }

        var evkMenuData = <?php
            global $menu;
            $mo_items = [];
            foreach ((array) $menu as $pos => $item) {
                $slug  = $item[2] ?? '';
                $title = preg_replace('/<span[^>]*>.*<\/span>/Us', '', $item[0] ?? '');
                $title = trim(strip_tags($title));
                $is_sep = (strpos($slug, 'separator') === 0 || ($item[4] ?? '') === 'wp-menu-separator');
                $mo_items[] = [
                    'slug'  => $slug ?: 'separator-' . $pos,
                    'label' => $is_sep ? '— separator —' : ($title ?: $slug),
                    'sep'   => $is_sep,
                ];
            }
            echo wp_json_encode([
                'items'       => $mo_items,
                'saved_order' => $wl['sidebar_menu_order'] ?? [],
            ]);
        ?>;

        $('#evk-menu-order-loading').remove();
        renderList(evkMenuData.items, evkMenuData.saved_order);

        if (document.readyState === 'complete') initSortable();
        else $(window).on('load', initSortable);

        // Dodaj separator
        $('#evk-menu-order-add-sep').on('click', function(){
            var slug = 'separator-custom-' + (++sepCount);
            $list.append(buildRow({ slug: slug, sep: true }));
        });

        // Reset kolejności
        $('#evk-menu-order-reset').on('click', function(){
            if (!confirm('Zresetować kolejność do domyślnej WP?')) return;
            renderList(evkMenuData.items, []);
            $('#evk-wl-menu-order-json').val('[]');
        });

        // Przed submitem — serializuj kolejność do hidden inputa
        $('#evk-wl-form').on('submit', function(){
            var order = [];
            $list.find('.evk-mo-row').each(function(){ order.push($(this).data('slug')); });
            $('#evk-wl-menu-order-json').val(JSON.stringify(order));
        });

    })(jQuery);
    </script>

<!-- WŁASNE MENU PASKA — wewnątrz tej samej formy -->
<div style="margin-top:32px;padding-top:24px;border-top:1px solid var(--evo-border,#e0e0e0);">
    <p class="evo-section-title">Pasek górny — własne pozycje i podmenu</p>
    <p class="evo-desc" style="margin-bottom:16px;">
        Dodaj własne linki. <strong>Dropdown (rodzic)</strong> tworzy rozwijane menu —
        element podmenu musi mieć w polu <em>Parent ID</em> wpisane ID rodzica.
        Kolejność zmieniasz przeciągając uchwyt <span class="dashicons dashicons-menu" style="vertical-align:middle;font-size:14px;"></span>.
    </p>

    <div id="evk-bar-builder"></div>

    <div class="evk-bar-toolbar">
        <button type="button" class="button" id="evk-bar-add-parent" style="display:inline-flex;align-items:center;gap:4px;">
            <span class="dashicons dashicons-menu" style="font-size:16px;width:16px;height:16px;line-height:1;"></span>Dodaj Dropdown
        </button>
        <button type="button" class="button" id="evk-bar-add-item" style="display:inline-flex;align-items:center;gap:4px;">
            <span class="dashicons dashicons-plus" style="font-size:16px;width:16px;height:16px;line-height:1;"></span>Dodaj Element
        </button>
    </div>

    <div style="margin-top:14px;padding:10px 14px;background:#f0f6fc;border-left:3px solid #2271b1;border-radius:0 4px 4px 0;font-size:12px;color:#444;line-height:1.7;">
        <strong>Jak stworzyć dropdown:</strong>
        1. "Dodaj Dropdown" → ustaw nazwę, ID np. <code>moje-menu</code><br>
        2. "Dodaj Element" → nazwa, URL, w <em>Parent ID</em> wpisz: <code>moje-menu</code>
    </div>
</div>

<hr class="evo-divider" style="margin-top:32px;">
<div class="evo-save-bar"><?php submit_button('Zapisz White Label', 'primary', 'submit', false); ?></div>
</form>

<style>
.evk-color-wrap { display:flex; gap:4px; align-items:center; }
.evk-color-reset {
    padding: 0 5px !important; min-height: 26px !important; line-height: 1 !important;
    font-size: 14px !important; cursor: pointer; flex-shrink: 0;
    opacity: .45; transition: opacity .15s, color .15s;
}
.evk-color-reset.is-set   { opacity: 1; color: #2271b1 !important; }
.evk-color-reset.is-reset { opacity: .7; color: #b32d2e !important; text-decoration: line-through; }
input[type=color].evk-was-reset { opacity: .45; outline: 2px dashed #b32d2e; }
</style>

<script>
(function($){

/* ── Przyciski reset dla inputów kolorów ──────────────────────────────── */
$(function() {
    var $form    = $('#evk-wl-form');
    var $resets  = $('#evk-wl-resets');
    var resetList = [];

    $form.find('input[type=color][data-field]').each(function() {
        var $inp  = $(this);
        var field = $inp.data('field');
        var saved = $inp.data('saved');

        if (!$inp.parent().hasClass('evk-color-wrap')) {
            $inp.wrap('<span class="evk-color-wrap"></span>');
        }

        var $hex = $('<input type="text" class="evk-hex-input" maxlength="7" spellcheck="false">')
            .val($inp.val())
            .css({width:'72px', fontFamily:'monospace', fontSize:'12px', padding:'2px 4px'});
        $inp.parent().append($hex);

        $inp.on('input change', function() { $hex.val($inp.val()); });
        $hex.on('input', function() {
            var v = $hex.val().trim();
            if (/^#[0-9a-fA-F]{6}$/.test(v)) {
                $inp.val(v).trigger('change'); $hex.css('color', '');
            } else if (/^[0-9a-fA-F]{6}$/.test(v)) {
                $inp.val('#' + v).trigger('change'); $hex.val('#' + v).css('color', '');
            } else { $hex.css('color', '#c0392b'); }
        });
        $hex.on('blur', function() { $hex.val($inp.val()).css('color', ''); });

        var $btn = $('<button type="button" class="button evk-color-reset" title="Resetuj do domyślnego WP">↺</button>');
        $btn.data('field', field);
        $inp.parent().append($btn);

        if (saved !== '') $btn.addClass('is-set');

        $btn.on('click', function() {
            if ($btn.hasClass('is-reset')) {
                resetList = resetList.filter(function(f){ return f !== field; });
                $inp.removeClass('evk-was-reset').css('opacity', '');
                $btn.removeClass('is-reset').toggleClass('is-set', saved !== '');
            } else {
                if (!resetList.includes(field)) resetList.push(field);
                $inp.addClass('evk-was-reset');
                $btn.removeClass('is-set').addClass('is-reset');
            }
            $resets.val(JSON.stringify(resetList));
        });
    });

    $form.on('submit', function() {
        $resets.val(JSON.stringify(resetList));
    });
});

})(jQuery);
</script>

<script>
(function($){
'use strict';

/* ── Media picker ── */
$('#evk-wl-logo-pick').on('click', function(e){
    e.preventDefault();
    var frame = wp.media({title:'Wybierz logo', button:{text:'Użyj'}, multiple:false});
    frame.on('select', function(){
        $('#evk-wl-logo-url').val(frame.state().get('selection').first().toJSON().url);
    });
    frame.open();
});

/* ── Bar builder ── */
var $builder   = $('#evk-bar-builder');
var existItems = <?php echo wp_json_encode($bar_items); ?>;

function slugify(s) {
    return 'evk-' + s.toLowerCase().replace(/[^a-z0-9\s-]/g,'').trim().replace(/\s+/g,'-').slice(0,40);
}

function esc(s) {
    return String(s).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function makeRow(item) {
    item = item || {};
    var isParent = (item.type === 'parent');

    var $row = $('<div class="evk-bar-row"></div>').css({
        display:      'flex',
        gap:          '6px',
        alignItems:   'center',
        marginBottom: '5px',
        padding:      '7px 10px',
        background:   isParent ? '#eef3fb' : 'var(--evo-surface,#f8f8f8)',
        borderRadius: '4px',
        borderLeft:   isParent ? '3px solid #2271b1' : '3px solid #ddd',
    });

    var badge = isParent
        ? '<span style="font-size:11px;font-weight:700;color:#2271b1;white-space:nowrap;min-width:76px;flex-shrink:0;">▼ DROPDOWN</span>'
        : '<span style="font-size:11px;color:#888;white-space:nowrap;min-width:76px;flex-shrink:0;">→ ELEMENT</span>';

    var idField = isParent
        ? '<input type="text" class="evk-f-id" placeholder="ID (auto)" title="Używany jako Parent ID dla elementów podmenu" style="width:140px;flex-shrink:0;" value="'+esc(item.id||'')+'">' : '';

    var hrefField = isParent ? '' :
        '<input type="text" class="evk-f-href" placeholder="/strona lub https://..." style="flex:1;" value="'+esc(item.href||'')+'">';

    var parentField = isParent ? '' :
        '<input type="text" class="evk-f-parent" placeholder="Parent ID" title="Zostaw puste = samodzielny element" style="width:120px;flex-shrink:0;" value="'+esc(item.parent||'')+'">';

    var targetField = '<label style="font-size:11px;white-space:nowrap;cursor:pointer;flex-shrink:0;">'
        + '<input type="checkbox" class="evk-f-target" style="margin-right:3px;"'+(item.target==='_blank'?' checked':'')+'>_blank</label>';

    $row.html(
        '<span class="evk-drag-handle dashicons dashicons-menu" style="font-size:18px;flex-shrink:0;" title="Przeciągnij aby zmienić kolejność"></span>'
        + badge
        + '<input type="hidden" class="evk-f-type" value="' + (isParent?'parent':'item') + '">' 
        + '<input type="text" class="evk-f-title" placeholder="Tytuł *" style="flex:1;" value="'+esc(item.title||'')+'">' 
        + idField
        + hrefField
        + '<input type="text" class="evk-f-icon" placeholder="dashicons-xxx" style="width:130px;flex-shrink:0;" title="np. dashicons-admin-home" value="'+esc(item.icon||'')+'">' 
        + parentField
        + targetField
        + '<button type="button" class="button evk-row-del" title="Usuń" style="flex-shrink:0;"><span class="dashicons dashicons-trash" style="font-size:16px;width:16px;height:16px;line-height:1.4;"></span></button>'
    );

    if (isParent) {
        $row.find('.evk-f-title').on('blur', function(){
            var $id = $row.find('.evk-f-id');
            if (!$id.val().trim()) $id.val(slugify($(this).val()));
        });
    }

    $row.find('.evk-f-icon').on('input', function(){
        var cls = $(this).val().trim();
        var $p  = $(this).next('.evk-icon-prev');
        if (!$p.length) $p = $('<span class="evk-icon-prev dashicons" style="font-size:18px;color:#2271b1;flex-shrink:0;"></span>').insertAfter($(this));
        $p.attr('class', 'evk-icon-prev dashicons ' + cls);
    });

    return $row;
}

existItems.forEach(function(item){ $builder.append(makeRow(item)); });

$(window).on('load', function(){
    if (typeof Sortable !== 'undefined') {
        Sortable.create(document.getElementById('evk-bar-builder'), {
            animation:   180,
            handle:      '.evk-drag-handle',
            ghostClass:  'evk-drag-ghost',
            chosenClass: 'evk-drag-chosen',
        });
    }
});

$('#evk-bar-add-parent').on('click', function(){ $builder.append(makeRow({type:'parent'})); });
$('#evk-bar-add-item'  ).on('click', function(){ $builder.append(makeRow({type:'item'}));   });

$builder.on('click', '.evk-row-del', function(){
    $(this).closest('.evk-bar-row').remove();
});

// Przed submitem — serializuj bar items do hidden inputa
$('#evk-wl-form').on('submit', function(){
    var items = [];
    $builder.find('.evk-bar-row').each(function(){
        var title = $(this).find('.evk-f-title').val().trim();
        if (!title) return;
        var isParent = $(this).find('.evk-f-type').val() === 'parent';
        var id = isParent
            ? ($(this).find('.evk-f-id').val().trim() || slugify(title))
            : ('evk-' + Math.random().toString(36).slice(2,8));
        items.push({
            type:   isParent ? 'parent' : 'item',
            id:     id,
            title:  title,
            href:   isParent ? '' : ($(this).find('.evk-f-href').val().trim() || '#'),
            icon:   $(this).find('.evk-f-icon').val().trim(),
            parent: isParent ? '' : $(this).find('.evk-f-parent').val().trim(),
            target: $(this).find('.evk-f-target').is(':checked') ? '_blank' : '',
        });
    });
    $('#evk-wl-bar-items-json').val(JSON.stringify(items));
});

})(jQuery);
</script>
