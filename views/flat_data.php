<?php

?>
		<h2>Sorted by <?php echo str_replace('<br>', ' ', $config->descriptions[$config->sort_col]);?></h3>
		<table id="stats" class="zebra-striped">
<?php foreach (array('thead', 'tfoot') as $t) {?>
			<?php echo "<$t>";?> 
				<tr>
<?php 
foreach ($config->stats as $stat) {
	$desc = $config->{($config->diff_mode ? 'diff_' : '').'descriptions'}[$stat];

	if (array_key_exists($stat, $config->sortable_columns)) {
		$header = '<a href="/?'.http_build_query(array_filter(array_merge($params, array('sort' => $stat)))).'">'.$desc.'</a>';
	} else {
		$header = $desc;
	}
?>
					<th class="<?php if ($config->sort_col == $stat) echo 'headerSortUp blue';?>"<?php if ($stat != 'fn') echo ' colspan="2"';?>><?php echo $header;?></th>
<?php 
}
?>
				</tr>
			<?php echo "</$t>";?> 
<?php }?>
			<tbody>
				<tr>
					<td>Totals:</td>
<?php if ($config->display_calls) {?>
					<td colspan="2" class="center"><?php echo number_format($config->totals['ct']);?></td>
<?php }?>
<?php foreach ($config->metrics as $metric) {?>
					<td colspan="2" class="center"><?php echo number_format($config->totals[$metric]).' '.$config->possible_metrics[$metric][1];?></td>
					<td colspan="2" class="center">&ndash;</td>
<?php }?>
				</tr>
<?php 

// $size  = count($flat_data);
// $neg_limit = $limit < 0;
// $limit = min($size, $limit);
for ($i = 0; $i < count($flat_data); $i++) {
	// $info = $neg_limit ? $flat_data[$size - $i - 1] : $flat_data[$i];
	$info = $flat_data[$i];
?>
			<tr><td class="fn"><a href="<?php echo '/?'.http_build_query(array_merge($params, array('symbol' => $info['fn'])));?>"><?php echo $info['fn'];?></td><?php
	if ($config->display_calls) {
		echo XHProf_Utils::td_num($info['ct'], $config->format_cbk['ct'], ($config->sort_col == 'ct'));
		echo XHProf_Utils::td_pct($info['ct'], $config->totals['ct'], ($config->sort_col == 'ct'));
	}
	
	foreach ($config->metrics as $metric) {
		// Inclusive metric
		echo XHProf_Utils::td_num($info[$metric], $config->format_cbk[$metric], ($config->sort_col == $metric));
		echo XHProf_Utils::td_pct($info[$metric], $config->totals[$metric], ($config->sort_col == $metric));

		// Exclusive Metric
		echo XHProf_Utils::td_num($info['excl_'.$metric], $config->format_cbk['excl_' . $metric], ($config->sort_col == 'excl_' . $metric));
		echo XHProf_Utils::td_pct($info['excl_'.$metric], $config->totals[$metric], ($config->sort_col == 'excl_' . $metric));
	}
?></tr>
<?php 
}
?>
			</tbody>
		</table>
<?php 