import Vue from 'vue'

// Form Section ...
Vue.component('form-section', require('~fields/Form/FormSection.vue').default)

// Text Field...
Vue.component('index-text-field', require('~fields/Index/TextField.vue').default)
Vue.component('form-text-field', require('~fields/Form/TextField.vue').default)
Vue.component('detail-text-field', require('~fields/Detail/TextField.vue').default)

// Textarea Field...
Vue.component('index-textarea-field', require('~fields/Index/TextareaField.vue').default)
Vue.component('form-textarea-field', require('~fields/Form/TextareaField.vue').default)

// ID Field...
Vue.component('index-id-field', require('~fields/Index/IDField.vue').default)

// Image Field...
Vue.component('index-image-field', require('~fields/Index/ImageField.vue').default)

// FastActions Field...
Vue.component('index-fast-actions-field', require('~fields/Index/FastActionsField.vue').default)

// Parent Field...
Vue.component('index-tree-field', require('~fields/Index/TreeField.vue').default)
Vue.component('form-tree-field', require('~fields/Form/TreeField.vue').default)

// Boolean Field...
Vue.component('index-boolean-field', require('~fields/Index/BooleanField.vue').default)
Vue.component('form-boolean-field', require('~fields/Form/BooleanField.vue').default)

// Editor Field...
Vue.component('form-editor-field', require('~fields/Form/EditorField.vue').default)
Vue.component('index-editor-field', require('~fields/Index/EditorField.vue').default)

// Editor Field...
Vue.component('form-select-field', require('~fields/Form/SelectField.vue').default)
Vue.component('index-select-field', require('~fields/Index/SelectField.vue').default)

// Icon Field...
Vue.component('index-icon-field', require('~fields/Index/IconField.vue').default)

// Status Field...
Vue.component('index-status-field', require('~fields/Index/StatusField.vue').default)