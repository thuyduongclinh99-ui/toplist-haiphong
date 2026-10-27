/* Toplist Hải Phòng: mobile menu toggle and hero slider. */
(function () {
	var toggle = document.querySelector('[data-thp-menu-toggle]');
	if (toggle) {
		var panel = document.getElementById(toggle.getAttribute('aria-controls'));
		toggle.addEventListener('click', function () {
			var open = toggle.getAttribute('aria-expanded') === 'true';
			toggle.setAttribute('aria-expanded', String(!open));
			panel.classList.toggle('hidden', open);
		});
	}

	document.querySelectorAll('[data-thp-slider]').forEach(function (slider) {
		var track = slider.querySelector('[data-thp-track]');
		var dots = slider.querySelectorAll('[data-thp-dot]');
		var count = track.children.length;
		var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		var timer;

		function current() {
			return Math.round(track.scrollLeft / track.clientWidth);
		}
		function go(i) {
			track.scrollTo({ left: ((i + count) % count) * track.clientWidth, behavior: reduce ? 'auto' : 'smooth' });
		}
		function mark() {
			var i = current();
			dots.forEach(function (d, n) {
				d.setAttribute('aria-current', n === i ? 'true' : 'false');
			});
		}
		function play() {
			if (reduce || count < 2) return;
			clearInterval(timer);
			timer = setInterval(function () {
				go(current() + 1);
			}, 6000);
		}

		var prev = slider.querySelector('[data-thp-prev]');
		var next = slider.querySelector('[data-thp-next]');
		if (prev) prev.addEventListener('click', function () { go(current() - 1); play(); });
		if (next) next.addEventListener('click', function () { go(current() + 1); play(); });
		dots.forEach(function (d) {
			d.addEventListener('click', function () { go(+d.getAttribute('data-thp-dot')); play(); });
		});
		track.addEventListener('scroll', function () { window.requestAnimationFrame(mark); }, { passive: true });
		slider.addEventListener('mouseenter', function () { clearInterval(timer); });
		slider.addEventListener('mouseleave', play);
		slider.addEventListener('focusin', function () { clearInterval(timer); });

		mark();
		play();
	});
})();
