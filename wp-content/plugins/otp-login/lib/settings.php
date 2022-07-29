<div class="wrap">
    <h2>OTP Login Settings</h2>
	<div id="otpl-tab-menu"><a id="otpl-general" class="otpl-tab-links active" >General</a> <a  id="otpl-support" class="otpl-tab-links">Support</a> <a  id="otpl-other" class="otpl-tab-links">Our Other Plugins</a></div>
    <form method="post" action="options.php" id="otpl-option-form"> 
      <?php settings_fields('otpl'); ?>
        <div class="otpl-setting">
			<!-- General Setting -->	
			<div class="first otpl-tab" id="div-otpl-general">
				<table class="form-table">  
				<tr>
				<td style="vertical-align:top;"><table>
					<tr valign="top">
						<th width="10"><input type="checkbox" value="1" name="otpl_enable" id="otpl_enable" <?php checked(get_option('otpl_enable'),1); ?> /> <label for="otpl_enable">Enable OTP Login</label></th>
					</tr>
					<tr valign="top">
						<th><label for="otpl_redirect_url">Redirect URL</label><input type="text" value="<?php echo get_option('otpl_redirect_url'); ?>" name="otpl_redirect_url" id="otpl_redirect_url"  size="40"/><em>define redirect url after logged in user</em></th>
					</tr>
					<tr><td><?php @submit_button(); ?></td></tr>
					</table>
					</td>
					
					</tr>
				</table>
				<hr>
				<h3>Login Popup Class Name:</h3>
				<p><strong>otpl-popup</strong> using this class your can add OTP login popup on your website</p>
				Exmaple:
				<code>&lt;div class="otpl-popup"&gt;&lt;a href="javascript:"&gt;Login&lt;/a&gt;&lt;/div&gt;</code>
			</div>
			<div class="otpl-tab" id="div-otpl-support"> <h2>Support</h2> 
				<p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZEMSYQUZRUK6A" target="_blank" style="font-size: 17px; font-weight: bold;"><img src="<?php echo  plugins_url( '../images/btn_donate_LG.gif' , __FILE__ );?>" title="Donate for this plugin"></a></p>
				<p><strong>Plugin Author:</strong><br><a href="https://www.wp-experts.in/contact-us" target="_blank">WP Experts Team</a></p>
				<p><a href="mailto:raghunath.0087@gmail.com" target="_blank" class="contact-author">Contact Author</a></p>
			</div>
			<div class="last otpl-tab" id="div-otpl-other">
				<h2>Our Other plugins</h2>
				<p>
				  <ol>
					<li><a href="https://wordpress.org/plugins/custom-share-buttons-with-floating-sidebar" target="_blank">Custom Share Buttons With Floating Sidebar</a></li>
							<li><a href="https://wordpress.org/plugins/protect-wp-admin/" target="_blank">Protect WP-Admin</a></li>
							<li><a href="https://wordpress.org/plugins/wc-sales-count-manager/" target="_blank">WooCommerce Sales Count Manager</a></li>
							<li><a href="https://wordpress.org/plugins/wp-protect-content/" target="_blank">WP Protect Content</a></li>
							<li><a href="https://wordpress.org/plugins/wp-categories-widget/" target="_blank">WP Categories Widget</a></li>
							<li><a href="https://wordpress.org/plugins/wp-importer" target="_blank">WP Importer</a></li>
							<li><a href="https://wordpress.org/plugins/wp-youtube-gallery/" target="_blank">WP Youtube Gallery</a></li>
							<li><a href="https://wordpress.org/plugins/wp-social-buttons/" target="_blank">WP Social Buttons</a></li>
							<li><a href="https://wordpress.org/plugins/seo-manager/" target="_blank">SEO Manager</a></li>
							<li><a href="https://wordpress.org/plugins/optimize-wp-website/" target="_blank">Optimize WP Website</a></li>
							<li><a href="https://wordpress.org/plugins/wp-version-remover/" target="_blank">WP Version Remover</a></li>
							<li><a href="https://wordpress.org/plugins/wp-tracking-manager/" target="_blank">WP Tracking Manager</a></li>
							<li><a href="https://wordpress.org/plugins/wp-posts-widget/" target="_blank">WP Post Widget</a></li>
							<li><a href="https://wordpress.org/plugins/optimize-wp-website/" target="_blank">Optimize WP Website</a></li>
							<li><a href="https://wordpress.org/plugins/wp-testimonial/" target="_blank">WP Testimonial</a></li>
							<li><a href="https://wordpress.org/plugins/wp-sales-notifier/" target="_blank">WP Sales Notifier</a></li>
							<li><a href="https://wordpress.org/plugins/cf7-advance-security" target="_blank">Contact Form 7 Advance Security WP-Admin</a></li>
							<li><a href="https://wordpress.org/plugins/wp-easy-recipe/" target="_blank">WP Easy Recipe</a></li>
					</ol>
				</p>
			</div>
		</div>
    </form>
</div>
