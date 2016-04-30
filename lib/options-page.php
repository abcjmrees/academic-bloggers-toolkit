<?php


class ABT_Options {

	public $citation_styles;

	public function __construct() {
		$this->get_citation_styles();
		add_action('admin_menu', array($this,'add_options_page'));
	}

	private function get_citation_styles() {
		include(dirname(__FILE__).'/../vendor/citationstyles.php');
		$this->citation_styles = $citation_styles;
	}

	public function add_options_page() {
		add_options_page(
			"Academic Blogger's Toolkit Options",
			"Academic Blogger's Toolkit",
			'manage_options',
			'abt-options',
			array($this, 'ABT_options_page')
		);
	}

	public function ABT_options_page() {

		// Permissions Check
		if ( !current_user_can( 'manage_options' ) ) {
			wp_die('You do not have sufficient permissions to access this page.');
		}

		$abt_options = get_option('abt_options');

		// Are hidden form fields set? If so, save them as variables
		$hidden_field_1 = isset( $_POST['abt_custom_css_editor_form_submitted'] ) ? esc_html( $_POST['abt_custom_css_editor_form_submitted'] ) : '';
		$hidden_field_2 = isset( $_POST['abt_citation_style_form_submitted'] ) ? esc_html( $_POST['abt_citation_style_form_submitted'] ) : '';
		$hidden_field_3 = isset( $_POST['abt_display_options_form_submitted'] ) ? esc_html( $_POST['abt_display_options_form_submitted'] ) : '';

		// Form Submits -- If form is submitted, set data as variables within the 'abt_options' array in the database
		if ($hidden_field_1 == 'Y') {

			$abt_options['custom_css'] = stripslashes_deep($_POST['abt_custom_css_editor']);
			update_option( 'abt_options', $abt_options );

		}

		if ($hidden_field_2 == 'Y') {

			$abt_options['abt_citation_style'] = esc_html( $_POST['abt_citation_style'] );
			update_option( 'abt_options', $abt_options );

		}

		if ($hidden_field_3 == 'Y') {
			$abt_options['display_options']['bibliography'] = esc_html( $_POST['abt_bibliography_display'] );
			$abt_options['display_options']['PR_boxes'] = esc_html( $_POST['abt_PR_display'] );
			$abt_options['display_options']['bib_heading'] = esc_html( $_POST['abt_bib_heading'] );
			update_option( 'abt_options', $abt_options );
		}


		// Check if options are set. If they are, save them as variables
		$abt_saved_css = isset($abt_options['custom_css']) ? $abt_options['custom_css'] : '';
		$selected_style = isset($abt_options['abt_citation_style']) ? $abt_options['abt_citation_style'] : '';
		$selected_PR_box_display = isset($abt_options['display_options']['PR_boxes']) ? $abt_options['display_options']['PR_boxes'] : '';
		$selected_bib_display = isset($abt_options['display_options']['bibliography']) ? $abt_options['display_options']['bibliography'] : '';
		$bib_heading = isset($abt_options['display_options']['bib_heading']) ? $abt_options['display_options']['bib_heading'] : '';


		?>

		<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2><?php esc_attr_e("Academic Blogger's Toolkit Options", 'wp_admin_style'); ?></h2>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<div class="postbox">
							<h3>Please send your feedback!</h3>
							<div class="inside">
	                            If you experience a bug or would like to request a new feature, please visit my <a href='https://github.com/dsifford/academic-bloggers-toolkit' target='_blank'>GitHub Repository</a> and submit an issue. I'll do my best to get it handled in a timely manner. Comments can also be sent to me on twitter <a href='http://www.twitter.com/flightmed1' target='_blank'>@flightmed1</a>.
							</div>
						</div>
						<div class="postbox">
							<h3>Preferred Citation Style</h3>
							<div class="inside">
								<form method="post" name="abt_citation_style_form" action="">
									<input type="hidden" name="abt_citation_style_form_submitted" value="Y">
									<select name="abt_citation_style" id="abt_citation_style">
									<?php foreach ($this->citation_styles as $key => $value): ?>
										<option value="<?php echo $value['value'] ?>" <?php selected($selected_style, $value['value']); ?>><?php echo $value['label'] ?></option>
										<option value="<?php echo $value['value'] ?>" <?php selected($selected_style, $value['value']); ?>><?php echo $value['label'] ?></option>
									<?php endforeach?>
									</select>
									<input class="button-primary" type="submit" name="abt_citation_style_submit" value="Update" />
								</form>
							</div>
						</div>
						<div class="postbox">
							<h3>Display Options</h3>
							<div class="inside">
								<form method="post" name="abt_citation_style_form" action="">
									<input type="hidden" name="abt_display_options_form_submitted" value="Y">
									<table style="width: 100%;">
										<tr>
											<td style="width: 25%;">
												<label for="abt_bibliography_display">The bibliography should </label>
											</td>
											<td style="width: 75%;">
												<select name="abt_bibliography_display" style="width: 100%;">
													<option value="fixed" <?php selected($selected_bib_display, 'fixed'); ?>>be fixed on the page</option>
													<option value="toggle" <?php selected($selected_bib_display, 'toggle'); ?>>toggle on click</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="width: 25%;">
												<label for="abt_PR_display">The peer review boxes should </label>
											</td>
											<td style="width: 75%;">
												<select name="abt_PR_display" style="width: 100%;">
													<option value="toggle" <?php selected($selected_PR_box_display, 'toggle'); ?>>toggle on click</option>
													<option value="fixed" <?php selected($selected_PR_box_display, 'fixed'); ?>>be fixed on the page</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="width: 25%;">
												<label style="white-space: nowrap;" for="abt_bib_heading">The heading for bibliographies should be </label>
											</td>
											<td style="width: 75%;">
												<input type="text" class="large-text" name="abt_bib_heading" value="<?php echo $bib_heading; ?>" /><br>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<input class="button-primary" type="submit" name="abt_display_options_submit" value="Update" />
											</td>
										</tr>
									</table>
								</form>
							</div>
						</div>
						<div class="postbox">
							<h3>Override CSS</h3>
							<table class="form-table">
								<tr valign="top">
									<td scope="row" width="50%">
										<form method="post" name="abt_custom_css_editor_form" action="">
										<input type="hidden" name="abt_custom_css_editor_form_submitted" value="Y">
										<textarea name="abt_custom_css_editor" id="abt_custom_css_editor" class="large-text" cols="80" rows="10" style="font-family: monospace;"><?php echo $abt_saved_css ?></textarea><br>
										<input class="button-primary" type="submit" name="abt_custom_css_submit" value="Update" />
										</form>
									</td>
									<td width="50%" style="vertical-align: top;">
										<table>
											<tr>
												<th colspan="2">CSS selectors used in this plugin:</th>
											</tr>
											<tr>
												<td><strong>Inline Citations:</strong></td>
												<td><code>.abt_cite</code></td>
											</tr>
											<tr>
												<td><strong>Peer Review Boxes:</strong></td>
												<td><code>.abt_PR_heading, .abt_PR_info, .abt_PR_headshot, .abt_chat_bubble</code></td>
											</tr>
											<tr>
												<td><strong>Citation Tooltips:</strong></td>
												<td><code>.abt_tooltip, .abt_tooltip_arrow, .abt_tooltip_touch_close</code></td>
											</tr>
											<tr>
												<td><strong>Bibliography:</strong></td>
												<td><code>#abt-smart-bib, #abt-smart-bib-toggle, #abt-smart-bib-container h3</code></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</div>
						<div class="postbox">
							<h3>How do I....</h3>
							<div class="inside">
								<div style="display: flex; flex-wrap: wrap;">
									<div style="flex: 1; min-width: 300px; padding: 0 5px;">
										<strong>Make my tooltips a different color?</strong>
										<iframe width="100%" height="200" src="//jsfiddle.net/dsifford/e93h26yf/embedded/css,result/" allowfullscreen="allowfullscreen" frameborder="0"></iframe>
									</div>
									<div style="flex: 1; min-width: 300px; padding: 0 5px;">
										<strong>Make my citations superscript?</strong>
										<iframe width="100%" height="200" src="//jsfiddle.net/dsifford/0hvotx2r/embedded/css,result/" allowfullscreen="allowfullscreen" frameborder="0"></iframe>
									</div>
									<div style="flex: 1; min-width: 300px; padding: 0 5px;">
										<strong>Apply style to the bibliography list?</strong>
										<iframe width="100%" height="200" src="//jsfiddle.net/dsifford/9hhh5ut6/embedded/css,result/" allowfullscreen="allowfullscreen" frameborder="0"></iframe>
									</div>
									<div style="flex: 1; min-width: 300px; padding: 0 5px;">
										<strong>Change the style of the peer review boxes?</strong>
										<iframe width="100%" height="300" src="//jsfiddle.net/dsifford/ky4LangL/embedded/css,result/" allowfullscreen="allowfullscreen" frameborder="0"></iframe>
									</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>
		</div>
		<?php
	}

}
new ABT_Options();
