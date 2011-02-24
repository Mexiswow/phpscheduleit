<script type="text/javascript"> 
  $("#{$ControlId}").datepicker({ldelim} 
		 defaultDate: new Date({$DefaultDate->Year()}, {$DefaultDate->Month()-1}, {$DefaultDate->Day()}),
		 numberOfMonths: {$NumberOfMonths},
		 showButtonPanel: {$ShowButtonPanel},
		 onSelect: {$OnSelect},
		 dayNames: {$DayNames},
		 dayNamesShort: {$DayNamesShort},
		 dayNamesMin: {$DayNamesMin},
		 dateFormat: '{$DateFormat}',
		 firstDay: {$FirstDay},
		 monthNames: {$MonthNames},
		 monthNamesShort: {$MonthNamesShort},
		 currentText: "{translate key='Today'}"
  {rdelim});
</script>