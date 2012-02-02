<?php	##################
	#
	#	rah_sql_query-plugin for Textpattern
	#	version 0.1
	#	by Jukka Svahn
	#	http://rahforum.biz
	#
	###################

	if (@txpinterface == 'admin') {
		add_privs('rah_sql_query', '1,2');
		register_tab("extensions", "rah_sql_query", "Run Queries");
		register_callback("rah_sql_query", "rah_sql_query");
	}

	function rah_sql_query() {
		pagetop('rah_sql_query');
		
		echo 
			'<form method="post" action="index.php" style="width:800px;margin:0 auto;">'.n.
			'	<h1>rah_sql_query: Run Queries</h1>'.n.
			'	<input type="hidden" name="event" value="rah_sql_query" />'.n.
			'	<input type="hidden" name="query_do" value="1" />'.n.
			'	<label for="query">Query to run:</label>'.n.
			'	<textarea id="query" name="query" rows="10" cols="30" style="width:790px;"></textarea>'.n.
			'	<button style="margin:10px;" type="submit">Run Query</button>'.n.((ps('query') && ps('query_do')) ?
					'	<hr /><p>Done Query:</p><pre>'.htmlspecialchars(ps('query')).'</pre>'.n.
					'	<hr />'.n.
					'	<p>Returned:</p><pre>'.safe_query(ps('query')).'</pre>'.n : '').
			'	<p style="margin: 15px auto;width: 600px;padding:15px;line-height:1.65em;border:1px solid #cc0000;background: #ffffc0;color: #000;">'.n.
			'		<strong style="color: #cc0000;">Warning!</strong> If you don\'t know what you are doing, don\'t never run a query! Missused queries can permanently loss your data, harm your MySQL-database and/or Textpattern itself. If you are any ensure, remember to backup first your Textpattern database. Recommended to only use on development and test installs, not in live sites.'.n.
			'	</p>'.n.
			'</form>';	
	}?>