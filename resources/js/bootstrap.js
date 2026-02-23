import axios from 'axios';
import $ from 'jquery';
window.axios = axios;


window.$ = window.jQuery = $;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// set CSRF token header for axios and jQuery if meta tag present
const tokenMeta = document.querySelector('meta[name="csrf-token"]');
if (tokenMeta) {
	const token = tokenMeta.getAttribute('content');
	window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
	if (window.$ && typeof window.$.ajaxSetup === 'function') {
		window.$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': token } });
	}
}
