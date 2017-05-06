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
  $(".draggable_container").droppable({
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

function get_all_tags_container(id) {
  if (id == 'like-container' || id == 'dislike-container') {
    return $("#tags_container");
  } else if (id == 'test_case_a_like-container' || id == 'test_case_a_dislike-container') {
    return $("#test_case_a_tags_container");
  } else if (id == 'test_case_b_like-container' || id == 'test_case_b_dislike-container') {
    return $("#test_case_b_tags_container");
  }

  return null;
}

function get_related_tags_container(id) {
  if (id == 'like-container' || id == 'dislike-container') {
    return [$('#like-container'), $('#dislike-container')];
  } else if (id == 'test_case_a_like-container' || id == 'test_case_a_dislike-container') {
    return [$('#test_case_a_like-container'), $('#test_case_a_dislike-container')];
  } else if (id == 'test_case_b_like-container' || id == 'test_case_b_dislike-container') {
    return [$('#test_case_b_like-container'), $('#test_case_b_dislike-container')];
  }

  return null;
}

$(document).ready(function() {

  // collect tags from an existing container, i.e., the all tags
  // container
  tags = [];
  $('.bootstrap-tagsinput').first().find('span').each(function() {
    tags.push($(this).text());
  });

  if (tags.length == 0) {
    return;
  }

  init_draggables();
  init_droppables();

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
  $('.draggable_container').on('beforeItemAdd', function(event) {
    var tags_container = get_all_tags_container($(this).attr('id'));
    var draggable_containers = get_related_tags_container($(this).attr('id'));

    if (tags_container == null || draggable_containers == null) {
      console.log("tags_container is null!");
      console.log("draggable_containers is null!");
      return;
    }

    // first, remove tag from 'all' tags container
    tags_container.find("#" + event.item).remove();

    // then, remove from any other container
    for (i = 0; i < draggable_containers.length; i++) {
      removeAfterAdding = true;
      draggable_containers[i].find('.tagsinput-typeahead').tagsinput('remove', event.item);
    }
  });

  // after adding a tag to a container, set its' id
  $('.draggable_container').on('itemAdded', function(event) {
    $(this).find('span').each(function() {
      if ($(this).text() == event.item) {
        $(this).attr('id', event.item);
      }
    });
  });

  // add tag back to the 'all' tags container
  $('.draggable_container').on('itemRemoved', function(event) {
    if (removeAfterAdding) {
      removeAfterAdding = false;
    } else {
      var tags_container = get_all_tags_container($(this).attr('id'));
      if (tags_container == null) {
        console.log("tags_container is null!");
        return;
      }

      // append tag
      tags_container.append("<span id=\"" + event.item + "\" class=\"btn tag-item tag label label-info\">"+event.item+"</span>");

      // restart draggables
      init_draggables();
    }
  });
});

