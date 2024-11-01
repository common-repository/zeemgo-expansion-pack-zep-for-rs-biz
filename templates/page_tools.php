<?php

global $wpdb;

$sql = "
SELECT ID, post_title
FROM $wpdb->posts
WHERE post_type = 'cfs' AND post_status = 'publish'
ORDER BY post_title";
$results = $wpdb->get_results($sql);
?>

<style type="text/css">
.nav-tab { cursor: pointer; }
.nav-tab:first-child { margin-left: 15px; }
.tab-content { display: none; }
.tab-content.active { display: block; }
#button-export, #button-sync { margin-top: 4px; }
#icon-edit { background: url(<?php echo $this->url; ?>/assets/images/logo.png) no-repeat; }
</style>

<script>
(function($) {
    $(function() {
        $('.nav-tab').click(function() {
            $('.tab-content').removeClass('active');
            $('.nav-tab').removeClass('nav-tab-active');
            $('.tab-content.' + $(this).attr('rel')).addClass('active');
            $(this).addClass('nav-tab-active');
        });

        $('#button-export').click(function() {
            var groups = $('#export-field-groups').val();
            if (null != groups) {
                $.post(ajaxurl, {
                    action: 'cfs_ajax_handler',
                    action_type: 'export',
                    field_groups: $('#export-field-groups').val()
                },
                function(response) {
                    $('#export-output').text(response);
                    $('#export-area').show();
                });
            }
        });

        $('#button-import').click(function() {
            $.post(ajaxurl, {
                action: 'cfs_ajax_handler',
                action_type: 'import',
                import_code: $('#import-code').val()
            },
            function(response) {
                $('#import-message').html(response);
            });
        });

        $('#button-reset').click(function() {
            if (confirm('This will delete all Zeemgo Expansion Pack data. Are you sure?')) {
                $.post(ajaxurl, {
                    action: 'cfs_ajax_handler',
                    action_type: 'reset'
                },
                function(response) {
                    window.location.replace(response);
                });
            }
        });
    });
})(jQuery);
</script>


<div class="wrap">
    <h2><?php _e( 'Zeemgo Expansion Pack (ZEP) Tools', 'cfs' ); ?></h2>

	<h3>Step 1: Download</h3>
	<p><a href="http://zeemgo.com/downloadvID=eprsbizfree&pU8r3p6VdraQ3puxuM0sk2s9&uMawrachaC=EjAMedrE4EpUb"><b>Download this zip</b></a> that contains the file designed for this plugin to work. Unzip the file to your hard drive. Then, upload the individual file to the root directory of the RS Biz theme folder on your site's server.

	<h3>Step 2: Import</h3>
	<p>Click on the 'Import' tab below twice, read, and click on the 'Import' button. That's it!</p>

    <h3 class="nav-tab-wrapper">

        <a class="nav-tab" rel="import"><?php _e('Import', 'cfs'); ?></a>

        <a class="nav-tab" rel="reset"><?php _e('Reset', 'cfs'); ?></a>
    </h3>

    <div class="content-container">

        <!-- Import -->

        <div class="tab-content import">
            <h3><?php _e('You are about to import the following RS Biz features:', 'cfs'); ?></h3>

		<p><img src="<?php echo $this->url; ?>/assets/images/zep-green.png" style="vertical-align:middle;  padding:5px;">RS Biz Top Bar Options: Top Bar Text, Phone Number, and Text Before Phone Number on Top Bar features.</p>

		<p><img src="<?php echo $this->url; ?>/assets/images/zep-green.png" style="vertical-align:middle;  padding:5px;">RS Biz Video Options: YouTube Video Code, Headline Below Video, and Embed Video/Image Code features.</p>

		<p><img src="<?php echo $this->url; ?>/assets/images/zep-green.png" style="vertical-align:middle;  padding:5px;">RS Biz Footer Options: Footer Headline feature.</p>

		<p>This process will import the features mentioned above. It is separate from the theme options page and will  not conflict with the theme's original design and functionality. These features are specifically designed for individual  pages (not yet blog posts), so that your inner pages can all have their own unique look.</p>

            <h3><?php _e('Get the full plugin <a href="http://zeemgo.com/eprsbiz/">here</a>, if you want the 3 features above plus the following two RS Biz features below:', 'cfs'); ?></h3>

		<p><img src="<?php echo $this->url; ?>/assets/images/zep-green.png" style="vertical-align:middle;  padding:5px;">RS Biz Background Options: Color Scheme, Page Background Color, and Image Behind Video features.</p>
		<p><img src="<?php echo $this->url; ?>/assets/images/zep-green.png" style="vertical-align:middle;  padding:5px;">RS Biz Bullet List Below Video Options: List Item #1-10, and List Item #1-10, Optional Description features.</p>

            <h3>Ready to get started? Click the 'Import' button below to import the 'RS Biz Top Bar Options, RS Biz Video Options, and RS Biz Footer Options.</h3>

            <table>
                <tr>
                    <td style="width:300px; vertical-align:top">
                        <div>
                            <textarea style="display:none;" id="import-code" style="width:98%;  height:200px">
