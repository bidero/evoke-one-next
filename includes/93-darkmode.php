<?php
if (!defined('ABSPATH')) exit;

/**
 * Evoke One — Moduł Dark Mode
 */

class EVK_DarkMode {

    private static $instance = null;

    private $defaults = [
        'enabled'           => 1,
        // Przełącznik
        'toggle_selector'   => '.brxe-toggle-mode',
        // Przejście między stronami (wipe)
        'wipe_enabled'      => 1,
        'wipe_direction'    => 'to bottom',
        'wipe_color'        => '#ffffff',
        'wipe_duration'     => 1.5,
        'wipe_blur'         => 15,
        'wipe_easing'       => 'cubic-bezier(0.4, 0, 0.2, 1)',
        // Globalne przejścia CSS
        'global_duration'   => 0.4,
        'global_easing'     => 'ease',
        'global_selectors'  => "[data-brx-theme]\nbody\n#brx-content\nsection",
        'global_properties' => "background-color\ncolor\nborder-color\nfill\nstroke\nfilter",
        // Elementy Bricks
        'bricks_enabled'    => 1,
        'bricks_duration'   => 1.0,
        'bricks_easing'     => 'cubic-bezier(0.33, 1, 0.68, 1)',
        'bricks_selectors'  => ".brxe-text\n.brxe-text-basic\n.brxe-heading\n.brxe-text-link\n.brx-submenu-toggle\ninput::placeholder\n.form-group\n.form-group textarea\ninput[type=checkbox]+label\n.splide\n.brxe-slider-nested\nsvg\n.brxe-div",
        'bricks_properties' => "color\nfilter\nborder-color\nbackground-color",
        // Logo transition
        'logo_enabled'      => 1,
        'logo_light_class'  => 'item-light',
        'logo_dark_class'   => 'item-dark',
        'logo_duration'     => 1.0,
        'logo_easing'       => 'ease-in-out',
        // Ripple
        'ripple_enabled'    => 1,
        'ripple_duration'   => 1200,
        'ripple_blur'       => 20,
        'ripple_easing'     => 'cubic-bezier(0.4, 0, 0.2, 1)',
    ];

    public static function get_instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_head',    [$this, 'render_logo_block_script'], 1);
        add_action('wp_head',    [$this, 'render_styles'], 5);
        add_action('wp_footer',  [$this, 'render_scripts'], 20);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function get_settings(): array {
        return wp_parse_args(get_option('evk_darkmode', []), $this->defaults);
    }

    public function register_settings(): void {
        register_setting('evoke_one_darkmode', 'evk_darkmode', [
            'type'              => 'array',
            'sanitize_callback' => [$this, 'sanitize_settings'],
        ]);
    }

    public function sanitize_settings($input): array {
        $clean = [];

        foreach (['enabled', 'bricks_enabled', 'logo_enabled', 'ripple_enabled', 'wipe_enabled'] as $key) {
            $clean[$key] = !empty($input[$key]) ? 1 : 0;
        }

        $floats = [
            'global_duration' => [0.1, 5.0, 0.4],
            'bricks_duration' => [0.1, 5.0, 1.0],
            'logo_duration'   => [0.1, 5.0, 1.0],
            'wipe_duration'   => [0.3, 5.0, 1.5],
        ];
        foreach ($floats as $key => [$min, $max, $default]) {
            $clean[$key] = isset($input[$key]) ? max($min, min($max, floatval($input[$key]))) : $default;
        }

        $ints = [
            'ripple_duration' => [200, 5000, 1200],
            'ripple_blur'     => [0, 100, 20],
            'wipe_blur'       => [0, 50, 15],
        ];
        foreach ($ints as $key => [$min, $max, $default]) {
            $clean[$key] = isset($input[$key]) ? max($min, min($max, intval($input[$key]))) : $default;
        }

        $texts = [
            'global_selectors', 'global_properties',
            'bricks_selectors', 'bricks_properties',
            'logo_light_class', 'logo_dark_class',
            'toggle_selector',
        ];
        foreach ($texts as $key) {
            $clean[$key] = isset($input[$key]) ? sanitize_textarea_field($input[$key]) : $this->defaults[$key];
        }

        $color = $input['wipe_color'] ?? $this->defaults['wipe_color'];
        $clean['wipe_color'] = preg_match('/^#[0-9a-fA-F]{6}$/', $color) ? $color : $this->defaults['wipe_color'];

        $allowed_directions = ['to bottom', 'to top', 'to right', 'to left'];
        $clean['wipe_direction'] = in_array($input['wipe_direction'] ?? '', $allowed_directions, true)
            ? $input['wipe_direction']
            : $this->defaults['wipe_direction'];

        $easings = ['global_easing', 'bricks_easing', 'logo_easing', 'ripple_easing', 'wipe_easing'];
        $allowed_easings = ['ease', 'ease-in', 'ease-out', 'ease-in-out', 'linear',
                            'cubic-bezier(0.33, 1, 0.68, 1)', 'cubic-bezier(0.4, 0, 0.2, 1)'];
        foreach ($easings as $key) {
            $val = $input[$key] ?? $this->defaults[$key];
            if (preg_match('/^cubic-bezier\(\s*[\d.]+\s*,\s*[\d.-]+\s*,\s*[\d.]+\s*,\s*[\d.-]+\s*\)$/', $val)) {
                $clean[$key] = $val;
            } elseif (in_array($val, $allowed_easings, true)) {
                $clean[$key] = $val;
            } else {
                $clean[$key] = $this->defaults[$key] ?? 'ease';
            }
        }

        return $clean;
    }

