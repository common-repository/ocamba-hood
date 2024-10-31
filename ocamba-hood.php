<?php
/**
 * @package             OcambaHood
 * @author              Del Systems
 * @copyright           2024 Del Systems
 * @license             GPL-2.0-or-later
 * @version             1.0.4
 * Plugin Name:         Ocamba Hood
 * Plugin URI:          http://wordpress.org/plugins/ocamba-hood/
 * Description:         Ocamba Hood - Boost Your Site's Traffic & Engagement! Send Targeted, Automated Push Notifications with Our Lightweight, Fast WP Integration!
 * Author:              Del Systems
 * Author URI:          https://delsystems.net
 * Version:             1.0.4
 * License:             GPL v2 or later
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:         ocamba-hood
 * Domain Path:         /languages/
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2003-2023 Del Systems
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('WPINC')) {
    die;
}

if (!function_exists('WP_Filesystem')) {
    require_once ABSPATH . '/wp-admin/includes/file.php';
    WP_Filesystem();
}

if (!class_exists('Ocamba_Hood')) {
    /**
     * A class definition that includes attributes and functions used across both the
     * public-facing side of the site and the admin area.
     *
     * @link              https://delsystems.net
     * @author            Del Systems
     * @copyright         2024 Del Systems
     * @license           GPL-2.0-or-later
     * @version           1.0.4
     */
    class Ocamba_Hood
    {
        private $plugin_version = '1.0.4';
        private $domain;

        private $msg_sw_error;
        private $tpl_code_key = '!function(h,oo,d,y,t,a,g){h[y]=t[y]||function(){(h[y].q=h[y].q||[]).push(arguments)},h[y].l=1*new Date,a=oo.createElement(d),g=oo.getElementsByTagName(d)[0],a.async=1,a.crossOrigin="anonymous",a.src=t,g.parentNode.insertBefore(a,g)}(window,document,"script","Hood","https://sdk.ocmhood.com/sdk/ht.js?tag={{CODE_KEY}}");';
        private $tpl_sw = 'importScripts("https://cdn.ocmhood.com/sdk/osw.js");';

        private $plugin_directory;

        /**
         * Constructor for the class.
         *
         * Initializes the class by setting the domain and plugin directory,
         * adding various actions and filters.
         *
         * @return void
         */
        public function __construct()
        {

            $urlparts = wp_parse_url(home_url());
            $this->domain = $urlparts['host'];
            $this->plugin_directory = plugin_dir_url(__FILE__);

            add_action('plugins_loaded', [$this, 'ocamba_hood_plugin_load_textdomain'], 100, 0);
            add_action('admin_menu', [$this, 'admin_menu']);
            add_action('wp_enqueue_scripts', [$this, 'ocamba_code_key'], 0);
            add_action('admin_enqueue_scripts', [$this, 'admin_scripts_and_styles'], 100, 0);

            if (wp_doing_ajax()) {

                add_action('wp_ajax_admin_code_key_subbmision', [$this, 'admin_code_key_subbmision'], 100, 0);
                add_action('wp_ajax_admin_activate_deativate_code_key_submision', [$this, 'admin_activate_deativate_code_key_submision'], 100, 0);

            }
            add_filter('script_loader_code_key', [$this, 'wpdocs_load_module'], 10, 2);


        }
        /**
         * Replaces the opening `<script` tag of the given `$code_key` with `<script type="module"`, if the `$handle` is 'ocamba-admin-script'.
         *
         * @param string $code_key The script tag to be modified.
         * @param string $handle The handle of the script tag.
         * @return string The modified script tag.
         */
        public function wpdocs_load_module($code_key, $handle)
        {
            if ('ocamba-admin-script' === $handle) {
                $code_key = str_replace('<script ', '<script type="module" ', $code_key);
            }
            return $code_key;
        }
        /**
         * Loads the text domain for the Ocamba_Hood plugin.
         *
         * This function calls the `load_plugin_textdomain` function to load the text domain
         * for the Ocamba_Hood plugin. The text domain is used for internationalization
         * and localization of the plugin's strings.
         *
         * @return void
         */
        public function ocamba_hood_plugin_load_textdomain()
        {
            load_plugin_textdomain('ocamba-hood', false, dirname(plugin_basename(__FILE__)) . '/languages/');
        }
        /**
         * Handles the submission of the admin Code Key.
         *
         * This function checks if the nonce is valid and if the current user has the necessary permissions.
         * It also checks if the 'Code Key' field is set and not empty. If the 'Code Key' field is set and not empty,
         * it calls the `save_settings` method with the 'Code Key' value. Otherwise, it sends a JSON error
         * response with a message, state, and status code.
         *
         * @throws None
         * @return void
         */
        public function admin_code_key_subbmision()
        {
            if (!isset($_POST['ocamba_hood_options_verify']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ocamba_hood_options_verify'])), 'ocamba_hood_options_verify')) {
                wp_send_json_error('invalid nonce');
            }

            if (!current_user_can('manage_options')) {
                wp_send_json_error('invalid permissions');
            }

            if (isset($_POST['code_key']) && !empty($_POST['code_key'])) {
                $code_key = isset($_POST['code_key']) ? sanitize_text_field($_POST['code_key']) : '';
                $this->save_settings($code_key);
            } else {
                wp_send_json_error(
                    [
                        'message' => esc_html(__('Code Key is empty! Please enter valid Code Key.', 'ocamba-hood')),
                        'state' => 'error',
                        'status_code' => 400,
                    ],
                    400
                );
            }

        }
        /**
         * Handles the submission of Code Key inside of wordpress the admin .
         *
         * This function checks if the nonce is valid and if the current user has the necessary permissions.
         * It also checks if the 'code_key_active' field is set and not empty. If the 'code_key_active' field is set and not empty,
         * it calls the `save_settings` method with the 'code_key_active' value.
         *
         * @throws None
         * @return void
         */
        public function admin_activate_deativate_code_key_submision()
        {

            if (!isset($_POST['ocamba_hood_options_verify_activate_deactivate']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ocamba_hood_options_verify_activate_deactivate'])), 'ocamba_hood_options_verify_activate_deactivate')) {
                wp_send_json_error('invalid nonce');
            }

            if (!current_user_can('manage_options')) {
                wp_send_json_error('invalid permissions');
            }

            if (isset($_POST['code_key_active']) && !empty($_POST['code_key_active'])) {
                $code_key_active = isset($_POST['code_key_active']) ? sanitize_text_field($_POST['code_key_active']) : '';
                $this->save_settings($code_key_active, true);
            }

        }
        /**
         * Enqueues admin scripts and styles for the 'toplevel_page_ocamba-menu' screen.
         *
         * This function checks if the current screen is the 'toplevel_page_ocamba-menu' screen and if so,
         * it registers and enqueues the necessary scripts and styles.
         *
         * @return void
         */
        public function admin_scripts_and_styles()
        {
            $currentScreen = get_current_screen();

            if (is_object($currentScreen) && $currentScreen->id === 'toplevel_page_ocamba-menu') {

                wp_register_script('ocamba-admin-script', $this->plugin_directory . '/assets/js/admin-app.js', [], $this->plugin_version, ['strategy' => 'defer']);
                wp_enqueue_script('ocamba-admin-script');

                wp_register_style('ocamba-admin-styles', $this->plugin_directory . '/assets/css/admin-app.css', [], $this->plugin_version, 'all');
                wp_enqueue_style('ocamba-admin-styles');

                wp_register_script('ocamba-hood-localization', '', [], $this->plugin_version, false);
                wp_enqueue_script('ocamba-hood-localization');

                wp_add_inline_script('ocamba-hood-localization', 'const ocambaI18n = ' . wp_json_encode(
                    array(
                        "emptyCodeKey" => esc_html(__('Code Key is empty! Please enter valid Code Key.', 'ocamba-hood')),
                        "notValidCodeKeyLength" => esc_html(__('Code Key need to be 32 up to 44 character length! Please enter valid Code Key.', 'ocamba-hood')),
                        "dismisThisNotice" => esc_html(__('Dismiss this notice.', 'ocamba-hood')),
                        "CodeKeyDidNotChaged" => esc_html(__('Code Key did not changed', 'ocamba-hood')),
                    )
                ), 'after');

            }
        }
        /**
         * Retrieves the settings for the 'ocamba_hood' plugin.
         *
         * This function retrieves the settings for the 'ocamba_hood' plugin from the WordPress options table.
         * If the settings do not exist, it returns an array with a default value for the 'Code Key' key.
         *
         * @return array The settings for the 'ocamba_hood' plugin.
         */
        private function get_settings()
        {
            $default_settings = array('code_key' => '');
            $settings = array_map('sanitize_text_field', get_option('ocamba_hood_settings', array()));

            return wp_parse_args($settings, $default_settings);
        }
        /**
         * Generate the Ocamba Hood Code Key.
         *
         * This function retrieves the settings for the 'ocamba_hood' plugin from the WordPress options table.
         * If the 'code_key_active' setting is set to 'false', the function returns early.
         * If the 'code_key' setting is not set or empty, the function returns early.
         *
         * The function then sanitizes the 'code_key' setting and assigns it to the 'Code Key' key in the $macros array.
         * It generates the code_key using the 'tpl' method with the 'tpl_code_key' and $macros as arguments.
         * The generated code_key is then sanitized using 'wp_kses_post' and assigned to the $code_key variable.
         *
         * The function registers and enqueues the 'ocamba-hood' script with an empty source and the current plugin version.
         * It adds the $code_key as inline script to the 'ocamba-hood' script.
         *
         * @return void
         */
        public function ocamba_code_key()
        {
            $settings = $this->get_settings();

            if (isset($settings["code_key_active"]) && $settings["code_key_active"] == 'false') {
                return;
            }

            if (!isset($settings["code_key"]) || $settings["code_key"] == '') {
                return;
            }

            $macros["CODE_KEY"] = sanitize_text_field($settings["code_key"]);

            $code_key = wp_kses_post($this->tpl(sanitize_text_field($this->tpl_code_key), $macros));

            wp_register_script('ocamba-hood', '', [], $this->plugin_version, false);
            wp_enqueue_script('ocamba-hood');


            wp_add_inline_script('ocamba-hood', wp_kses_post($code_key));

        }
        /**
         * Adds the admin menu for the plugin.
         *
         * This function adds a menu page to the WordPress admin menu with the title "Ocamba" and the slug "ocamba-menu".
         * The menu page is accessible to users with the "manage_options" capability. It also adds a submenu page titled "Hood Configuration"
         * with the same slug and accessible to users with the same capability. The submenu page is linked to the "settings" method of the current instance.
         *
         * @return void
         */
        public function admin_menu()
        {
            $menu_slug = 'ocamba-menu';

            if (empty($GLOBALS['admin_page_hooks'][$menu_slug])) {
                add_menu_page(
                    'Ocamba',
                    'Ocamba',
                    'manage_options',
                    $menu_slug,
                    false,
                    esc_html('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTI4IiBoZWlnaHQ9IjEyOCIgdmlld0JveD0iMCAwIDEyOCAxMjgiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxwYXRoIGQ9Ik03Ni4wOTM0IDkuMzAxMDJlLTA1Qzc3LjU4MTggMi4zMTY4OSA3OC40NTQxIDUuMDc2MiA3OC40NTQxIDguMDQ1NzlDNzguNDU0MSAxNi4yMTU4IDcxLjkxMzMgMjIuODM3MSA2My44NDM4IDIyLjgzNzFDNTUuNzc0MyAyMi44MzcxIDQ5LjIzMzYgMTYuMjE1OCA0OS4yMzM2IDguMDQ1NzlDNDkuMjMzNiA1LjA3OTg1IDUwLjEwMzkgMi4zMjQyIDUxLjU4NjkgMC4wMDkyMzQzMUMzNS45MDUxIDMuMDk3NjggMjIuMjY5NSAxMS45NzM2IDEyLjk1MTEgMjQuMzY5NEMxMi40Nzc1IDI0Ljk5ODQgMTIuMDIyMiAyNS42NDIxIDExLjU3MjQgMjYuMjg5NEM5Ljc1NDc3IDI5LjE2MjEgOC42ODg3MiAzMi41Njg3IDguNjg4NzIgMzYuMjMzMkM4LjY4ODcyIDQ2LjQ0NTcgMTYuODY2MSA1NC43MjM2IDI2Ljk1MjUgNTQuNzIzNkMzMy4yMDI1IDU0LjcyMzYgMzguNjIyNCA1MS43Mzc2IDQxLjkxMzggNDYuODlDNDEuOTI2NiA0Ni44NzM2IDQxLjk0MTIgNDYuODU5IDQxLjk1MjIgNDYuODQ0M0M0Ny43MDMgMzYuNTc1MSA1OC41NzU3IDI5LjYzMzkgNzEuMDY2NiAyOS42MzM5Qzc0LjkwMTIgMjkuNjMzOSA3OC41NjkzIDMwLjMxNzcgODEuOTk3OCAzMS41MTkxQzg2LjY3NzEgMjMuMDM2NCA5NS42MDYgMTcuMjkxIDEwNS44ODYgMTcuMjkxQzEwNi44NDEgMTcuMjkxIDEwNy43ODQgMTcuMzQwNCAxMDguNzEzIDE3LjQzNzNDOTkuOTM5NyA4LjY3ODQ4IDg4LjY3MzkgMi40NzU5NyA3Ni4wOTM0IDkuMzAxMDJlLTA1WiIgZmlsbD0iIzQyOEZDNCIvPgo8cGF0aCBkPSJNMTExLjI5MiAxMDYuMDQxQzEwMy4yMjMgMTA2LjA0MSA5Ni42ODIxIDk5LjQxODIgOTYuNjgyMSA5MS4yNDgxQzk2LjY4MjEgODMuMDc4MSAxMDMuMjIzIDc2LjQ1NjkgMTExLjI5MiA3Ni40NTY5QzExNy4wOTMgNzYuNDU2OSAxMjIuMDkgNzkuODg3MyAxMjQuNDQ5IDg0Ljg0NjNDMTI5Ljc0NiA2OS40ODA5IDEyOC45OTMgNTIuOTc5OSAxMjMuMDEzIDM4LjUzMDVDMTIyLjcxMiAzNy44MDA5IDEyMi4zOSAzNy4wODA1IDEyMi4wNjMgMzYuMzYxOUMxMjAuNTEyIDMzLjMzMTkgMTE4LjEzMSAzMC42OTMzIDExNC45OTUgMjguODYxMUMxMDYuMjYyIDIzLjc1NTcgOTUuMDkxMyAyNi43ODU2IDkwLjA0ODEgMzUuNjI4NkM4Ni45MjMxIDQxLjEwODggODYuNzY3NiA0Ny4zNTM0IDg5LjI2OTEgNTIuNjYzNUM4OS4yNzY0IDUyLjY4MTggODkuMjgwMSA1Mi43MDE5IDg5LjI4OTIgNTIuNzIwMkM5NS4xOTczIDYyLjg5NjIgOTUuNzAwMiA3NS45MDI4IDg5LjQ1MzggODYuODU0MUM4Ny41Mzc1IDkwLjIxNSA4NS4xMTgzIDkzLjA4NzcgODIuMzc1NCA5NS40OTQxQzg3LjI5MjQgMTAzLjgzOCA4Ny43NDIzIDExNC41MzkgODIuNjAyMiAxMjMuNTUzQzgyLjEyMzEgMTI0LjM5MSA4MS42MDkyIDEyNS4xOTIgODEuMDYyNSAxMjUuOTU4QzkyLjk3NzQgMTIyLjYzNCAxMDMuOTQ1IDExNS44MjggMTEyLjM2NCAxMDUuOTg2QzExMi4wMDkgMTA2LjAxMiAxMTEuNjU0IDEwNi4wNDEgMTExLjI5MiAxMDYuMDQxWiIgZmlsbD0iIzMxNkM5NCIvPgo8cGF0aCBkPSJNNzQuODQ3MSAxMDAuMjc4QzcxLjcyMjEgOTQuNzk3MyA2Ni40NTk0IDkxLjUzODggNjAuNjY0NyA5MS4wNzYyQzYwLjY0NDYgOTEuMDc0NCA2MC42MjYzIDkxLjA2ODkgNjAuNjA2MiA5MS4wNjUyQzQ4Ljk0NzIgOTEuMTU4NSAzNy41NzE3IDg1LjA5NDkgMzEuMzI1MyA3NC4xNDM2QzI5LjQwOSA3MC43ODI3IDI4LjE2MTkgNjcuMjI0MyAyNy40NzQ0IDYzLjYxNjZDMTcuODc4MSA2My43NTU2IDguNDk5MzMgNTguNzk4MyAzLjM1OTIyIDQ5Ljc4NTNDMi44ODE5NyA0OC45NDk2IDIuNDU0MDkgNDguMDk3NSAyLjA3MTkxIDQ3LjIzNDRDLTEuMDI1NjggNTkuMjczNyAtMC43MTExNjQgNzIuMjEwOSAzLjQyODcxIDg0LjQ0OTVDNS44NjA3MSA3OS43MDQzIDEwLjc0ODUgNzYuNDU4NiAxNi4zOTUxIDc2LjQ1ODZDMjQuNDYyNyA3Ni40NTg2IDMxLjAwNTMgODMuMDc5OSAzMS4wMDUzIDkxLjI0OTlDMzEuMDA1MyA5OS40MTgxIDI0LjQ2MjcgMTA2LjA0MSAxNi4zOTUxIDEwNi4wNDFDMTYuMTYxIDEwNi4wNDEgMTUuOTMyNSAxMDYuMDE3IDE1LjcwMjEgMTA2LjAwNkMyNi4xODUyIDExOC4yMjcgNDAuNjA3MiAxMjUuNzU5IDU1Ljg4MTIgMTI3LjczQzU2LjY1NjUgMTI3LjgyOSA1Ny40MzU1IDEyNy45MDcgNTguMjE0NCAxMjcuOTc5QzYxLjU4MDggMTI4LjEzNCA2NS4wMjc3IDEyNy4zNjYgNjguMTYxOCAxMjUuNTM2Qzc2Ljg5NjkgMTIwLjQzIDc5Ljg5MDMgMTA5LjEyIDc0Ljg0NzEgMTAwLjI3OFoiIGZpbGw9IiMxQzNDNTYiLz4KPC9zdmc+Cg==')
                );
            }

            add_submenu_page($menu_slug, 'Hood Configuration', 'Hood', 'manage_options', $menu_slug, array($this, 'settings'));
        }
        /**
         * Saves the settings for the Ocamba Hood plugin.
         *
         * @param string $code_key The Code Key to be saved.
         * @param bool $activate_deactivate_ponter (optional) Whether to activate or deactivate the ponter. Default is false.
         * @throws \Throwable If an error occurs during the activation or deactivation process.
         * @return void
         */
        private function save_settings($code_key, $activate_deactivate_ponter = false)
        {

            if ($activate_deactivate_ponter) {
                try {
                    $settings = array_map('sanitize_text_field', get_option('ocamba_hood_settings', array()));
                    if (isset($settings['code_key_active'])) {
                        $settings["code_key_active"] = sanitize_text_field($code_key);
                    } else if (empty($settings)) {
                        $settings = ['code_key_active' => sanitize_text_field($code_key)];
                    }

                    update_option('ocamba_hood_settings', array_map('sanitize_text_field', $settings));

                    wp_send_json_success(
                        [
                            'message' => $code_key === 'true' ?
                                esc_html(__('Code Key activated successfully! Ocamba Hood Code Key snippet is added to your website.', 'ocamba-hood')) :
                                esc_html(__('Code Key deactivated successfully! Ocamba Hood Code Key snippet will be removed from your website.', 'ocamba-hood')),
                            'state' => $code_key === 'true' ? true : false,
                            'status' => 200,
                        ],
                        200
                    );
                } catch (\Throwable $th) {
                    wp_send_json_error(
                        [
                            'message' => esc_html($th->getMessage()),
                            'state' => 'error',
                            'status_code' => 400,
                        ],
                        400
                    );
                }
            } else {

                global $wp_filesystem;

                $response = wp_remote_get('https://cdn.ocmtag.com/tag/' . $code_key . '.json');
                $http_code = wp_remote_retrieve_response_code($response);

                if ($http_code != "200") {
                    wp_send_json_error(
                        [
                            'message' => esc_html(__('Code Key not found! Please enter valid Code Key.', 'ocamba-hood')),
                            'state' => 'error',
                            'status_code' => $http_code,
                        ],
                        $http_code
                    );
                }

                $sw = 'sw.js';
                $content = file_exists(ABSPATH . $sw) ? file_get_contents(ABSPATH . $sw) : '';
                $this->msg_sw_error = '<span>' . esc_html(__('The file sw.js in the root directory of Wordpress is not writable. Please change its permissions and try again. Otherwise replace its contents manually:', 'ocamba-hood')) . '<pre class="dif m0"><code><b>{{SW}}</b></code></pre>' . esc_html(__('Also make sure that the file is accessible at', 'ocamba-hood')) . '<pre class="dif m0"><code><b>{{DOMAIN}}/sw.js</b></code></pre></span>';
                try {
                    if (strpos($content, $this->tpl_sw) === false) {

                        $errorMesage = esc_html($this->tpl($this->msg_sw_error, ["SW" => $sw, "DOMAIN" => $this->domain]));

                        if (!$wp_filesystem->is_writable(ABSPATH) || ($wp_filesystem->exists(ABSPATH . $sw) && !$wp_filesystem->is_writable(ABSPATH . $sw))) {
                            wp_send_json_error(
                                [
                                    'message' => $errorMesage,
                                    'state' => 'error',
                                    'status_code' => 403,
                                ],
                                403
                            );
                        }
                        $content = $this->tpl_sw . PHP_EOL . $content;
                        if ($wp_filesystem->put_contents(ABSPATH . $sw, $content, FS_CHMOD_FILE) === false) {
                            wp_send_json_error(
                                [
                                    'message' => $errorMesage,
                                    'state' => 'error',
                                    'status_code' => 403,
                                ],
                                403
                            );
                        }
                    }
                } catch (\Throwable $th) {
                    wp_send_json_success(
                        [
                            'message' => esc_html($th->getMessage()),
                            'state' => 'success',
                            'status' => 200,
                        ],
                        200
                    );
                }

                $settings = array_map('sanitize_text_field', get_option('ocamba_hood_settings', array()));
                $settings["code_key"] = sanitize_text_field($code_key);
                update_option('ocamba_hood_settings', array_map('sanitize_text_field', $settings));
                wp_send_json_success(
                    [
                        'message' => esc_html(__('Settings successfully updated.', 'ocamba-hood')),
                        'state' => 'success',
                        'status' => 200,
                    ],
                    200
                );
            }

        }
        /**
         * Retrieves the plugin settings and sanitizes the code_key and code_key_active values.
         * Includes the admin template file.
         *
         * @return void
         */
        public function settings()
        {
            $settings = array_map('sanitize_text_field', $this->get_settings());

            $macros["CODE_KEY"] = sanitize_text_field($settings['code_key']);
            $macros["CODE_KEY_ACTIVE"] = sanitize_text_field($settings['code_key_active']);

            include_once plugin_dir_path(__FILE__) . '/templates/admin-template.php';
        }
        /**
         * Replaces placeholders in the given content with sanitized values from the macros array.
         *
         * @param string $content The content with placeholders to be replaced.
         * @param array $macros An associative array of placeholders and their corresponding values.
         * @return string The content with placeholders replaced by sanitized values.
         */
        private function tpl($content, $macros)
        {
            foreach ($macros as $key => $value) {
                $content = str_replace("{{" . $key . "}}", sanitize_text_field($value), $content);
            }
            $content = wp_kses_post(preg_replace('/({{\w+}})/', "", $content));
            return $content;
        }
        /**
         * Replaces placeholders in the given content with sanitized values from the macros array.
         *
         * @param string $content The content with placeholders to be replaced.
         * @param array $macros An associative array of placeholders and their corresponding values.
         * @return string The content with placeholders replaced by sanitized values.
         */
        public static function activate()
        {
            $settings = array_map('sanitize_text_field', get_option('ocamba_hood_settings', array()));

            if (empty($settings)) {
                $settings["code_key_active"] = 'true';
            }

            update_option('ocamba_hood_settings', array_map('sanitize_text_field', $settings));
        }
        /**
         * Deactivates the plugin silently.
         *
         * This function does not perform any actions or clean up operations during deactivation.
         * It simply exists without any output or side effects.
         *
         * @return void
         */
        public static function deactivate()
        {
            //silence is golden
        }
        /**
         * Uninstalls the plugin by deleting the 'sw.js' file if it exists and removing the 'ocamba_hood_settings' option.
         *
         * @global WP_Filesystem_Base $wp_filesystem The WordPress filesystem object.
         * @throws \Throwable If an error occurs during file deletion.
         * @return void
         */
        public static function uninstall()
        {
            global $wp_filesystem;

            $file_path = ABSPATH . 'sw.js';

            if ($wp_filesystem->exists($file_path)) {
                try {
                    $wp_filesystem->delete($file_path);
                } catch (\Throwable $th) {
                    wp_send_json_error(
                        [
                            'message' => esc_html($th->getMessage()),
                            'state' => 'error',
                            'status_code' => 400,
                        ],
                        400
                    );
                }
            }
            delete_option('ocamba_hood_settings');
        }
    }
}
/**
 * Initializes the Ocamba_Hood class and registers hooks for activation, deactivation, and uninstallation.
 *
 * @throws \Throwable If an error occurs while initializing the Ocamba_Hood class.
 * @return void
 */
function initOcambaHood()
{
    if (class_exists('Ocamba_Hood')) {

        register_activation_hook(__FILE__, ['Ocamba_Hood', 'activate']);

        register_deactivation_hook(__FILE__, ['Ocamba_Hood', 'deactivate']);

        register_uninstall_hook(__FILE__, ['Ocamba_Hood', 'uninstall']);

        try {
            new Ocamba_Hood();
        } catch (\Throwable $th) {
            echo esc_html($th->getMessage());
        }

    }
}

initOcambaHood();