import Vue from 'vue'
import kebabCase from 'lodash/kebabCase'

const requireComponent = require.context(
    '.', true, /[\w-]+\.vue/
)

requireComponent.keys().forEach(fileName => {
    const componentConfig = requireComponent(fileName)

    const componentName = kebabCase(fileName.replace(/^.*[\\\/]/, '').replace(/\.\w+$/, ''))

    const component = componentConfig.default || componentConfig

    Vue.component(component.name || componentName, component)
})