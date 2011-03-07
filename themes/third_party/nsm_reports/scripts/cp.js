(function($){

	// Plugin for tab and form functionality
	$.fn.NSM_Reports_Config = function(opts) {

		return this.each(function() {

			var obj = {
				id: this.id,
				dom: {
					$container: $(this),
					$output_trigger: $("#nsm_reports-generate-output", this),
					$email: $("#nsm_reports-generate-email", this),
					$email_row: $("#nsm_reports-generate-email", this).parent().parent(),
					$report_name: $("#nsm_reports-generate-save_report_name", this),
					$report_description: $("#nsm_reports-generate-save_report_description", this),
					$preset_container: $("#nsm_reports-generate-report-preset-container"),
					$action_trigger: $("#nsm_reports-generate-report-action")
				},
				options : opts
			}
			
			obj.dom.$output_trigger.bind('change', function(event) {
				var condition = $(this).val();
				if(condition == 'browser'){
					obj.dom.$email_row.hide();
				}else{
					obj.dom.$email_row.show();
				}
				obj.dom.$container.trigger('update');
			});
			
			obj.dom.$action_trigger.bind('change', function(event) {
				var condition =  $(this).val();
				if(condition == 'generate'){
					obj.dom.$preset_container.hide();
				}else{
					obj.dom.$preset_container.show();
				}
				obj.dom.$container.trigger('update');
			});
			
			obj.dom.$output_trigger.trigger('change');
			obj.dom.$action_trigger.trigger('change');
			
		});
		
		
		
		// private function for debugging
		function debug($obj) {
			if (window.console && window.console.log)
			window.console.log($obj);
		};

	};
	
	$.fn.NSM_Reports_Config.defaults = {};

	$('#nsm-report-config table').NSM_Reports_Config();

})(jQuery);