<?php
/*
Plugin Name: Hive's Code Snippets Manager
Description: Manage and toggle Hive's custom code snippets in WordPress.
Version: 0.0.1
Author: Hive Digital
*/

// Define all PHP snippets
function get_hardcoded_snippets()
{
    return [
        'wp_dark_mode' => [
            'name' => 'WP Dark Mode',
            'description' => 'This enables dark mode for the WordPress Dashboard.',
            'code' => function () {
                add_action('admin_head', function () {
                    echo '
                    <style>
                    /* Change link colour to white */
                    #wpbody-content a {
                        filter: invert(1) hue-rotate(180deg) saturate(10);
                        color: white !important;
                    }
                    /* Change link colour to red on hover */
                    #wpbody-content a:hover {
                        filter: invert(1) hue-rotate(180deg) saturate(10);
                        color: red !important;
                    }
                    /* Dark mode background and content area */
                    .block-editor-page .editor-styles-wrapper,
                    .wp-admin {
                        background: #262626;
                        color: lightgray;
                    }
                    </style>';
                });
            },
        ],
        'woocommerce_out_of_stock_enquiry' => [
            'name' => 'Woocommerce Out of Stock Enquiry',
            'description' => 'Adds an enquiry button to Out of Stock Products.',
            'class' => 'oos-enquiry-btn', // Specify the class to identify <a> elements
            'code' => function ($url) {
                add_filter('woocommerce_get_availability_text', function ($availability, $product) use ($url) {
                    if ($availability === 'Out of stock') {
                        $availability = sprintf('<a href="%s" class="oos-enquiry-btn"> Sold Out, Enquire Now </a>', esc_url($url));
                    }
                    return $availability;
                }, 10, 2);
            },
        ],
    ];
}

// Define all CSS snippets
function get_hardcoded_css_snippets()
{
    // Get user-defined scrollbar color or use default
    $scrollbar_thumb_color = get_option('scrollbar_thumb_color', '#018a13');

    return [
        'custom_scrollbar_style' => [
            'name' => 'Custom Scrollbar Style',
            'description' => 'Applies a custom style to the Scrollbar across the website',
            'css' => "
                html, body {
                    scrollbar-color: {$scrollbar_thumb_color} #fff;
                    scrollbar-width: thin;
                }

                ::-webkit-scrollbar {
                    width: 7px;
                }

                ::-webkit-scrollbar-track {
                    background: #fff;
                }

                ::-webkit-scrollbar-thumb {
                    background: {$scrollbar_thumb_color};
                    border: 1px solid #eee;
                }
            ",
        ],
        'dark_mode_body' => [
            'name' => 'Dark Mode Body',
            'description' => 'Applies a dark background to the body element.',
            'css' => '
                body {
                    background-color: #121212;
                    color: #f1f1f1;
                }
            ',
        ],
    ];
}

