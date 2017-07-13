<?php
global $wpdb;
$table = $wpdb->prefix."DaySwitcher";

if (!empty($_POST)) {
	/* TRAITEMENT */
	if (isset($_POST['themes_submit'])) {
		$themes = get_themes();
		foreach ($themes as $theme) {
			$tName = md5($theme['Name']);
			if (in_array($tName, $_POST['themes'])) {
				if (!empty($_POST['template_hour_start_'.$tName]) && !empty($_POST['template_hour_end_'.$tName])) {
					$start = explode(":",$_POST['template_hour_start_'.$tName]);
					$end = explode(":",$_POST['template_hour_end_'.$tName]);
					
					$start 	= $start[0] * 60 + $start[1];
					$end 	= $end[0] * 60 + $end[1];
					
					$startM = ($_POST['template_month_start_'.$tName] !== "")
						? "'".$_POST['template_month_start_'.$tName]."'"
						: "''";
					
					$endM = ($_POST['template_month_end_'.$tName] !== "") 
						? "'".$_POST['template_month_end_'.$tName]."'"
						: $startM;
						
					$sql = "REPLACE INTO $table (name, month_start, month_end, hour_start, hour_end) VALUES ('$tName', $startM, $endM, $start, $end)";
					mysql_query($sql);
				}
			}
		}	
	}
}

$sql = "SELECT * FROM ".$table;
$a = mysql_query($sql);
$res = array();
while ($data = @mysql_fetch_assoc($a)) {
	$res[$data['name']] = $data;
}

$themes = get_themes();

$months = array(1=> 'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');

?>
<div class="wrap">
	<h2>Add a period for a theme</h2><br />
	Current hour : <strong><?php echo date('G').":".date('i'); ?></strong><br />
<em>For each theme you want to use with DaySwitcher, you must set a starting and ending month (same month in the two boxes if you want the theme to last one month, for instance March/March. Hours are in  12:34 format (with 24 hours).</em><br />
	<form method="post" action="">
		<table class="form-table">
			<?php
			foreach($themes as $theme) {
				$s = get_bloginfo('siteurl').'/wp-content/'.$theme['Template Dir'].'/'.$theme['Screenshot'];
				$img = '<img width="150" height="113" src="'.$s.'" />';
				$tPrint = md5($theme['Name']);
				
				$startValue = '';
				$endValue = '';
				$startMValue = '';
				$endMValue = '';
				
				if (isset($res[$tPrint])) {
					$startValue = str_pad(floor($res[$tPrint]['hour_start'] / 60),2,'0',STR_PAD_LEFT).':'.str_pad(($res[$tPrint]['hour_start'] % 60),2,'0',STR_PAD_LEFT);
					$endValue 	= str_pad(floor($res[$tPrint]['hour_end'] / 60),2,'0',STR_PAD_LEFT).':'.str_pad(($res[$tPrint]['hour_end'] % 60),2,'0',STR_PAD_LEFT);
					$startMValue = $res[$tPrint]['month_start'];
					$endMValue 	 = $res[$tPrint]['month_end'];
				}
//				$startMValue =7;
				echo '<input type="hidden" name="themes[]" value="'.$tPrint.'" >';
				echo '<tr valign="top">
			<th scope="row">';
				echo $theme['Name'].'<br />'.$img;
			echo '</th>
			<td>
				<fieldset>
					<legend class="hidden">'.$theme['Name'].'</legend>
						<label for="template_month_start_'.$tPrint.'">
						Starting month :<br /></label>';

						echo '<select name="template_month_start_'.$tPrint.'"><option value="">--</option>';
						foreach($months as $idx => $month) {
							echo '<option value="'.$idx.'"';
							if ($idx === intval($startMValue)) {
								echo ' selected';
							}
							echo '>'.$month.'</option>';
						}
						
						
						echo '</select>
						<br /><br />
						<label for="template_month_end_'.$tPrint.'">
						Ending month :<br />';
						
						echo '<select name="template_month_end_'.$tPrint.'"><option value="">--</option>';
						foreach($months as $idx => $month) {
							echo '<option value="'.$idx.'"';
							if ($idx === intval($endMValue)) {
								echo ' selected';
							}
							echo '>'.$month.'</option>';
						}
						echo '<br />
						<br />
				</fieldset>
			</td>
			<td>
				<fieldset>
					<legend class="hidden">'.$theme['Name'].'</legend>
						<label for="template_hour_start_'.$tPrint.'">
						Starting hour :<br /> <input name="template_hour_start_'.$tPrint.'" type="text" id="template_hour_start_'.$tPrint.'" value="'.$startValue.'" /> (ex: 21:00)</label>
						<br /><br />
						<label for="template_hour_end_'.$tPrint.'">
						Ending hour :<br /> <input name="template_hour_end_'.$tPrint.'" type="text" id="template_hour_end_'.$tPrint.'" value="'.$endValue.'" /> (ex: 07:59)<br />
						<br />
				</fieldset>
			</td>
		</tr>';

			}
			 ?>
			</table>
	<div class="submit"><input type="submit" name="themes_submit" value="Save" /></div>
	</form>
</div>
