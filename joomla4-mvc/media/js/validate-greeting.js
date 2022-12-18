jQuery(function() {
    document.formvalidator.setHandler('greeting',
        function (value) {
            regex=/^[^x]+$/;
            return regex.test(value);
        });
});