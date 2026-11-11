(function () {
	// スマホのメニュー開閉
	var btn = document.querySelector('.nav-toggle');
	if (btn) {
		btn.addEventListener('click', function () {
			var open = document.body.classList.toggle('nav-open');
			btn.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
	}

	// 地方を選んだら、都道府県の選択肢をその地方だけにする
	var region = document.querySelector('[data-region-select]');
	var pref = document.querySelector('[data-pref-select]');
	if (region && pref) {
		var sync = function () {
			var r = region.value;
			Array.prototype.forEach.call(pref.querySelectorAll('optgroup'), function (g) {
				var show = !r || g.getAttribute('data-region') === r;
				g.hidden = !show;
				g.disabled = !show;
			});
			var sel = pref.options[pref.selectedIndex];
			if (sel && sel.parentNode.tagName === 'OPTGROUP' && sel.parentNode.disabled) {
				pref.value = '';
			}
		};
		region.addEventListener('change', sync);
		sync();
	}
})();
