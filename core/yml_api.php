<?php
require_once( 'includes/spyc.php' );

#==================================================================================	
class yml {
#==================================================================================	

	var $loaded_test = array();      
	var $filename = "";      
	var $view = array();      

	const first_date_str  = '1st Interview Date';

	#==================================================================================	
	public function __construct($filename) {
	#==================================================================================	
		$this->i_headers = "From: COSMOS@nsn.com\n"; 
		$this->filename = $filename;
		$this->loaded_test = Spyc::YAMLLoad($this->filename);
	}
	#==================================================================================	
	public function view_set($set, $section) {
	#==================================================================================	
		$this->view[] =  '<tr class="row-3">';
		$this->view[] =  '<td class="login_cat" colspan="2">';
		$this->view[] =  $section;
		$this->view[] =  '</td>';
		$this->view[] =  '</tr>';

		$c=1;
		foreach ($set as $key => $value) {
			$s = ($c % 2) + 1;
			$this->view[] =  "<tr class=\"row-$s\">";
			$this->view[] =  '<td class="bold">';
			$this->view[] =  "$key"; 
			$this->view[] =  '</td>';
			$this->view[] =  '<td class="bold">';
			$this->view[] =  $value['texto'];
			$this->view[] =  '</td>';
			$this->view[] =  '</tr>';
			$c++;
		}
	}
	#==================================================================================	
	public function view() {
	#==================================================================================
		
		$set1_items = $this->loaded_test['set1']['items'];
		$set2_items = $this->loaded_test['set2']['items'];
		$set3_items = $this->loaded_test['set3']['items'];
		$this->view[] =  '<div align="center">';
		$this->view[] =  '<table class="width75_space" cellspacing="1">';
		$this->view[] =  '<tr>';
		$this->view[] =  '<td class="document-form" colspan="1">';
		$this->view[] =  'Questions';
		$this->view[] =  '</td>';
		$this->view[] =  '<td class="document-form" colspan="2">';
		$this->view[] =  'Content'; 
		$this->view[] =  '</td>';
		$this->view_set($set1_items, 'C Section');
		$this->view_set($set2_items, 'Linux Section');
		$this->view_set($set3_items, 'IP Section');
		$this->view[] =  '</tr>';
		$this->view[] =  '</table>';
		$this->view[] =  '</form>';
		$this->view[] =  '</div>';

		foreach($this->view as $v){	
			echo $v;
		}
	}
}
?>
