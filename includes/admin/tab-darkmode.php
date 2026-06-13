<?php
if (!defined('ABSPATH')) exit;
/**
 * Evoke ONE — Tab: darkmode
 */
?>
<?php $dm = EVK_DarkMode::get_instance()->get_settings(); ?>
            <form method="post" action="options.php">
                <?php settings_fields('evoke_one_darkmode'); ?>

                <div class="evo-status-card">
                    <div class="evo-status-icon <?php echo !empty($dm['enabled']) ? 'on' : 'off'; ?>">
                        <span class="dashicons <?php echo !empty($dm['enabled']) ? 'dashicons-visibility' : 'dashicons-hidden'; ?>"></span>
                    </div>
                    <div class="evo-status-text">
                        <h3>Moduł Dark Mode: <?php echo !empty($dm['enabled']) ? 'WŁĄCZONY' : 'WYŁĄCZONY'; ?></h3>
                        <p>Przejścia CSS i efekty View Transition API dla przełączania motywu.</p>
                    </div>
                    <div class="evo-status-actions">
                        <span class="evo-toggle-label"><?php echo !empty($dm['enabled']) ? 'Włączony' : 'Wyłączony'; ?></span>
                        <label class="evo-toggle">
                            <input type="checkbox" name="evk_darkmode[enabled]" data-option="evk_darkmode" data-field="enabled" value="1" <?php checked(!empty($dm['enabled'])); ?>>
                            <span class="evo-slider"></span>
                        </label>
                    </div>
                </div>

                <p class="evo-section-title">Przełącznik motywu</p>
                <div class="evo-field">
                    <label>Selektor CSS przycisku przełączającego</label>
                    <input type="text" name="evk_darkmode[toggle_selector]" value="<?php echo esc_attr($dm['toggle_selector']); ?>" placeholder=".brxe-toggle-mode" style="max-width:340px;">
                    <div class="evo-desc">Dowolny selektor CSS — klasa, ID lub atrybut. Domyślnie: <code>.brxe-toggle-mode</code></div>
                </div>

                <hr class="evo-divider">
                <p class="evo-section-title">Zasłona przy nawigacji między stronami</p>
                <div class="evo-info-box">
                    <span class="dashicons dashicons-info"></span>
                    <div>Efekt zasłony przy przechodzeniu między podstronami. Wymaga Chrome/Edge 111+ lub innej przeglądarki z <code>@view-transition</code>.</div>
                </div>

                <div class="evo-field">
                    <label style="display:flex;align-items:center;gap:10px;">
                        <input type="checkbox" name="evk_darkmode[wipe_enabled]" value="1" <?php checked(!empty($dm['wipe_enabled'])); ?>>
                        Włącz efekt zasłony przy nawigacji
                    </label>
                </div>

                <div class="evo-field">
                    <label>Kierunek zasłony</label>
                    <select name="evk_darkmode[wipe_direction]">
                        <?php foreach (['to bottom' => 'Z góry na dół ↓', 'to top' => 'Z dołu do góry ↑', 'to right' => 'Od lewej do prawej →', 'to left' => 'Od prawej do lewej ←'] as $val => $label): ?>
                        <option value="<?php echo esc_attr($val); ?>" <?php selected($dm['wipe_direction'], $val); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="evo-field">
                    <label>Kolor zasłony</label>
                    <div class="evo-color-row">
                        <button type="button" class="evo-color-swatch" id="wipe-color-swatch"
                            style="background:<?php echo esc_attr($dm['wipe_color']); ?>;"
                            onclick="document.getElementById('wipe_color_input').click();">
                        </button>
                        <input type="color" id="wipe_color_input" name="evk_darkmode[wipe_color]"
                            value="<?php echo esc_attr($dm['wipe_color']); ?>"
                            style="display:none;"
                            oninput="document.getElementById('wipe-color-swatch').style.background=this.value;document.getElementById('wipe-color-text').value=this.value;">
                        <input type="text" id="wipe-color-text" value="<?php echo esc_attr($dm['wipe_color']); ?>"
                            style="width:110px;font-family:monospace;"
                            oninput="var v=this.value;if(/^#[0-9a-fA-F]{6}$/.test(v)){document.getElementById('wipe_color_input').value=v;document.getElementById('wipe-color-swatch').style.background=v;}">
                        <span style="font-size:12px;color:#64748b;">Hex, np. <code>#ffffff</code></span>
                    </div>
                    <div class="evo-desc">Kolor zasłony zakrywającej stronę podczas nawigacji.</div>
                </div>

                <div style="display:flex;gap:24px;flex-wrap:wrap;">
                    <div class="evo-field">
                        <label>Czas trwania (s)</label>
                        <input type="number" name="evk_darkmode[wipe_duration]" value="<?php echo esc_attr($dm['wipe_duration']); ?>" min="0.3" max="5" step="0.1" style="width:80px;">
                    </div>
                    <div class="evo-field">
                        <label>Rozmycie krawędzi (%)</label>
                        <input type="number" name="evk_darkmode[wipe_blur]" value="<?php echo esc_attr($dm['wipe_blur']); ?>" min="0" max="50" step="5" style="width:80px;">
                        <div class="evo-desc">0 = ostra krawędź, 50 = miękka</div>
                    </div>
                    <div class="evo-field">
                        <label>Easing</label>
                        <select name="evk_darkmode[wipe_easing]">
                            <?php foreach (['ease', 'ease-in', 'ease-out', 'ease-in-out', 'linear', 'cubic-bezier(0.4, 0, 0.2, 1)'] as $e): ?>
                            <option value="<?php echo $e; ?>" <?php selected($dm['wipe_easing'], $e); ?>><?php echo $e; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <hr class="evo-divider">
                <p class="evo-section-title">Przejścia CSS przy zmianie motywu (globalne)</p>
                <div class="evo-info-box">
                    <span class="dashicons dashicons-info"></span>
                    <div>Przejścia dla głównych kontenerów strony (<code>body</code>, <code>section</code>, etc.).</div>
                </div>
                <div class="evo-field">
                    <label>Selektory (jeden na linię)</label>
                    <textarea name="evk_darkmode[global_selectors]" rows="4"><?php echo esc_textarea($dm['global_selectors']); ?></textarea>
                </div>
                <div class="evo-field">
                    <label>Właściwości CSS (jeden na linię)</label>
                    <textarea name="evk_darkmode[global_properties]" rows="4"><?php echo esc_textarea($dm['global_properties']); ?></textarea>
                </div>
                <div style="display:flex;gap:24px;flex-wrap:wrap;">
                    <div class="evo-field">
                        <label>Czas trwania (s)</label>
                        <input type="number" name="evk_darkmode[global_duration]" value="<?php echo esc_attr($dm['global_duration']); ?>" min="0.1" max="5" step="0.1" style="width:80px;">
                    </div>
                    <div class="evo-field">
                        <label>Easing</label>
                        <select name="evk_darkmode[global_easing]">
                            <?php foreach (['ease', 'ease-in', 'ease-out', 'ease-in-out', 'linear'] as $e): ?>
                            <option value="<?php echo $e; ?>" <?php selected($dm['global_easing'], $e); ?>><?php echo $e; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <hr class="evo-divider">
                <p class="evo-section-title">Elementy Bricks Builder</p>
                <div class="evo-field">
                    <label style="display:flex;align-items:center;gap:10px;">
                        <input type="checkbox" name="evk_darkmode[bricks_enabled]" value="1" <?php checked(!empty($dm['bricks_enabled'])); ?>>
                        Włącz przejścia dla elementów Bricks
                    </label>
                </div>
                <div class="evo-field">
                    <label>Selektory Bricks (jeden na linię, bez prefiksu <code>[data-brx-theme]</code>)</label>
                    <textarea name="evk_darkmode[bricks_selectors]" rows="6"><?php echo esc_textarea($dm['bricks_selectors']); ?></textarea>
                </div>
                <div class="evo-field">
                    <label>Właściwości CSS</label>
                    <textarea name="evk_darkmode[bricks_properties]" rows="3"><?php echo esc_textarea($dm['bricks_properties']); ?></textarea>
                </div>
                <div style="display:flex;gap:24px;flex-wrap:wrap;">
                    <div class="evo-field">
                        <label>Czas trwania (s)</label>
                        <input type="number" name="evk_darkmode[bricks_duration]" value="<?php echo esc_attr($dm['bricks_duration']); ?>" min="0.1" max="5" step="0.1" style="width:80px;">
                    </div>
                    <div class="evo-field">
                        <label>Easing</label>
                        <input type="text" name="evk_darkmode[bricks_easing]" value="<?php echo esc_attr($dm['bricks_easing']); ?>" style="width:280px;" placeholder="np. cubic-bezier(0.33, 1, 0.68, 1)">
                    </div>
                </div>

                <hr class="evo-divider">
                <p class="evo-section-title">Przejście logo (View Transition)</p>
                <div class="evo-field">
                    <label style="display:flex;align-items:center;gap:10px;">
                        <input type="checkbox" name="evk_darkmode[logo_enabled]" value="1" <?php checked(!empty($dm['logo_enabled'])); ?>>
                        Włącz animację przejścia logo
                    </label>
                </div>
                <div style="display:flex;gap:24px;flex-wrap:wrap;">
                    <div class="evo-field">
                        <label>Klasa logo jasnego</label>
                        <input type="text" name="evk_darkmode[logo_light_class]" value="<?php echo esc_attr($dm['logo_light_class']); ?>" style="width:160px;">
                    </div>
                    <div class="evo-field">
                        <label>Klasa logo ciemnego</label>
                        <input type="text" name="evk_darkmode[logo_dark_class]" value="<?php echo esc_attr($dm['logo_dark_class']); ?>" style="width:160px;">
                    </div>
                    <div class="evo-field">
                        <label>Czas animacji (s)</label>
                        <input type="number" name="evk_darkmode[logo_duration]" value="<?php echo esc_attr($dm['logo_duration']); ?>" min="0.1" max="5" step="0.1" style="width:80px;">
                    </div>
                </div>

                <hr class="evo-divider">
                <p class="evo-section-title">Efekt Ripple (przełączanie motywu)</p>
                <div class="evo-info-box">
                    <span class="dashicons dashicons-info"></span>
                    <div>Fala rozchodząca się od przycisku przy zmianie motywu. Wymaga Chrome/Edge 111+.</div>
                </div>
                <div class="evo-field">
                    <label style="display:flex;align-items:center;gap:10px;">
                        <input type="checkbox" name="evk_darkmode[ripple_enabled]" value="1" <?php checked(!empty($dm['ripple_enabled'])); ?>>
                        Włącz efekt ripple
                    </label>
                </div>
                <div style="display:flex;gap:24px;flex-wrap:wrap;">
                    <div class="evo-field">
                        <label>Czas trwania (ms)</label>
                        <input type="number" name="evk_darkmode[ripple_duration]" value="<?php echo esc_attr($dm['ripple_duration']); ?>" min="200" max="5000" step="100" style="width:100px;">
                    </div>
                    <div class="evo-field">
                        <label>Rozmycie krawędzi (px)</label>
                        <input type="number" name="evk_darkmode[ripple_blur]" value="<?php echo esc_attr($dm['ripple_blur']); ?>" min="0" max="100" step="5" style="width:80px;">
                    </div>
                    <div class="evo-field">
                        <label>Easing</label>
                        <input type="text" name="evk_darkmode[ripple_easing]" value="<?php echo esc_attr($dm['ripple_easing']); ?>" style="width:280px;">
                    </div>
                </div>

                <div class="evo-save-bar">
                    <?php submit_button('Zapisz ustawienia Dark Mode', 'primary', 'submit', false); ?>

                <hr class="evo-divider">
                <p class="evo-section-title">Przejścia elementów lista → wpis (View Transition)</p>
                <div class="evo-info-box">
                    <span class="dashicons dashicons-info"></span>
                    <div>Tytuł i obrazek z listy wpisów "przefruną" płynnie do ich odpowiedników na stronie wpisu. Wymaga Chrome/Edge 111+ z <code>@view-transition { navigation: auto }</code>. Podaj klasy CSS elementów Bricks — bez kropki.</div>
                </div>

                <div class="evo-field">
                    <label style="display:flex;align-items:center;gap:10px;">
                        <input type="checkbox" name="evk_darkmode[post_trans_enabled]" value="1" <?php checked(!empty($dm['post_trans_enabled'])); ?>>
                        Włącz przejścia lista → wpis
                    </label>
                </div>

                <p class="evo-section-title" style="font-size:12px;color:#666;margin-top:16px;">Na liście wpisów — klasy elementów Bricks</p>
                <div class="evo-field">
                    <label>Klasa elementu tytułu <span style="color:#999;font-weight:400;">(na liście)</span></label>
                    <input type="text" name="evk_darkmode[post_trans_title_class]" value="<?php echo esc_attr($dm['post_trans_title_class']); ?>" placeholder="post-title-item" style="max-width:340px;">
                    <div class="evo-desc">Klasa CSS elementu Bricks z tytułem wpisu w query loop (bez kropki). Kilka klas oddziel przecinkami.</div>
                </div>
                <div class="evo-field">
                    <label>Klasa elementu obrazka <span style="color:#999;font-weight:400;">(na liście)</span></label>
                    <input type="text" name="evk_darkmode[post_trans_image_class]" value="<?php echo esc_attr($dm['post_trans_image_class']); ?>" placeholder="post-image-item" style="max-width:340px;">
                    <div class="evo-desc">Klasa CSS elementu Bricks z obrazkiem wpisu w query loop (bez kropki). Kilka klas oddziel przecinkami.</div>
                </div>

                <p class="evo-section-title" style="font-size:12px;color:#666;margin-top:16px;">Na stronie pojedynczego wpisu — selektory CSS</p>
                <div class="evo-field">
                    <label>Selektor tytułu <span style="color:#999;font-weight:400;">(na singlu)</span></label>
                    <input type="text" name="evk_darkmode[post_trans_title_single]" value="<?php echo esc_attr($dm['post_trans_title_single']); ?>" placeholder=".single-post h1.brxe-heading" style="max-width:340px;">
                    <div class="evo-desc">Pełny selektor CSS elementu z tytułem na stronie wpisu. Np. <code>.single-post .brxe-post-title</code></div>
                </div>
                <div class="evo-field">
                    <label>Selektor obrazka <span style="color:#999;font-weight:400;">(na singlu)</span></label>
                    <input type="text" name="evk_darkmode[post_trans_image_single]" value="<?php echo esc_attr($dm['post_trans_image_single']); ?>" placeholder=".single-post .featured-image img" style="max-width:340px;">
                    <div class="evo-desc">Pełny selektor CSS featured image na stronie wpisu. Np. <code>.single-post .brxe-post-image img</code></div>
                </div>

                <div class="evo-field">
                    <label>Czas animacji (s)</label>
                    <input type="number" name="evk_darkmode[post_trans_duration]" value="<?php echo esc_attr($dm['post_trans_duration']); ?>" min="0.1" max="3.0" step="0.1" style="width:100px;">
                </div>
                <div class="evo-field">
                    <label>Easing</label>
                    <input type="text" name="evk_darkmode[post_trans_easing]" value="<?php echo esc_attr($dm['post_trans_easing']); ?>" style="width:280px;" placeholder="ease-in-out">
                    <div class="evo-desc">Np. <code>ease-in-out</code>, <code>cubic-bezier(0.4, 0, 0.2, 1)</code></div>
                </div>

                    <?php submit_button('Zapisz ustawienia Dark Mode', 'primary', 'submit', false); ?>
                </div>
            </form>
