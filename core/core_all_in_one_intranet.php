<?php

class core_all_in_one_intranet {
	
	protected function __construct() {
		$this->add_actions();
	}
	
	// PRIVATE SITE
	
	public function aioi_template_redirect() {
		if (substr($_SERVER['REQUEST_URI'], 0, 16) == '/wp-activate.php') return;
		if (substr($_SERVER['REQUEST_URI'], 0, 11) == '/robots.txt') return;

		$options = $this->get_option_aioi();
		if (!$options['aioi_privatesite']) {
			return;
		}
		
		// We do want a private site
		if (!is_user_logged_in()) {
			auth_redirect();
		}
		else {
			if (is_multisite()) {
				$this->handle_private_loggedin_multisite($options);
			}
			else {
				// Bar access to users with no role
				$user = wp_get_current_user();
				if (!$user || !is_array($user->roles) || count($user->roles) == 0) {
					wp_logout();
					$output = '<p>'.esc_html('You attempted to login to the site, but you do not have any permissions. If you believe you should have access, please contact your administrator.').'</p>';
					wp_die($output);
				}
			}
		}
	}
	
	// Override to decide what to do for Multisite
	protected function handle_private_loggedin_multisite($options) {
	}
	
	// Handler for robots.txt - just disallow if private
	public function aioi_robots_txt($output, $public) {
		$options = $this->get_option_aioi();
		if ($options['aioi_privatesite']) {	
			return "Disallow: /\n";
		}
		return $output;
	}
	
	// Don't allow ping backs if private
	public function aioi_option_ping_sites($sites) {
		$options = $this->get_option_aioi();
		if ($options['aioi_privatesite']) {
			return '';
		}
		return $sites;
	}
	
	// LOGIN REDIRECT
	
	public function aioi_login_redirect($redirect_to, $requested_redirect_to, $user) {
		if (isset($user->user_login)) {
			$options = $this->get_option_aioi();
			if ($options['aioi_loginredirect'] != '' && admin_url() == $redirect_to) {
				return $options['aioi_loginredirect']; 
			}
		}
		return $redirect_to;
	}
	
	// AUTO-LOGOUT
	
	// Reset timer on login
	public function aioi_wp_login($username, $user) {
		try {
			if ($user->ID) {
				update_user_meta($user->ID, 'aioi_last_activity_time', time());
			}
		} catch (Exception $ex) {
		}
	}
	
	// Check whether user should be auto-logged out this time
	public function aioi_check_activity() {
		if (is_user_logged_in()) {
			$user_id = get_current_user_id();
			$last_activity_time = (int)get_user_meta($user_id, 'aioi_last_activity_time', true);
			$logout_time_in_sec = $this->get_autologout_time_in_seconds();
			if ($logout_time_in_sec > 0 && $last_activity_time + $logout_time_in_sec < time()) {
				$current_url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
				wp_logout();
				wp_redirect($current_url); // Should hit the Login wall if site is private
				exit;
			} else {
				update_user_meta($user_id, 'aioi_last_activity_time', time());
			}
		}
	}
	
	protected function get_autologout_time_in_seconds() {
		$options = $this->get_option_aioi();
		if ($options['aioi_autologout_time'] == 0) {
			return 0;
		}
		
		switch ($options['aioi_autologout_units']) {
			case 'days':
				return $options['aioi_autologout_time'] * 60 * 60 * 24;
				break;

			case 'hours':
				return $options['aioi_autologout_time'] * 60 * 60;
				break;
			
			case 'minutes':
			default:
				return $options['aioi_autologout_time'] * 60;
				break;
		}
	}
	
	
	// PUT SETTINGS MENU ON PLUGINS PAGE
	
	public function aioi_plugin_action_links( $links, $file ) {
		if ($file == $this->my_plugin_basename()) {
			$settings_link = '<a href="'.$this->get_settings_url().'">Settings</a>';
			array_unshift( $links, $settings_link );
		}
	
		return $links;
	}
	
