<?php
#
# Created by Manos Maragkos 
# Module that imports CSV candidate form to COSMOS database
#
?>
<?php 
require_once( 'core.php' );
require_once ('Form.php');
require_once ('utils/LoadForm.php');
?>
<?php
	access_ensure_project_level( config_get( 'manage_tests_threshold' ) );
?>
<?php html_page_top1( lang_get( 'manage_link' ) ) ?>
<?php html_page_top2() ?>


<?php # import menu page Form BEGIN ?>
<br />
<div align="center">
<form method="post" action="manage_test.php">
<?php echo form_security_field( 'manage_tests_page' ); ?>
<table class="width75" cellspacing="1">
<tr>
	<td class="document-form" colspan="2">
		<?php echo lang_get( 'manage_tests_active' ) ?>
	</td>
</tr>
<?php
$test_file = array();

?>
<?php
$handle = fopen(config_get('tests_path') . config_get('active_test'), 'r');
$found_title = false;
while (($buffer = fgets($handle, 4096)) !== false) {
	if(stristr($buffer,'quiz')){
		$found_quiz = true;	
	}
	if ($found_quiz == true && stristr($buffer, 'title')){
		$found_title = true;
		$arr = array();
		$arr = explode(":", $buffer);
		$title = $arr[1];
		$found_quiz = false;
	}
}
if(!$found_title){
	$title = "Title of the active test is not found. Check if it is set correctly. ";
}
echo '<tr class="row-2">';
echo "<td class = \"fixed\">$title</td>";
echo '</tr>';
?>
</table>
<table class="width75_space" cellspacing="1">
<tr>
	<td class="document-form" colspan="3">
		<?php echo lang_get( 'manage_tests_available' ) ?>
	</td>
</tr>
<?php
$tests_pool_files = array();

$tests_pool = config_get('tests_pool_path') . '*.yml';
foreach (glob($tests_pool) as $filename) {
   array_push($tests_pool_files, "$filename");
}
?>
<?php 
foreach($tests_pool_files as $f){
echo '<tr class="row-1">';
echo "<td class=\"fixed\"> <input name=\"radio_list[]\" value=\"$f\" type=\"radio\"/> </td>";
$ff = basename($f);
echo "<td colspan=\"2\">$ff</td>";
echo '</tr>';
}
?>

<tr>
 <td class="left">    
  <input name="activate_test_button" type="submit" class="button" value="<?php echo lang_get( 'post_activate_test_button' ) ?>" />
 </td>
 <td class="left">    
  <input name="view_test_button" type="submit" class="button" value="<?php echo lang_get( 'post_view_test_button' ) ?>" />
  <input name="edit_test_button" type="submit" class="button" value="<?php echo lang_get( 'post_edit_test_button' ) ?>" />
 
<?php $t_link = 'http://' . config_get('p') . '/?test=active';
	 print_button_link( $t_link, lang_get( 'go_to_active_test' ), 'button' );?>
 </td>
 <td class="right">    
  <input name="delete_test_button" type="submit" class="button" value="<?php echo lang_get( 'post_delete_test_button' ) ?>" />
 </td>
</tr>

</table>
</form>
</div>


<div align="center">
<form method="post" action="create_test.php">
<?php echo form_security_field( 'manage_tests_page' ); ?>
<table class="width75_space" cellspacing="1">
<tr>
	<td class="document-form" colspan="2">
		<?php echo lang_get( 'manage_tests_new' ) ?>
	</td>
</tr>
<tr class="row-2">
<td> <input size="35" name="new_test" type="text"/> </td>
</tr>

<tr>
 <td class="left">    
  <input name="new_test_button" type="submit" class="button" value="<?php echo lang_get( 'post_new_test_button' ) ?>" />
 </td>
</tr>

</table>

</form>
</div>
<?php # Edit/Delete News Form END ?>

<?php html_page_bottom1( __FILE__ ) ?>
