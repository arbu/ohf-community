export default {
    methods: {
        whatsAppUrl(value) {
            return 'whatsapp://send?phone=' + value
        },
        telUrl(value) {
            return 'tel:' + value
        },
        mailUrl(address, name = null) {
            var value = name != null ? name + ' <' + address + '>': address
            return 'mailto:' + value
        },
        skypeUrl(value) {
            return 'skype:' + value + '?chat'
        },
        /**
         * Extracts message from AJAX error response
         * 
         * @param {*} err the error object
         * @param {*} name the field name, in case the error response should be searched for matching field validation error message
         */
        extractResponseErrorMessage(err, name) {
            if (err.response && err.response.data) {
                if (name != null && err.response.data.errors && err.response.data.errors[name]) {
                    return Array.isArray(err.response.data.errors[name]) ? err.response.data.errors[name].join(' ') : err.response.data.errors[name]
                } else if (err.response.data.message) {
                    return err.response.data.message
                }
            }
            return err
        }
    }
}