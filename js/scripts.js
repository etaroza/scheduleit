jQuery(document).ready(function () {

    jQuery("input:radio[name=inlineRadioOptions]").change(function() {
        if (this.value === "all") {
            jQuery(".Winterthur").removeClass("display-none");
            jQuery(".Zurich").removeClass("display-none");
        } else if (this.value === "zurich") {
            jQuery(".Zurich").removeClass("display-none");
            jQuery(".Winterthur").addClass("display-none");
        } else if (this.value === "winterthur") {
            jQuery(".Winterthur").removeClass("display-none");
            jQuery(".Zurich").addClass("display-none");
        }

        showHideSeparator();
        showHideH4WithMonth();
    });

    function showHideSeparator()
    {
        var events = jQuery("#events");

        /**
         * february, march etc.
         */
        var months = events.children();

        months.each(function(index, value) {

            /**
             * array of month event days
             */
            var separatorChildren = jQuery(value).children(".separator");

            separatorChildren.each(function(indexChild, valueChild) {

                /**
                 * event row array
                 */
                var city = jQuery(valueChild).children();

                var winterthurCount = city.length;
                var counter = 0;

                city.each(function(indexCity, valueCity) {

                    console.log(valueCity);

                    if (jQuery(valueCity).hasClass("display-none")) {
                        counter++;
                    }

                    if (counter === winterthurCount) {
                        jQuery(valueChild).addClass("display-none");
                    } else {
                        jQuery(valueChild).removeClass("display-none");
                    }

                });

            });

        });
    }

    function showHideH4WithMonth() {
        var events = jQuery("#events");

        /**
         * february, march etc.
         */
        var months = events.children();

        months.each(function(indexMonth, valueMonth) {
            var separatorChildren = jQuery(valueMonth).children(".separator");

            var separatorCount = separatorChildren.length;
            var counter = 0;

            separatorChildren.each(function(indexSeparator, valueSeparator) {

                if (jQuery(valueSeparator).hasClass("display-none")) {
                    counter++;
                }

            });

            if (counter === separatorCount) {
                jQuery("#events div h4").addClass("display-none");
            } else {
                jQuery("#events div h4").removeClass("display-none");
            }
        });
    }

});