    private function parse_lines(string $text): array {
        return array_filter(array_map('trim', explode("\n", $text)));
    }

    /**
     * Blokuje flash podwójnego logo tuż po załadowaniu strony.
     * Musi działać SYNCHRONICZNIE przed renderem — umieszczamy jako
     * pierwszy skrypt w <head>, bez defer/async.
     */
    public function render_logo_block_script(): void {
        $s = $this->get_settings();
        if (empty($s['enabled']) || empty($s['logo_enabled'])) return;
        $light = esc_js($s['logo_light_class']);
        $dark  = esc_js($s['logo_dark_class']);
        ?>
<script id="evk-logo-init">
/* EVK: blok flash logo — synchroniczny, przed renderem */
(function(){
    var m = localStorage.getItem('brx_mode') || 'light';
    var h = document.documentElement;
    h.setAttribute('data-theme', m);
    if (m === 'dark') h.classList.add('dark');
    var s = document.createElement('style');
    if (m === 'dark') {
        s.textContent = '.<?php echo $light; ?>{display:none!important}';
    } else {
        s.textContent = '.<?php echo $dark; ?>{display:none!important}';
    }
    s.id = 'evk-logo-init-css';
    document.head.appendChild(s);
})();
</script>
        <?php
    }

    public function render_styles(): void {
        $s = $this->get_settings();
        if (empty($s['enabled'])) return;

        $global_selectors  = $this->parse_lines($s['global_selectors']);
        $global_properties = $this->parse_lines($s['global_properties']);
        $bricks_selectors  = $this->parse_lines($s['bricks_selectors']);
        $bricks_properties = $this->parse_lines($s['bricks_properties']);

        $global_transition = implode(', ', array_map(
            fn($prop) => "{$prop} {$s['global_duration']}s {$s['global_easing']}",
            $global_properties
        ));

        $bricks_transition = implode(', ', array_map(
            fn($prop) => "{$prop} {$s['bricks_duration']}s {$s['bricks_easing']}",
            $bricks_properties
        ));

        $dir = $s['wipe_direction'];
        $axis_map = [
            'to bottom' => ['to bottom', 'to top'],
            'to top'    => ['to top',    'to bottom'],
            'to right'  => ['to right',  'to left'],
            'to left'   => ['to left',   'to right'],
        ];
        $gradient_dir = $axis_map[$dir][0] ?? 'to bottom';

        $wipe_blur   = intval($s['wipe_blur']);
        $wipe_dur    = floatval($s['wipe_duration']);
        $wipe_easing = esc_attr($s['wipe_easing']);
        $wipe_color  = esc_attr($s['wipe_color']);
        $ripple_blur = intval($s['ripple_blur']);

        echo "<style id=\"evk-darkmode-css\">\n";

        if (!empty($global_selectors) && !empty($global_properties)) {
            echo implode(",\n", $global_selectors) . " {\n";
            echo "    transition: {$global_transition};\n";
            echo "    -webkit-transition: {$global_transition};\n";
            echo "}\n\n";
        }

        if (!empty($s['bricks_enabled']) && !empty($bricks_selectors) && !empty($bricks_properties)) {
            $prefixed = array_map(fn($sel) => "[data-brx-theme] {$sel}", $bricks_selectors);
            echo implode(",\n", $prefixed) . " {\n";
            echo "    transition: {$bricks_transition};\n";
            echo "}\n\n";
        }

        if (!empty($s['logo_enabled'])) {
            $light = esc_attr($s['logo_light_class']);
            $dark  = esc_attr($s['logo_dark_class']);
            echo <<<CSS
.{$light},
.{$dark} {
    view-transition-name: site-logo;
}
::view-transition-group(site-logo) {
    animation-duration: {$s['logo_duration']}s;
    animation-timing-function: {$s['logo_easing']};
}
[data-theme="light"] .{$dark} { display: none !important; }
[data-theme="dark"]  .{$light} { display: none !important; }

CSS;
        }

        echo <<<CSS
@property --wipe-pos {
    syntax: '<percentage>';
    inherits: false;
    initial-value: -20%;
}
@property --ripple-radius {
    syntax: '<length>';
    inherits: false;
    initial-value: 0px;
}

CSS;

        if (!empty($s['wipe_enabled'])) {
            $mask_gradient = "linear-gradient({$gradient_dir}, {$wipe_color} calc(var(--wipe-pos) - {$wipe_blur}%), transparent var(--wipe-pos))";

            echo <<<CSS
@view-transition {
    navigation: auto;
}
::view-transition-group(root) {
    animation-duration: {$wipe_dur}s;
}
::view-transition-old(root) {
    animation: none;
    z-index: 1;
}
::view-transition-new(root) {
    z-index: 2;
    animation: evk-wipe {$wipe_dur}s {$wipe_easing} both;
    -webkit-mask-image: {$mask_gradient};
    mask-image: {$mask_gradient};
}
@keyframes evk-wipe {
    from { --wipe-pos: -20%; }
    to   { --wipe-pos: 120%; }
}
::view-transition-image-pair(root) {
    isolation: isolate;
}

CSS;
        }

        if (!empty($s['ripple_enabled'])) {
            echo <<<CSS
html.is-theme-toggling {
    view-transition-name: theme-ripple;
}
::view-transition-group(theme-ripple) {
    animation: none !important;
    background-color: transparent !important;
}
::view-transition-old(theme-ripple) {
    animation: none !important;
    z-index: 1;
    opacity: 1;
}
::view-transition-new(theme-ripple) {
    animation: none !important;
    z-index: 2;
    -webkit-mask-image: radial-gradient(
        circle at var(--ripple-x, 50%) var(--ripple-y, 50%),
        black calc(max(0px, var(--ripple-radius) - {$ripple_blur}px)),
        transparent var(--ripple-radius)
    ) !important;
    mask-image: radial-gradient(
        circle at var(--ripple-x, 50%) var(--ripple-y, 50%),
        black calc(max(0px, var(--ripple-radius) - {$ripple_blur}px)),
        transparent var(--ripple-radius)
    ) !important;
}

CSS;
        }

        echo "</style>\n";
    }

