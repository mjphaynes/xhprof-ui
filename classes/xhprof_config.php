<?php 

class XHProf_Config {

	public $possible_metrics =  array(
		'wt'      => array('Wall', '&micro;s', 'walltime'),
		'ut'      => array('User', '&micro;s', 'user cpu time'),
		'st'      => array('Sys', '&micro;s', 'system cpu time'),
		'cpu'     => array('Cpu', '&micro;s', 'cpu time'),
		'mu'      => array('MUse', 'bytes', 'memory usage'),
		'pmu'     => array('PMUse', 'bytes', 'peak memory usage'),
		'samples' => array('Samples', 'samples', 'cpu time')
	);

	// The following column headers are sortable
	public $sortable_columns = array(
		'fn'           => 1,
		'ct'           => 1,
		'wt'           => 1,
		'excl_wt'      => 1,
		'ut'           => 1,
		'excl_ut'      => 1,
		'st'           => 1,
		'excl_st'      => 1,
		'mu'           => 1,
		'excl_mu'      => 1,
		'pmu'          => 1,
		'excl_pmu'     => 1,
		'cpu'          => 1,
		'excl_cpu'     => 1,
		'samples'      => 1,
		'excl_samples' => 1
	);

	// Textual descriptions for column headers in 'single run' mode
	public $descriptions = array(
		'fn'           => 'Function Name',
		'ct'           => 'Calls',

		'wt'           => 'Inc. Wall Time (&micro;s)',
		'excl_wt'      => 'Ex. Wall Time (&micro;s)',

		'ut'           => 'Inc. User (&micro;s)',
		'excl_ut'      => 'Ex. User (&micro;s)',

		'st'           => 'Inc. Sys (&micro;s)',
		'excl_st'      => 'Ex. Sys (&micro;s)',

		'cpu'          => 'Inc. CPU (&micro;s)',
		'excl_cpu'     => 'Ex. CPU (&micro;s)',

		'mu'           => 'Incl. MemUse (bytes)',
		'excl_mu'      => 'Excl. MemUse (bytes)',

		'pmu'          => 'Incl. Peak MemUse (bytes)',
		'excl_pmu'     => 'Excl. Peak MemUse (bytes)',

		'samples'      => 'Incl. Samples',
		'excl_samples' => 'Excl. Samples',
	);

	// Formatting Callback Functions...
	public $format_cbk = array(
		'fn'           => '',
		'ct'           => array('XHProf_Utils', 'count_format'),
		'Calls%'       => array('XHProf_Utils', 'percent_format'),

		'wt'           => 'number_format',
		'IWall%'       => array('XHProf_Utils', 'percent_format'),
		'excl_wt'      => 'number_format',
		'EWall%'       => array('XHProf_Utils', 'percent_format'),

		'ut'           => 'number_format',
		'IUser%'       => array('XHProf_Utils', 'percent_format'),
		'excl_ut'      => 'number_format',
		'EUser%'       => array('XHProf_Utils', 'percent_format'),

		'st'           => 'number_format',
		'ISys%'        => array('XHProf_Utils', 'percent_format'),
		'excl_st'      => 'number_format',
		'ESys%'        => array('XHProf_Utils', 'percent_format'),

		'cpu'          => 'number_format',
		'ICpu%'        => array('XHProf_Utils', 'percent_format'),
		'excl_cpu'     => 'number_format',
		'ECpu%'        => array('XHProf_Utils', 'percent_format'),

		'mu'           => 'number_format',
		'IMUse%'       => array('XHProf_Utils', 'percent_format'),
		'excl_mu'      => 'number_format',
		'EMUse%'       => array('XHProf_Utils', 'percent_format'),

		'pmu'          => 'number_format',
		'IPMUse%'      => array('XHProf_Utils', 'percent_format'),
		'excl_pmu'     => 'number_format',
		'EPMUse%'      => array('XHProf_Utils', 'percent_format'),

		'samples'      => 'number_format',
		'ISamples%'    => array('XHProf_Utils', 'percent_format'),
		'excl_samples' => 'number_format',
		'ESamples%'    => array('XHProf_Utils', 'percent_format'),
	);

	// Textual descriptions for column headers in 'diff' mode
	public $diff_descriptions = array(
		'fn'           => 'Function Name',
		'ct'           => 'Calls Diff',
		'Calls%'       => 'Calls Diff%',

		'wt'           => 'Inc. Wall Diff (&micro;s)',
		'excl_wt'      => 'Ex. Wall Diff (&micro;s)',

		'ut'           => 'Inc. User Diff (&micro;s)',
		'excl_ut'      => 'Ex. User Diff (&micro;s)',

		'cpu'          => 'Inc. CPU Diff (&micro;s)',
		'excl_cpu'     => 'Ex. CPU Diff (&micro;s)',

		'st'           => 'Inc. Sys Diff (&micro;s)',
		'excl_st'      => 'Ex. Sys Diff (&micro;s)',

		'mu'           => 'Inc. MemUse Diff (bytes)',
		'excl_mu'      => 'Ex. MemUse Diff (bytes)',

		'pmu'          => 'Inc.  Peak MemUse Diff (bytes)',
		'excl_pmu'     => 'Ex. Peak MemUse Diff (bytes)',

		'samples'      => 'Inc. Samples Diff',
		'excl_samples' => 'Ex. Samples Diff',
	);

	// default column to sort on -- wall time
	public $sort_col = 'wt';

	// default is 'single run' report
	public $diff_mode = false;

	// call count data present?
	public $display_calls = true;

	// columns that'll be displayed in a top-level report
	public $stats = array();

	// columns that'll be displayed in a function's parent/child report
	public $pc_stats = array();

	// Various total counts
	public $totals   = 0;
	public $totals_1 = 0;
	public $totals_2 = 0;

	/*
	* The subset of $possible_metrics that is present in the raw profile data.
	*/
	public $metrics = null;
	
}