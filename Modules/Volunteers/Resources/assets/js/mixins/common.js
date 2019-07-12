export default {
    methods: {
        whatsAppUrl(value) {
            return 'whatsapp://send?phone=' + value
        },
        telUrl(value) {
            return 'tel:' + value
        },
        mailUrl(value) {
            return 'mailto:' + value
        }
    }
}