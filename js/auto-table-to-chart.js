function create_data(graph_type,graph_title,array_labels,array_values,obj_setup,array_id_title,array_values_title){
	//get the max value out of the array_values
	var height_graph=obj_setup.graph_h;
	var max_array=Math.max.apply( Math, array_values[0] );
	var steps=Math.ceil(height_graph/max_array);
	var max_y=(steps*Math.ceil(max_array/steps));
	var array_pie=[];
	var size_3d=0;
	var obj_elements=[];
	if(graph_type == 'pie'){
		for(i=0;i<array_values[0].length;i++){
			array_pie.push({"value": array_values[0][i],"label": array_labels[i] +":"+array_values[0][i]});
		}
		obj_elements[0]={
	      "type": graph_type,
	      "values":array_values[0] ,
	       "colours": [
	        obj_setup.x_axis_colour,
	        obj_setup.y_axis_colour
	      ],
	      "gradient-fill": true,
	  	  "start-angle": 35,
	  	  "values": array_pie
	    };
	}else{
		if(graph_type == 'bar_3d'){
			var size_3d=3;
			var bottom_colour='#E2E2E2';
		}else{
			var bottom_colour=obj_setup.x_axis_colour;
		}
		for(x=0;x<array_values.length;x++){
			if(x % 2 == 0){
		       cl = obj_setup.x_axis_colour;
		    }else{
		       cl = obj_setup.y_axis_colour;
		    } 
			obj_elements[x]={
		      "type": graph_type,
		      "alpha":0.1,
		      "values":array_values[x] ,
		      "colour": cl,
		      "text": array_values_title[x]
		    };
		    //todo need to increase/change the color value each time
		}
	}
	var data = {
	  "elements": obj_elements,
	  "title": {
	    "text": graph_title
	  },
	  "x_axis": {
	    "3d":  size_3d,
	    "colour": bottom_colour,
	    "grid-colour": obj_setup.x_grid_colour,
	    "labels": {
	    	"labels": array_labels
	    }
	  },
	  "x_legend": {
	    "text": array_id_title[0],
	    "style": "{font-size: 12px; color: #000033}"
	    },
	  "y_axis": {
	    "colour": obj_setup.y_axis_colour,
	    "grid-colour": obj_setup.y_grid_colour,
	    "max": max_y,
	    "steps": steps
	  },
	  "bg_colour": obj_setup.bg_colour
	};
	return data;
}

