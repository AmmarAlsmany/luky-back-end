/*
Template Name: Larkon - Responsive 5 Admin Dashboard
Author: Techzaa
File: form - Flatpickr js
*/
import flatpickr from "flatpickr";

class FlatpickrDemo {

    init() {
        // Only initialize if elements exist
        const basicEl = document.getElementById('basic-datepicker');
        if (basicEl) basicEl.flatpickr();

        const datetimeEl = document.getElementById('datetime-datepicker');
        if (datetimeEl) {
            datetimeEl.flatpickr({
                enableTime: true,
                dateFormat: "Y-m-d H:i"
            });
        }

        const humanfdEl = document.getElementById('humanfd-datepicker');
        if (humanfdEl) {
            humanfdEl.flatpickr({
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
            });
        }

        const minmaxEl = document.getElementById('minmax-datepicker');
        if (minmaxEl) {
            minmaxEl.flatpickr({
                minDate: "2020-01",
                maxDate: "2020-03"
            });
        }

        const disableEl = document.getElementById('disable-datepicker');
        if (disableEl) {
            disableEl.flatpickr({
                onReady: function () {
                    this.jumpToDate("2025-01")
                },
                disable: ["2025-01-10", "2025-01-21", "2025-01-30", new Date(2025, 4, 9) ],
                dateFormat: "Y-m-d",
            });
        }

        const multipleEl = document.getElementById('multiple-datepicker');
        if (multipleEl) {
            multipleEl.flatpickr({
                mode: "multiple",
                dateFormat: "Y-m-d"
            });
        }

        const conjunctionEl = document.getElementById('conjunction-datepicker');
        if (conjunctionEl) {
            conjunctionEl.flatpickr({
                mode: "multiple",
                dateFormat: "Y-m-d",
                conjunction: " :: "
            });
        }

        const rangeEl = document.getElementById('range-datepicker');
        if (rangeEl) {
            rangeEl.flatpickr({
                mode: "range"
            });
        }

        const inlineEl = document.getElementById('inline-datepicker');
        if (inlineEl) {
            inlineEl.flatpickr({
                inline: true
            });
        }

        const basicTimeEl = document.getElementById('basic-timepicker');
        if (basicTimeEl) {
            basicTimeEl.flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i"
            });
        }

        const hours24El = document.getElementById('24hours-timepicker');
        if (hours24El) {
            hours24El.flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true
            });
        }

        const minmaxTimeEl = document.getElementById('minmax-timepicker');
        if (minmaxTimeEl) {
            minmaxTimeEl.flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                minDate: "16:00",
                maxDate: "22:30",
            });
        }

        const preloadingEl = document.getElementById('preloading-timepicker');
        if (preloadingEl) {
            preloadingEl.flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                defaultDate: "01:45"
            });
        }
    }

}
document.addEventListener('DOMContentLoaded', function (e) {
    new FlatpickrDemo().init();
});