// Render admin page
function render_snippets_page()
{
    $php_snippets = get_hardcoded_snippets();
    $css_snippets = get_hardcoded_css_snippets();

    $enabled_php_snippets = get_option('enabled_hardcoded_snippets', []);
    $php_snippet_urls = get_option('php_snippet_urls', []);

    $enabled_css_snippets = get_option('enabled_hardcoded_css_snippets', []);
    $css_snippet_urls = get_option('css_snippet_urls', []);
    $scrollbar_thumb_color = get_option('scrollbar_thumb_color', '#018a13'); // Default color

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        check_admin_referer('update_snippets', 'snippets_nonce');

        // Save enabled PHP snippets
        $enabled_php_snippets = array_keys($_POST['enabled_php_snippets'] ?? []);
        update_option('enabled_hardcoded_snippets', $enabled_php_snippets);

        // Save PHP snippet URLs
        $php_snippet_urls = $_POST['php_snippet_urls'] ?? [];
        update_option('php_snippet_urls', $php_snippet_urls);

        // Save enabled CSS snippets
        $enabled_css_snippets = array_keys($_POST['enabled_css_snippets'] ?? []);
        update_option('enabled_hardcoded_css_snippets', $enabled_css_snippets);

        $css_snippet_urls = $_POST['css_snippet_urls'] ?? [];
        update_option('css_snippet_urls', $css_snippet_urls);

        // Save custom scrollbar thumb color
        if (isset($_POST['scrollbar_thumb_color'])) {
            update_option('scrollbar_thumb_color', sanitize_hex_color($_POST['scrollbar_thumb_color']));
        }

        // Refresh the page
        wp_safe_redirect(admin_url('options-general.php?page=hardcoded-snippets'));
        exit;
    }

    ?>
    <div class="wrap">
        <h1>Hive's Code Snippets Manager</h1>
        <form method="post">
            <?php wp_nonce_field('update_snippets', 'snippets_nonce'); ?>

            <h2>Code Snippets</h2>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Enable</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>URL / Options</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- PHP Snippets -->
                    <?php foreach ($php_snippets as $key => $snippet): ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="enabled_php_snippets[<?php echo esc_attr($key); ?>]" value="1"
                                    <?php checked(in_array($key, $enabled_php_snippets)); ?>>
                            </td>
                            <td>PHP</td>
                            <td><?php echo esc_html($snippet['name']); ?></td>
                            <td><?php echo esc_html($snippet['description']); ?></td>
                            <td>
                                <?php if (isset($snippet['class'])): ?>
                                    <input type="text" name="php_snippet_urls[<?php echo esc_attr($key); ?>]"
                                        value="<?php echo esc_attr($php_snippet_urls[$key] ?? ''); ?>"
                                        placeholder="Enter URL for <?php echo esc_html($snippet['name']); ?>">
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- CSS Snippets -->
                    <?php foreach ($css_snippets as $key => $snippet): ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="enabled_css_snippets[<?php echo esc_attr($key); ?>]" value="1"
                                    <?php checked(in_array($key, $enabled_css_snippets)); ?>>
                            </td>
                            <td>CSS</td>
                            <td><?php echo esc_html($snippet['name']); ?></td>
                            <td><?php echo esc_html($snippet['description']); ?></td>
                            <td>
                                <?php if ($key === 'custom_scrollbar_style'): ?>
                                    <label for="scrollbar_thumb_color">Thumb Color:</label>
                                    <input type="color" id="scrollbar_thumb_color" name="scrollbar_thumb_color"
                                        value="<?php echo esc_attr($scrollbar_thumb_color); ?>">
                                <?php else: ?>
                                    <input type="text" name="css_snippet_urls[<?php echo esc_attr($key); ?>]"
                                        value="<?php echo esc_attr($css_snippet_urls[$key] ?? ''); ?>"
                                        placeholder="Enter URL for <?php echo esc_html($snippet['name']); ?>">
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php submit_button('Save Settings'); ?>
        </form>
    </div>
    <?php
}

// Enqueue enabled CSS snippets
function enqueue_enabled_css_snippets()
{
    $snippets = get_hardcoded_css_snippets();
    $enabled_snippets = get_option('enabled_hardcoded_css_snippets', []);
    $css_snippet_urls = get_option('css_snippet_urls', []);

    if (!empty($enabled_snippets)) {
        $css = '';
        foreach ($enabled_snippets as $key) {
            if (isset($snippets[$key])) {
                $url = $css_snippet_urls[$key] ?? '';
                if (empty($url) || strpos($_SERVER['REQUEST_URI'], wp_make_link_relative($url)) !== false) {
                    $css .= $snippets[$key]['css'] . "\n";
                }
            }
        }

        if (!empty($css)) {
            wp_add_inline_style('custom-css-snippets', $css);
        }
    }
}

// Register CSS snippets
function register_custom_css_snippets()
{
    wp_register_style('custom-css-snippets', false);
    wp_enqueue_style('custom-css-snippets');
}
add_action('wp_enqueue_scripts', 'register_custom_css_snippets');
add_action('wp_enqueue_scripts', 'enqueue_enabled_css_snippets');

// Execute enabled PHP snippets
add_action('init', function () {
    $snippets = get_hardcoded_snippets();
    $enabled_snippets = get_option('enabled_hardcoded_snippets', []);
    $php_snippet_urls = get_option('php_snippet_urls', []);

    foreach ($enabled_snippets as $snippet_key) {
        if (isset($snippets[$snippet_key]) && is_callable($snippets[$snippet_key]['code'])) {
            $url = $php_snippet_urls[$snippet_key] ?? '#';
            call_user_func($snippets[$snippet_key]['code'], $url);
        }
    }
});

// Add menu page
add_action('admin_menu', function () {
    add_options_page(
        'Hardcoded Snippets',
        'Snippets Manager',
        'manage_options',
        'hardcoded-snippets',
        'render_snippets_page'
    );
});
