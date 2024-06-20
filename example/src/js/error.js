
var errorContainer;
var errors;



document.addEventListener("DOMContentLoaded", function(e) {
	errorContainer = document.getElementById("error-container");

	let errorAnimations = (() => {
		errors = Array.from(errorContainer.children);
		let d = 0;
	
		errors.forEach((c) => {
			setTimeout(() => {
				c.style.display = 'flex';
				c.classList.add('error-slideIn');
				c.addEventListener('animationend', addErrorSlideOut(c));
			}, d);
			d += 250;
		});
	
		d = 0;
	});

	function addErrorSlideOut(c) {
		setTimeout(() => {
			c.classList.remove('error-slideIn');
			c.classList.add('error-slideOut');
			setTimeout(() => {
				c.style.display = 'none';
				c.classList.remove('error-slideOut');
			}, 3000)
			//c.addEventListener('animationend', remErrorSlideOut(c));
		}, 3000);
	}
	
	function remErrorSlideOut(c) {
		c.classList.remove('error_slideOut');
	}
	errorAnimations()
});