	// ADMIN OPTIONS
	// *************
	
	protected function get_options_menuname() {
		return 'aioi_list_options';
	}
	
	protected function get_options_pagename() {
		return 'aioi_options';
	}
	
	protected function get_settings_url() {
		return is_multisite()
		? network_admin_url( 'settings.php?page='.$this->get_options_menuname() )
		: admin_url( 'options-general.php?page='.$this->get_options_menuname() );
	}
	
	// Add All-In-One Intranet to the Settings menu in admin panel
	public function aioi_admin_menu() {
		if (is_multisite()) {
			add_submenu_page( 'settings.php', 'All-In-One Intranet settings', 'All-In-One Intranet',
			'manage_network_options', $this->get_options_menuname(),
			array($this, 'aioi_options_do_page'));
		}
		else {
			add_options_page( 'All-In-One Intranet settings', 'All-In-One Intranet',
			'manage_options', $this->get_options_menuname(),
			array($this, 'aioi_options_do_page'));
		}
	}
	
	// Entry point of admin settings page
	public function aioi_options_do_page() {
		
		wp_enqueue_script( 'aioi_admin_js', $this->my_plugin_url().'js/aioi-admin.js', array('jquery') );
	
		$submit_page = is_multisite() ? 'edit.php?action='.$this->get_options_menuname() : 'options.php';
	
		if (is_multisite()) {
			$this->aioi_options_do_network_errors();
		}
		?>
			
		<h2>All-In-One Intranet setup</h2>
		
		<hr />
		<br />
		
		<form action="<?php echo $submit_page; ?>" method="post">
		
		<?php 
		settings_fields($this->get_options_pagename());
		$this->aioi_privacysection_text();
		$this->aioi_memberssection_text();
		$this->aioi_loginredirectsection_text();
		$this->aioi_autologoutsection_text();
		?>
		<p class="submit">
			<input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit">
		</p>
		
		</form>

		<?php
	}
	
	protected function aioi_privacysection_text() {
		$options = $this->get_option_aioi();
				
		echo "<h3>Privacy</h3>";
		
		echo "<input id='input_aioi_privatesite' name='".$this->get_options_name()."[aioi_privatesite]' type='checkbox' ".($options['aioi_privatesite'] ? 'checked' : '')." class='checkbox' />";
		echo '<label for="input_aioi_privatesite" class="checkbox plain">';
		echo 'Force site to be entirely private';
		echo '</label>';
		
		echo "<br />";
		
		if (is_multisite()) {
			echo "<input id='input_aioi_ms_requiremember' name='".$this->get_options_name()."[aioi_ms_requiremember]' type='checkbox' ".($options['aioi_ms_requiremember'] ? 'checked' : '')." class='checkbox' />";
			echo '<label for="input_aioi_ms_requiremember" class="checkbox plain">';
			echo 'Require logged-in users to be members of a sub-site to view it';
			echo '</label>';
			
			echo "<br />";
		}
		
		echo "<p>Note that your media uploads (e.g. photos) will still be accessible to anyone who knows their direct URLs.</p>";
		
		$this->display_registration_warning();
		echo "<br />";
	}
	
	protected function display_registration_warning() {
		if (get_option('users_can_register')) {
			echo '<p><b>Warning:</b> Your site is set so that &quot;Anyone can register&quot; themselves. ';
			echo '<a href="'
					.admin_url( 'options-general.php' )
					.'">Turn off here</a>';
			echo '</p>';
		}
	}
	
	// Override to deal with members of sub-sites in a multisite
	protected function aioi_memberssection_text() {
	}
	
