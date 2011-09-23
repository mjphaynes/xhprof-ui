<?php 


// $callgraph_report_title = '[View Full Callgraph]';
// print("<center><br><h3>" .$callgraph_report_title."$base_path/callgraph.php" . "?" . http_build_query($url_params)."</h3></center>");

$flat_data = array();
foreach ($symbol_tab as $symbol => $info) {
	$flat_data[] = $info + array('fn' => $symbol);
}
usort($flat_data, function($a, $b) use ($config) {
	return XHProf_Utils::sort_cbk($a, $b, $config);
});

include XHPROF_ROOT.'/views/flat_data.php';
