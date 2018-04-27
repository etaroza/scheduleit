jQuery(document).ready(function () {

    muteDateHoursText();

    function getFilteredCity()
    {
        var filterId = window.location.hash;
        return filterId.split('=')[1];
    }

    jQuery(window).on('hashchange', function(e) {
        if (getFilteredCity() === "all") {
            jQuery(".Winterthur").removeClass("display-none");
            jQuery(".Zurich").removeClass("display-none");
        } else if (getFilteredCity() === "zurich") {
            jQuery(".Zurich").removeClass("display-none");
            jQuery(".Winterthur").addClass("display-none");
        } else if (getFilteredCity() === "winterthur") {
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

        months.each(function(indexMonth, valueMonth) {

            /**
             * array of month event days
             */
            var separatorChildren = jQuery(valueMonth).children(".separator");

            separatorChildren.each(function(indexSeparator, valueSeparator) {

                /**
                 * event row array
                 */
                var city = jQuery(valueSeparator).children();

                var winterthurCount = city.length;
                var counter = 0;

                city.each(function(indexCity, valueCity) {

                    if (jQuery(valueCity).hasClass("display-none")) {
                        counter++;
                    }

                    if (counter === winterthurCount) {
                        jQuery(valueSeparator).addClass("display-none");
                    } else {
                        jQuery(valueSeparator).removeClass("display-none");
                    }

                });

            });

        });
    }

    function showHideH4WithMonth()
    {
        var events = jQuery("#events");

        /**
         * get event months february, march etc.
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
                jQuery(valueMonth).addClass("display-none");
            } else {
                jQuery(valueMonth).removeClass("display-none");
            }
        });
    }

    /**
     * if every event of that day has passed, add text-muted to event date and hours
     */
    function muteDateHoursText() {
        var events = jQuery("#events");

        /**
         * get event months february, march etc.
         */
        var months = events.children();

        months.each(function(indexMonth, valueMonth) {

            /**
             * get divs (one day) with events in it
             */
            var separatorChildren = jQuery(valueMonth).children(".separator");

            separatorChildren.each(function(indexSeparator, valueSeparator) {

                var muted = false;

                var city = jQuery(valueSeparator).children();

                var lastEventMessageInTheDay = jQuery(city).children().last()[0];

                if (jQuery(jQuery(lastEventMessageInTheDay).children()[0]).hasClass("text-muted")) {
                    muted = true;
                }

                city.each(function(indexCity, valueCity) {
                    var eventDate = jQuery(valueCity).children(".event-date")[0];
                    var eventHours = jQuery(valueCity).children(".event-hours")[0];
                    var eventMessages = jQuery(valueCity).children(".event-message")[0];

                    if (jQuery(jQuery(eventMessages).children()[0]).hasClass("text-muted")) {
                        jQuery(jQuery(eventHours).children("h2")[0]).addClass("text-muted");
                    }

                    if (muted === true) {
                        jQuery(jQuery(eventDate).children("h2")[0]).addClass("text-muted");
                    }

                });

            });
        });
    }

});
