<?php

?>
		<table id="stats" class="zebra-striped">
<?php foreach (array('thead', 'tfoot') as $t) {?>
			<?php echo "<$t>";?> 
				<tr>
<?php 
foreach ($ui->stats as $stat) {
	$desc = $ui->config->{($ui->diff_mode ? 'diff_' : '').'descriptions'}[$stat];

	if (array_key_exists($stat, $ui->config->sortable_columns)) {
		$header = '<a href="'.$ui->url(array('sort' => $stat)).'">'.$desc.'</a>';
	} else {
		$header = $desc;
	}
?>
					<th class="<?php if ($ui->config->sort == $stat) echo 'headerSortUp blue';?>"<?php if ($stat != 'fn') echo ' colspan="2"';?>><?php echo $header;?></th>
<?php 
}
?>
				</tr>
			<?php echo "</$t>";?> 
<?php }?>
			<tbody>
				<tr>
					<td>#<?php echo $ui->runs[0]->run_id?> summary:</td>
<?php if ($ui->display_calls) {?>
					<td colspan="2" class="center"><?php echo number_format($ui->totals['ct']);?></td>
<?php }?>
<?php foreach ($ui->metrics as $metric) {?>
					<td colspan="2" class="center"><?php echo number_format($ui->totals[$metric]).' '.$ui->config->possible_metrics[$metric][1];?></td>
					<td colspan="2" class="center">&ndash;</td>
<?php }?>
				</tr>
<?php 

// $size  = count($data);
// $neg_limit = $limit < 0;
// $limit = min($size, $limit);
for ($i = 0; $i < count($data); $i++) {
	// $info = $neg_limit ? $data[$size - $i - 1] : $data[$i];
	$info = $data[$i];
?>
			<tr><td class="fn"><a href="<?php echo $ui->url(array('fn' => XHProf_UI\Utils::safe_symbol($info['fn'])));?>"><?php echo $info['fn'];?></td><?php
	if ($ui->display_calls) {
		echo XHProf_UI\Utils::td_num($info['ct'], $ui->config->format_cbk['ct'], ($ui->sort == 'ct'));
		echo XHProf_UI\Utils::td_pct($info['ct'], $ui->totals['ct'], ($ui->sort == 'ct'));
	}
	
	foreach ($ui->metrics as $metric) {
		// Inclusive metric
		echo XHProf_UI\Utils::td_num($info[$metric], $ui->config->format_cbk[$metric], ($ui->sort == $metric));
		echo XHProf_UI\Utils::td_pct($info[$metric], $ui->totals[$metric], ($ui->sort == $metric));

		// Exclusive Metric
		echo XHProf_UI\Utils::td_num($info['excl_'.$metric], $ui->config->format_cbk['excl_' . $metric], ($ui->sort == 'excl_' . $metric));
		echo XHProf_UI\Utils::td_pct($info['excl_'.$metric], $ui->totals[$metric], ($ui->sort == 'excl_' . $metric));
	}
?></tr>
<?php 
}
?>
			</tbody>
		</table>
<?php 