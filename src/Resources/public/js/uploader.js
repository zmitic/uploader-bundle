import plupload from './../../../../../../moxiecode/plupload/js/plupload.full.min';
global.plupload = plupload;

$(document).on('click', '[data-wjb-uploader-image-remover]:not(.remover-initialized)', function () {
    let remover = $(this);
    remover.addClass('remover-initialized');
    let targetId = remover.attr('data-wjb-uploader-image-remover');

    $(targetId).remove();
    console.log(targetId);
});

$(document).on('mousedown', '.wjb_uploader:not(.initialized)', function () {
    let button = $(this);
    button.addClass('initialized');
    let url = button.attr('data-upload-url');
    let prototype = button.attr('data-prototype');
    let allowedMimeTypes = button.attr('data-allowed-mime-types');
    let target = $(button.attr('data-target'));

    let index = Date.now();

    let uploader = new plupload.Uploader({
        browse_button: button.attr('id'), // this can be an id of a DOM element or the DOM element itself
        url: url,
        filters: {
            mime_types : allowedMimeTypes
        },
        resize: {
            width: 100,
            height: 100
        },
        init : {
            FilesAdded: function (up) {
                up.start();
            },
            FileUploaded: function (up, file, result) {
                let response = JSON.parse(result.response);
                let html = prototype.replace(/__name__/g, index);
                index ++;
                let node = $(html);

                node.find('img').attr('src', response.preview_url);
                node.find('[data-file-widget-filename]').val(response.filename);
                node.find('[data-file-widget-mime]').val(response.mime);

                target.append(node);
            }
        }
    });

    uploader.init();
});

$(document).ready(function () {





});
