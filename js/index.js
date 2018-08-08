import './std-js/deprefixer.js';
import './std-js/shims.js';
import {$, ready, getLocation, notify} from './std-js/functions.js';

ready().then(async () => {
	function showResults(data) {
		const container = document.getElementById('container');
		const table = document.getElementById('search-results-template').content.cloneNode(true);
		const rowTemplate = document.getElementById('search-results-row-template').content;
		data.forEach(item => {
			const row = rowTemplate.cloneNode(true);
			const dtime = new Date(item.dtime);
			$('[data-field="longitude"]', row).text(item.coords.longitude);
			$('[data-field="latitude"]', row).text(item.coords.latitude);
			$('[data-field="dtime"]', row).text(dtime.toLocaleString());
			$('[data-field="dtime"]', row).attr({datetime: dtime.toISOString()});
			$('tbody', table).append(row);
		});
		[...container.children].forEach(child => child.remove());
		container.append(table);
	}
	document.documentElement.classList.replace('no-js', 'js');

	$('[data-show-modal]').click(event => {
		const target = document.querySelector(event.target.closest('[data-show-modal]').dataset.showModal);
		target.showModal();
	});

	$('[data-close]').click(event => {
		const target = document.querySelector(event.target.closest('[data-close]').dataset.close);
		target.close();
	});

	$('[data-action="find-location"]').click(async event => {
		const location = await getLocation({enableHighAccuracy: true});
		const field = event.target.closest('fieldset');
		console.log(location);
		$('.latitude', field).attr({value: location.coords.latitude});
		$('.longitude', field).attr({value: location.coords.longitude});
		notify('Location found', {
			body: `Longitude: ${location.coords.longitude} Latitude: ${location.coords.latitude}`,
			icon: new URL('img/adwaita-icons/actions/find-location.svg', document.baseURI),
		});
	});

	$('form').submit(async event => {
		event.preventDefault();
		const form = event.target;
		const $formEls = $('input, button', form);
		const url = new URL(form.action, document.baseURI);
		const headers = new Headers();
		const body = new FormData(form);

		headers.set('Accept', 'application/json');
		try {
			$formEls.disable();
			let resp;
			switch (form.method.toLowerCase()) {
			case 'post':
				resp = await fetch(url, {
					body,
					headers,
					method: 'POST'
				});
				break;
			case 'get':
				[...body.entries()].forEach(([key, value]) => {
					url.searchParams.append(key, value);
				});
				resp = await fetch(url, {
					headers,
				});
				break;
			default:
				throw new Error(`Unsupported form method: ${form.method}`);
			}

			if (resp.ok) {
				const json = await resp.json();
				console.log(json);
				$formEls.enable();
				form.reset();
				if (form.name === 'searchLocation') {
					showResults(json);
				}
			} else {
				throw new Error(`${resp.url} [${resp.status} ${resp.statusText}]`);
			}
		} catch(err) {
			console.error(err);
			$formEls.enable();
		}
	});

	$('form').reset(event => {
		const dialog = event.target.closest('dialog[open]');

		if (dialog instanceof HTMLElement) {
			dialog.close();
		}
	});
});
