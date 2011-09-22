<?php 

  // if (!$limit) {              // no limit
  //   $limit = $size;
  //   $display_link = "";
  // } else {
  //   $display_link = xhprof_render_link(" [ <b class=bubble>display all </b>]",                                       "$base_path/?" .                                       http_build_query(xhprof_array_set($url_params,                                                                         'all', 1)));
  // }

?>
		<h3 align=center>$title $display_link</h3>
		<table id="stats" class="zebra-striped">
<?php foreach (array('thead', 'tfoot') as $t) {?>
			<?php echo "<$t>";?>
				<tr>
<?php 
foreach ($config->stats as $stat) {
	$desc = $config->{($config->diff_mode ? 'diff_' : '').'descriptions'}[$stat];

	if (array_key_exists($stat, $config->sortable_columns)) {
		$header = '<a href="/?'.http_build_query(array_merge($params, array('sort' => $stat))).'">'.$desc.'</a>';
	} else {
		$header = $desc;
	}
?>
					<th class="<?php echo $stat;?>"><nobr><?php echo $header;?></th>
<?php 
}
?>
				</tr>
			<?php echo "</$t>";?>
<?php }?>
			<tbody>
<?php 

$size  = count($flat_data);
$neg_limit = $limit < 0;
$limit = min($size, $limit);
for ($i = 0; $i < $limit; $i++) {
	$info = $neg_limit ? $flat_data[$size - $i - 1] : $flat_data[$i];
?>
			<tr><td><a href="<?php echo '/?'.http_build_query(array_merge($params, array('symbol' => $info['fn'])));?>"><?php echo $info["fn"];?></td><?php
	if ($config->display_calls) {
		echo XHProf_Utils::td_num($info['ct'], $config->format_cbk['ct'], ($config->sort_col == 'ct'));
		echo XHProf_Utils::td_pct($info['ct'], $config->totals['ct'], ($config->sort_col == 'ct'));
	}
	
	foreach ($config->metrics as $metric) {
	  // Inclusive metric
	  echo XHProf_Utils::td_num($info[$metric], $config->format_cbk[$metric], ($config->sort_col == $metric));
	  echo XHProf_Utils::td_pct($info[$metric], $config->totals[$metric], ($config->sort_col == $metric));

	  // Exclusive Metric
	  echo XHProf_Utils::td_num($info['excl_' . $metric], $config->format_cbk['excl_' . $metric], ($config->sort_col == 'excl_' . $metric));
	  echo XHProf_Utils::td_pct($info['excl_' . $metric], $config->totals[$metric], ($config->sort_col == 'excl_' . $metric));
	}
?></tr>
<?php 
}
?>
			</tbody>
		</table>
<?php 