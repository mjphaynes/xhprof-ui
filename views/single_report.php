<?php 

?>
	<div>
		<table id="xhprof_summary">
			<tr>
				<th style='text-align:right'>Overall Summary</th>
				<th></th>
			</tr>
<?php 
foreach ($config->metrics as $metric) {
?>
			<tr>
				<td>Total <?php echo str_replace('<br>', ' ', $config->descriptions[$metric]);?>:</td>
				<td><?php echo number_format($config->totals[$metric]).' '.$config->possible_metrics[$metric][1];?></td>
			</tr>
<?php 
}
if ($config->display_calls) {
?>
			<tr>
				<td>Number of Function Calls:</td>
				<td><?php echo number_format($config->totals['ct']);?></td>
			</tr>
<?php
}
?>
		</table>
	</div>
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

$desc = str_replace('<br>', ' ', $config->descriptions[$config->sort_col]);

if (!empty($url_params['all'])) {
	$all = true;
	$limit = 0;    // display all rows
	$title = "Sorted by $desc";
} else {
	$all = false;
	$limit = 100;  // display only limited number of rows
	$title = "Displaying top $limit functions: Sorted by $desc";
}

include XHPROF_ROOT.'/views/flat_data.php';
