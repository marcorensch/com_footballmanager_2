document.addEventListener('DOMContentLoaded', function(){
    "use strict";
    setTimeout(function() {
        if (document.formvalidator) {
            document.formvalidator.setHandler('letter', function (value) {
                let regex = /^([a-z]+)$/i;
                return regex.test(value)
            });
        }
    }, (1000));
});