    public function render_scripts(): void {
        $s = $this->get_settings();
        if (empty($s['enabled'])) return;

        $ripple_enabled  = !empty($s['ripple_enabled']);
        $ripple_duration = intval($s['ripple_duration']);
        $ripple_easing   = esc_js($s['ripple_easing']);
        $toggle_selector = esc_js($s['toggle_selector'] ?: '.brxe-toggle-mode');
        ?>
<script id="evk-darkmode-js">
(function () {
    var html       = document.documentElement;
    var storageKey = 'brx_mode';
    var rippleEnabled  = <?php echo $ripple_enabled ? 'true' : 'false'; ?>;
    var rippleDuration = <?php echo $ripple_duration; ?>;
    var rippleEasing   = '<?php echo $ripple_easing; ?>';
    var toggleSelector = '<?php echo $toggle_selector; ?>';

    var savedMode = localStorage.getItem(storageKey) || 'light';
    html.setAttribute('data-theme', savedMode);
    if (savedMode === 'dark') html.classList.add('dark');

    window.addEventListener('DOMContentLoaded', function () {
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                var tmpStyle = document.getElementById('evk-logo-init-css');
                if (tmpStyle) tmpStyle.remove();
            });
        });

        var toggleBtns = document.querySelectorAll(toggleSelector);
        toggleBtns.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                var currentMode = html.getAttribute('data-theme');
                var newMode     = currentMode === 'light' ? 'dark' : 'light';

                if (!rippleEnabled || !document.startViewTransition) {
                    updateTheme(newMode);
                    return;
                }

                var rect = this.getBoundingClientRect();
                var x    = rect.left + rect.width  / 2;
                var y    = rect.top  + rect.height / 2;
                var endRadius = Math.hypot(
                    Math.max(x, window.innerWidth  - x),
                    Math.max(y, window.innerHeight - y)
                );

                html.style.setProperty('--ripple-x', x + 'px');
                html.style.setProperty('--ripple-y', y + 'px');
                html.classList.add('is-theme-toggling');

                var transition = document.startViewTransition(function () {
                    updateTheme(newMode);
                });

                transition.ready.then(function () {
                    html.animate(
                        { '--ripple-radius': ['0px', (endRadius + 150) + 'px'] },
                        {
                            duration: rippleDuration,
                            easing: rippleEasing,
                            pseudoElement: '::view-transition-new(theme-ripple)',
                            fill: 'forwards'
                        }
                    );
                    html.animate(
                        { opacity: [1, 0.8] },
                        {
                            duration: rippleDuration,
                            easing: rippleEasing,
                            pseudoElement: '::view-transition-old(theme-ripple)',
                            fill: 'forwards'
                        }
                    );
                });

                transition.finished.then(function () {
                    html.classList.remove('is-theme-toggling');
                });
            });
        });
    });

    function updateTheme(mode) {
        html.setAttribute('data-theme', mode);
        localStorage.setItem(storageKey, mode);
        if (mode === 'dark') {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }
    }
})();
</script>
        <?php
    }
}

EVK_DarkMode::get_instance();
