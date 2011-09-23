<?php








/**
 * Return attribute names and values to be used by javascript tooltip.
 */
function get_tooltip_attributes($type, $metric) {
  return "type='$type' metric='$metric'";
}

/**
 * Print info for a parent or child function in the
 * parent & children report.
 *
 * @author Kannan
 */
function pc_info($info, $base_ct, $base_info, $parent) {
  global $sort_col;
  global $metrics;
  global $format_cbk;
  global $display_calls;

  if ($parent)
    $type = "Parent";
  else $type = "Child";

  if ($display_calls) {
    $mouseoverct = get_tooltip_attributes($type, "ct");
    /* call count */
    print_td_num($info["ct"], $format_cbk["ct"], ($sort_col == "ct"), $mouseoverct);
    print_td_pct($info["ct"], $base_ct, ($sort_col == "ct"), $mouseoverct);
  }

  /* Inclusive metric values  */
  foreach ($metrics as $metric) {
    print_td_num($info[$metric], $format_cbk[$metric],
                 ($sort_col == $metric),
                 get_tooltip_attributes($type, $metric));
    print_td_pct($info[$metric], $base_info[$metric], ($sort_col == $metric),
                 get_tooltip_attributes($type, $metric));
  }
}

function print_pc_array($url_params, $results, $base_ct, $base_info, $parent,
                        $run1, $run2) {
  global $base_path;

  // Construct section title
  if ($parent) {
    $title = 'Parent function';
  }
  else {
    $title = 'Child function';
  }
  if (count($results) > 1) {
    $title .= 's';
  }

  print("<tr bgcolor='#e0e0ff'><td>");
  print("<b><i><center>" . $title . "</center></i></b>");
  print("</td></tr>");

  $odd_even = 0;
  foreach ($results as $info) {
    $href = "$base_path/?" .
      http_build_query(xhprof_array_set($url_params,
                                        'symbol', $info["fn"]));

    $odd_even = 1 - $odd_even;

    if ($odd_even) {
      print('<tr>');
    }
    else {
      print('<tr bgcolor="#e5e5e5">');
    }

    print("<td>" . xhprof_render_link($info["fn"], $href));
    print_source_link($info);
    print("</td>");
    pc_info($info, $base_ct, $base_info, $parent);
    print("</tr>");
  }
}



function print_symbol_summary($symbol_info, $stat, $base) {

  $val = $symbol_info[$stat];
  $desc = str_replace("<br>", " ", stat_description($stat));

  print("$desc: </td>");
  print(number_format($val));
  print(" (" . pct($val, $base) . "% of overall)");
  if (substr($stat, 0, 4) == "excl") {
    $func_base = $symbol_info[str_replace("excl_", "", $stat)];
    print(" (" . pct($val, $func_base) . "% of this function)");
  }
  print("<br>");
}


