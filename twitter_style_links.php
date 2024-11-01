<?php
/*
Plugin Name: Twitter style links
Version: 1.1
Plugin URI: http://io.facciocose.it/2008/06/03/plugin-link-nei-commenti-stile-twitter/
Description: This plugin allows you to add Twitter style links to comments. If you write something like "@nick, how are you?" this plugin will search for @nick's url in the comment list of the current post and will convert his/her name to a link.
Author: neon
Author URI: http://io.facciocose.it
*/

/* questa funzione ritorna un array associativo
 * del tipo ("nick" -> "url", ...) contenente
 * tutti i valori dei commenti del post con l'id specificato
 */
function fc_get_authors($id) {
	global $wpdb;
	$out = array();
	$query = "SELECT comment_author AS author, comment_author_url AS url FROM $wpdb->comments WHERE comment_post_id='$id';";
	$temp = $wpdb->get_results($query);
	foreach ($temp as $i) {
		if (!empty($i->url) && ($i->url != "http://")) {
			$out[$i->author] = $i->url;
		}
	}
	return($out);
}

function fc_filter_comment($a) {
	$fc_url = fc_get_authors($a['comment_post_ID']);
	uksort($fc_url, 'fc_nick_cmp');
	foreach(array_keys($fc_url) as $i) {
		$a['comment_content'] = str_ireplace('@' . $i, '@<a href="' . $fc_url[$i] . '">' . $i . '</a>', $a['comment_content']);
	}
	return $a;
}

/* funzione di confronto per ordinare l'array
 * per nick dal più grande al più piccolo
 */
function fc_nick_cmp($a, $b) {
	if (strlen($a) == strlen($b))
		return 0;
	return (strlen($a) > strlen($b)) ? -1 : 1;
}

add_filter('preprocess_comment', 'fc_filter_comment');

?>
