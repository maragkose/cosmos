<?php
#
# Created by Manos Maragkos 
# Module that imports CSV candidate form to COSMOS database
#
?>
<?php require_once( 'core.php' ) ?>
<?php
	access_ensure_project_level( config_get( 'manage_news_threshold' ) );
?>
<?php html_page_top1( lang_get( 'import_link' ) ) ?>
<?php html_page_top2() ?>


<?php # import menu page Form BEGIN ?>
<br />
<div align="center">
<form method="post" action="import_csv.php">
<?php echo form_security_field( 'import_page' ); ?>
<table class="width75" cellspacing="1">
<tr>
	<td class="document-form" colspan="2">
		<?php echo lang_get( 'import_page_title' ) ?>
	</td>
</tr>
<?php
$csv_files = array();

foreach (glob("/var/ftp/cosmos/*.csv") as $filename) {
   array_push($csv_files, "$filename");
}
?>
<?php 
foreach($csv_files as $csv){
echo '<tr class="row-1">';
echo "<td> <input name=\"check_list[]\" value=\"$csv\" type=\"checkbox\"/> </td>";
echo "<td>$csv</td>";
echo '</tr>';
}
?>

<tr>
 <td class="left">    
  <input name="delete_button" type="submit" class="button" value="<?php echo lang_get( 'post_import_delete_button' ) ?>" />
 </td>
 <td class="left">    
  <input name="import_all_button" type="submit" class="button" value="<?php echo lang_get( 'post_import_all_button' ) ?>" />
  <input name="import_selected_button" type="submit" class="button" value="<?php echo lang_get( 'post_import_selected_button' ) ?>" />
 </td>
</tr>
</table>

<table class="width75_space" cellspacing="1">
<tr>
	<td class="document-form" colspan="2">
		<?php echo lang_get( 'import_tests_title' ) ?>
	</td>
</tr>
<?php
$answer_files = array();

$str = config_get('answers_path') . '*.' . config_get('answers_ext');
foreach (glob("$str") as $answer) {
   array_push($answer_files, basename("$answer"));
}
?>
<?php 
foreach($answer_files as $csv){
echo '<tr class="row-1">';
echo "<td> <input name=\"check_list[]\" value=\"$csv\" type=\"checkbox\"/> </td>";
echo "<td>$csv</td>";
echo '</tr>';
}
?>
<tr>
 	<td class="left">    
  	<input name="evaluate_delete_button" type="submit" class="button" value="<?php echo lang_get( 'post_evaluate_delete_button' ) ?>" />
 	</td>
 	<td class="left">    
  	<input name="evaluate_button" type="submit" class="button" value="<?php echo lang_get( 'post_evaluate_button' ) ?>" />
 	</td>
</tr>
</table>
</form>
</div>
<?php # Edit/Delete News Form END ?>

<?php html_page_bottom1( __FILE__ ) ?>
