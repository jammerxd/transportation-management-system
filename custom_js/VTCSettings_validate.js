function notifyOnErrorInput(input){
	var message = input.data('validateHint');
	$.Notify({
		caption: 'Error',
		content: message,
		type: 'alert'
	});
}