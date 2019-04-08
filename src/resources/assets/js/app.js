import './plugins'

import Admin from "~js/Admin"

import '~fields/fields'

;(function() {
    this.CreateApp = function(config) {
        return new Admin(config)
    }
}.call(window))
