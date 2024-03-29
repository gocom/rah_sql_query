<?php

/**
 * Rah_sql_query plugin for Textpattern CMS
 *
 * @author Jukka Svahn
 * @date 2008-
 * @license GNU GPLv2
 * @link http://rahforum.biz/plugins/rah_sql_query
 *
 * Copyright (C) 2012 Jukka Svahn <http://rahforum.biz>
 * Licensed under GNU Genral Public License version 2
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

	if(@txpinterface == 'admin') {
		add_privs('rah_sql_query', '1,2');
		add_privs('plugin_prefs.rah_sql_query', '1,2');
		register_tab('extensions', 'rah_sql_query', gTxt('rah_sql_query'));
		register_callback('rah_sql_query', 'rah_sql_query');
		register_callback('rah_sql_query_head', 'admin_side', 'head_end');
		register_callback('rah_sql_query_prefs', 'plugin_prefs.rah_sql_query');
		register_callback('rah_sql_query_cleanup', 'plugin_lifecycle.rah_sql_query', 'deleted');
	}

/**
 * The pane
 */

	function rah_sql_query() {
		
		require_privs('rah_sql_query');
		
		global $event, $prefs;
		
		/*
			Add pref if the message is signed
		*/
		
		if(gps('rah_sql_query_hidemsg')) {
			set_pref('rah_sql_query_hidemsg',1,'rah',PREF_HIDDEN,'text_input',0,PREF_PRIVATE);
			$prefs['rah_sql_query_hidemsg'] = 1;
		}
		
		$msg = '';
		
		$out[] = 
			'<form method="post" action="index.php" id="rah_sql_query_container" class="rah_ui_container">'.n.
			tInput().n.
			eInput($event).n;
		
		if(ps('query') && ps('_txp_token') == form_token()) {
			@$query = safe_query(ps('query'));
			$msg = $query !== FALSE ? gTxt('rah_sql_query_ok') : gTxt('rah_sql_query_error');
		}
		
		if(!isset($prefs['rah_sql_query_hidemsg']))
			$out[] = 
				'	<p>'.gTxt('rah_sql_query_notice').' <a href="?event='.$event.'&amp;rah_sql_query_hidemsg=1">'.gTxt('rah_sql_query_hide').'</a></p>'.n;
		
		$out[] =

			'	<p>'.n.
			'		<label>'.n.
			'			'.gTxt('rah_sql_query_to_run').'<br />'.n.
			'			<textarea name="query" rows="10" class="code" cols="30">'.htmlspecialchars(ps('query')).'</textarea>'.n.
			'		</label>'.n.
			'	</p>'.n.
			'	<p>'.n.
			'		<input type="submit" value="'.gTxt('rah_sql_query_go').'" class="publish" />'.n.
			'	</p>'.n;
		
		$out[] =
			'</form>';
			
		pagetop(gTxt('rah_sql_query'), $msg);
		echo implode('', $out);
	}

/**
 * Adds styles and JavaScript to the <head>
 */

	function rah_sql_query_head() {
		
		global $event;
		
		if($event != 'rah_sql_query')
			return;
		
		$string = gTxt('are_you_sure');
		
		echo <<<EOF
			<style type="text/css">
				#rah_sql_query_container {
					width: 650px;
					margin: 0 auto;
				}
				#rah_sql_query_container textarea {
					width: 100%;
				}
			</style>
			<script type="text/javascript">
				<!--
				$(document).ready(function(){
					$('form#rah_sql_query_container').submit(
						function() {
							return verify('{$string}');
						}
					)
				});
				-->
			</script>
EOF;
	}

/**
 * Do clean up when uninstalling. Removes added prefs.
 */

	function rah_sql_query_cleanup() {
		return 
			safe_delete(
				'txp_prefs',
				"name='rah_sql_query_hidemsg'"
			);
	}

/**
 * Redirect to the prefs pane
 */

	function rah_sql_query_prefs() {
		header('Location: ?event=rah_sql_query');
		echo 
			'<p>'.n.
			'	<a href="?event=rah_sql_query">'.gTxt('continue').'</a>'.n.
			'</p>';
	}
?>