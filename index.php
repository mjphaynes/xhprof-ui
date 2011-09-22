<?php

define('XHPROF_ROOT', realpath(__DIR__));
require_once XHPROF_ROOT.'/classes/xhprof_ui.php';


/**
 * @param string  $source       Category/type of the run. The source in
 *                              combination with the run id uniquely
 *                              determines a profiler run.
 *
 * @param string  $run          run id, or comma separated sequence of
 *                              run ids. The latter is used if an aggregate
 *                              report of the runs is desired.
 *
 * @param string  $wts          Comma separate list of integers.
 *                              Represents the weighted ratio in
 *                              which which a set of runs will be
 *                              aggregated. [Used only for aggregate
 *                              reports.]
 *
 * @param string  $symbol       Function symbol. If non-empty then the
 *                              parent/child view of this function is
 *                              displayed. If empty, a flat-profile view
 *                              of the functions is displayed.
 *
 * @param string  $run1         Base run id (for diff reports)
 *
 * @param string  $run2         New run id (for diff reports)
 *
 */
$xhprof_ui = new XHProf_UI(
	array(
		'run'    => array(XHProf_Utils::STRING_PARAM, ''),
		'wts'    => array(XHProf_Utils::STRING_PARAM, ''),
		'symbol' => array(XHProf_Utils::STRING_PARAM, ''),
		'sort'   => array(XHProf_Utils::STRING_PARAM, 'wt'),
		'run1'   => array(XHProf_Utils::STRING_PARAM, ''),
		'run2'   => array(XHProf_Utils::STRING_PARAM, ''),
		'source' => array(XHProf_Utils::STRING_PARAM, 'xhprof'),
		'all'    => array(XHProf_Utils::UINT_PARAM, 0),
	)
);

$xhprof_ui->generate_report();