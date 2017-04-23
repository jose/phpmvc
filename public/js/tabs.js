/**
 * Adapted from http://jsfiddle.net/vinodlouis/pb6EM/1/
 * 
 * 23 April 2017
 */

var currentTab;
var composeCount = 0;

// Initilize tabs
$(function () {
  // when ever any tab is clicked this method will be call
  $("#test_case_tab").on("click", "a", function (e) {
      e.preventDefault();

      $(this).tab('show');
      $currentTab = $(this);
  });

  registerOpenJavaFileEvent();
  registerCloseEvent();
});

// Create an even to create tabs (i.e., open Java files in a new tab)
function registerOpenJavaFileEvent() {
  $('.openJavaFile').click(function (e) {
    e.preventDefault();

    var line = $(this).attr('line');
    var total_lines = $(this).attr('total_lines');

    if (line == -1) {
      // openning a file
      var tabName = $(this).html();
    } else {
      // openning a class/method
      var tabName = $(this).attr('parent_name');
    }

    var path = $(this).attr('path');
    //var tabName = fileName.replace(".java", "");
    var tabId = "compose_" + tabName.replace(/\./g, "_");

    // if the tabId already exists in the DOM
    // just show it
    if ($('#' + tabId).length != 0) {
      showTab(tabId, total_lines, line);
    } else {
      // otherwise create a new tab
      $('.nav-tabs').append('<li>' +
        '<a href="#' + tabId + '" aria-controls="settings" role="tab" data-toggle="tab">' +
          '<button class="close closeTab" type="button" >×</button>' +
          tabName + '</a>'+
      '</li>');

      $('.tab-content').append('<div role="tabpanel" class="tab-pane" id="' + tabId + '"></div>');

      // load it
      loadJavaFile(path, tabId, total_lines, line);
    }

    registerCloseEvent();
  });
}

// Create an even to close tabs
function registerCloseEvent() {
  $(".closeTab").click(function () {
    //there are multiple elements which has .closeTab icon so close the tab whose close icon is clicked
    var tabContentId = $(this).parent().attr("href");
    $(this).parent().parent().remove(); //remove li of tab
    $('#test_case_tab a:first').tab('show'); // Select first tab
    $(tabContentId).remove(); //remove respective tab content
  });
}

// Shows the tab with passed content div id..paramter tabid indicates the div where the content resides
function showTab(tabId, total_lines, line) {
  $('#test_case_tab a[href="#' + tabId + '"]').tab('show');

  // workaround to IE and Mozilla
  /*if ($.browser.msie || $.browser.mozilla) {
    console.log("highlighting again ...");
    Prism.highlightAll();
  }*/

  if (line == -1) {
    line = 0;
  }
  var y = 16 + ((line - 1) * 21);

  // jump to line
  var _pre = document.getElementById(tabId).children[0];
  _pre.scrollTop = y;
  // highlighting
  _pre.setAttribute("data-line", line);
  Prism.highlightAll();
}

// Return current active tab
function getCurrentTab() {
  return currentTab;
}

// This function creates a new tab and load the url content in tab
// content div.
function loadJavaFile(path, loadDivSelector, total_lines, line) {
  var url = path;
  $.get(url).done(function (html) {
    $("#" + loadDivSelector).html('<pre class="scrollable_snippet line-numbers" id="_pre" ><code class="language-java"></code></pre>');
    var _code = $("#" + loadDivSelector).children().first().children().first();
    _code.text(html);

    showTab(loadDivSelector, total_lines, line);
  });
}

// This function returns the element of current tab
function getElement(selector) {
  var tabContentId = $currentTab.attr("href");
  return $("" + tabContentId).find("" + selector);
}

function removeCurrentTab() {
  var tabContentId = $currentTab.attr("href");
  $currentTab.parent().remove(); //remove li of tab
  $('#test_case_tab a:last').tab('show'); // Select first tab
  $(tabContentId).remove(); //remove respective tab content
}