	protected function aioi_loginredirectsection_text() {
		$options = $this->get_option_aioi();
	
		echo "<h3>Login Redirect</h3>";
	
		echo '<label for="input_aioi_loginredirect" class="textbox plain">';
		echo 'Redirect after login to URL: ';
		echo '</label>';
	
		echo "<input id='input_aioi_loginredirect' name='".$this->get_options_name()."[aioi_loginredirect]' type='input' value='".esc_attr($options['aioi_loginredirect'])."' size='60' />";
		
		echo "<br />";
		
		echo "<p>Effective when users login via /wp-login.php directly. Otherwise, they will be taken to the page they were trying to access before being required to login.</p>";
		
		echo "<br />";
		echo "<br />";
	}
	
	protected function aioi_autologoutsection_text() {
		$options = $this->get_option_aioi();
		
		echo "<h3>Auto Logout</h3>";
		
		echo '<label for="input_aioi_autologout_time" class="textbox plain">';
		echo 'Auto logout inactive users after ';
		echo '</label>';
		
		echo "<input id='input_aioi_autologout_time' name='".$this->get_options_name()."[aioi_autologout_time]' type='input' value='".esc_attr($options['aioi_autologout_time'] == 0 ? '' : $options['aioi_autologout_time'])."' size='10' />";
		
		echo "<select name='".$this->get_options_name()."[aioi_autologout_units]'>";
		echo $this->list_options(Array('minutes', 'hours', 'days'), $options['aioi_autologout_units']);
		echo "</select> (leave blank to turn off auto-logout)"; 

		echo "<br />";
	}

	protected function list_options($list, $current) {
		$output = '';
		foreach ($list as $opt) {
			$output .= '<option value="'.esc_attr($opt).'" '.($current == $opt ? 'selected="selected"' : '').'>'.esc_html($opt).'</option>';
		}
		return $output;
	}
	
	public function aioi_options_validate($input) {
		$newinput = Array();
		$newinput['aioi_version'] = $this->PLUGIN_VERSION;
		$newinput['aioi_privatesite'] = isset($input['aioi_privatesite']) ? (boolean)$input['aioi_privatesite'] : false;
		$newinput['aioi_ms_requiremember'] = isset($input['aioi_ms_requiremember']) ? (boolean)$input['aioi_ms_requiremember'] : false;
		
		$newinput['aioi_autologout_time'] = isset($input['aioi_autologout_time']) ? trim($input['aioi_autologout_time']) : '';
		if(!preg_match('/^[0-9]*$/i', $newinput['aioi_autologout_time'])) {
			add_settings_error(
			'aioi_autologout_time',
			'nan_texterror',
			self::get_error_string('aioi_autologout_time|nan_texterror'),
			'error'
			);
			$newinput['aioi_autologout_time'] = 0;
		}
		else {
			$newinput['aioi_autologout_time'] = intval($newinput['aioi_autologout_time']);
		}
		
		$newinput['aioi_autologout_units'] = isset($input['aioi_autologout_units']) ? $input['aioi_autologout_units'] : '';
		if (!in_array($newinput['aioi_autologout_units'], Array('minutes', 'hours', 'days'))) {
			$newinput['aioi_autologout_units'] = 'minutes';
		}
		
		$newinput['aioi_loginredirect'] = isset($input['aioi_loginredirect']) ? $input['aioi_loginredirect'] : '';
		
		return $newinput;
	}
	
	protected function get_error_string($fielderror) {
		$local_error_strings = Array(
				'aioi_autologout_time|nan_texterror' => 'Auto logout time should be blank or a whole number'
		);
		if (isset($local_error_strings[$fielderror])) {
			return $local_error_strings[$fielderror];
		}
		return 'Unspecified error';
	}
	
