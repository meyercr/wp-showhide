<?php
/*
Plugin Name: WP-ShowHide
Plugin URI: http://lesterchan.net/portfolio/programming/php/
Description: Allows you to embed content within your blog post via WordPress ShortCode API and toggling the visibility of the cotent via a link. By default the content is hidden and user will have to click on the "Show Content" link to toggle it. Similar to what Engadget is doing for their press releases. Example usage: <code>[showhide type="pressrelease"]Press Release goes in here.[/showhide]</code>
Version: 1.04
Author: Lester 'GaMerZ' Chan & Craig Meyer
Author URI: http://lesterchan.net & http://meyerscitech.com
Text Domain: wp-showhide
*/


/*
	Copyright 2014  Lester Chan  (email : lesterchan@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


### Function: Enqueue JavaScripts
add_action('wp_enqueue_scripts', 'showhide_scripts');
function showhide_scripts() {
	wp_enqueue_script('jquery');
}


### Function: Short Code For Inserting Press Release Into Post
add_shortcode('showhide', 'showhide_shortcode');
function showhide_shortcode($atts, $content = null) {
	// Variables
	$post_id = get_the_id();
	$word_count = number_format_i18n(sizeof(explode(' ', strip_tags($content))));

	// Extract ShortCode Attributes
	$attributes = shortcode_atts(array(
		'type' => 'pressrelease',
		'more_text' => __('Show Press Release (%s More Words)'),
		'less_text' => __('Hide Press Release (%s Less Words)'),
		'hidden' => 'yes'
	), $atts);

	// More/Less Text
	$more_text = sprintf($attributes['more_text'], $word_count);
	$less_text = sprintf($attributes['less_text'], $word_count);

	// Determine Whether To Show Or Hide Press Release
	$hidden_class = 'sh-hide';
	$hidden_css = 'display: none;';
	if($attributes['hidden'] == 'no') {
		$hidden_class = 'sh-show';
		$hidden_css = 'display: block;';
		$tmp_text = $more_text;
		$more_text = $less_text;
		$less_text = $tmp_text;
	}

	// Format HTML Output

	$type = $attributes['type'];
	$output  = '<div class="sh-link ' . $type . '-link ' . $hidden_class . '">';
	$output .= '<a href="#" onclick="showhide_toggle( this, ';
	$output .= '\'' . esc_js($more_text) . '\', ' . '\'' . esc_js($less_text) . '\');" class="' . $hidden_class . '">';
	$output .= '<span>' .$more_text . '</span></a></div>';
	$output .= '<div class="sh-content ' . $type . '-content ' . $hidden_class . '" style="' . $hidden_css . '">';
	$output .= do_shortcode( $content ) . '</div>';

	return $output;
}


### Function: Add JavaScript To Footer
add_action('wp_footer', 'showhide_footer');
function showhide_footer() {
?>
	<?php if(WP_DEBUG): ?>
		<script type="text/javascript">
			function showhide_toggle(obj, more_text, less_text) {
                            var  toggle = jQuery(obj).children().first(),
			    show_hide_class = 'sh-show sh-hide';
			    jQuery(obj).toggleClass(show_hide_class);
			    jQuery(obj).parent().next().toggleClass(show_hide_class).toggle();
			    toggle.text( (toggle.text() === more_text) ? less_text : more_text );
			    return( false );
			}
		</script>
	<?php else : ?>
	<script type="text/javascript">function showhide_toggle(o, m, l) {var  toggle = jQuery(o).children().first(), c = 'sh-show sh-hide'; jQuery(o).toggleClass(c); jQuery(o).parent().next().toggleClass(c).toggle(); toggle.text( (toggle.text() === m) ? l : m ); return( false );}</script>
	<?php endif; ?>
<?php
}
