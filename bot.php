#!/usr/bin/php
<?PHP

#error_reporting(E_ERROR|E_CORE_ERROR|E_ALL|E_COMPILE_ERROR);

require_once ( '/data/project/quickstatements/public_html/quickstatements.php' ) ;

function iterate() {
	$ret = 0 ;
	$qs = new QuickStatements ;
	$db = $qs->getDB() ;
	$pipes = array() ;
	$sql = "SELECT id,status FROM batch WHERE status IN ('INIT','RUN')" ;
	if(!$result = $db->query($sql)) die('There was an error running the query [' . $db->error . ']');
	while($o = $result->fetch_object()){
//		$pipes[] = popen ( "/data/project/quickstatements/run_single_command.php {$o->id}" , 'r' ) ;

		$qs2 = new QuickStatements ;
		if ( $o->status == 'INIT' ) {
			if ( !$qs2->startBatch ( $o->id ) ) {
				print $qs2->last_error_message."\n" ;
				continue ;
			}
		}
		if ( !$qs2->runNextCommandInBatch ( $o->id ) ) print $qs2->last_error_message."\n" ;
		else $ret++ ;

	}
/*
	foreach ( $pipes AS $pipe ) {
		$ret++ ;
		pclose ( $pipe ) ;
	}
*/
	return $ret ;
}

while ( 1 ) {
	$worked = iterate() ;
	if ( $worked == 0 ) sleep ( 5 ) ;
}

?>