// resources/js/pages/humanfd.js
import flatpickr from 'flatpickr';

document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('humanfd-datepicker');
  if (el) {
    flatpickr(el, {
      altInput: true,
      altFormat: "F j, Y",
      dateFormat: "Y-m-d",
    });
  }
});
