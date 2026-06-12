<?php
if (!defined('ABSPATH')) exit;
// Evoke ONE — TL tab content. Zmienne z tl_render_page(): $data $langs $codes $tab $base $nonce $ajax_url $stats
?>
<?php
            $tl_module_enabled = !empty(get_option('evk_tl_module_enabled', 1));
            $pl_flag_id = get_option('tl_pl_flag', 0);
            $pl_flag_url = $pl_flag_id ? wp_get_attachment_image_url($pl_flag_id, 'thumbnail') : '';
            ?>
            <!-- STATUS modułu tłumaczeń -->
            <div class="evo-status-card" style="margin-bottom:24px;">
                <div class="evo-status-icon <?php echo $tl_module_enabled ? 'on' : 'off'; ?>">
                    <span class="dashicons dashicons-translation" style="font-size:24px;width:24px;height:24px;line-height:1;"></span>
                </div>
                <div class="evo-status-text">
                    <h3>Moduł tłumaczeń: <?php echo $tl_module_enabled ? 'WŁĄCZONY' : 'WYŁĄCZONY'; ?></h3>
                    <p>Gdy wyłączony — cały silnik tłumaczeń (URL, engine, switcher, sitemap) nie jest ładowany. Ustawienia są zachowane.</p>
                </div>
                <div class="evo-status-actions">
                    <label class="evo-toggle">
                        <input type="checkbox" id="evk-tl-module-toggle" data-option="evk_tl_module_enabled" value="1"
                            <?php checked($tl_module_enabled); ?>>
                        <span class="evo-slider"></span>
                    </label>
                </div>
            </div>
            <script>
            (function($){
                $('#evk-tl-module-toggle').on('change', function(){
                    var $cb  = $(this);
                    var val  = $cb.is(':checked') ? 1 : 0;
                    $.post(ajaxurl, {
                        action: 'evk_save_option',
                        nonce:  '<?php echo wp_create_nonce('evk_save_option'); ?>',
                        option: 'evk_tl_module_enabled',
                        value:  val
                    }, function(r){
                        if (r.success) {
                            location.reload();
                        } else {
                            alert('Błąd zapisu. Spróbuj ponownie.');
                            $cb.prop('checked', !val);
                        }
                    });
                });
            })(jQuery);
            </script>
            ?>
            <div class="tl-info-box">
                <strong>Prefiksy językowe w URL:</strong> System używa prefiksów w URL zamiast parametrów <code>?lang=</code>.<br>
                Przykłady: <code>/en/aktualnosci</code>, <code>/de/kontakt</code>. Polski (PL) nie ma prefiksu.<br>
                Po zmianie języków kliknij "Zapisz ustawienia" — reguły URL zostaną odświeżone automatycznie.
            </div>
            <div class="tl-menu-settings">
                <h3>Lokalizacja menu</h3>
                <p>Wybierz miejsce wyswietlania wtyczki w panelu bocznym:</p>
                <select id="tl-menu-location">
                    <option value="options-general.php" <?php selected($menu_location,'options-general.php'); ?>>Ustawienia</option>
                    <option value="index.php" <?php selected($menu_location,'index.php'); ?>>Kokpit</option>
                    <option value="tools.php" <?php selected($menu_location,'tools.php'); ?>>Narzedzia</option>
                    <option value="none" <?php selected($menu_location,'none'); ?>>Osobna pozycja</option>
                </select>
            </div>
            <table class="lang-table">
                <thead><tr><th style="width:30px;"></th><th>Kod</th><th>Nazwa</th><th>Tag HTML</th><th>Flaga</th><th></th></tr></thead>
                <tbody id="lang-body">
                    <tr class="lang-row-pl">
                        <td></td>
                        <td>pl</td>
                        <td>Polski</td>
                        <td>pl-PL</td>
                        <td>
                            <?php if ($pl_flag_url): ?>
                            <img src="<?php echo esc_url($pl_flag_url); ?>" class="tl-lang-flag-preview" data-att="<?php echo esc_attr($pl_flag_id); ?>" style="width:32px;height:20px;object-fit:cover;border-radius:2px;border:1px solid #dcdcde;cursor:pointer;" onclick="tlOpenLangFlag(this,'pl')">
                            <?php else: ?>
                            <div class="tl-lang-flag-empty" data-att="0" style="width:32px;height:20px;border:1px dashed #c3c4c7;border-radius:2px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#a7aaad;font-size:12px;" onclick="tlOpenLangFlag(this,'pl')">+</div>
                            <?php endif; ?>
                        </td>
                        <td></td>
                    </tr>
                    <?php foreach (get_option('tl_languages',[]) as $lang):
                        $flag_id = absint($lang['flag'] ?? 0);
                        $flag_url = $flag_id ? tl_get_flag_url($flag_id) : '';
                    ?>
                    <tr>
                        <td><span class="drag-handle" title="Przeciagnij">☰</span></td>
                        <td><input type="text" class="lang-code" value="<?php echo esc_attr($lang['code']); ?>" placeholder="en"></td>
                        <td><input type="text" class="lang-name" value="<?php echo esc_attr($lang['name']); ?>" placeholder="Angielski"></td>
                        <td><input type="text" class="lang-html" value="<?php echo esc_attr($lang['html']); ?>" placeholder="en-GB"></td>
                        <td>
                            <?php if ($flag_url): ?>
                            <img src="<?php echo esc_url($flag_url); ?>" class="tl-lang-flag-preview" data-att="<?php echo esc_attr($flag_id); ?>" style="width:32px;height:20px;object-fit:cover;border-radius:2px;border:1px solid #dcdcde;cursor:pointer;" onclick="tlOpenLangFlag(this)">
                            <?php else: ?>
                            <div class="tl-lang-flag-empty" data-att="0" style="width:32px;height:20px;border:1px dashed #c3c4c7;border-radius:2px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#a7aaad;font-size:12px;" onclick="tlOpenLangFlag(this)">+</div>
                            <?php endif; ?>
                        </td>
                        <td><button type="button" class="button-link-delete" onclick="jQuery(this).closest('tr').remove();tlMarkDirty();">Usun</button></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="tl-footer">
                <button type="button" class="button" onclick="tlAddLang()">+ Dodaj jezyk</button>
                <button type="button" class="button button-primary" onclick="tlSaveSettings()">Zapisz ustawienia</button>
                <span class="tl-save-status" id="save-status-settings"></span>
            </div>
