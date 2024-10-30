<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Ma_Visioconference{

  private $_api = null;

  function set_settings_menu($data)
  {
    update_option("ma-visioconference_apitoken", $_POST["ma_visioconference_apitoken"]);
  }

  function get_api() {
    if (is_null($this->_api))
      $this->_api = new Ma_Visioconference_API(get_option("ma-visioconference_apitoken"));
    return $this->_api;
  }

  function _show_header()
  {
    $user = $this->get_api()->get_user_details();
    echo '
<div class="wrap">
<h2><div style="float:left;margin-right: 10px;"><img src="'.plugins_url('../images/icon.png', __FILE__).'"></div>
Ma-Visioconference</h2>
';
    if ($user[0] == '-')
      echo '<h4 style="color: red;line-height: 34px;">'.__("API Token is wrong or not set.", 'ma-visioconference')." ".__("You must create an account on", 'ma-visioconference').' <a href="https://ma-visioconference.diva-cloud.com/" target="_blank">Ma-Visioconference.fr</a> '.__("and generate your API Token via the profile section", 'ma-visioconference').'</h4>';
    else
      echo '
<h4 style="color: #04B404;line-height: 34px;"><div class="icon16 icon-users"></div><span style="padding-right: 10px;float:left;">'.$user[0].'</span><div class="icon16 icon-dashboard"></div><span style="padding-right: 10px;">'.$user[1].' credits</span></h4>
';
  }

  function _show_footer()
  {
    echo '
</div>
';
  }

  function show_settings_menu()
  {
    if (!empty($_POST['ma_visioconference_apitoken']))
      $this->set_settings_menu($_POST);
    $this->_show_header();
    echo '
<form method="post" action="admin.php?page=wp_ma-visioconference_options_top_menu&action=save_options">

<h3>Plugin Ma-Visioconference</h3>
<table class="form-table"><tbody></tbody>

<tr valign="top">
<th scope="row"><label for="ma_visioconference_apitoken">'.__('API Token').'</label></th>
      <td><input name="ma_visioconference_apitoken" type="text" id="ma_visioconference_apitoken" value="'.get_option("ma-visioconference_apitoken").'" class="regular-text code"></td>
      </tr>
</table>
<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="'.__("Save options", 'ma-visioconference').'"></p>
</form>
';
    $this->_show_footer();
  }


  function show_list_menu()
  {
    $this->_show_header();
    echo '
<iframe style="width: 100%;height: 1200px;" src="https://ma-visioconference.diva-cloud.com/?apitoken='.get_option("ma-visioconference_apitoken").'#listMeets"></iframe>
';
    $this->_show_footer();
  }

  function show_schedule_menu()
  {
    $this->_show_header();
    echo '
<iframe style="width: 100%;height: 1200px;" src="https://ma-visioconference.diva-cloud.com/?apitoken='.get_option("ma-visioconference_apitoken").'#addFMeet"></iframe>
';
    $this->_show_footer();
  }

  function show_credit_menu()
  {
    $this->_show_header();
    echo '
<iframe style="width: 100%;height: 1200px;" src="https://ma-visioconference.diva-cloud.com/?apitoken='.get_option("ma-visioconference_apitoken").'#prepaidCredit"></iframe>
';
    $this->_show_footer();
  }

  /*
   * Add admin button
   */
  function admin_menu() {
    add_menu_page(
		  __('Manage your ma-visioconference account', 'ma-visioconference'),
		  'Visioconference',
		  'manage_options',
		  'wp_ma-visioconference_options_top_menu',
		  array($this, 'show_settings_menu'),
		  plugins_url('../images/icon.png', __FILE__));
    if (!function_exists('add_submenu_page')) return;
    add_submenu_page('wp_ma-visioconference_options_top_menu', __('Change your ma-visioconference settings', 'ma-visioconference'), __('Settings', 'ma-visioconference'), 'manage_options', 'wp_ma-visioconference_options_top_menu', array($this, 'show_settings_menu'));
    add_submenu_page('wp_ma-visioconference_options_top_menu', __('List your meetings', 'ma-visioconference'), __('List', 'ma-visioconference'), 'manage_options', 'wp_ma-visioconference_options_list_menu', array($this, 'show_list_menu'));
    add_submenu_page('wp_ma-visioconference_options_top_menu', __('Schedule a meeting', 'ma-visioconference'), __('Schedule', 'ma-visioconference'), 'manage_options', 'wp_ma-visioconference_options_schedule_menu', array($this, 'show_schedule_menu'));
    add_submenu_page('wp_ma-visioconference_options_top_menu', __('Purchase credits', 'ma-visioconference'), __('Purchase', 'ma-visioconference'), 'manage_options', 'wp_ma-visioconference_options_credit_menu', array($this, 'show_credit_menu'));

  }

  /*
   * Register shortcodes for MCE
   */
  function register_shortcodes() {
    add_shortcode('Ma-Visioconference-Link', array($this, 'shortcode_link'));
    add_shortcode('Ma-Visioconference-IFrame', array($this, 'shortcode_iframe'));
  }

  /*
   * [Ma-Visioconference-IFrame border="0" width="100%" height="100%"]
   */
  function shortcode_iframe($atts) {
    extract(shortcode_atts(array(
				 'height' => '100%',
				 'width' => '1200px',
				 'border' => '0',
				 ), $atts));
    $url = $this->get_api()->get_last_meeting_public();
    if ($url != '')
      return '<iframe style="height:'.$height.';width:'.$width.';border:'.$border.';" src="'.$url.'?embed=1"></iframe>';
    return __("There is no public meeting scheduled");
  }

  /*
   * [Ma-Visioconference-Link text="test"]
   */
  function shortcode_link($atts) {
    extract(shortcode_atts(array(
				 'text' => __("Join the meeting"),
				 ), $atts));
    $api = new Ma_Visioconference_API(get_option("ma-visioconference_apitoken"));
    $url = $api->get_last_meeting_public();
    if ($url != '')
      return '<a href="'.$url.'" alt="'.$text.'" title="'.$text.'">'.$text.'</a>';
    return __("There is no public meeting scheduled");
  }

  /*
   * Add js for button in MCE
   */
  function register_mce_js($plugin_array)
  {
    $plugin_array['ma_visioconference_mce_button'] = plugins_url('../js/mce-button.js', __FILE__);
    return $plugin_array;
  }

  /*
   * Add button in MCE
   */
  function register_mce_button( $buttons ) {
    array_push( $buttons, 'ma_visioconference_mce_button_link' );
    array_push( $buttons, 'ma_visioconference_mce_button_iframe' );
    return $buttons;
  }

  /*
   * Register MCE
   */
  function register_mce() {
    if ($this->is_mce_capable()){
      add_filter( 'mce_external_plugins', array($this, 'register_mce_js'));
      add_filter( 'mce_buttons', array($this, 'register_mce_button'));
    }
  }

  /*
   * Is MCE capable (user can edit and rich editing enabled)
   */
  function is_mce_capable() {
    // check user permissions
    if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) )
      return false;
    // check if WYSIWYG is enabled
    if ( 'true' == get_user_option( 'rich_editing' ) )
      return true;
    return false;
  }

  /*
   * Manage Translation files
   */
  function load_translation_files() {
    load_plugin_textdomain('ma-visioconference', false, dirname( plugin_basename( __FILE__ ) ).'/../languages');
  }

  /*
   * Add icon for mce
   */
  function admin_head() {
    if ($this->is_mce_capable())
      echo '
<!-- Ma-Visioconference MCE Shortcode Plugin -->
<script type="text/javascript">
   var tinymceMaVisioconferenceIconLink = "'.plugins_url('../images/iconLink.png', __FILE__).'";
   var tinymceMaVisioconferenceIconIFrame = "'.plugins_url('../images/iconIFrame.png', __FILE__).'";
</script>
<!-- Ma-Visioconference MCE Shortcode Plugin -->
';
  }
}

