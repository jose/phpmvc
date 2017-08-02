/**
 * WebTour JS
 */

// Instance the tour
var tour = new Tour({
  name: "rating_tour",
  steps: [
  {
    element: "#test",
    placement: "top",
    title: "Welcome to Bootstrap Tour!",
    content: "Introduce new users to your product by walking them through it step by step."
  },
  {
    element: "#tags",
    placement: "left",
    title: "Welcome to Bootstrap Tour!",
    content: "Introduce new users to your product by walking them through it step by step.",
    // Override template for the last step, i.e., no need to show "Next",
    // but "End tour" should be shown
    template: "<div class='popover tour'>\
              <div class='arrow'></div>\
              <h3 class='popover-title'></h3>\
              <div class='popover-content'></div>\
              <div class='popover-navigation'>\
                <div class='btn-group'>\
                  <button class='btn btn-sm btn-default' data-role='prev'>« Prev</button>\
                </div>\
                <button class='btn btn-sm btn-default' data-role='end'>End tour</button>\
              </div>\
            </div>",
  }],
  // Disable storage persistence, i.e., the tour starts from beginning
  // every time the page is loaded
  storage: false,
  // Show a dark backdrop behind the popover and its element,
  // highlighting the current step.
  backdrop: true,
  template: "<div class='popover tour'>\
              <div class='arrow'></div>\
              <h3 class='popover-title'></h3>\
              <div class='popover-content'></div>\
              <div class='popover-navigation'>\
                <div class='btn-group'>\
                  <button class='btn btn-sm btn-default' data-role='prev'>« Prev</button>\
                  <button class='btn btn-sm btn-default' data-role='next'>Next »</button>\
                </div>\
                <!--<button class='btn btn-sm btn-default' data-role='end'>End tour</button>-->\
              </div>\
            </div>",
  onEnd: function (tour) {
    console.log("--- END ---");
    // in theory we might want to redirect to another URL, e.g.,
    // document.location.href = '/url/' + userId;
  }
});

// Initialize the tour
tour.init();

// Start the tour
tour.start(true);