	public function aioi_save_network_options() {
		check_admin_referer( $this->get_options_pagename().'-options' );
	
		if (isset($_POST[$this->get_options_name()]) && is_array($_POST[$this->get_options_name()])) {
			$inoptions = $_POST[$this->get_options_name()];
			$outoptions = $this->aioi_options_validate($inoptions);
				
			$error_code = Array();
			$error_setting = Array();
			foreach (get_settings_errors() as $e) {
				if (is_array($e) && isset($e['code']) && isset($e['setting'])) {
					$error_code[] = $e['code'];
					$error_setting[] = $e['setting'];
				}
			}
	
			update_site_option($this->get_options_name(), $outoptions);
				
			// redirect to settings page in network
			wp_redirect(
			add_query_arg(
			array( 'page' => $this->get_options_menuname(),
			'updated' => true,
			'error_setting' => $error_setting,
			'error_code' => $error_code ),
			network_admin_url( 'admin.php' )
			)
			);
			exit;
		}
	}
	
	protected function aioi_options_do_network_errors() {
		if (isset($_REQUEST['updated']) && $_REQUEST['updated']) {
			?>
					<div id="setting-error-settings_updated" class="updated settings-error">
					<p>
					<strong>Settings saved</strong>
					</p>
					</div>
				<?php
			}
	
			if (isset($_REQUEST['error_setting']) && is_array($_REQUEST['error_setting'])
				&& isset($_REQUEST['error_code']) && is_array($_REQUEST['error_code'])) {
				$error_code = $_REQUEST['error_code'];
				$error_setting = $_REQUEST['error_setting'];
				if (count($error_code) > 0 && count($error_code) == count($error_setting)) {
					for ($i=0; $i<count($error_code) ; ++$i) {
						?>
					<div id="setting-error-settings_<?php echo $i; ?>" class="error settings-error">
					<p>
					<strong><?php echo htmlentities2($this->get_error_string($error_setting[$i].'|'.$error_code[$i])); ?></strong>
					</p>
					</div>
						<?php
				}
			}
		}
	}
	
	// OPTIONS
	
	protected function get_default_options() {
		return Array('aioi_version' => $this->PLUGIN_VERSION,
					 'aioi_privatesite' => true,
					 'aioi_ms_requiremember' => true,
					 'aioi_autologout_time' => 0,
					 'aioi_autologout_units' => 'minutes',
					 'aioi_loginredirect' => '');
	}
	
	protected $aioi_options = null;
	protected function get_option_aioi() {
		if ($this->aioi_options != null) {
			return $this->aioi_options;
		}
	
		$option = get_site_option($this->get_options_name(), Array());
	
		$default_options = $this->get_default_options();
		foreach ($default_options as $k => $v) {
			if (!isset($option[$k])) {
				$option[$k] = $v;
			}
		}
	
		$this->aioi_options = $option;
		return $this->aioi_options;
	}
	
	// ADMIN
	
	public function aioi_admin_init() {
		register_setting( $this->get_options_pagename(), $this->get_options_name(), Array($this, 'aioi_options_validate') );

		global $pagenow;	
	}
	
	protected function add_actions() {
		
		if (is_admin()) {
			add_action( 'admin_init', array($this, 'aioi_admin_init'), 5, 0 );
			
			add_action(is_multisite() ? 'network_admin_menu' : 'admin_menu', array($this, 'aioi_admin_menu'));
			
			if (is_multisite()) {
				add_action('network_admin_edit_'.$this->get_options_menuname(), array($this, 'aioi_save_network_options'));
				add_filter('network_admin_plugin_action_links', array($this, 'aioi_plugin_action_links'), 10, 2 );
			}
			else {
				add_filter( 'plugin_action_links', array($this, 'aioi_plugin_action_links'), 10, 2 );
			}
		}

		add_action( 'template_redirect', array($this, 'aioi_template_redirect') );
		add_filter( 'robots_txt', array($this, 'aioi_robots_txt'), 0, 2);
		add_filter( 'option_ping_sites', array($this, 'aioi_option_ping_sites'), 0, 1);
		
		add_filter( 'login_redirect', array($this, 'aioi_login_redirect'), 10, 3);
		
		add_action( 'wp_login', array($this, 'aioi_wp_login'), 10, 2);
		add_action( 'init', array($this, 'aioi_check_activity'), 1);
	}

}

?>