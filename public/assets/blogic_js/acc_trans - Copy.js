//var host = window.location.host;
    var compcode = $('#company_code').val();
    var amount = 0;

    $('form').bind("keypress", function(e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            return false;
        }
    });

    $(document).on('keypress', '.autocomplete_txt', function () {
            compcode = $('#company_code').val()
            el = $(this).attr('id')
            $(this).autocomplete({

                source: function(req, res){
                    $.ajax({
                        url: "/get-acchead/all",
                        dataType: "json",
                        data:{'item':encodeURIComponent(req.term),
                            'compcode':encodeURIComponent(compcode),
                            'acc_type':1 },

                        error: function (request, error) {
                           console.log(arguments);
                           alert(" Can't do because: " +  console.log(arguments));
                        },

                        success: function (data) {

                            res($.map(data.data, function (item) {
                              //alert('IQII:'+item.acc_head)
                                return {
                                    label: item.acc_head,
                                    value: item.acc_head,
                                    acc_id: item.id,
                                    el:el,
                                };
                            }));

                        }
                    });
                },
                autoFocus:true,
                select: function(event, ui){
                  $.get('get-acchead/getdetails/'+ui.item.acc_id+'/getfirst', function(data){
                      item=data.data
                  }).then(function(){
                          id_arr = ui.item.el
                          id = id_arr.split("_")
                          $('tr.duplicate').removeClass('duplicate')
                          checkDuplicateAccHead(id, item)
                          $('Debit_'+id[1]).focus();
                        //  calcluteTotalBill()
                        //  totalQuantityCount()
                  })
                }
            })
        })

        function checkDuplicateAccHead(id, names){
            var arr = []
            var item_id_class = $('.item_id_class')
            if(item_id_class.length>0){
                item_id_class.each(function(index, item){
                    arr.push({item:$(item).val(), id:$(item).attr('id').split('_')[1]})
                })
            }
            var flag = inArray(names.id, arr)
            //alert(names.acc_origin);
            $('#AccHeadCodeId_' + id[1]).val(names.id);
            $('#AccHeadCode_' + id[1]).val(names.acc_code);
            $('#AccHeadDesc_' + id[1]).val(names.acc_origin);
            $('#Debit_'+id[1]).focus()
        }

        function inArray(needle, haystack) {
            var length = haystack.length;
            for(var i = 0; i < length; i++) {
                if(haystack[i].item == needle) return [true, haystack[i].id];
            }
            return [false];
        }

        function toggleQuantity(id, event){
            console.log(id);
            id = id.split('_')[1]
            if(event.keyCode==9){
                $('#AccHeadDesc_'+id).focus()
            }
        }


        function search(v,t) {
            if(event.keyCode == 13) {
                amount = v;
                row_increment(amount);
            }
        }

        // this onkeydown function is only for enter into account head table


        function row_increment(amount) {

            var i = $('#acc_table tr').length ;
            html = '<tr >';
            html += '<td width="3%" class="text-center">' + i + '</td>';
            html += '<td width="2%"><input type="text" data-type="AccHeadCodeId" name="AccHeadCodeId[]" id="AccHeadCodeId_' + i + '"  class="form-control item_id_class" autocomplete="off"></td>';
            html += '<td width="2%"><input type="text" data-type="AccHeadCode" name="AccHeadCode[]" id="AccHeadCode_' + i + '" class="form-control" autocomplete="off"></td>';
            html += '<td width="15%"><input type="text" data-type="AccHead" name="AccHead[]" id="AccHead_' + i + '" onkeydown="toggleQuantity(this.id, event)" id="AccHead_' + i + '" class="form-control input-sm autocomplete_txt" autocomplete="off"></td>';
            html += '<td width="25%"><input type="text" data-type="AccHeadDesc" name="AccHeadDesc[]" id="AccHeadDesc_' + i + '" class="form-control" autocomplete="off" readonly></td>';
            html += '<td width="10%"><input type="text" name="Debit[]" id="Debit_' + i + '" onkeydown="search(this.value,1)" class="form-control input-sm changesDebit dAmount" autocomplete="off"></td>';
            html += '<td width="10%"><input type="text" name="Crebit[]" id="Crebit_' + i + '" onkeydown="search(this.value,2)" class="form-control input-sm changesCredit cAmount" autocomplete="off"></td>';
            html += '<td width="3%"><div class="btn-group btn-corner"><button type="button" tabindex="-1" class="btn btn-danger btn-xs delete" title="Delete This Row" onclick="removeRow(this)"><i class="fa fa-trash"></i>Del</button></div></td>';
            html += '</tr>';

            if( amount > 0){
                $('#acc_table').append(html);
                document.getElementById('AccHead_'+i).focus();
                i++;
            }

        }

        function removeRow  (el) {
            $(el).parents("tr").remove()
            //calcluteTotalBill();
        }

        // this funciton is for summation of Debit amount
        $(document).on('change keyup blur', '.changesDebit', function () {

          id_arr = $(this).attr('id');
          id = id_arr.split("_");
          debit = parseFloat($('#Debit_' + id[1]).val());
          //alert('HHH' + debit);
          totalDebitAmount();

        });

        function totalDebitAmount()
        {
            var total_debitamount = 0
            $('.dAmount').each(function(){
                if(parseFloat($(this).val())>0)
                    total_debitamount += parseFloat($(this).val())
            })

            $('#total_debit').text(total_debitamount)
            //$('#total_debit').val(total_debitamount)
        }

        // this funciton is for summation of Credit amount
        $(document).on('change keyup blur', '.changesCredit', function () {

          id_arr = $(this).attr('id');
          id = id_arr.split("_");
          debit = parseFloat($('#Debit_' + id[1]).val());
          //alert('HHH' + debit);
          totalDebitAmount();

        });

        function totalDebitAmount()
        {
            var total_debitamount = 0
            $('.dAmount').each(function(){
                if(parseFloat($(this).val())>0)
                    total_debitamount += parseFloat($(this).val())
            })

            $('#total_debit').text(total_debitamount)
            //$('#total_debit').val(total_debitamount)
        }

        function formSubmit()
        {
            var i = false;
            if(i){
                var netBill = $('#netBill').val()
                var paidAmount = $('#paid_amount').val()
                if(parseFloat(netBill)!=parseFloat(paidAmount)){
                    alert('Net bill and paid amount should be equal')
                }else{
                    $('#jvForm').submit()
                    console.log('rifat')
                }
            }else{
                $('#jvForm').submit()
            }
        }