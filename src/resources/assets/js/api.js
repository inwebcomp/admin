import axios from 'axios'

const cancelTokenSource = axios.CancelToken.source()

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

export default class Api {
    constructor(config) {
        Api.root = '/' + config.baseUrl + '/api'
    }

    url(url) {
        return Api.root + (url ? '/' + url : '')
    }

    cancelToken() {
        return cancelTokenSource
    }

    resource(params, other) {
        params.action = params.action || ''
        return this.request(params, other).then((data) => {
            // App.app.$store.commit('resource/set', data.info)
            return data
        })
    }

    action(params, other) {
        if (!params.method)
            params.method = 'post'

        return this.request(params, other)
    }

    request({resourceName, action, resourceId, full, url, method, data, params}, other = []) {
        if (url)
            url = Api.root + '/' + url
        else
            url = Api.root + '/' + resourceName + (resourceId ? '/' + resourceId : '') + '/' + action

        method = method || 'get'

        return axios({
            method,
            url,
            data,
            params,
            ...other,
        }).then(response => {
            if (response.data && response.data.redirect)
                App.app.$router.push({path: response.data.redirect})

            return full ? response : response.data
        }).catch((r) => {
            if (axios.isCancel(r)) {
                console.log('Request canceled', r.message);
            }

            let response = r.response

            if (response)
                App.$emit('error', (response.data.message != '' ? response.data.message : response.status + ' ' + response.statusText))

            throw (response || r)
        })
    }
}
