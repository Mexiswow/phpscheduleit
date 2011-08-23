function Calendar(opts, reservations)
{
	var _options = opts;
	var _reservations = reservations;
	
	Calendar.prototype.init = function()
	{
		$('#calendar').fullCalendar({
			header: '',
			editable: false,
			defaultView: _options.view,
			year: _options.year,
			month: _options.month-1,
			date: _options.date,
			events: _reservations,
			eventRender: function(event, element) { element.attachReservationPopup(event.id); },
			dayClick: function(date) { dayClick(date); },
			dayNames: _options.dayNames,
			dayNamesShort: _options.dayNamesShort,
			monthNames: _options.monthNames,
			monthNamesShort: _options.monthNamesShort,
			weekMode: 'variable'
		});

		$('.fc-widget-content').hover(
			function() {
				$(this).addClass('hover');
			},
				
			function() {
				$(this).removeClass('hover');
			}
		);

		$(".reservation").each(function() {
			var refNum = $(this).attr('refNum');
			$(this).attachReservationPopup(refNum);
		});

		$('#calendarFilter').change(function() {
			var day = getQueryStringValue('d');
			var month = getQueryStringValue('m');
			var year = getQueryStringValue('y');
			var type = getQueryStringValue('ct');
			var scheduleId = '';
			var resourceId = '';

			if ($(this).hasClass('schedule'))
			{
				scheduleId = '&sid=' + $(this).val();
			}
			else
			{
				resourceId = '&rid=' + $(this).val();
			}

			var url = 'calendar.php?ct=' + type + '&d=' + day + '&m=' + month + '&y=' + year + scheduleId + resourceId;
			
			window.location = url;
		});
	};

	var dayClick = function(date)
	{
		var month =  date.getMonth()+1;
		window.location = _options.dayClickUrl + '&y=' + date.getFullYear() + '&m=' + month + '&d=' + date.getDate();
	}

}