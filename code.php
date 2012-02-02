<?php
/**
	Rah_sql_query v0.3
	Plugin for Textpattern
	by Jukka Svahn
	http://rahforum.biz

	Copyright (C) 2011 Jukka Svahn <http://rahforum.biz>
	Licensed under GNU Genral Public License version 2
	http://www.gnu.org/licenses/gpl-2.0.html
*/

	if(@txpinterface == 'admin') {
		add_privs('rah_sql_query', '1,2');
		add_privs('plugin_prefs.rah_sql_query','1,2');
		register_tab('extensions','rah_sql_query','Run Queries');
		register_callback('rah_sql_query','rah_sql_query');
		register_callback('rah_sql_query_head','admin_side','head_end');
		register_callback('rah_sql_query_prefs','plugin_prefs.rah_sql_query');
		register_callback('rah_sql_query_cleanup','plugin_lifecycle.rah_sql_query','deleted');
	}

/**
	The pane
*/

	function rah_sql_query() {
		
		global $event, $textarray, $txp_user, $prefs;
		
		/*
			Add pref if the message is signed
		*/
		
		if(
			gps('rah_sql_query_hidemsg') &&
			!isset($prefs['rah_sql_query_hidemsg'])
		) {
			safe_insert(
				'txp_prefs',
				"prefs_id=1,
				name='rah_sql_query_hidemsg',
				val='1',
				type=2,
				event='admin',
				html='',
				position=0,
				user_name='".doSlash($txp_user)."'"
			);
			$prefs['rah_sql_query_hidemsg'] = 1;
		}
		
		/*
			Make sure language strings
			are set
		*/
		
		foreach(
			array(
				'rah_sql_query_to_run' => 'Query to run',
				'rah_sql_query_go' => 'Run the query',
				'rah_sql_query_ok' => 'Query executed succesfully',
				'rah_sql_query_error' => 'Something gone terribly wrong with the query',
				'rah_sql_query_notice' => 'Please note that missused queries can cause loss of data, harm your database and/or Textpattern install. If you are unsure, remember to backup the database before running a query. Recommended to use only use on development and test installs, not on live sites.',
				'rah_sql_query_hide' => 'Hide the message'
			) as $string => $translation
		)
			if(!isset($textarray[$string]))
				$textarray[$string] = $translation;
		
		$msg = '';
		
		$out[] = 
			'<form method="post" action="index.php" id="rah_sql_query_container">'.n;
			
		/*
			Run the query if something was
			sent
		*/
		
		if(ps('query')) {
			@$query = safe_query(ps('query'));
			$msg = $query ? gTxt('rah_sql_query_ok') : gTxt('rah_sql_query_error');
		}
		
		if(!isset($prefs['rah_sql_query_hidemsg']))
			$out[] = 
				'	<p>'.gTxt('rah_sql_query_notice').' <a href="?event='.$event.'&amp;rah_sql_query_hidemsg=1">'.gTxt('rah_sql_query_hide').'</a>.</p>'.n;
		
		$out[] =
		
			'	<input type="hidden" name="event" value="'.$event.'" />'.n.
			'	<p>'.n.
			'		<label>'.n.
			'			'.gTxt('rah_sql_query_to_run').':<br />'.n.
			'			<textarea name="query" rows="10" class="code" cols="30">'.htmlspecialchars(ps('query')).'</textarea>'.n.
			'		</label>'.n.
			'	</p>'.n.
			'	<p>'.n.
			'		<input type="submit" value="'.gTxt('rah_sql_query_go').'" class="publish" />'.n.
			'	</p>'.n;
		
		$out[] =
			'</form>';
			
		pagetop('Run queries',$msg);
		echo implode('',$out);
	}

/**
	Adds styles and JavaScript to the <head>
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
				$(document).ready(function(){
					$('form#rah_sql_query_container').submit(
						function() {
							return verify('{$string}');
						}
					)
				});
			</script>
EOF;
	}

/**
	Do clean up when uninstalling. Removes added prefs.
*/

	function rah_sql_query_cleanup() {
		safe_delete(
			'txp_prefs',
			"name='rah_sql_query_hidemsg'"
		);
	}

/**
	Redirect to the prefs pane
*/

	function rah_sql_query_prefs() {
		header('Location: ?event=rah_sql_query');
		echo 
			'<p>'.n.
			'	<a href="?event=rah_sql_query">'.gTxt('continue').'</a>'.n.
			'</p>';
	}
?>