<?php
$key_Specialisation = "Specialization";
$key_Source = "Source";
$key_Highest_Qualification = "Highest Qualification";
$key_Work_Experience_Years = "Years of Work Experience";
$key_Name = "Name";
$key_Degree_Title = "Degree Title";
$key_City = "City";
$key_Address = "Address";
$key_Email = "E-mail";
$key_Contact_Number = "Contact No";
$key_Gender = "Gender";
$key_Parents_Surname = "Parents Surname";
$key_Military_Obligations_Completed = "Military Obligations Completed";
$key_First_Degree_Type = "First Degree Type";
$key_Nationality = "Nationality";
$key_Marital_Status = "Marital Status";
$key_Number_of_Children = "Number of Children";
$key_SpouseSurname = "Spouse's Surname";
$key_Unemployment_Card = "Unemployment Card";
$key_Social_Security = "Social Security";
$key_DOY = "DOY";
$key_AMKA = "AMKA";
$key_ID= "ID(Number: return Date,AT)";
$key_AFM = "AFM";
$key_Desired_Salary = "Desired Salary";

function getQNumber ($str) {
	switch ($str){
	case 'Under Graduate': return 10 ;
	case 'Diploma': return 	 20 ;
	case 'Post Graduate': return  30 ;
	case 'Masters': return 	 40 ;
	case 'Graduate': return 	 50 ;
	case 'Doctorate': return 	 60 ;
	case 'Unknown': return 	 70 ;
	case 'Other': return 	 80 ;
	}
}
function getFinalResultNumber ($str) {
	switch ($str){
        case 'open': return 10;
	case 'Selected': return 20;
	case 'reopened': return 30;
	case 'On hold': return 40;
	case 'Rejected': return 50;
	case 'Own Refusal': return  60;
	case 'Require more review': return 70;
	case 'Require more interviews': return 80;
	case 'Other': return 90;
	}
}

function getSNumber ($str) {
	switch ($str){
	case 'Employee': return 	  	  10 ;
	case 'Newspaper': return 		  30 ;
	case 'Internet': return 		  50 ;
	case 'Website': return  		  70 ;
	case 'Internally Recommended': return  90 ;
	case 'Other': return  		 100 ;
	}
}
function getExperienceLevel($years) {

	$years = intval($years);

	if ($years < 1) {
		return 10;
	} else if ($years >= 1 and $years <= 2){
		return 20;
	} else if ($years >=3 and $years <= 5){
		return 30;
	} else if ($years >=6 and $years <= 8){
		return 40;
	} else if ($years >=9 and $years <=10){
		return 50; 
	} else if ($years >=10){
		return 60; 
	}	
}

#$map_highest_qualification = array     ('Under Graduate' =>  'FEATURE': return 
#					'Diploma' => 'TRIVIAL': return 
#					'Post Graduate' => 'TEXT': return 
#					'Masters' => 'TEXT': return 
#					'Graduate' => 'MINOR': return 
#					'Doctorate'  => 'MAJOR': return 		
#					'Unknown'  => 'CRASH': return 
#					'Other'  => 'BLOCK');
#$map_source = array 			('Employee' =>  'REPRODUCIBILITY_ALWAYS': return 
#				 	 'Newspaper' =>	'REPRODUCIBILITY_SOMETIMES': return 
#					 'Internet' =>	'REPRODUCIBILITY_RANDOM': return 
#					 'Website' =>	'REPRODUCIBILITY_HAVENOTTRIED': return 
#					 'Internally Recommended' =>	'REPRODUCIBILITY_UNABLETODUPLICATE': return 
#					 'Other' =>	'REPRODUCIBILITY_NOTAPPLICABLE');

?>
