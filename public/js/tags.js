/**
 * Tags
 */

function init_draggables_and_droppables() {
  $('.box-item').draggable({
    cursor: 'move',
    helper: "clone"
  });

  $("#container1").droppable({
    drop: function(event, ui) {
      var itemid = $(event.originalEvent.toElement).attr("itemid");
      $('.box-item').each(function() {
        if ($(this).attr("itemid") === itemid) {
          $(this).appendTo("#container1");
          return ;
        }
      });
    }
  });

  $("#container2").droppable({
    drop: function(event, ui) {
      var itemid = $(event.originalEvent.toElement).attr("itemid");
      $('.box-item').each(function() {
        if ($(this).attr("itemid") === itemid) {
          //$(this).prependTo("#container2 .bootstrap-tagsinput");
          $('#container2 .tagsinput-typeahead').tagsinput('add', itemid);
          return ;
        }
      });
    }
  });

  $("#container3").droppable({
    drop: function(event, ui) {
      var itemid = $(event.originalEvent.toElement).attr("itemid");
      $('.box-item').each(function() {
        if ($(this).attr("itemid") === itemid) {
          //$(this).prependTo("#container3 .bootstrap-tagsinput");
          $('#container3 .tagsinput-typeahead').tagsinput('add', itemid);
          return ;
        }
      });
    }
  });
}

$(document).ready(function() {
  init_draggables_and_droppables();

  tags = $.get('../public/tags.json', function(data) {
    // add all tags to 'all tags' panel
    $.each(data, function(index) {
      console.log(data[index]);
      //$('#container1').tagsinput('add', data[index]);
    });

  }, 'json');

  $('.tagsinput-typeahead').tagsinput({
    allowDuplicates: false,
    freeInput: false,
    typeahead: {
      source: tags,
      items: "all",
      minLength: 1,
      name: "tags",
      afterSelect: function() {
      	this.$element[0].value = '';
      }
    }
  });

  // FIXME do we really need two functions (on per container) with the same code?!

  /* before adding a new element */

  $('#container2 .tagsinput-typeahead').on('beforeItemAdd', function(event) {
    $("#" + event.item).remove();
  });
  $('#container3 .tagsinput-typeahead').on('beforeItemAdd', function(event) {
    $("#" + event.item).remove();
  });

  /* right after adding a new element */

  var removeAfterAdding = false;

  $('#container2 .tagsinput-typeahead').on('itemAdded', function(event) {
    removeAfterAdding = true;
    $('#container3 .tagsinput-typeahead').tagsinput('remove', event.item);
  });
  $('#container3 .tagsinput-typeahead').on('itemAdded', function(event) {
    removeAfterAdding = true;
    $('#container2 .tagsinput-typeahead').tagsinput('remove', event.item);
  });

  /* right after removing an element */

  $('#container2 .tagsinput-typeahead').on('itemRemoved', function(event) {
    if (removeAfterAdding) {
      removeAfterAdding = false;
    } else {
      $("#container1").append("<span id=\"" + event.item + "\" itemid=\"" + event.item + "\" class=\"btn box-item tag label label-info\">"+event.item+"</span>");
      init_draggables_and_droppables();
    }
  });
  $('#container3 .tagsinput-typeahead').on('itemRemoved', function(event) {
    if (removeAfterAdding) {
      removeAfterAdding = false;
    } else {
      $("#container1").append("<span id=\"" + event.item + "\" itemid=\"" + event.item + "\" class=\"btn box-item tag label label-info\">"+event.item+"</span>");
      init_draggables_and_droppables();
    }
  });
});

