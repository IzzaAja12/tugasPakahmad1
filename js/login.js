(function () {
	'use strict';

	// Fetch all the forms we want to apply custom validation styles to
	const forms = document.querySelectorAll('.needs-validation');

	// Loop over them and prevent submission
	Array.prototype.slice.call(forms).forEach((form) => {
			form.addEventListener('submit', (event) => {
					if (!form.checkValidity()) {
							event.preventDefault();
							event.stopPropagation();
					}

					form.classList.add('was-validated');

					Array.from(form.elements).forEach((element) => {
							if (element.checkValidity() === false) {
									element.classList.add('border-red-500'); 
									const feedback = element.nextElementSibling;
									if (feedback && feedback.classList.contains('invalid-feedback')) {
											feedback.classList.remove('hidden'); // Show feedback message
									}
							} else {
									element.classList.remove('border-red-500'); 
									const feedback = element.nextElementSibling;
									if (feedback && feedback.classList.contains('invalid-feedback')) {
											feedback.classList.add('hidden'); 
									}
							}
					});
			}, false);
	});
})();