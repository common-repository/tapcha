jQuery(document).ready(function() {
	let siteKey = jQuery("#tapcha-site-key")[0].value;

	generateTapcha(siteKey);

	jQuery("#tapcha-reload").on('click.reload', function () {
		generateTapcha(siteKey);
		jQuery("#tapcha-submit").off('click.submit');
	});
});

function generateTapcha(siteKey) {
	showTapchaLoading(true);
	hideAlerts();
	jQuery.post({
		url: `${getApi()}/challenge/${siteKey}`,
		dataType: 'json',
		type: 'GET',
		success: function (data) {
			renderChallenge(data);
			showTapchaLoading(false);
		},
		error: function (data) {
			let statusCode = data.status;
			if (statusCode === 405) {
				console.log("Invalid site key or host!");
			} else if (statusCode === 422) {
				let validationErrorJSON = data.responseJSON;
				if ("site_key" in validationErrorJSON) {
					console.log("Site key not found!");
					showInvalidDomainAlert(true);
				} else {
					console.log("Error 422! Data:");
					console.log(data);
				}
			} else {
				console.log("Error! Data:");
				console.log(data);
			}
		},
		complete: function () { }
	});
}

function showTapchaLoading(show) {
	showView("#tapcha", !show);
	showView("#tapcha-loading", show);
}

function renderChallenge(data) {
	let challengeId = data.challenge_id;
	let coordinates = data.coordinates;
	let leftShapes = coordinates.left;
	let rightShapes = coordinates.right;
	let shapes = leftShapes.concat(rightShapes);
	let measurements = coordinates.measurements;
	let imageData = data.tapcha_image;

	jQuery('#tapcha-challenge-id')[0].value = challengeId;

	renderInstructionImage(imageData);

	let stage = new Konva.Stage({
		container: 'tapcha-canvas',
		width: measurements.canvasWidth,
		height: measurements.canvasHeight
	});

	let layer = new Konva.Layer();
	let tempLayer = new Konva.Layer();

	// For debugging
	// addBackgroundLayerToStage(stage);
	addHalfWayLineToStage(layer, stage);

	drawShapes(shapes, measurements, layer);

	setupSubmitButtonTrigger(challengeId);

	addStageEventListener(shapes, stage, layer, tempLayer);

	stage.add(layer);
	stage.add(tempLayer);

	// Set initial value of challenge answer
	updateChallengeAnswer(shapes, stage);
}

function addStageEventListener(shapes, stage, layer, tempLayer) {
	stage.off("dragstart");
	stage.on("dragstart", function(e){
		e.target.moveTo(tempLayer);
		layer.draw();
	});

	stage.off("dragend");
	stage.on("dragend", function(e){
		e.target.moveTo(layer);
		layer.draw();
		tempLayer.draw();

		updateChallengeAnswer(shapes, stage);
	});
}

function updateChallengeAnswer(shapes, stage) {
	let answer = [];
	for (let index = 0; index < shapes.length; index++) {
		let shape = shapes[index];
		let id = shape.id;
		let stageShape = stage.findOne(`#${id}`);
		let x = stageShape.attrs.x;
		let y = stageShape.attrs.y;

		answer[index] = new Shape(id, x, y);
	}
	jQuery('#tapcha-challenge-answer')[0].value = JSON.stringify(answer);
}

function renderInstructionImage(imageData) {
	let image = document.getElementById('image');
	image.setAttribute('src', imageData);
}

function addBackgroundLayerToStage(stage) {
	// Create background layer for debugging
	let backgroundLayer = new Konva.Layer();
	let background = new Konva.Rect({
		x: 0,
		y: 0,
		width: stage.width(),
		height: stage.height(),
		fill: 'black'
	});
	backgroundLayer.add(background);
	stage.add(backgroundLayer);
}

function addHalfWayLineToStage(layer, stage) {
	// Create half way line
	let divider = new Konva.Line({
		points: [
			stage.width() / 2,
			0,
			stage.width() / 2,
			stage.height()
		],
		stroke: '#eee',
		strokeWidth: 1
	});
	layer.add(divider);
}

function drawShapes(shapes, measurements, layer) {
	for (let index = 0; index < shapes.length; index++) {
		let shape = shapes[index];
		drawShape(shape, layer, measurements);
	}
}

function drawShape(shape, layer, measurements) {
	if (shape.type === "Square") {
		drawSquare(shape, layer, measurements.size);
	} else if (shape.type === "Rectangle") {
		drawRectangle(shape, layer, measurements.size);
	} else if (shape.type === "Circle") {
		drawCircle(shape, layer, measurements.circleRadius);
	} else if (shape.type === "Triangle") {
		drawTriangle(shape, layer, measurements.circleRadius);
	} else if (shape.type === "Ellipse") {
		drawEllipse(shape, layer, measurements.circleRadius);
	} else if (shape.type === "Star") {
		drawStar(shape, layer, measurements.circleRadius);
	} else if (shape.type === "Ring") {
		drawRing(shape, layer, measurements.circleRadius);
	}
}

