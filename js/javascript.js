$(document).ready(function() {
    function checkTextFont() {
        if (!$('#buttonUseTtf').is(':checked')) {
            $('#default-fonts').show();
            $('#personalize-font').hide();
        } else {
            $('#default-fonts').hide();
            $('#personalize-font').show();
        }
    }

    // const modalPreviewPicture = new bootstrap.Modal('#modal-preview', {
    //     keyboard: false
    // })


    checkTextFont();

    $('#form-protect-picture').submit(function (e) {
        let typeSubmit = $(this).find('button[type=submit]:focus').attr('value');
        if (typeSubmit !== 'preview') {
            return true;
        }

        e.preventDefault();
        const formData = new FormData(this);
        formData.append($(this).find('button[type=submit]:focus').attr('name'), typeSubmit)

        $.ajax({
            url : $(this).attr("action"),
            type: "POST",
            data : formData,
            processData: false,
            contentType: false,
            success: function(data, textStatus, jqXHR){
                if (!data.valid) {
                    $('#modal-preview').find('.modal-body').html('Erreur !');
                } else {
                    $('#modal-preview').find('.modal-dialog').html($('<img/>').attr('src', 'data:image/png;base64,' + data.imageBase64).addClass('img-preview'));
                    $('#preview-picture').html($('<img/>').attr('src', 'data:image/png;base64,' + ((data.imageBase64Adapt !== null) ? data.imageBase64Adapt : data.imageBase64)).addClass('img-preview'));

                    console.log(data.imageBase64 === data.imageBase64Adapt);

                    // modalPreviewPicture.show();
                }


                console.log('success');
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log('error');
            }
        });
    });

    $('#buttonUseTtf').click(function () {
        checkTextFont();
    });
});