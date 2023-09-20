var $collectionHolder;
var $addimage = $('<a href="#" class="btn btn-dark">Add Image</a>');

$(document).ready(function () {

    $collectionHolder = $('#image_list');
    $collectionHolder.append($addimage);
    $collectionHolder.data('index',$collectionHolder.find('.panel').length);
    $addimage.click(function (e) {
        e.preventDefault();
        addnewform();
    })

    $('.imagedisplay button').click(function (e) {
        e.preventDefault();
        $(e.target).parents('.imagedisplay').slideUp(1000, function () {
            $(this).remove();
        });
    })

});

function addnewform() {

    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.attr('data-index');
    var newform = prototype;

    newform = newform.replace(/__name__/g, index);
    $collectionHolder.attr('data-index', (parseInt(index) + 1) );

    var $panel = $('<div class="panel panel-warning"></div>');
    var $panelBody = $('<div class="panel-body d-flex align-items-center mb-3"></div>').append(newform);

    $panelBody.append($('<a href="#"><i class="fa fa-trash text-dark d-inline-block"></i></a>').click(function (e) {
        e.preventDefault();
        $(e.target).parents('.panel').slideUp(1000, function () {
            $(this).remove();
        });
    }));

    $panel.append($panelBody);
    $addimage.before($panel);

}

