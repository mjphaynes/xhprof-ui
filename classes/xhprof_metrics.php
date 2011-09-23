<?php 

class XHProf_Metrics {
	
	/**
	* Initialize the metrics we'll display based on the information
	* in the raw data.
	*/
	public function __construct(XHProf_Config &$config, $run_data, $rep_symbol, $sort, $diff_report = false) {
		
		$xhprof_data = $run_data['xhprof_data'];
		
		if (!empty($sort)) {
			if (array_key_exists($sort, $config->sortable_columns)) {
				$config->sort_col = $sort;
			} else {
				throw new Exception("Invalid Sort Key $sort specified in URL");
			}
		}
		
		// For C++ profiler runs, walltime attribute isn't present.
		// In that case, use "samples" as the default sort column.
		if (!isset($xhprof_data['main()']['wt'])) {
			if ($config->sort_col == 'wt') {
				$config->sort_col = 'samples';
			}

			// C++ profiler data doesn't have call counts.
			// ideally we should check to see if "ct" metric
			// is present for "main()". But currently "ct"
			// metric is artificially set to 1. So, relying
			// on absence of "wt" metric instead.
			$config->display_calls = false;
		} else {
			$config->display_calls = true;
		}

		$config->diff_mode = $diff_report;

		// parent/child report doesn't support exclusive times yet.
		// So, change sort hyperlinks to closest fit.
		if (!empty($rep_symbol)) {
			$config->sort_col = str_replace('excl_', '', $config->sort_col);
		}

		// $config->pc_stats = $config->stats = $config->display_calls ? array('fn', 'ct', 'Calls%') : array('fn');
		$config->pc_stats = $config->stats = $config->display_calls ? array('fn', 'ct') : array('fn');

		foreach ($config->possible_metrics as $metric => $desc) {
			if (isset($xhprof_data['main()'][$metric])) {
				$config->metrics[] = $metric;
				// flat (top-level reports): we can compute
				// exclusive metrics reports as well.
				$config->stats[] = $metric;
				// $config->stats[] = "I" . $desc[0] . "%";
				$config->stats[] = "excl_" . $metric;
				// $config->stats[] = "E" . $desc[0] . "%";

				// parent/child report for a function: we can
				// only breakdown inclusive times correctly.
				$config->pc_stats[] = $metric;
				$config->pc_stats[] = "I" . $desc[0] . "%";
			}
		}
	}

	/*
	* Get the list of metrics present in $xhprof_data as an array.
	*/
	public function get_metrics($possible_metrics, $xhprof_data) {
		// return those that are present in the raw data.
		// We'll just look at the root of the subtree for this.
		$metrics = array();
		foreach ($possible_metrics as $metric => $desc) {
			if (isset($xhprof_data["main()"][$metric])) {
				$metrics[] = $metric;
			}
		}

		return $metrics;
	}







	/**
	* Takes raw XHProf data that was aggregated over "$num_runs" number
	* of runs averages/nomalizes the data. Essentially the various metrics
	* collected are divided by $num_runs.
	*/
	function xhprof_normalize_metrics($raw_data, $num_runs) {

		if (empty($raw_data) || ($num_runs == 0)) {
			return $raw_data;
		}

		$raw_data_total = array();

		if (isset($raw_data["==>main()"]) && isset($raw_data["main()"])) {
			xhprof_error("XHProf Error: both ==>main() and main() set in raw data...");
		}

		foreach ($raw_data as $parent_child => $info) {
			foreach ($info as $metric => $value) {
				$raw_data_total[$parent_child][$metric] = ($value / $num_runs);
			}
		}

		return $raw_data_total;
	}



	

	
}