<?php 

class XHProf_Report {

	
	/**
	 * Analyze raw data & generate the profiler report
	 * (common for both single run mode and diff mode).
	 */
	public function __construct(XHProf_Config $config, XHProf_Metrics $metrics, array $params, $symbol, $sort, array $run_data) {

		// if we are reporting on a specific function, we can trim down
		// the report(s) to just stuff that is relevant to this function.
		// That way compute_flat_info()/compute_diff() etc. do not have
		// to needlessly work hard on churning irrelevant data.
		if (!empty($rep_symbol)) {
			$run_data['xhprof_data'] = xhprof_trim_run($run_data['xhprof_data'], array($symbol));

			// if ($config->diff_mode) {
			// 	$run2_data = xhprof_trim_run($run2_data, array($symbol));
			// }
		}

		// if ($config->diff_mode) {
		// 	$run_delta = xhprof_compute_diff($run1_data, $run2_data);
		// 	$symbol_tab  = xhprof_compute_flat_info($run_delta, $totals);
		// 	$symbol_tab1 = xhprof_compute_flat_info($run1_data, $totals_1);
		// 	$symbol_tab2 = xhprof_compute_flat_info($run2_data, $totals_2);
		// 
		// } else {
			$symbol_tab = XHProf_Utils::compute_flat_info($config, $metrics, $run_data['xhprof_data']);
		// }

		$run1_txt = sprintf("<b>Run #%s:</b> %s", $run_data['run_id'], $run_data['description']);

		$top_link_query_string = "/?" . http_build_query(array_filter(
			array_merge(
				$params,
				array(
					'all' => null,
					'symbol' => null,
				)
			)
		));

		// if ($config->diff_mode) {
		// 	$diff_text = "Diff";
		// 
		// 	$base_url_params = xhprof_array_unset($base_url_params, 'run1');
		// 	$base_url_params = xhprof_array_unset($base_url_params, 'run2');
		// 
		// 	$run1_link = xhprof_render_link('View Run #' . $run1, "$base_path/?".http_build_query(xhprof_array_set($base_url_params, 'run', $run1)));
		// 	$run2_txt = sprintf("<b>Run #%s:</b> %s", $run2, $run2_desc);
		// 	$run2_link = xhprof_render_link('View Run #' . $run2, "$base_path/?" . http_build_query(xhprof_array_set($base_url_params, 'run', $run2)));
		// 
		// } else {
			// $diff_text = "Run";
		// }

		// set up the action links for operations that can be done on this report
		// $links = array();
		// $links [] =  xhprof_render_link("View Top Level $diff_text Report", $top_link_query_string);

		// if ($diff_mode) {
		// 	$inverted_params = $url_params;
		// 	$inverted_params['run1'] = $url_params['run2'];
		// 	$inverted_params['run2'] = $url_params['run1'];
		// 
		// 	// view the different runs or invert the current diff
		// 	$links [] = $run1_link;
		// 	$links [] = $run2_link;
		// 	$links [] = xhprof_render_link('Invert ' . $diff_text . ' Report', "$base_path/?". http_build_query($inverted_params));
		// }

		// lookup function typeahead form
		// $links [] = '<input class="function_typeahead"  type="input" size="40" maxlength="100" />';

		// echo
		//   '<dl class=phprof_report_info>' .
		//   '  <dt>' . $diff_text . ' Report</dt>' .
		//   '  <dd>' . ($diff_mode ?
			//               $run1_txt . '<br><b>vs.</b><br>' . $run2_txt :
			//               $run1_txt) .
			//   '  </dd>' .
			//   '  <dt>Tip</dt>' .
			//   '  <dd>Click a function name below to drill down.</dd>' .
			//   '</dl>' .
			//   '<div style="clear: both; margin: 3em 0em;"></div>';


		// data tables
		// if (!empty($symbol)) {
		// 	if (!isset($symbol_tab[$symbol])) {
		// 		echo "<hr>Symbol <b>$rep_symbol</b> not found in XHProf run</b><hr>";
		// 		return;
		// 	}
		// 
		// 	/* single function report with parent/child information */
		// 	if ($diff_mode) {
		// 		$info1 = isset($symbol_tab1[$rep_symbol]) ? $symbol_tab1[$rep_symbol] : null;
		// 		$info2 = isset($symbol_tab2[$rep_symbol]) ?	$symbol_tab2[$rep_symbol] : null;
		// 		symbol_report($url_params, $run_delta, $symbol_tab[$rep_symbol], $sort, $rep_symbol, $run1, $info1, $run2, $info2);
		// 	} else {
		// 		symbol_report($url_params, $run1_data, $symbol_tab[$rep_symbol], $sort, $rep_symbol, $run1);
		// 	}
		// } else {
			/* flat top-level report of all functions */
			
			
			include XHPROF_ROOT.'/views/single_report.php';
		// }
	}


	
}