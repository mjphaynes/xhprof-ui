<?php 


class XHProf_Utils {
	
	/**
	 * Type definitions for URL params
	 */
	const STRING_PARAM = 1;
	const UINT_PARAM   = 2;
	const FLOAT_PARAM  = 3;
	const BOOL_PARAM   = 4;

	
	/**
	 * Initialize params from URL query string. The function
	 * creates globals variables for each of the params
	 * and if the URL query string doesn't specify a particular
	 * param initializes them with the corresponding default
	 * value specified in the input.
	 *
	 * @params array $params An array whose keys are the names
	 *                       of URL params who value needs to
	 *                       be retrieved from the URL query
	 *                       string. PHP globals are created
	 *                       with these names. The value is
	 *                       itself an array with 2-elems (the
	 *                       param type, and its default value).
	 *                       If a param is not specified in the
	 *                       query string the default value is
	 *                       used.
	 */
	public static function parse_params($params) {

		/* Create variables specified in $params keys, init defaults */
		foreach ($params as $k => &$v) {
			$p = XHProf_Utils::_get_param($k, $v[1]);
			
			switch ($v[0]) {
				case XHProf_Utils::STRING_PARAM:
					$v = $p;
				break;
				case XHProf_Utils::UINT_PARAM:
					$v = filter_var($p, FILTER_VALIDATE_INT);
				break;
				case XHProf_Utils::FLOAT_PARAM:
					$v = filter_var($p, FILTER_VALIDATE_FLOAT);
				break;
				case XHProf_Utils::BOOL_PARAM:
					$v = filter_var($p, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
				break;
				default:
					throw new Exception('Invalid param type passed to xhprof_param_init: '.$v[0]);
				break;
			}

		}
		
		return $params;
	}
	
	/**
	 * Extracts value for string param $param from query
	 * string. If param is not specified, return the
	 * $default value.
	 *
	 * @param string   name of the URL query string param
	 */
	private static function _get_param($name, $default = '') {
		if (isset($_GET[$name])) {
			return $_GET[$name];
		
		} elseif (isset($_POST[$name])) {
			return $_POST[$name];

		} elseif (isset($_REQUEST[$name])) {
			return $_REQUEST[$name];
		}

		return $default;
	}
	

	
	
	


	/**
	* Analyze hierarchical raw data, and compute per-function (flat)
	* inclusive and exclusive metrics.
	*
	* Also, store overall totals in the 2nd argument.
	*
	* @param  array $raw_data          XHProf format raw profiler data.
	* @param  array &$overall_totals   OUT argument for returning
	*                                  overall totals for various
	*                                  metrics.
	* @return array Returns a map from function name to its
	*               call count and inclusive & exclusive metrics
	*               (such as wall time, etc.).
	*/
	function compute_flat_info(XHProf_Config $config, XHProf_Metrics $metrics, $raw_data) {

		$available_metrics = $metrics->get_metrics($config->possible_metrics, $raw_data);
		
		$config->totals = array(
			'ct'      => 0,
			'wt'      => 0,
			'ut'      => 0,
			'st'      => 0,
			'cpu'     => 0,
			'mu'      => 0,
			'pmu'     => 0,
			'samples' => 0
		);

		// compute inclusive times for each function
		$symbol_tab = XHProf_Utils::compute_inclusive_times($raw_data, $available_metrics, $config->display_calls);

		/* total metric value is the metric value for 'main()' */
		foreach ($available_metrics as $metric) {
			$config->totals[$metric] = $symbol_tab['main()'][$metric];
		}

		/*
		* initialize exclusive (self) metric value to inclusive metric value
		* to start with.
		* In the same pass, also add up the total number of function calls.
		*/
		foreach ($symbol_tab as $symbol => $info) {
			foreach ($available_metrics as $metric) {
				$symbol_tab[$symbol]['excl_' . $metric] = $symbol_tab[$symbol][$metric];
			}
			if ($config->display_calls) {
				/* keep track of total number of calls */
				$config->totals['ct'] += $info['ct'];
			}
		}

		/* adjust exclusive times by deducting inclusive time of children */
		foreach ($raw_data as $parent_child => $info) {
			list($parent, $child) = XHProf_Utils::parse_parent_child($parent_child);

			if ($parent) {
				foreach ($available_metrics as $metric) {
					// make sure the parent exists hasn't been pruned.
					if (isset($symbol_tab[$parent])) {
						$symbol_tab[$parent]['excl_' . $metric] -= $info[$metric];
					}
				}
			}
		}

		return $symbol_tab;
	}

	

	/**
	* Compute inclusive metrics for function. This code was factored out
	* of ompute_flat_info().
	*
	* The raw data contains inclusive metrics of a function for each
	* unique parent function it is called from. The total inclusive metrics
	* for a function is therefore the sum of inclusive metrics for the
	* function across all parents.
	*
	* @return array  Returns a map of function name to total (across all parents)
	*                inclusive metrics for the function.
	*/
	function compute_inclusive_times($raw_data, $metrics, $display_calls) {
		$symbol_tab = array();

		/*
		* First compute inclusive time for each function and total
		* call count for each function across all parents the
		* function is called from.
		*/
		foreach ($raw_data as $parent_child => $info) {
			list($parent, $child) = XHProf_Utils::parse_parent_child($parent_child);

			if ($parent == $child) {
				/*
				* XHProf PHP extension should never trigger this situation any more.
				* Recursion is handled in the XHProf PHP extension by giving nested
				* calls a unique recursion-depth appended name (for example, foo@1).
				*/
				throw new Exception("Error in Raw Data: parent & child are both: $parent");
				return;
			}

			if (!isset($symbol_tab[$child])) {
				if ($display_calls) {
					$symbol_tab[$child] = array('ct' => $info['ct']);
				} else {
					$symbol_tab[$child] = array();
				}
				foreach ($metrics as $metric) {
					$symbol_tab[$child][$metric] = $info[$metric];
				}
			} else {
				if ($display_calls) {
					/* increment call count for this child */
					$symbol_tab[$child]['ct'] += $info['ct'];
				}

				/* update inclusive times/metric for this child  */
				foreach ($metrics as $metric) {
					$symbol_tab[$child][$metric] += $info[$metric];
				}
			}
		}

		return $symbol_tab;
	}

	/**
	 * Takes a parent/child function name encoded as
	 * "a==>b" and returns array("a", "b").
	 *
	 * @author Kannan
	 */
	public static function parse_parent_child($parent_child) {
		$ret = explode('==>', $parent_child);

		// Return if both parent and child are set
		if (isset($ret[1])) {
			return $ret;
		}

		return array(null, $ret[0]);
	}

	/**
	 * Given parent & child function name, composes the key
	 * in the format present in the raw data.
	 *
	 * @author Kannan
	 */
	public static function build_parent_child_key($parent, $child) {
		if ($parent) {
			return $parent.'==>'.$child;
		} else {
			return $child;
		}
	}

	


	
	
	
	/**
	 * Callback comparison operator (passed to usort() for sorting array of
	 * tuples) that compares array elements based on the sort column
	 * specified in $sort_col (global parameter).
	 *
	 * @author Kannan
	 */
	public function sort_cbk($a, $b, XHProf_Config $config) {
		if ($config->sort_col == 'fn') {
			// case insensitive ascending sort for function names
			$left = strtoupper($a['fn']);
			$right = strtoupper($b['fn']);

		} else {
			// descending sort for all others
			$left = $a[$config->sort_col];
			$right = $b[$config->sort_col];

			// if diff mode, sort by absolute value of regression/improvement
			if ($config->diff_mode) {
				$left = abs($left);
				$right = abs($right);
			}
		}

		return ($left == $right) ? 0 : (($left > $right) ? -1 : 1);
	}
	
	/**
	 * Computes percentage for a pair of values, and returns it
	 * in string format.
	 */
	function pct($a, $b) {
		if ($b == 0) {
			return "N/A";
		} else {
			$res = (round(($a * 1000 / $b)) / 10);
			return $res;
		}
	}

	/**
	 * Given a number, returns the td class to use for display.
	 *
	 * For instance, negative numbers in diff reports comparing two runs (run1 & run2)
	 * represent improvement from run1 to run2. We use green to display those deltas,
	 * and red for regression deltas.
	 */
	function td_class($num, $bold, $diff_mode = false) {
		if ($bold) {
			if ($diff_mode) {
				if ($num <= 0) {
					$class = 'green'; // green (improvement)
				} else {
					$class = 'red'; // red (regression)
				}
			} else {
				$class = 'blue'; // blue
			}
		} else {
			$class = 'black';  // default (black)
		}

		return $class;
	}

	/**
	 * Prints a <td> element with a numeric value.
	 */
	public static function td_num($num, $fmt_func, $bold = false, $attributes = null, $diff_mode = false) {
		$class = XHProf_Utils::td_class($num, $bold);

		if (!empty($fmt_func)) {
			$num = call_user_func($fmt_func, $num);
		}

		return "<td class=\"right $class\">$num</td>";
	}

	/**
	 * Prints a <td> element with a pecentage.
	 */
	public static function td_pct($numer, $denom, $bold = false, $attributes = null, $diff_mode = false) {
		$class = XHProf_Utils::td_class($numer, $bold);

		if ($denom == 0) {
			$pct = "N/A%";
		} else {
			$pct = XHProf_Utils::percent_format($numer / abs($denom));
		}

		return "<td class=\"right $class\">$pct</td>";
	}
	
	
	/*
	 * Formats call counts for XHProf reports.
	 *
	 * Description:
	 * Call counts in single-run reports are integer values.
	 * However, call counts for aggregated reports can be
	 * fractional. This function will print integer values
	 * without decimal point, but with commas etc.
	 *
	 *   4000 ==> 4,000
	 *
	 * It'll round fractional values to decimal precision of 3
	 *   4000.1212 ==> 4,000.121
	 *   4000.0001 ==> 4,000
	 *
	 */
	public static function count_format($num) {
		$num = round($num, 3);
		if (round($num) == $num) {
			return number_format($num);
		} else {
			return number_format($num, 3);
		}
	}

	public static function percent_format($s, $precision = 1) {
		return sprintf('%.'.$precision.'f%%', 100 * $s);
	}



	
	
}