/* Toplist Hải Phòng: floating travel chatbot. Answers come from POST /wp-json/thp/v1/chat. */
(function () {
	var root = document.querySelector('[data-thp-chat]');
	if (!root || !window.thpChat) return;

	var config = window.thpChat;
	var panel = root.querySelector('#thp-chat-panel');
	var toggle = root.querySelector('[data-thp-chat-toggle]');
	var log = root.querySelector('[data-thp-chat-log]');
	var form = root.querySelector('[data-thp-chat-form]');
	var input = form.querySelector('textarea');
	var send = form.querySelector('button[type="submit"]');
	var storeKey = 'thp-chat-' + config.lang;
	var history = load();
	var busy = false;

	function load() {
		try {
			return JSON.parse(sessionStorage.getItem(storeKey)) || [];
		} catch (e) {
			return [];
		}
	}
	function save() {
		try {
			sessionStorage.setItem(storeKey, JSON.stringify(history.slice(-20)));
		} catch (e) {}
	}

	function escapeHtml(text) {
		return text.replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	}

	// Plain text with [text](url), bare URLs, **bold** and "- " bullets.
	function format(text) {
		var html = escapeHtml(text).replace(
			/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)|(https?:\/\/[^\s<]+[^\s<.,;:!?)\]])/g,
			function (m, label, url, bare) {
				var href = url || bare;
				// Site articles open in place (the chat history survives); other sites in a new tab.
				var external = href.indexOf(location.origin + '/') !== 0 ? ' target="_blank" rel="noopener nofollow"' : '';
				return '<a class="font-semibold text-primary underline underline-offset-2' + (bare ? ' break-all' : '') + '" href="' + href + '"' + external + '>' + (label || bare) + '</a>';
			}
		);
		return html
			.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
			.replace(/^\s*[-*]\s+/gm, '• ')
			.replace(/\n/g, '<br>');
	}

	function bubble(role, text, sources) {
		var el = document.createElement('div');
		if (role === 'user') {
			el.className = 'self-end max-w-[85%] whitespace-pre-wrap break-words rounded-2xl rounded-br-md bg-primary px-3.5 py-2 text-body-sm text-on-primary';
			el.textContent = text;
		} else {
			el.className = 'self-start max-w-[90%] break-words rounded-2xl rounded-bl-md bg-surface-container-low px-3.5 py-2 text-body-sm text-on-surface';
			el.innerHTML = format(text);
			if (sources && sources.length) {
				var list = document.createElement('div');
				list.className = 'mt-2 flex flex-wrap gap-1.5';
				list.innerHTML = '<span class="w-full text-label-md text-on-surface-variant">' + escapeHtml(config.related) + '</span>';
				sources.forEach(function (s) {
					var a = document.createElement('a');
					a.className = 'inline-flex rounded-full border border-primary/30 bg-surface-container-lowest px-3 py-1 text-label-md text-primary hover:bg-primary hover:text-on-primary';
					a.href = s.url;
					a.textContent = s.title;
					list.appendChild(a);
				});
				el.appendChild(list);
			}
		}
		log.appendChild(el);
		log.scrollTop = log.scrollHeight;
		return el;
	}

	function render() {
		log.innerHTML = '';
		bubble('assistant', log.getAttribute('data-greeting'));
		history.forEach(function (m) {
			bubble(m.role, m.content, m.sources);
		});
	}

	function setOpen(open) {
		panel.classList.toggle('hidden', !open);
		panel.classList.toggle('flex', open);
		toggle.setAttribute('aria-expanded', String(open));
		toggle.querySelector('[data-thp-chat-icon="closed"]').classList.toggle('hidden', open);
		toggle.querySelector('[data-thp-chat-icon="open"]').classList.toggle('hidden', !open);
		if (open) {
			if (!log.children.length) render();
			input.focus();
		}
	}

	function ask(question) {
		busy = true;
		send.disabled = true;
		history.push({ role: 'user', content: question });
		bubble('user', question);
		var typing = bubble('assistant', log.getAttribute('data-typing'));
		typing.classList.add('animate-pulse');

		fetch(config.endpoint, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({
				lang: config.lang,
				messages: history.map(function (m) {
					return { role: m.role, content: m.content };
				})
			})
		})
			.then(function (r) {
				return r.json();
			})
			.then(function (data) {
				return data && data.reply ? data : { reply: config.error, sources: [] };
			})
			.catch(function () {
				return { reply: config.error, sources: [] };
			})
			.then(function (data) {
				typing.remove();
				bubble('assistant', data.reply, data.sources);
				history.push({ role: 'assistant', content: data.reply, sources: data.sources || [] });
				save();
				busy = false;
				send.disabled = false;
				input.focus();
			});
	}

	toggle.addEventListener('click', function () {
		setOpen(toggle.getAttribute('aria-expanded') !== 'true');
	});
	root.querySelector('[data-thp-chat-close]').addEventListener('click', function () {
		setOpen(false);
		toggle.focus();
	});
	root.querySelector('[data-thp-chat-clear]').addEventListener('click', function () {
		history = [];
		save();
		render();
		input.focus();
	});
	panel.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') {
			setOpen(false);
			toggle.focus();
		}
	});

	form.addEventListener('submit', function (e) {
		e.preventDefault();
		var question = input.value.trim();
		if (!question || busy) return;
		input.value = '';
		input.style.height = '';
		ask(question);
	});
	// Enter sends, Shift+Enter starts a new line.
	input.addEventListener('keydown', function (e) {
		if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) {
			e.preventDefault();
			form.requestSubmit();
		}
	});
	input.addEventListener('input', function () {
		input.style.height = '';
		input.style.height = input.scrollHeight + 'px';
	});
})();