function flash_chart_data(passed_string){
	var re=new RegExp('[^0-9]', "g");
	var passed_array=passed_string.split(',');
	var table_id=passed_array[0];
	var obj_setup={
		"bg_colour":passed_array[1],
		"y_grid_colour":passed_array[2],
		"y_axis_colour":passed_array[3],
		"x_axis_colour":passed_array[4],
		"x_grid_colour":passed_array[5],
		"graph_h":passed_array[6],
		"graph_w":passed_array[7],
		"chart_type":passed_array[8]
	};
	var table_elm=$('#'+table_id);
	var the_var ='';
 	var th_count=table_elm.find("thead tr th").length;
 	var array_id_column=[];
 	var array_values_column=[];
 	var array_id=[];
 	var array_values=[];
 	var chart_type=obj_setup.chart_type;
 	var chart_name=table_elm.attr("summary");
 	var array_id_title=[];
 	var array_values_title=[];
 	table_elm.find("thead tr th").each(function(i) {
 		if($(this).hasClass("graph_id")){
 			array_id_column.push(i+1);
 			array_id_title.push($(this).attr("title"));
 		}
    	if($(this).hasClass("graph_value")){
 			array_values_column.push(i+1);
 			array_values_title.push($(this).attr("title"));
 		}
 	});
 	
 	var values_array_counter=-1;
 	for(i=1;i<=th_count;i++){
 		//create new sub array
 		if(jQuery.inArray(i,array_id_column) !=-1){
 			b_array_id=true;
 			b_array_values=false;
 		}
 		if(jQuery.inArray(i,array_values_column)!=-1){
 			//update the array counter
	    	values_array_counter+=1;
 			array_values[values_array_counter]=[];
 			b_array_id=false;
 			b_array_values=true;
 		}
 		
	 	table_elm.find("tbody tr td:nth-child("+i+")").each(function(x) {
	 		if(b_array_id){
	 			array_id.push(jQuery.trim($(this).text()));
	 		}
	 		if(b_array_values){
	 			//values must to numbers so strip any text
	 			array_values[values_array_counter].push(parseInt($(this).text().replace(re,'')));
	 		}
	    });
 	}
 	
 	the_data=create_data(chart_type,chart_name,array_id,array_values,obj_setup,array_id_title,array_values_title);	
 	return JSON.stringify(the_data);		
}
$(function() {
	var the_src='open-flash-chart.swf';//default to current directory
	$(document.getElementsByTagName('script')).each(function(i) {
		var the_index=this.src.indexOf('openflashchart');
		if(the_index!=-1){
			var new_src=this.src;
			//new_src.substr(0,the_index+15);
			new_src=new_src.substr(0,the_index+15)+the_src;
			the_src=new_src;
			return false;
		}
	});
	//console.log(the_src);
	var flash_chart_path=the_src;
	var flash_div_name='flash_chart_';
	//default setup
	var flash_chart_h=300;
	var flash_chart_w=500;
	var flash_chart_bg='#ffffff';
	var	flash_chart_y_grid='#E2E2E2';
	var	flash_chart_y_axis='#000066';
	var	flash_chart_x_axis='#F65327';
	var	flash_chart_x_grid='#E2E2E2';
	var chart_type='bar';
	$(".graph_table").each(function(i) {
		//insert the div for the flash object	
		var current_name=flash_div_name+$(this).attr("id");
		$($(this)).after('<div id="'+current_name+'">hello</div>');
		//give the div the flash chart class so it can be read
		var current_el=$('#'+current_name);
		chart_type=get_chart_type(this);
		current_el.addClass("flash_chart_setup");
		if(current_el.css("background-image") !='none'){
			flash_chart_w=parseInt(current_el.css("width"));
			flash_chart_h=parseInt(current_el.css("height"));
			flash_chart_bg=rgb2html(current_el.css("background-color"));
			flash_chart_y_grid=rgb2html(current_el.css("border-top-color"));
			flash_chart_y_axis=rgb2html(current_el.css("border-left-color"));
			flash_chart_x_axis=rgb2html(current_el.css("border-right-color"));
			flash_chart_x_grid=rgb2html(current_el.css("border-bottom-color"));
		}
		current_el.removeClass("flash_chart_setup");
		
		var pass_string=$(this).attr("id")+','+flash_chart_bg+','+flash_chart_y_grid+','+flash_chart_y_axis+','+flash_chart_x_axis+','+flash_chart_x_grid+','+flash_chart_h+','+flash_chart_w+','+chart_type;
		//add the flash to the page pointing at the table for its data
		swfobject.embedSWF(flash_chart_path, current_name, flash_chart_w,flash_chart_h, "9.0.0", "expressInstall.swf",{"get-data":"flash_chart_data","id":pass_string} );
		//now add the links to show a chart or data
		show_hide_chart($('#'+current_name),this);
	});
	$("a.flash_chart_link").click(function(event){
	  	event.preventDefault();
	    var the_id=$(this).attr("id").substr(16);
	    var flash_chart=$('#flash_chart_'+the_id);
	    if (flash_chart.is(":hidden")) {
	    	flash_chart.show();
	    	$(this).text("Hide chart");
	    }else{
	    	flash_chart.hide();
	    	$(this).text("Show chart");
	    }
	});
	$("a.html_table_link").click(function(event){
	  	event.preventDefault();
	    var the_id=$(this).attr("id").substr(16);
	    var html_table=$('#'+the_id);
	    if (html_table.is(":hidden")) {
	    	html_table.show();
	    	$(this).text("Hide table");
	    }else{
	    	html_table.hide();
	    	$(this).text("Show table");
	    }
	});
});

function rgb2html(rgb_string) {
	hex=rgb_string;
	if(hex.indexOf('#') == -1){
		//replace the rgb test etc with nothing, to leave the commas and numbers
		var re = new RegExp('[rgb(]|[)]|[ ]', "g");
		triplet=rgb_string.replace(re,'').split(',');
		var hex_alphabets = "0123456789ABCDEF";
		var hex = "#";
		var int1,int2;
		for(var i=0;i<3;i++) {
			int1 = triplet[i] / 16;
			int2 = triplet[i] % 16;
			hex += hex_alphabets.charAt(int1) + hex_alphabets.charAt(int2); 
		}
	}
	return(hex);
}
function get_chart_type(table_el){
	var output='bar';
	if( $(table_el).is(".chart_type_pie") ) {
    	output='pie';
	}
	if( $(table_el).is(".chart_type_bar") ) {
    	output='bar';
	}
	if( $(table_el).is(".chart_type_bar_3d") ) {
    	output='bar_3d';
	}
	return output;
}
function show_hide_chart(flash_chart,html_table){
	var current_name=$(html_table).attr("id");
	$(html_table).before('<div class="show_hide_container"><a href="##" id="show_hide_flash_'+current_name+'" class="icon_link flash_chart_link">Show/hide chart</a><a href="##" id="show_hide_table_'+current_name+'" class="icon_link html_table_link">Show/hide table</a></div>');
	if ($(html_table).is(".chart_hidden")) {
		$(flash_chart).hide();
	}
	if ($(html_table).is(".table_hidden")) {
		$(html_table).hide();
	}
}	