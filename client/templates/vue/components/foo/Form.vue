<template>
    <v-flex>
        <c-form
            :fields="fields"
            :values="values"
            :errors="violations"
            :initial-values="item"
            :handle-update-field="handleUpdateField"
        ></c-form>
    </v-flex>
</template>

<script>
    export default {
        props: {
            handleUpdateField: {
                type: Function,
                required: true
            },

            values: {
                type: Object,
                required: true
            },

            errors: {
                type: Object,
                default: () => {}
            },

            initialValues: {
                type: Object,
                default: () => {}
            }
        },

        data: () => {
            return {
                fields: [
                {{#each formFields}}
                    { name: '{{{name}}}', type: 'text', label: '{{{name}}}' },
                {{/each}}
                ]
            }
        },

        computed: {
            // eslint-disable-next-line
            item () {
                return this.initialValues || this.values
            },

            violations () {
                return this.errors || {}
            }
        }
    }
</script>
