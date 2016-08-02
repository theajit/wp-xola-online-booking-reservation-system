<?php
/*
  Plugin Name: Xola Booking & Reservation System
  Description: Add Xola Booking Software to your WordPress site
  Version: 1.1
  Author: Xola.com
  Author URI: http://www.xola.com
 */
if (!class_exists('Xolaecommerce')) {

    class Xolaecommerce {
        /*         * ************ VERSION NUMBER ********** */

        const CURRENT_VERSION = '2.0';
        /*         * ******************************** */
        const TEXT_DOMAIN = 'xola';

        /*         * ************** INSTALL SCRIPT ******************************* */

        function __construct() {
            global $wpdb, $wp_version;
            register_activation_hook(__FILE__, array($this, 'get_xola_user_data'));
            add_action('admin_init', array($this, 'xola_plugin_redirect'));
            add_action('admin_init', array($this, 'xola_check_if_email'));
            add_action('admin_menu', array($this, 'xola_menu'));
            add_action('init', array($this, 'xola_forms_action'), 1);
            if (get_option('xola_user_email')) {
                add_action('admin_init', array($this, 'action_admin_init'));
                add_action('admin_enqueue_scripts', array($this, 'load_admin_popup'));
                add_action('admin_bar_menu', array($this, "xola_adminbar_links"), 800);
            }
            add_shortcode('xola-button', array($this, 'xola_shortcode'));
            add_action('admin_enqueue_scripts', array($this, 'copy_enqueue'));
        }

        /*         * ***************** ADD MENU IN ADMIN BAR ***************************** */

        function add_root_xola_menu($name, $id, $href) {
            global $wp_admin_bar;
            if (!is_super_admin() || !is_admin_bar_showing())
                return;
            $wp_admin_bar->add_menu(array(
                'id' => $id,
                'meta' => array('class' => 'xola_menu'),
                'title' => $name,
                'href' => $href));
        }

        /*         * ***************** ADD SUB MENU IN ADMIN BAR ***************************** */

        function add_xola_sub_menu($name, $link, $root_menu, $id, $meta = FALSE) {
            global $wp_admin_bar;
            if (!is_super_admin() || !is_admin_bar_showing())
                return;

            $wp_admin_bar->add_menu(array(
                'parent' => $root_menu,
                'id' => $id,
                'title' => $name,
                'href' => $link,
                'meta' => $meta
            ));
        }

        /*         * ******************************* MENUES FOR XOLA ************************************ */

        function xola_adminbar_links() {
            $this->add_root_xola_menu("Xola", "xola", admin_url('admin.php?page=xola'));
            $this->add_xola_sub_menu('Account Sync', admin_url('admin.php?page=xola'), "xola", 'xola_page');
            $this->add_xola_sub_menu('Button Settings', admin_url('admin.php?page=xola_settings'), "xola", 'xola_button');
            $this->add_xola_sub_menu('Tutorial & Tips', admin_url('admin.php?page=xola_tutorial'), "xola", 'xola_tutorial');
        }

        /*         * ************* Add button in Editor ************************** */

        function action_admin_init() {
            if (current_user_can('edit_posts') && current_user_can('edit_pages')) {
                add_filter('mce_buttons', array($this, 'register_xola_button'));
                add_filter('mce_external_plugins', array($this, 'add_xola_tinymce_plugin'));
            }
        }

        function register_xola_button($buttons) {
            array_push($buttons, "|", "xola");
            return $buttons;
        }

        function add_xola_tinymce_plugin($plugins) {

            $plugin_array['xola'] = plugin_dir_url(__FILE__) . 'js/xola.js';
            return $plugin_array;
        }

        function copy_enqueue() {
            wp_enqueue_script( 'my_custom_script', plugin_dir_url(__FILE__) . 'js/copy.js' );
        }



        /*         * ******************** LOAD JAVASCRIPTS *************** */

        function load_admin_popup() {
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
            wp_enqueue_style('thickbox');
        }

        /*         * ******** plugin activation ***************** */

        public function get_xola_user_data() {
            add_option('xola_do_activation_redirect', true);
            add_option('xola_button_type', 'default');
        }

        /*         * ************ redirect to login page when activate ***************** */

        public function xola_plugin_redirect() {
            if (get_option('xola_do_activation_redirect', false)) {
                delete_option('xola_do_activation_redirect');
                wp_redirect("admin.php?page=xola");
                exit;
            }
        }

        /*         * ************* Add menu ************************* */

        public function xola_menu() {
            add_menu_page('Xola Login', 'Xola', 'manage_options', 'xola', array($this, 'xola_settings'), plugin_dir_url(__FILE__) . 'xola.png');
            add_submenu_page('xola', 'Xola', 'Account Sync', 'administrator', 'xola', array($this, 'xola_settings'));
            add_submenu_page('xola', 'Button Styling', 'Button Settings', 'administrator', 'xola_settings', array($this, 'xola_buttons'));
            add_submenu_page('xola', 'Tutorial', 'Tutorial & Tips', 'administrator', 'xola_tutorial', array($this, 'xola_tutorial'));
        }

        /*         * ****************** XOLA BUTTON *************************** */

        public function xola_buttons() {
            wp_enqueue_style('xola-css', plugin_dir_url(__FILE__) . 'css/xola.css');
            include_once( dirname(__FILE__) . '/lib/xolabutton.php' );
        }

        /*         * ************************************* XOLA TUTORIAL ***************** */

        public function xola_tutorial() {
            ?>
            <div class="wrap">
                <div id="xola_step_2">
                    <h2 class="xola_btn_hd"><?php _e('Tutorial Video', self::TEXT_DOMAIN); ?></h2>
                    <div class="xola_video">
                        <iframe src="http://player.vimeo.com/video/95507240?title=0&amp;byline=0&amp;portrait=0" width="660" height="380" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                    </div>
                </div>
            </div>

            <?php
        }

        /*         * ************************ XOLA LOGIN PAGE **************** */

        public function xola_settings() {
            wp_enqueue_style('xola-css', plugin_dir_url(__FILE__) . 'css/xola.css');
            include_once( dirname(__FILE__) . '/lib/xolaapi.php' );
        }

        /*         * ************* XOLA form actions ************************ */

        public function xola_forms_action() {
            if (isset($_POST['enable_xola_submitted']) && wp_verify_nonce($_POST['enable_xola_submitted'], 'enable_xola')) {
                $email = $_POST['xola_email'];
                $data_req = self::xola_api_data($email);
                if ($data_req['data']) {
                    ?>
                    <div class="updated settings-error" id="setting-error-settings_updated" style="clear:both"><p><strong><?php _e('Email Successfully Added', self::TEXT_DOMAIN); ?></strong></p></div>	
                    <?php
                    update_option('xola_user_email', $email);
                } else {
                    ?>
                    <div class="error settings-error" id="setting-error-settings_updated" style="clear:both"><p><strong><?php _e('Invalid User', self::TEXT_DOMAIN); ?></strong></p></div>	
                    <?php
                }
            }
            if (get_option('xola_user_email')) {
                add_action('wp_head', array($this, 'add_content_in_header'), 1);
                add_action('in_admin_header', array($this, 'add_xola_popup_in_header'), 1);
            }

            /*             * ******************************** Add type for xola button *********************** */
            if (isset($_POST['select_xola_button_submitted']) && wp_verify_nonce($_POST['select_xola_button_submitted'], 'select_xola_button')) {
                $type_button = $_POST['xola_button'];
                if ($type_button == 'custom') {
                    $html_btn = $_POST['xola_custom_html'];

                    update_option('xola_custom_html', $html_btn);
                }
                update_option('xola_button_type', $type_button);
                ?>
                <div class="updated settings-error" id="setting-error-settings_updated" style="clear:both"><p><strong><?php _e('Xola Settings Updated', self::TEXT_DOMAIN); ?></strong></p></div>	
                <?php
            }
        }

        /*         * ************************************************ XOLA API REQUEST ***************************** */

        public function xola_api_data($email) {
            $userAgent = 'Xola WP Integration on Seller Website/AnyBrowser';
            $url = 'https://xola.com/api/experiences?seller=' . urlencode($email);
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $data = curl_exec($ch);
            $data_req = json_decode($data, TRUE);
            curl_close($ch);
            return $data_req;
        }

        /*         * ******************************** XOLA CHECK IF EMAIL EXITS *********************** */

        public function xola_check_if_email() {
            if (!get_option('xola_user_email')) {
                add_action('admin_notices', array($this, 'valid_admin_notice'));
            }
        }

        /*         * ******************** Display xola message *********************************** */

        function valid_admin_notice() {
            ?>
            <div class="error">
                <p><?php _e('Please add your Xola email on the <a href="admin.php?page=xola">Xola Settings page</a>', 'my-text-domain'); ?></p>
            </div>
            <?php
        }

        /*         * *********************** ADD SCRIPT CODE IN HEADER *************************** */

        function add_content_in_header() {
            ?>
            <script type="text/javascript">
            // <![CDATA[
                (function () {
                    var co = document.createElement("script");
                    co.type = "text/javascript";
                    co.async = true;
                    co.src = "https://xola.com/checkout.js";
                    var s = document.getElementsByTagName("script")[0];
                    s.parentNode.insertBefore(co, s);
                })();
            // ]]>
            </script>

                <?php
            }

            /*             * ************************************* ADD POPUP FOR XOLA ******************** */

            function add_xola_popup_in_header() {
                wp_enqueue_style('xola-css', plugin_dir_url(__FILE__) . 'css/xola.css');
                ?>
            <div id="test_edit" style="display:none">
                <?php
                $email = get_option('xola_user_email');
                if ($email) {
                    $data_req = self::xola_api_data($email);
                    if ($data_req) {
                        echo '<div id="xola_step_1">';
                        echo '<div class="xola_select_tour">Select a listing to insert a book now button</div>';
                        echo '<ul id="image_xola_popup">';
                        foreach ($data_req['data'] as $data) {
                            echo '<li id="' . $data['id'] . '">';
                            if ($data['photo']['src']) {
                                echo '<img src="http://xola.com/' . $data['photo']['src'] . '" alt="' . $data['name'] . '"/>';
                            }
                            echo '<span>'. $data['name'] .'</span>';

                            echo '</li>';
                        }

                        echo '</ul>';
                        echo '</div>';
                        echo '<div class="clear_li" style="padding-top:35px"></div>';
                    }
                }
                ?>
            </div>
            <?php
        }

        /*         * ***************************************** XOLA SHORTCODE **************************** */

        public function xola_shortcode($atts) {
            extract(shortcode_atts(array(
                'id' => ''
                            ), $atts));
            $content = "";
            $email = get_option('xola_user_email');
            if ($email) {
                $data_req = self::xola_api_data($email);
                if ($data_req) {
                    foreach ($data_req['data'] as $data){
                        $seller = $data['seller']['id'];
                    }
                }
            }
            if (isset($id,$seller)) {
                $type_button = get_option('xola_button_type');
                $html_button = get_option('xola_custom_html');
                if ($type_button == 'custom') {
                    $content .= '<div class="xola-checkout xola-custom" data-seller="'. $seller .'" data-experience="' . $id . '" data-version="2">';
                    $content .= stripslashes($html_button);
                    $content .= '</div>';
                } else {
                    $content .= '<div class="xola-checkout" data-seller="'. $seller .'" data-experience="' . $id . '" data-version="2"></div>';
                }
            }
            
            return $content;
        }

    }

    $__xola_ecommerce = new Xolaecommerce();
} else {
    add_action('admin_notices', 'add_notice_in_wp_admin');
}

function add_notice_in_wp_admin() {
    echo '<div id="message" class="error fade"><p style="line-height: 150%">';
    _e('<strong>Xola E-commerce plugin </strong> already exits.  <a href="plugins.php">Deactivate Xola E-commerce</a>.', 'xola');
    echo '</p></div>';
}
?>