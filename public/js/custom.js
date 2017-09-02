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

$('.chosen_snippet').click(function() {
  // reset all
  $('.chosen_snippet').removeClass("btn-primary");
  $('.chosen_snippet').removeClass("btn-default");
  $('.chosen_snippet').addClass("btn-default");
  $('.chosen_snippet').removeClass("active");
  // set
  $(this).removeClass("btn-default");
  $(this).addClass("btn-primary");
  $(this).addClass("active");
});

/**
 * Balance snippets boxes
 */
$(document).ready(function() {
  var box_a = $('#scrollable_snippet_a');
  var box_b = $('#scrollable_snippet_b');

  var max_height = Math.max(box_a.height(), box_b.height());
  box_a.height(max_height);
  box_b.height(max_height);
});

/**
 * Start tour modal if any
 */
$(document).ready(function() {
  $('#tour_modal').modal({
    // Closes the modal when escape key is pressed
    keyboard: false,
    // true, false, or static. Specify 'static' for a backdrop which
    // does not close the modal on click
    backdrop: 'static',
    // Shows the modal when initialized
    show: true
  });
});

function enableTour(question_type) {
  if (question_type == "rate") {
    if (rate_tour.ended()) {
      rate_tour.restart();
    } else {
      // Initialize the tour
      rate_tour.init();
      // Start the tour
      rate_tour.start(true);
    }
  } else if (question_type == "forced_choice") {
    if (forced_choice_tour.ended()) {
      forced_choice_tour.restart();
    } else {
      // Initialize the tour
      forced_choice_tour.init();
      // Start the tour
      forced_choice_tour.start(true);
    }
  } else {
    console.log("Tour '" + question_type + "' not recognized");
  }
}

/**
 * Start survey modal if any
 */
$(document).ready(function() {
  $('#survey_modal').modal({
    // Closes the modal when escape key is pressed
    keyboard: true,
    // true, false, or static. Specify 'static' for a backdrop which
    // does not close the modal on click
    backdrop: 'static',
    // Shows the modal when initialized
    show: true
  });
});

/**
 * Disable submit button right after it is pushed to prevent double
 * form submissions
 */

function disableSubmitButton() {
  var elem = document.getElementById('submit_button');
  elem.disabled = true;
  elem.innerText = "Submitting your data, please wait.";
  return true;
}

