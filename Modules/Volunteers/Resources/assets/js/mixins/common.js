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
        }
    }
}