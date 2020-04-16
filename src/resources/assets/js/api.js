import axios from 'axios'

export default class Api {
    constructor(config) {
        Api.root = '/' + config.baseUrl + '/api'
    }

    url(url) {
        return Api.root + (url ? '/' + url : '')
    }

    resource(params) {
        params.action = params.action || ''
        return this.request(params).then((data) => {
            // App.app.$store.commit('resource/set', data.info)
            return data
        })
    }

    action(params) {
        if (! params.method)
            params.method = 'post'

        return this.request(params);
    }

    request({ resourceName, action, resourceId, full, url, method, data, params }, other) {
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
            ...other
        }).then(response => {
            if (response.data && response.data.redirect)
                App.app.$router.push({ path: response.data.redirect })

            return full ? response : response.data;
        }).catch(({response}) => {
            console.error(response)
            App.$emit('error', (response.data.message != '' ? response.data.message : response.status + ' ' + response.statusText))

            throw response
        })
    }
}