[{"post_title":"[ZEP] RSBIZ Video Options","post_name":"zep-rsbiz-video-options","cfs_extras":{"order":"3","context":"normal","hide_editor":"0"},"cfs_fields":[{"id":1,"name":"rock4p_video","label":"YouTube Video Code","type":"text","notes":"Insert a YouTube video id (the 11-character code right after http:\/\/www.youtube.com\/watch?v=). Leaving it blank will supress the video area. Type the word 'embed' (without the quotes) if you're using the embed option below.","parent_id":0,"weight":0,"options":{"default_value":"","required":"0"}},{"id":2,"name":"rock4p_video_text","label":"Headline Below Video","type":"text","notes":"Insert the headline to display below the video on the home page. The headline should be about 6 words. You may include bold tags to bold words (ie. <b>word to bold<\/b>).","parent_id":0,"weight":1,"options":{"default_value":"","required":"0"}},{"id":3,"name":"rock4p_video_embedcode ","label":"Embed Video\/Image Code","type":"textarea","notes":"If you prefer to use an embed code from a video player other than You Tube, first add the word 'embed' (without the quotes) in the 'YouTube Video Code' field above. Then in the box below, add your own video code. You can also add a static image. Required video\/image dimensions: 505px wide and 281px tall.","parent_id":0,"weight":2,"options":{"default_value":"","formatting":"none","required":"0"}}],"cfs_rules":{"post_types":{"operator":"==","values":["page"]}}},{"post_title":"[ZEP] RSBIZ Top Bar Options","post_name":"zep-rsbiz-top-bar-options","cfs_extras":{"order":"1","context":"normal","hide_editor":"0"},"cfs_fields":[{"id":7,"name":"rock4p_top_bar_text","label":"Top Bar Text","type":"text","notes":"Insert text to be displayed at the top left of this page.","parent_id":0,"weight":0,"options":{"default_value":"","required":"0"}},{"id":8,"name":"rock4p_phone_number","label":"Phone Number","type":"text","notes":"Insert a phone number to be display in the header of this page. Leaving it blank will keep the phone number text suppressed.","parent_id":0,"weight":1,"options":{"default_value":"","required":"0"}},{"id":9,"name":"rock4p_phone_text","l abel":"Text Before Phone Number on Top Bar","type":"text","notes":"Insert your text to display it to the left of the phone number in top bar of this page. Leaving it blank will make the default 'Call Us Now:' message appear.","parent_id":0,"weight":2,"options":{"default_value":"","required":"0"}}],"cfs_rules":{"post_types":{"operator":"==","values":["page"]}}},{"post_title":"[ZEP] RSBIZ Footer Options","post_name":"zep-rsbiz-footer-options","cfs_extras":{"order":"7","context":"normal","hide_editor":"0"},"cfs_fields":[{"id":"26","name":"rock4p_footer_head","label":"Footer Headline","type":"text","notes":"On the bar above the footer, this is the text that will be shown - leaving this blank will output a blank space in that area.","parent_id":0,"weight":0,"options":{"default_value":"","required":"0"}}],"cfs_rules":{"post_types":{"operator":"==","values":["page"]}}}]
                            </textarea>
                        </div>
                        <div>
                            <input type="button" id="button-import" class="button" value="<?php _e('Import', 'cfs'); ?>" />
                        </div>
                    </td>
                    <td style="width:300px; vertical-align:top">
                        <div id="import-message"></div>
                    </td>
                </tr>
            </table>

	<hr style="margin-top:10px; margin-bottom:10px;">

	<h3>Step 3: Congratulations!</h3>
		<p><img src="<?php echo $this->url; ?>/assets/images/zep-green.png" style="vertical-align:middle;  padding:5px;">Now you can create or edit any page with the 3 new ZEP sections near the bottom of any add/edit page.</p>
		<p><img src="<?php echo $this->url; ?>/assets/images/zep-green.png" style="vertical-align:middle;  padding:5px;"><b>Be sure to select 'Home Page Template [Zeemgo Expansion Pack]' from the 'Page Attributes' section before  saving changes.</b></p>
		<p><img src="<?php echo $this->url; ?>/assets/images/zep-green.png" style="vertical-align:middle;  padding:5px;">Have fun!</p>

        </div>

        <!-- Debug Information -->

        <div class="tab-content debug">
            <h2><?php _e('Debug information', 'cfs'); ?></h2>
<?php
global $wp_version;

echo '<textarea style="width:600px; height:200px">';
echo 'WordPress ' . $wp_version . "\n";
echo 'PHP ' . phpversion() . "\n";
echo $_SERVER['SERVER_SOFTWARE'] . "\n";
echo $_SERVER['HTTP_USER_AGENT'] . "\n";
echo "\n--- Active Plugins ---\n";

$all_plugins = get_plugins();
foreach ($all_plugins as $plugin_file => $plugin_data) {
    if (is_plugin_active($plugin_file)) {
        echo $plugin_data['Name'] . ' ' . $plugin_data['Version'] . "\n";
    }
}

echo '</textarea>';
?>
        </div>

        <!-- Reset -->

        <div class="tab-content reset">
            <h2><?php _e('Reset and deactivate.', 'cfs'); ?></h2>
            <p><?php _e('This will delete all Zeemgo Expansion Pack data and deactivate the plugin. Be sure to manually remove the zep-template-home.php files from the root directory of the RS Biz theme folder. The file will not work without the plugin activated.', 'cfs'); ?></p>
            <input type="button" id="button-reset" class="button" value="<?php _e('Delete everything', 'cfs'); ?>" />
        </div>
    </div>
</div>