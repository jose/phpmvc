/**
 * Custom JS
 */

$(document).ready(function() {
  start = new Date().getTime();

  $("#_form").submit(function(e) {
    end = new Date().getTime();
    timeSpent = Math.round((end - start) / 1000);

    var elem = document.getElementById('time_to_answer');
    elem.value = timeSpent;
  });
});

function enableDisableDontKnowTextArea(button) {
  if (button.name == "dont_know_button") {
    var elem = document.getElementById('dont_know_textarea');
    if (elem.style.display == 'none') {
      elem.style.display = '';
      elem.disabled = false;
    } else {
      elem.style.display = 'none';
      elem.disabled = true;
    }
  }
}

