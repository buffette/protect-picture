$(document).ready(function() {
    function checkTextFont() {
        if ($('#selectTextFont').val() !== '') {
            $('#personalize-font-file').hide();
        } else {
            $('#personalize-font-file').show();
        }
    }

    function checkTextSize() {
        $('#textSizeValue').val($('#inputTextSize').val())
    }

    function checkTextColorAlpha() {
        $('#textColorAlphaValue').val($('#inputTextColorAlpha').val())
    }

    function checkTextAngle() {
        $('#textAngleValue').val($('#inputTextAngle').val())
    }

    function checkTextPosition() {
        if ($('#selectTextPosition').val() === 'middle_middle') {
            $('#personalize-text-border').hide();
        } else {
            $('#personalize-text-border').show();
        }
    }

    checkTextFont();
    checkTextSize();
    checkTextColorAlpha();
    checkTextAngle();
    checkTextPosition();

    $('#selectTextFont').change(function () {
        checkTextFont();
    });

    $('#buttonUseTtf').click(function () {
        $('#selectTextFont').val('');
        checkTextFont();
    });

    $('#inputTextSize').change(function () {
        checkTextSize();
    });

    $('#textSizeValue').change(function () {
        let minVal = parseInt($('#inputTextSize').attr('min'));
        let maxVal = parseInt($('#inputTextSize').attr('max'));
        let newVal = parseInt($(this).val());
        if (newVal < minVal || newVal > maxVal) {
            alert('La taille du texte doit être comprise entre ' + minVal + ' et ' + maxVal + ' !');
            $('#textSizeValue').val($('#inputTextSize').val());
        } else {
            $('#inputTextSize').val(newVal)
        }
    });

    $('#inputTextColorAlpha').change(function () {
        checkTextColorAlpha();
    });

    $('#textColorAlphaValue').change(function () {
        let minVal = parseInt($('#inputTextColorAlpha').attr('min'));
        let maxVal = parseInt($('#inputTextColorAlpha').attr('max'));
        let newVal = parseInt($(this).val());
        if (newVal < minVal || newVal > maxVal) {
            alert('La transparence de la couleur du texte doit être comprise entre ' + minVal + ' et ' + maxVal + ' !');
            $('#textColorAlphaValue').val($('#inputTextColorAlpha').val());
        } else {
            $('#inputTextColorAlpha').val(newVal)
        }
    });

    $('#inputTextAngle').change(function () {
        checkTextAngle();
    });

    $('#textAngleValue').change(function () {
        let minVal = parseInt($('#inputTextAngle').attr('min'));
        let maxVal = parseInt($('#inputTextAngle').attr('max'));
        let newVal = parseInt($(this).val());
        if (newVal < minVal || newVal > maxVal) {
            alert('L\'inclinaison du texte doit être comprise entre ' + minVal + ' et ' + maxVal + ' !');
            $('#textAngleValue').val($('#inputTextAngle').val());
        } else {
            $('#inputTextAngle').val(newVal)
        }
    });

    $('#selectTextPosition').change(function () {
        checkTextPosition();
    });
});