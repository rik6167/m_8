$(function() {

/*			
$(".datefrom").bdatepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
}).on('changeDate', function (selected) {
    var startDate = new Date(selected.date.valueOf());
    $('.dateTo').datepicker('setStartDate', startDate);
}).on('clearDate', function (selected) {
    $('.dateTo').datepicker('setStartDate', null);
});

$(".dateTo").bdatepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,	
}).on('changeDate', function (selected) {
    var endDate = new Date(selected.date.valueOf());
    $('.datefrom').datepicker('setEndDate', endDate);
}).on('clearDate', function (selected) {
    $('.datefrom').datepicker('setEndDate', null);
});
*/	


                function fnInitCompleteCallback(that)
                {
                    var p = that.parents('.dataTables_wrapper').first();
                    var l = p.find('.row').find('label');

                    l.each(function(index, el) {
                        var iw = $("<div>").addClass('col-md-8').appendTo($(el).parent());
                        $(el).parent().addClass('form-group margin-none').parent().addClass('form-horizontal');
                        $(el).find('input, select').addClass('form-control').removeAttr('size').appendTo(iw);
                        $(el).addClass('col-md-4 control-label');
                    });

                    var s = p.find('select');
                    s.addClass('.selectpicker').selectpicker();
                }

/*This function initialize a pagination on a table*/
                $('.datatable').dataTable({
                    "sPaginationType": "bootstrap",
                    "sDom": "R<'clear'><'row separator bottom'<'col-md-12'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                    "sScrollX": "100%",
                    "sScrollXInner": "100%",
                    "bScrollCollapse": true,
                    "fnInitComplete": function() {
                        fnInitCompleteCallback(this);
                    }
                });
				
				

            });