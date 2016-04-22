/**
 * Created by Rogelio on 10/17/2015.
 */
$(function () {

    $('#blockColorManagement')
        .load($('#blockColorManagement').attr('data'))
        .on('click', '#add_template', function () {

            var data = $('#formColors').serialize();
            var url = $('#add_template').attr('data');
            $('#blockColorManagement').load(url, data, function(){
                $('#fld_template').val($('#selectedTheme').val());
                $('#formPlot').submit();
                return false;
            });
            return false;

        })
        .on('click', '#update_template', function () {

            var data = $('#formColors').serialize();
            var url = $('#update_template').attr('data');
            $('#blockColorManagement').load(url, data, function(){
                var urlSvg = $('#blockSvg').attr('data-url');
                $('#blockSvg').load(urlSvg);
            });
            return false;

        })
        .on('click', '#delete_template', function () {
            if (confirm('Desea eliminar la combinacion de colores seleccionada?')) {
                var data = $('#formColors').serialize();
                var url = $('#delete_template').attr('data');
                $('#blockColorManagement').load(url, data);
            }
            return false;

        })
        .on('click', '#refresh_map', function () {
            $('#formPlot').submit();
            return false;

        })
        .on('click', '#closeColors', function () {
            var url = $('#closeColors').attr('data-url');
            var data = {template:$('#selectedTheme').val(), show:0};
            $('#blockColorManagement').load(url, data);
            return false;
        })
        .on('click', '#showColors', function () {
            var url = $('#showColors').attr('data-url');
            var data = {template:$('#selectedTheme').val(), show:1};
            $('#blockColorManagement').load(url, data);
            return false;
        });

    $('#btnGenerateGradient').click(function(){
        var url = $('#formGradient').attr('action');
        var data = $('#formGradient').serialize();
        $('#modalGradient').modal('hide');
        $.getJSON(url, data, function(j){
            var i=0;
            $('.colors'+$('#gradientdest').val()).each(function(){
                $(this).minicolors('value', j[i++]);
            });
        });
        return false;
    });

    $('select.select2').select2();
    $('select.update-answer').change(function(){
        var tr = $(this).closest('tr');
        var url = $(this).attr('data-update');
        var s = '';
        $(this).find('option:selected').each(function(){
            s += $(this).val() + ',';
        });
        tr.addClass('updating');
        $.post(url, {options: s}, function(){
            tr.removeClass('updating');
        });
    });

});
