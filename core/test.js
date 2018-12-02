function SwitchDateFields() {
	document.filters_open.start_month.disabled = ! document.filters_open.do_filter_by_date.checked;
	document.filters_open.start_day.disabled = ! document.filters_open.do_filter_by_date.checked;
	document.filters_open.start_year.disabled = ! document.filters_open.do_filter_by_date.checked;
	document.filters_open.end_month.disabled = ! document.filters_open.do_filter_by_date.checked;
	document.filters_open.end_day.disabled = ! document.filters_open.do_filter_by_date.checked;
	document.filters_open.end_year.disabled = ! document.filters_open.do_filter_by_date.checked;

	return true;
}
