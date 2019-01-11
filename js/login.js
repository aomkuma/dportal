/* Simple VanillaJS to toggle class */

document.getElementById('toggleProfile').addEventListener('click', function () {
  [].map.call(document.querySelectorAll('.profile'), function(el) {
  	alert('asdasd');
    el.classList.toggle('profile--open');
  });
});

setTimeout(function(){
		console.log('invoke');
		[].map.call(document.querySelectorAll('.profile'), function(el) {
		    el.classList.toggle('profile--open');
	  	});
	}, 3000);

