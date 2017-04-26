/**
 * Custom JS
 */

$(document).ready(function() {
  start = new Date().getTime();

  $("#_form").submit(function(e) {
    end = new Date().getTime();
    timeSpent = Math.round((end - start) / 1000);

    var elem = document.getElementById('user_time');
    elem.value = timeSpent;
  });
});

