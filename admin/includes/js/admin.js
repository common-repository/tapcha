jQuery(document).ready(function() {
	let admin_key = jQuery('#tapcha-admin-key')[0].value;

	if (admin_key != null  && admin_key.length > 0) {
		getStats(admin_key);
	}
});

function getStats(admin_key) {
	setLoading(true);
	jQuery.post({
		url: getApi() + '/stats/' + admin_key,
		dataType: 'json',
		type: 'GET',
		success: function (stats) {
			setLoading(false);
			showStats(stats);
		},
		error: function (data) {
			setLoading(false);
		},
		complete: function (data) { }
	});
}

function showStats(stats) {
	let success_rate_percentage = stats.success_rate_percentage;
	let response_rate_percentage = stats.response_rate_percentage;
	let number_of_challenges = stats.number_of_challenges;
	let number_of_responses = stats.number_of_responses;

	jQuery("#tapcha-success-rate").text(success_rate_percentage + "%");
	jQuery("#tapcha-response-rate").text(response_rate_percentage + "%");
	jQuery("#tapcha-number-of-challenges").text(number_of_challenges);
	jQuery("#tapcha-number-of-responses").text(number_of_responses);
}

function setLoading(isLoading) {
	let spinner = jQuery("#tapcha-stats-loading");
	let content = jQuery("#tapcha-stats");

	showView(spinner, isLoading);
	showView(content, !isLoading);
}

function showView(view, show) {
	if (show) {
		view.css('display', 'block');
	} else {
		view.css('display', 'none');
	}
}

function getApi() {
	let localApi = "http://localhost/api/v1";
	let productionApi = "http://api.tapcha.co.uk/api/v1";
	return productionApi;
}