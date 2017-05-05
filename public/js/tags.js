/**
 * Tags
 */

function init_draggables() {
  $('.tag-item').draggable({
    cursor: 'move',
    helper: "clone"
  });
}

function init_droppables() {
  // TODO add containers for 'pair' study
  $("#like-container, #dislike-container").droppable({
    drop: function(event, ui) {
      var container = $(this);
      //var itemid = $(event.originalEvent.target.attributes).attr("value");
      var itemid = ui.draggable.attr("id");
      $('.tag-item').each(function() {
        if ($(this).attr("id") === itemid) {
          container.find('.tagsinput-typeahead').tagsinput('add', itemid);
        }
      });
    }
  });
}

$(document).ready(function() {
  init_draggables();
  init_droppables();

  // read tags from a file
  tags = $.get('../public/tags.json');

  // TODO currently 'all' tags container is populated with tags from
  // a database. the autocomplete system works with a list of tags
  // from a file. we need to find a way to synchronise both, or just
  // one system (either db or file)
  $('.tagsinput-typeahead').tagsinput({
    allowDuplicates: false,
    freeInput: false,
    typeahead: {
      name: "tags",
      items: "all",
      source: tags,
      minLength: 1,
      afterSelect: function() {
      	this.$element[0].value = '';
      }
    }
  });

  /**
   * Catch remove/add methods of tags
   */

  var removeAfterAdding = false;

  // remove tag from any container before adding it to another container
  $('#like-container, #dislike-container').on('beforeItemAdd', function(event) {
    // remove it from 'all' tags container
    // TODO what if we have more than one tags container, as in the
    // 'pair' example?
    $("#" + event.item).remove();

    // TODO can we find all '.tagsinput-typeahead' and apply remove?
    removeAfterAdding = true;
    $('#like-container').find('.tagsinput-typeahead').tagsinput('remove', event.item);
    removeAfterAdding = true;
    $('#dislike-container').find('.tagsinput-typeahead').tagsinput('remove', event.item);
  });

  // after adding a tag to a container, set its' id
  $('#like-container, #dislike-container').on('itemAdded', function(event) {
    $(this).find('span').each(function() {
      if ($(this).text() == event.item) {
        $(this).attr('id', event.item);
      }
    });
  });

  // add tag back to the 'all' tags container
  $('#like-container, #dislike-container').on('itemRemoved', function(event) {
    if (removeAfterAdding) {
      removeAfterAdding = false;
    } else {
      // TODO what if we have more than one tags container, as in the
      // 'pair' example?
      $("#tags_container").append("<span id=\"" + event.item + "\" class=\"btn tag-item tag label label-info\">"+event.item+"</span>");
      // restart draggables
      init_draggables();
    }
  });
});

