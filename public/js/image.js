var $collectionHolder;
var $addimage = $('<a href="#" class="btn btn-info">Add Image</a>');

$(document).ready(function () {

    $collectionHolder = $('#image_list');
    $collectionHolder.append($addimage);
    $collectionHolder.data('index',$collectionHolder.find('.panel').length);
    $collectionHolder.find('.panel').each(function () {
        addRemoveButton($(this));
    });
    $addimage.click(function (e) {
        e.preventDefault();
        addnewform();
    })
});

function addnewform() {

    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var newform = prototype;

    newform = newform.replace(/__name__/g, index);
    $collectionHolder.data('index', index+1);

    var $panel = $('<div class="panel panel-warning"><div class="panel-heading"></div></div>');
    var $panelBody = $('<div class="panel-body"></div>').append(newform);

    $panel.append($panelBody);
    addRemoveButton($panel);
    $addimage.before($panel);
}

function addRemoveButton($panel) {

    var $removeButton = $('<a href="#"><i class="fa fa-trash text-dark"></i></a>');
    var $panelFooter = $('<div class="panel-footer"></div>').append($removeButton);

    $removeButton.click(function (e) {
        e.preventDefault();
        $(e.target).parents('.panel').slideUp(1000, function () {
            $(this).remove();
        })
    });

    $panel.append($panelFooter);

}