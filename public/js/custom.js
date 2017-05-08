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

function enableDisableDontKnowTextArea(checkbox) {
  if (checkbox.name == "dontknow") {
    document.getElementById('dontknow_textarea').disabled = checkbox.checked ? false : true;
  } else if (checkbox.name == "dontknow_test_case_a") {
    document.getElementById('dontknow_test_case_a_textarea').disabled = checkbox.checked ? false : true;
  } else if (checkbox.name == "dontknow_test_case_b") {
    document.getElementById('dontknow_test_case_b_textarea').disabled = checkbox.checked ? false : true;
  }
}

