<?php
/*
Plugin Name: Admin SMS Alert
Plugin URI: http://www.premiumdigitalservices.net/blog
Description: Admin SMS Alert sends a SMS Message (Text) to the provided cell phone on the chosen carrier for selected alerts including Google Voice!
Author: Scott E. Royalty
Version: 1.1.0
Author URI: http://www.premiumdigitalservices.net
*/ 

/*
Licensed under the MIT License
Copyright (c)  2010 Scott E. Royalty

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated
documentation files (the "Software"), to deal in the
Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software,
and to permit persons to whom the Software is furnished to
do so, subject to the following conditions:

The above copyright notice and this permission notice shall
be included in all copies or substantial portions of the
Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY
KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR
OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/


// Add the ASMSA options page to the admin menu
function asmsa_add_pages() {
	add_options_page("Admin SMS", "Admin SMS", 8, __FILE__, "asmsa_options_page");
}
add_action('admin_menu', 'asmsa_add_pages');

function asmsa_save_options($newOptions)
{
    $xDBArr = unserialize(get_option('asmsa_values'));
	if (!$xDBArr)
		$xDBArr = array();
	$result = array_merge($xDBArr, $newOptions);
    update_option('asmsa_values', serialize($result));
}


// Displays the Admin SMS Alerts Options Page
function asmsa_options_page() {
	// Cell Phone Providers
    if($_POST['asmsa_values']=="true")
	{
        $xPostArr['asmsa_activated'] = $_POST['asmsa_activated'];
        $xPostArr['asmsa_number'] = $_POST['asmsa_number'];
        $xPostArr['asmsa_carrier'] = $_POST['asmsa_carrier'];
        $xPostArr['asmsa_gvoice_login'] = $_POST['asmsa_gvoice_login'];
		$xPostArr['asmsa_gvoice_pass'] = $_POST['asmsa_gvoice_pass'];
        $xPostArr['asmsa_comment_spam'] = $_POST['asmsa_comment_spam'];
		$xPostArr['asmsa_comment_approved'] = $_POST['asmsa_comment_approved'];
		$xPostArr['asmsa_comment_disapproved'] = $_POST['asmsa_comment_disapproved'];
        $xPostArr['asmsa_comment_status_changed'] = $_POST['asmsa_comment_status_changed'];
        $xPostArr['asmsa_pingback'] = $_POST['asmsa_pingback'];
        asmsa_save_options($xPostArr);
    }
	if (is_string(get_option('asmsa_values')))
		$xDBArr = unserialize(get_option('asmsa_values'));
	else
		$xDBArr = get_option('asmsa_values');

	include_once( 'providers.php' );
	?><div class="wrap">
		<h2>Admin SMS Alerts Options</h2>
		<form method="POST" action="">
			<?php wp_nonce_field("update-options"); ?>
			<input type="hidden" name="asmsa_values" value="true" />
			<h3>Mobile Settings</h3>
			<table class="form-table">
				<tr valign="top"> 
					<th scope="row"><label for="asmsa_activated">Send SMS Alerts</label></th>
					<td>
						<input name="asmsa_activated" type="checkbox" id="asmsa_activated" <?php if($xDBArr['asmsa_activated']) echo 'checked'; ?> />
						<br /><span class="description">Text Msg rates may apply with carrier for msgs received.</span>
					</td> 
				</tr>
				<tr valign="top"> 
					<th scope="row"><label for="asmsa_number">Your Cell Number</label></th>
					<td>
						<input name="asmsa_number" type="text" id="asmsa_number" value="<?php echo $xDBArr['asmsa_number']; ?>" class="regular-text" />
						<br /><span class="description">Format is numbers only. Ex: 5556667777</span>
					</td> 
				</tr>
				<tr valign="top"> 
					<th scope="row"><label for="asmsa_carrier">Your Carrier</label></th>
					<td>
						<select name="asmsa_carrier">
							<option value=''>-= United States =-</option>
							<option value=''></option>
							<?php
							show_carrier_dropdown($us_providers, $xDBArr['asmsa_carrier']);
							?>
							<option value=''></option>
							<option value=''>-= Canada =-</option>
							<option value=''></option>
							<?php
							show_carrier_dropdown($ca_providers, $xDBArr['asmsa_carrier']);
							?>
							<option value=''></option>
							<option value=''>-= Denmark =-</option>
							<option value=''></option>
							<?php
							show_carrier_dropdown($dk_providers, $xDBArr['asmsa_carrier']);
							?>
							<option value=''></option>
							<option value=''>-= Germany =-</option>
							<option value=''></option>
							<?php
							show_carrier_dropdown($de_providers, $xDBArr['asmsa_carrier']);
							?>
							<option value=''></option>
							<option value=''>-= India =-</option>
							<option value=''></option>
							<?php
							show_carrier_dropdown($in_providers, $xDBArr['asmsa_carrier']);
							?>
							<option value=''></option>
							<option value=''>-= Italy =-</option>
							<option value=''></option>
							<?php
							show_carrier_dropdown($it_providers, $xDBArr['asmsa_carrier']);
							?>
							<option value=''></option>
							<option value=''>-= Norway =-</option>
							<option value=''></option>
							<?php
							show_carrier_dropdown($no_providers, $xDBArr['asmsa_carrier']);
							?>
							<option value=''></option>
							<option value=''>-= Singapore =-</option>
							<option value=''></option>
							<?php
							show_carrier_dropdown($sg_providers, $xDBArr['asmsa_carrier']);
							?>
							<option value=''></option>
							<option value=''>-= Sweden =-</option>
							<option value=''></option>
							<?php
							show_carrier_dropdown($se_providers, $xDBArr['asmsa_carrier']);
							?>
							<option value=''></option>
							<option value=''>-= United Kingdom =-</option>
							<option value=''></option>
							<?php
							show_carrier_dropdown($uk_providers, $xDBArr['asmsa_carrier']);
							?>
						</select>
						<br /><span class="description">If unsure about carrier, send a text message to your regular email address (Send to that, instead of a phone number).</span>
					</td> 
				</tr>
			</table>

			<h3>Google Voice Settings</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="asmsa_gvoice_login">Google Voice Email Login</label></th>
					<td>
						<input name="asmsa_gvoice_login" type="textfield" size="30" id="asmsa_gvoice_login" <?php if($xDBArr['asmsa_gvoice_login']) echo 'value="' . attribute_escape($xDBArr['asmsa_gvoice_login']) . '"'; ?> />
						<br /><span class="description">This is your email login for the Google Voice service.</span>
					</td> 
				</tr>
				<tr valign="top">
					<th scope="row"><label for="asmsa_gvoice_pass">Google Voice Password</label></th>
					<td>
						<input name="asmsa_gvoice_pass" type="password" size="30" id="asmsa_gvoice_pass" <?php if($xDBArr['asmsa_gvoice_pass']) echo 'value="' . attribute_escape($xDBArr['asmsa_gvoice_pass']) . '"'; ?> />
						<br /><span class="description">This is your password for the Google Voice service.</span>
					</td> 
				</tr>
			</table>
			
			<h3>Comment/Pingback Settings</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="asmsa_comment_spam">New Comment/Spam Alert</label></th>
					<td>
						<input name="asmsa_comment_spam" type="checkbox" id="asmsa_comment_spam" <?php if($xDBArr['asmsa_comment_spam']) echo 'checked'; ?> />
						<br /><span class="description">Text Msg when a comment is awaiting approval or marked as spam.</span>
					</td> 
				</tr>
				<tr valign="top"> 
					<th scope="row"><label for="asmsa_comment_approved">Approved Comment Alert</label></th>
					<td>
						<input name="asmsa_comment_approved" type="checkbox" id="asmsa_comment_approved" <?php if($xDBArr['asmsa_comment_approved']) echo 'checked'; ?> />
						<br /><span class="description">Text Msg when a comment is approved.</span>
					</td> 
				</tr>
				<tr valign="top"> 
					<th scope="row"><label for="asmsa_comment_disapproved">Disapproved Comment Alert</label></th>
					<td>
						<input name="asmsa_comment_disapproved" type="checkbox" id="asmsa_comment_disapproved" <?php if($xDBArr['asmsa_comment_disapproved']) echo 'checked'; ?> />
						<br /><span class="description">Text Msg when a comment is declined.</span>
					</td> 
				</tr>
				<tr valign="top"> 
					<th scope="row"><label for="asmsa_pingback">New Pingback Alert</label></th>
					<td>
						<input name="asmsa_pingback" type="checkbox" id="asmsa_pingback" <?php if($xDBArr['asmsa_pingback']) echo 'checked'; ?> />
						<br /><span class="description">Text Msg when a comment/pingback is added via XMLRPC.</span>
					</td> 
				</tr>
			</table>

			<p class="submit">
				<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
			</p>
		</form>
	</div><?php
}

function show_carrier_dropdown($provider, $selected) {
	foreach( $provider as $cname => $caddress )
	{
		$checked = $selected == $caddress?'selected':'';
		echo '<option value="'.$caddress.'" '.$checked.' >'.$cname.' ('.$caddress.')</option>';
	}
}

function wp_sms($to, $subject, $message, $headers = "") {
	$asmsa_options = unserialize(get_option('asmsa_values'));
	if ($headers == "") {
		$headers = "MIME-Version: 1.0\n" .
			"From: " . $asmsa_options["asmsa_number"] . "\n" .
			"Content-Type: text/plain; charset=\"" . get_option("blog_charset") . "\"\n";
	}
	if (strstr($asmsa_options["asmsa_carrier"], 'voice.google.com')) {
		return gvoice_sendsms($to, $subject, $message);
	} else {
		return @mail($to, $subject, $message, $headers);
	}
}

require_once 'class.xcurl.php';
function gvoice_sendsms($to, $subject, $message) {
	$asmsa_options = unserialize(get_option('asmsa_values'));
	// Set account login info
	$data['post'] = array(
		'accountType' => 'GOOGLE',
		'Email'       => $asmsa_options["asmsa_gvoice_login"],
		'Passwd'      => $asmsa_options["asmsa_gvoice_pass"],
		'service'     => 'grandcentral',
		'source'      => 'premiumdigitalservices.net-AdminSMSAlert-1.0.2'
	);

	$response = xcurl::fetch('https://www.google.com/accounts/ClientLogin', $data);

	// Test if unsuccessful
	if($response['http_code'] != '200') {
		return FALSE;
	}

	// Extract SID and Auth
	preg_match('/SID=(.+)/', $response['data'], $matches);
	$sid = $matches[1];
	preg_match('/Auth=(.+)/', $response['data'], $matches);
	$auth = $matches[1];

	// Erase POST variables used on the previous xcurl call
	$data['post'] = null;

	// Set gv and SID cookies for authentication
	// There is no official documentation and this might change without notice
	$data['cookies'] = array(
		'gv'  => $auth,  
		'SID' => $sid    
	);

	$response = xcurl::fetch('https://www.google.com/voice', $data);

	// Test if unsuccessful
	if($response['http_code'] != '200') {
		return FALSE;
	}

	// Extract _rnr_se
	preg_match("/'_rnr_se': '([^']+)'/", $response['data'], $matches);
	$rnrse = $matches[1];

	// $data['cookies'] still contains gv and SID for authentication

	// Set SMS options
	$data['post'] = array (
		'_rnr_se'     => $rnrse,
		'phoneNumber' => $asmsa_options["asmsa_number"], // International notation of phone numbers
		'text'        => $subject . '' . chr(13) . '' . $message,
		'id'          => ''  // thread ID of message, GVoice's way of threading the messages like GMail
	);

	// Send the SMS
	$response = xcurl::fetch('https://www.google.com/voice/sms/send/', $data);

	// Evaluate the response
	$value = json_decode($response['data']);

	if($value->ok) {
		return TRUE;
	} else {
		return FALSE;
	}
}

function build_message_data($heading, $comment_ID) {
		$commentdata = get_comment($comment_ID, ARRAY_A);
		$message = $heading.''.chr(10);
		$message .= 'Post ID:    '. $commentdata['comment_post_ID'] .''.chr(13);
		$message .= 'Author:     '. $commentdata['comment_author'] .''.chr(13);
		$message .= 'Email:      '. $commentdata['comment_author_email'] .''.chr(13);
		$message .= 'Website:    '. $commentdata['comment_author_url'] .''.chr(13);
		$message .= 'Comment:    '. $commentdata['comment_content'] .''.chr(13);
		return $message;
}

// Add the ASMSA handling for when posts are added to the database.
function asmsa_handle_new_comment($comment_ID, $status) {
	$asmsa_options = unserialize(get_option('asmsa_values'));
	$to = $asmsa_options["asmsa_number"] . "@" . $asmsa_options["asmsa_carrier"];
	if ($status == 'spam' && $asmsa_options['asmsa_comment_spam']) // Spam
	{
		$subject = get_option('blogname') .' New Comment Alert';
		$message = build_message_data('There is a new comment waiting for approval on your blog.', $comment_ID);
		wp_sms($to, $subject, $message);
	} else if ($status == 1) { // Approved
		$subject = get_option('blogname') .' Comment Approved Alert';
		$message = build_message_data('There is a new comment on your blog.', $comment_ID);
		wp_sms($to, $subject, $message);
	} else if ($status == 0) { // Not approved
		$subject = get_option('blogname') .' Comment Denied Alert';
		$message = build_message_data('There is a new comment that was denied on your blog.', $comment_ID);
		wp_sms($to, $subject, $message);	
	}
}
add_action('comment_post', 'asmsa_handle_new_comment', 10, 2);

// Add the ASMSA handling for when posts are added to the database.
function asmsa_handle_pingback($comment_ID) {
	$asmsa_options = unserialize(get_option('asmsa_values'));
	$to = $asmsa_options["asmsa_number"] . "@" . $asmsa_options["asmsa_carrier"];
	if ($asmsa_options['asmsa_pingback']) // Got a new pingback and want it sms
	{
		$subject = get_option('blogname') .' New Pingback Alert';
		$message = build_message_data('There is a new pingback waiting for approval on your blog.', $comment_ID);
		wp_sms($to, $subject, $message);
	}
}
add_action('pingback_post', 'asmsa_handle_pingback', 10, 1);

// On activation, add the intial options.
function asmsa_activate() {
	add_option("asmsa_activated");
	add_option("asmsa_number");
	add_option("asmsa_carrier");
}

// On deactivation, remove options from the database for cleanup.
function asmsa_deactivate() {
	delete_option("asmsa_activated");
	delete_option("asmsa_number");
	delete_option("asmsa_carrier");
}

// Plugin Activation Hook for initial options
register_activation_hook(basename(__FILE__), "asmsa_activate");
// Plugin Deactivation Hook for cleanup
register_deactivation_hook(basename(__FILE__), "asmsa_deactivate");

?>
