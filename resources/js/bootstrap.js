import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

// This will automatically add the X-XSRF-TOKEN header to requests.
// Laravel includes the XSRF-TOKEN cookie by default.
