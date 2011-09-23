<?php 
namespace XHProf_UI\Report;

class Diff {

	
	/**
	 * Analyze raw data & generate the profiler report
	 * (common for both single run mode and diff mode).
	 */
	public function __construct(XHProf_Config $config, XHProf_Metrics $metrics, array $params, $symbol, $sort, array $run_data) {

		if (!empty($rep_symbol)) {
			$run_data['xhprof_data'] = xhprof_trim_run($run_data['xhprof_data'], array($symbol));
		// 	$run2_data = xhprof_trim_run($run2_data, array($symbol));
		}

		// 	$run_delta = xhprof_compute_diff($run1_data, $run2_data);
		// 	$symbol_tab  = xhprof_compute_flat_info($run_delta, $totals);
		// 	$symbol_tab1 = xhprof_compute_flat_info($run1_data, $totals_1);
		// 	$symbol_tab2 = xhprof_compute_flat_info($run2_data, $totals_2);
		// 	$base_url_params = xhprof_array_unset($base_url_params, 'run1');
		// 	$base_url_params = xhprof_array_unset($base_url_params, 'run2');
		// 
		// 	$run1_link = xhprof_render_link('View Run #' . $run1, "$base_path/?".http_build_query(xhprof_array_set($base_url_params, 'run', $run1)));
		// 	$run2_txt = sprintf("<b>Run #%s:</b> %s", $run2, $run2_desc);
		// 	$run2_link = xhprof_render_link('View Run #' . $run2, "$base_path/?" . http_build_query(xhprof_array_set($base_url_params, 'run', $run2)));
		// 



		// data tables
		// if (!empty($symbol)) {
		// 		$info1 = isset($symbol_tab1[$rep_symbol]) ? $symbol_tab1[$rep_symbol] : null;
		// 		$info2 = isset($symbol_tab2[$rep_symbol]) ?	$symbol_tab2[$rep_symbol] : null;
		// 		symbol_report($url_params, $run_delta, $symbol_tab[$rep_symbol], $sort, $rep_symbol, $run1, $info1, $run2, $info2);
		// 
		// }
			
			
			include XHPROF_ROOT.'/views/single_report.php';
		// }
	}


	
}