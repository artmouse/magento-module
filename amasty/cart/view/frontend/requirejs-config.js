var config = {
    config: {
        mixins: {
            'Amasty_Conf/js/swatch-renderer': {
                'Amasty_Cart/js/swatch-renderer': true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'Amasty_Cart/js/swatch-renderer': true
            }
        }
    },
    map: {
        '*': {
            'showConfirmPopup': 'Amasty_Cart/js/show-confirm-popup'
        }
    }
};
