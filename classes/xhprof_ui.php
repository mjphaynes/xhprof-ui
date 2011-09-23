<?php 

require_once XHPROF_ROOT.'/classes/xhprof_utils.php';
require_once XHPROF_ROOT.'/classes/xhprof_config.php';
require_once XHPROF_ROOT.'/classes/xhprof_metrics.php';
require_once XHPROF_ROOT.'/classes/xhprof_report.php';

class XHProf_UI {
	
	private $_params = array();
	
	private $_dir = '';
	
	private $_suffix = 'xhprof';
	
	function __construct($params, $dir = null) {
		
		$this->_params = XHProf_Utils::parse_params($params);
		
	    // if user hasn't passed a directory location,
	    // we use the xhprof.output_dir ini setting
	    // if specified, else we default to the directory
	    // in which the error_log file resides.
		if (
			(!empty($dir) && !is_dir($dir)) || 
			(empty($dir) && !is_dir($dir = ini_get('xhprof.output_dir')))
		) {
			throw new Exception('Warning: Must specify directory location for XHProf runs. '.
				'You can either pass the directory location as an argument to the constructor or set xhprof.output_dir ini param.');
		}

		$this->_dir = $dir;

	}
	
	/**
	 * Generate a XHProf Display View given the various params
	 *
	 */
	function generate_report() {
		include XHPROF_ROOT.'/views/header.php';
		
		$config = new XHProf_Config();
		
		extract($this->_params, EXTR_SKIP);

		// specific run to display?
		if ($run) {
			// run may be a single run or a comma separate list of runs
			// that'll be aggregated. If "wts" (a comma separated list
			// of integral weights is specified), the runs will be
			// aggregated in that ratio.
			$runs = explode(',', $run);

			if (count($runs) == 1) {
				$run_data = $this->get_run($runs[0], $source);

			} else {
				$wts = strlen($wts) > 0 ? explode(',', $wts) : null;
				
				$data = xhprof_aggregate_runs($xhprof_runs_impl, $runs_array, $wts_array, $source, false);
				$xhprof_data = $data['raw'];
				$description = $data['description'];
			}

			if ($run_data) {
				$metrics = new XHProf_Metrics($config, $run_data, $symbol, $sort, false);

				new XHProf_Report($config, $metrics, $this->_params, $symbol, $sort, $run_data);
				include XHPROF_ROOT.'/views/footer.php';
				return true;
			}

		// diff report for two runs
		} else if ($run1 && $run2) {
			$run_data1 = $this->get_run($run1, $source);
			$run_data2 = $this->get_run($run2, $source);
			
			$metrics = new XHProf_Metrics($config, $run_data2, $symbol, $sort, true);

			// profiler_diff_report($url_params, $xhprof_data1, $description1, $xhprof_data2, $description2, $symbol, $sort, $run1, $run2);
			// init_metrics($xhprof_data2, $rep_symbol, $sort, true);
			// profiler_report($url_params,$rep_symbol, $sort, $run1, $run1_desc, $xhprof_data1,$run2,$run2_desc,$xhprof_data2);
			new XHProf_Report($config, $metrics, $this->_params, $symbol, $sort, $run_data1, $run_data2);
			include XHPROF_ROOT.'/views/footer.php';
			return true;
		}

		echo "No XHProf runs specified in the URL.";

		$this->list_runs();

		return false;
	}

	private function file_name($run_id, $source) {
		return (!empty($this->_dir) ? "{$this->_dir}/" : '')."$run_id.$source";
	}
	
	public function get_run($run_id, $source) {
		if (!file_exists($file_name = $this->file_name($run_id, $source))) {
			return null;
		}

		return array(
			'run_id' => $run_id,
			'source' => $source,
			'description' => $source,
			'xhprof_data' => unserialize(file_get_contents($file_name))
		);
	}

	public function list_runs() {
		echo "<hr/>Existing runs:\n<ul>\n";

		foreach (glob("{$this->_dir}/*") as $file) {
			list($run, $source) = explode('.', basename($file));

			echo '<li><a href="' . htmlentities($_SERVER['SCRIPT_NAME'])
				. '?run=' . htmlentities($run) . '&source='
				. htmlentities($source) . '">'
				. htmlentities(basename($file)) . "</a><small> "
				. date("Y-m-d H:i:s", filemtime($file)) . "</small></li>\n";
		}

		echo "</ul>\n";
	}

	

}