import axios from 'axios'

const root = '/admin/api'

class Api {
    static url(url) {
        return root + (url ? '/' + url : '')
    };

    static resource(params) {
        params.action = params.action || ''

        return this.request(params).then((data) => {
            App.app.$store.commit('resource/set', data.info)
            return data
        })
    };

    static action(params) {
        if (! params.method)
            params.method = 'post'

        return this.request(params);
    };

    static request({ controller, action, object, full, url, method, data, params }) {
        if (url)
            url = root + '/' + url
        else
            url = root + '/' + controller + (object ? '/' + object : '') + '/' + action

        method = method || 'get'

        return axios({
            method,
            url,
            data,
            params,
        }).then(response => {
            if (response.data && response.data.redirect)
                App.app.$router.push({ path: response.data.redirect })

            return full ? response : response.data;
        }).catch(({response}) => {
            console.error(response)
            App.$emit('error', (response.data.message != '' ? response.data.message : response.status + ' ' + response.statusText))

            throw response
        })
    };
}

export default Api;