function drawSquare(shape, layer, size) {
	let square = new Konva.Rect({
		x: shape.x,
		y: shape.y,
		width: size,
		height: size,
		offsetX: size / 2,
		offsetY: size / 2,
		fill: shape.color,
		draggable: true,
		id: shape.id,
		name: "square"
	});
	layer.add(square);
}

function drawRectangle(shape, layer, size) {
	let rectangle = new Konva.Rect({
		x: shape.x,
		y: shape.y,
		width: size * 1.5,
		height: size,
		offsetX: (size * 1.5) / 2,
		offsetY: size / 2,
		fill: shape.color,
		draggable: true,
		id: shape.id,
		name: "square"
	});
	layer.add(rectangle);
}

function drawCircle(shape, layer, circleRadius) {
	let circle = new Konva.Circle({
		x: shape.x,
		y: shape.y,
		radius: circleRadius,
		fill: shape.color,
		draggable: true,
		offset: circleRadius,
		id: shape.id,
		name: "circle",
	});
	layer.add(circle);
}

function drawTriangle(shape, layer, circleRadius) {
	let triangle = new Konva.RegularPolygon({
		x: shape.x,
		y: shape.y,
		sides: 3,
		radius: circleRadius,
		fill: shape.color,
		draggable: true,
		id: shape.id,
		name: "triangle"
	});
	layer.add(triangle);
}

function drawEllipse(shape, layer, circleRadius) {
	let ellipse = new Konva.Ellipse({
		x: shape.x,
		y: shape.y,
		radius: {
			x: circleRadius * 1.5,
			y: circleRadius
		},
		fill: shape.color,
		draggable: true,
		id: shape.id,
		name: "ellipse"
	});
	layer.add(ellipse);
}

function drawStar(shape, layer, circleRadius) {
	let star = new Konva.Star({
		x: shape.x,
		y: shape.y,
		numPoints: 5,
		innerRadius: circleRadius / 2.5,
		outerRadius: circleRadius,
		fill: shape.color,
		draggable: true,
		id: shape.id,
		name: "star"
	});
	layer.add(star);
}

function drawRing(shape, layer, circleRadius) {
	let ring = new Konva.Ring({
		x: shape.x,
		y: shape.y,
		innerRadius: circleRadius / 2,
		outerRadius: circleRadius,
		fill: shape.color,
		draggable: true,
		id: shape.id,
		name: "ring"
	});
	layer.add(ring);
}

class Shape {
	constructor(id, xPosition, yPosition) {
		this.id = id;
		this.xPosition = xPosition;
		this.yPosition = yPosition;
	}
}

function setupSubmitButtonTrigger(challengeId) {
	jQuery("#tapcha-submit").on('click.submit', function () {
		let answer = jQuery('#tapcha-challenge-answer')[0].value;
		console.log(answer);
		respondToChallenge(challengeId, answer);
	});
}

function respondToChallenge(challengeId, answer) {
	setLoading(true);
	hideAlerts();
	jQuery.post({
		url: `${getApi()}/response/${challengeId}`,
		dataType: 'json',
		type: 'POST',
		data: {
			"challenge_answer" : answer
		},
		success: function () {
			showSuccessAlert(true);
		},
		error: function (data) {
			let statusCode = data.status;
			if (statusCode === 429) {
				showChallengeConsumedAlert(true);
			} else if (statusCode === 422) {
				let validationErrorJSON = data.responseJSON;
				if ("challenge_id" in validationErrorJSON) {
					showInvalidChallengeIdAlert(true);
				} else if ("challenge_answer" in validationErrorJSON) {
					showInvalidChallengeAnswer(true);
				} else {
					showErrorAlert(true);
				}
			} else {
				showErrorAlert(true);
			}
		},
		complete: function () {
			setLoading(false);
		}
	});
}

function setLoading(isLoading) {
	let text;
	let spinner = jQuery("#tapcha-submitLoading");

	if (isLoading) {
		text = "Loading...";
		spinner.removeClass('d-none');
	} else {
		text = "Submit";
		spinner.addClass('d-none');
	}

	jQuery("#tapcha-submitText")[0].innerText = text;
	jQuery("#tapcha-submit")[0].disabled = isLoading;
}

function hideAlerts() {
	showSuccessAlert(false);
	showErrorAlert(false);
	showChallengeConsumedAlert(false);
	showInvalidChallengeIdAlert(false);
	showInvalidChallengeAnswer(false);
}

function showSuccessAlert(show) {
	showView("#alert-success", show);
}

function showErrorAlert(show) {
	showView("#alert-error", show);
}

function showChallengeConsumedAlert(show) {
	showView("#alert-challenge-consumed", show);
}

function showInvalidChallengeIdAlert(show) {
	showView("#alert-invalid-challenge-id", show);
}

function showInvalidChallengeAnswer(show) {
	showView("#alert-invalid-challenge-answer", show);
}

function showView(className, show) {
	let view = jQuery(className);
	if (show) {
		view.removeClass('d-none');
	} else {
		view.addClass('d-none');
	}
}

function getApi() {
	let localApi = "http://localhost/api/v1";
	let productionApi = "http://api.tapcha.co.uk/api/v1";
	return productionApi;
}