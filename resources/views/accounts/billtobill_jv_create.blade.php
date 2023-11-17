@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/blogic_css/acc_tb.css') }}" />
@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="ACC@1" class="form-control" required>
<input type="hidden" name="add" id="add" value="1" class="form-control" required>
<div class="title">

      <?php $title_color = "#e0e0e0"?>
      @if($trans_type  == 1)    <?php $title_color = "#e0e0e0"; $route = "acctrans.jv.index"; ?>  <?php // Journal ?>
      @elseif($trans_type == 2) <?php $title_color = "#ece0cf";?> <?php // Contra ?>
      @elseif($trans_type == 3) <?php $title_color = "#FFFFE0";?> <?php // Cash Recived ?>
      @elseif($trans_type == 4) <?php $title_color = "#FFFF99";?> <?php // Bank Recived ?>
      @elseif($trans_type == 5) <?php $title_color = "#FFFFE0";?> <?php // Cash Payment ?>
      @elseif($trans_type == 6) <?php $title_color = "#FFFF99";?> <?php // Bank Payment ?>
      @endif
      <div  style="background-color:<?php echo $title_color; ?>" class="widget-header widget-header-small">

      <h6 class="widget-title smaller">
      <font size="2" color="blue"><b>{{$title}}</b></font>
      </h6>
     <div class="widget-toolbar">

      @if($trans_type  == 1)
        <a href="{{route('acctrans.jv.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
      @elseif($trans_type == 2)
        <a href="{{route('acctrans.con.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
      @elseif($trans_type == 3)
        <a href="{{route('acctrans.cr.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
      @elseif($trans_type == 4)
         <a href="{{route('acctrans.cp.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
      @elseif($trans_type == 5)
           <a href="{{route('acctrans.br.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
      @elseif($trans_type == 6)
          <a href="{{route('acctrans.bp.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
      @endif

    </div>
  </div>
</div>
@if(Session::has('message'))
 <div class="row">
   <div class="col-md-12">
     <p class="alert alert-success"><b>{{ Session::get('message') }}</b></p>
   </div>
</div>
@endif
  <form id="acc_Form" action="{{route('billtobill.jv.store')}}" method="post">
    {{ csrf_field() }}
    <input type="text" id="transtype" name="transtype" value="{{$trans_type}}" class="form-control"/>
    <div class="widget-body">
      <div class="widget-main">
         <div class="row">
          <div class="col-md-4">
               <div class="input-group">
                 <div class="input-group-prepend">
                     <div class="input-group-text" style="min-width:130px">Company:</div>
                 </div>
                    <select name="company_code" class="autocomplete" id="company_code"  style="max-width:150px" required>
                    <option value="-1" >--Select--</option>
                        @if ($companies->count())
                            @foreach($companies as $company)
                                <option {{ $company_code == $company->comp_id ? 'selected' : '' }} value="{{$company->comp_id}}" >{{ $company->comp_id }}-{{ $company->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
           </div>
          <div class="col-md-4">
              <div class="input-group">
                  <div class="input-group-prepend">
                      <div class="input-group-text" style="min-width:130px">Finacial Year:</div>
                  </div>
                  <input type="text" name="finan_year" value="{{ $finan_year }}" class="form-control" autocomplete="off" readonly />
                </div>
          </div>
      </div>
      <div class="row">
           <div class="col-md-4">
               <div class="input-group ss-item-required">
                   <div class="input-group-prepend ">
                       <div class="input-group-text" style="min-width:130px">Transaction Date:</div>
                   </div>
                   <input type="text" size = "15" name="trans_date" onclick="displayDatePicker('trans_date');"  value="{{ old('trans_date') == "" ?  date('d-m-Y') :  date('d-m-Y',strtotime(old('trans_date'))) }}"  required />
                   <a href="javascript:void(0);" onclick="displayDatePicker('trans_date');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>

                   <!-- input type="date" name="trans_date" value="{{ old('trans_date') == "" ? $trans_date : old('trans_date') }}" class="form-control" autocomplete="off" required/ -->
              </div>
           </div>
           <div class="col-md-3">
               <div class="input-group">
                   <div class="input-group-prepend">
                       <div class="input-group-text" style="min-width:130px">Last Voucher No:</div>
                   </div>
                   <input type="text" name="lastVoucher" value="{{$voucher_no}}" class="form-control" readonly required/>
              </div>
           </div>

           <div class="col-md-3">
                <div class="input-group">
                  <div class="input-group-prepend">
                      <div class="input-group-text" style="min-width:130px">Voucher Type:</div>
                  </div>
                     <select name="doc_type" class="autocomplete" style="max-width:150px" required readonly="true">
                         @if ($accdoctype->count())
                             @foreach($accdoctype as $dcombo)
                                 <option {{ request()->get('doc_type') == $dcombo->doc_type ? 'selected' : '' }} value="{{ $dcombo->doc_type  }}" >{{ $dcombo->doc_type }}</option>
                             @endforeach
                         @endif
                     </select>
                 </div>
            </div>
       </div>
        <div class="row">
             <div class="col-md-10">
                      <div class="input-group ss-item-required">
                          <div class="input-group-prepend">
                              <div class="input-group-text" style="min-width:130px">Narration:</div>
                          </div>
                          <textarea name="narration" id="narration" rows="2" cols="300" class="form-control config" placeholder="Narration" maxlength="500" required></textarea>
                      </div>
              </div>
          </div>

     <div class="row justify-content-center">
       <div class="col-md-12">
        <table id="acc_table" class="table table-striped table-data table-report ">
          <thead class="accTable">
            <tr>
              <th width="3%" class="text-center">Id</th>
              <th width="2%" style="display: none" class="text-center">Account Id</th>
              <th width="2%" style="display: none" class="text-center">Account Code</th>
              <th width="15%" class="text-center">Head of Accounting</th>
              <th width="10%" class="text-center">Invoice</th>
              <th width="25%" class="text-center">Description</th>
              <th width="10%" class="text-center">Debit</th>
              <th width="10%" class="text-center">Credit</th>
              <th width="3%" class="text-center">&nbsp;</th>
            </tr>
          </thead>
          <tbody class="accTable" style="background-color: #ffffff;">
            <tr>
              <td width="3%" class="text-center">1</td>
              <td width="2%" style="display: none"><input type="text" data-type="AccHeadCodeId" name="AccHeadCodeId[]" id="AccHeadCodeId_1" class="form-control item_id_class" autocomplete="off"></td>
              <td width="2%" style="display: none" ><input type="text" data-type="AccHeadCode" name="AccHeadCode[]" id="AccHeadCode_1" class="form-control" autocomplete="off"></td>
              <td width="20%">
                 <!-- <input type="text" data-type="AccHead" name="AccHead[]" onkeydown="toggleQuantity(this.id, event)" id="AccHead_1" class="form-control autocomplete_txt" autocomplete="off" > -->
                <div><select data-type="AccHead" name="AccHead[]"  id ="AccHead_1" class="form-control chosen-select" onchange="loadAccHeadDet(this.id,this.value)">
                  <option value="" disabled selected>- Select Account Head -</option>
                </select></div>
              </td>
                <td width="10%">
                   <div><select data-type="AccInvoice" name="AccInvoice[]"  id ="AccInvoice_1" class="form-control chosen-select" onchange="loadInvoiceDet(this.id,this.value)">
                    <option value="0" selected>- Select Invoice -</option>
                  </select></div>
                </td>
                <td width="10%"><input type="text" name="AccInvoiceBal[]" id="AccInvoiceBal_1" class="form-control input-sm" style="text-align: right;" autocomplete="off"></td>

              <td width="20%"><input type="text" data-type="AccHeadDesc" name="AccHeadDesc[]" id="AccHeadDesc_1" class="form-control" autocomplete="off" readonly></td>
              <td width="10%"><input type="text" name="Debit[]" id="Debit_1" onkeydown="search(this.value,1)" class="form-control input-sm changesDebit dAmount" style="text-align: right;" autocomplete="off" ></td>
              <td width="10%"><input type="text" name="Credit[]" id="Credit_1" onkeydown="search(this.value,2)" class="form-control input-sm changesCredit cAmount"  style="text-align: right;" autocomplete="off"></td>
              <td width="3%"></td>
            </tr>
           </tbody>
        </table>
      </div>
    </div>
   <div class="row justify-content-center">
    <div class="col-md-12">
      <table class="table table-striped table-data table-report" style="margin-top: -1px; background-color: #ededed">
        <thead></thead>
        <tbody>
          <tr style="background: #fff6f9;">
            <td width="3%"><span id="" style="font-size: 8px;color: black"></span></td>
            <td width="2%" style="display: none"><span id="" style="font-size: 8px;color: black"></span></td>
            <td width="2%" style="display: none"><span id="" style="font-size: 8px;color: black"></span></td>
            <td width="15%"><span id="" style="font-size: 8px;color: black"></span></td>
            <td width="25%"><span id="" style="font-size: 17px; color: black"><b>Total:</b></span></td>
            <td width="10%"><span id="total_debit" style="font-size: 17px;color: black; text-align: right;"></span>
              <input type="hidden" name="total_debit_in" id="total_debit_in" class="form-control"  autocomplete="off">
            </td>
            <td width="10%"><span id="total_credit" style="font-size: 17px;color: black; text-align: right;"></span>
            <input type="hidden" name="total_credit_in" id="total_credit_in" class="form-control"  autocomplete="off"></td>
            <td width="3%"><button type="button" class="btn btn-primary btn-sm addmore" id="addMore">+</button></td>
          </tr>
        </tbody>
      </table>
     </div>
   </div><br/>
    <div class="row justify-content-left">
          <div class="col-sm-12 text-left">
              <button class="btn btn-sm btn-success button-prevent-multiple_submit" type="button" onclick="formcheck(); return false"><i class="fa fa-save"></i> Save</button>

              @if($trans_type  == 1)
                <a href="{{route('acctrans.jv.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
              @elseif($trans_type == 2)
                <a href="{{route('acctrans.con.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
              @elseif($trans_type == 3)
                <a href="{{route('acctrans.cr.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
              @elseif($trans_type == 4)
                 <a href="{{route('acctrans.cp.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
              @elseif($trans_type == 5)
                   <a href="{{route('acctrans.br.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
              @elseif($trans_type == 6)
                  <a href="{{route('acctrans.bp.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
              @endif

          </div>
      </div>
    </div>
    </div>
  </form>
</section>
@stop
@section('pagescript')
  <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace.min.js') }}"></script>
  <!--script src="{{ asset('assets/blogic_js/acc_trans.js') }}"></script -->
  <script>
  //var host = window.location.host;
      var compcode = $('#company_code').val();
      var transtype = $('#transtype').val();
      var add = $('#add').val();

      var amount = 0;
      totalAmount();

      //alert(add);
      if(add == 1) getDropdownAccountList(1);
      //if(transtype == 3) loadAccHeadList(transtype);

      $('form').bind("keypress", function(e) {
          if (e.keyCode == 13) {
              e.preventDefault();
              return false;
          }
      });

      function loadAccHeadList(transtype){
        compcode = parseInt($('#company_code').val());
        transtype = parseInt(transtype);
        //alert(transtype);
         $.get('/accheadLookup/'+compcode+'/'+transtype, function(response) {
           //alert(compcode+'--'+transtype);
           var selectList = $('select[id="AccountHead"]');
           selectList.chosen();
           selectList.empty();
           selectList.append('<option value="" disabled selected>--Select Account Head--</option>');
           $.each(response, function(index, element) {
             //alert(element.id+ ' SD ' +element.acc_head);
             selectList.append('<option value="' + element.id + '">' + element.acc_head +'('+ element.p_acc_head +')' +'</option>');
           });
           selectList.trigger('chosen:updated');
        });
      }

      function getDropdownAccountList(i){
        compcode = parseInt($('#company_code').val());
        transtype = parseInt($('#transtype').val());
        i = parseInt(i);
        $.get('/accheadLookup/'+compcode+'/'+transtype, function(response) {
          var selectList = $('select[id="AccHead_'+i+'"]');
          selectList.chosen();
          selectList.empty();
          //$('#itemid_' + i).append('<option value="">--Select Item--</option>');
          selectList.append('<option value="" disabled selected>--Select Account Head'+i+'--</option>');
          $.each(response, function(index, element) {
            //alert(element.id+ ' SD ' +element.acc_head);
            selectList.append('<option value="' + element.id + '">' + element.acc_head +'('+ element.p_acc_head +')' +'</option>');
          });
          selectList.trigger('chosen:updated');
        });
      }

      function getDropdownInvoiceList(i,accid){
        //  alert('/accInvoiceLookup/'+compcode+'/'+accid+'/'+transtype);
        var tag = true;
        compcode = parseInt($('#company_code').val());
        transtype = parseInt($('#transtype').val());
        i = parseInt(i);
        //alert('/accInvoiceLookup/'+compcode+'/'+accid+'/'+transtype);
         $.get('/accInvoiceJVLookup/'+compcode+'/'+accid+'/'+transtype, function(response) {
          var selectList = $('select[id="AccInvoice_'+i+'"]');
          selectList.chosen();
          selectList.empty();
          //$('#itemid_' + i).append('<option value="">--Select Item--</option>');
          selectList.append('<option value="0" selected>--Select Invoice'+i+'--</option>');
          $.each(response, function(index, element) {
            //alert(element.id+ ' SD ' +element.acc_head);
            selectList.append('<option value="' + element.acc_invoice_no + '">' + element.acc_invoice_no +'('+ element.balance +')' +'</option>');
            tag = false;
          });

          if(tag){
            selectList.append('<option value="0">0</option>');
          }
          selectList.trigger('chosen:updated');
        });
      }

     function loadAccHeadDetforBillToBill(el,accid){
          //var itemid = $('#itemid_1').val()
          //alert(el+' '+accid)
          compcode  = parseInt($('#company_code').val());
          transtype = parseInt($('#transtype').val());
          //alert('/accInvoiceLookup/'+compcode+'/'+accid+'/'+transtype);
        //  $("#acc_table tr").remove();
          $('#acc_table tbody').empty();
          $.get('/accInvoiceLookup/'+compcode+'/'+accid+'/'+transtype, function(response) {
              //alert(transtype);
              $.each(response, function(index, element) {
              //  alert(element.acc_invoice_no);
                var inv_date = (element.inv_date==null)?'':element.inv_date;
                var invvalue = parseFloat(element.dr);
                var cr = parseFloat(element.cr);
                var bal = parseFloat(element.balance);
                var i = $('#acc_table tr').length ;
                var link = '/invoice/sales-inv-view/'+element.acc_invoice_no;
                //alert(link);
                html = '<tr>';
                //html +='<td><span href="'+link+'" data-toggle="modal" data-id="'+ element.acc_invoice_no +'" class="btn btn-sm btn-info viewModal" title="View Details" data-placement="top" >'+ element.acc_invoice_no +'</span></td>';              html += '<td width="5%" class="text-center">' + i + '</td>';
                html += '<td width="8%" class="text-center">'+ inv_date +'</td>';
                html += '<td width="10%" style="display: none" ><input type="text" data-type="AccInvoiceNo" name="AccInvoiceNo[]" id="AccInvoiceNo_' + i + '" value="'+ element.acc_invoice_no +'" class="form-control text-center" autocomplete="off" readonly></td>';
                html += '<td width="10%" align="center"><a href="'+link+'" target="_blank">'+ element.acc_invoice_no +'</a></td>';
                html += '<td width="10%"><input type="text" data-type="AccInvoiceVal" name="AccInvoiceVal[]" id="AccInvoiceVal_' + i + '" value="'+ invvalue.toFixed(2) +'" class="form-control invAmount text-right" autocomplete="off" readonly></td>';
                html += '<td width="10%"><input type="text" data-type="AccInvoiceCol" name="AccInvoiceCol[]" id="AccInvoiceCol_' + i + '" value="'+ cr.toFixed(2) +'" class="form-control invAmountCol text-right" autocomplete="off" readonly></td>';
                html += '<td width="10%"><input type="text" data-type="AccInvoiceBal" name="AccInvoiceBal[]" id="AccInvoiceBal_' + i + '" value="'+ bal.toFixed(2) +'" onclick="make_credit(this.id,this.value);" class="form-control invAmountBal text-right" autocomplete="off" readonly></td>';
                html += '<td width="10%"><input type="text" data-type="Credit" name="Credit[]" id="Credit_' + i + '" class="form-control input-sm billtobillchangesCredit cAmount text-right" autocomplete="off"></td>';
                html += '<td width="3%"><div class="btn-group btn-corner"><button type="button" tabindex="-1" class="btn btn-danger btn-xs delete" title="Delete This Row" onclick="removeRow(this)">Del</button></div></td>';
                html += '</tr>';
                $('#acc_table').append(html);
                i++;
              });
          });
      }

      function make_credit(el,inv_value){
        id_arr = el;
        id = id_arr.split("_");
        if($('#Credit_' + id[1]).val() == '') {
          $('#Credit_' + id[1]).val(inv_value);
          var due =  inv_value - $('#Credit_' + id[1]).val();
          $('#Due_' + id[1]).val(due);
        }
        else {
           $('#Credit_' + id[1]).val('');
           $('#Due_' + id[1]).val('');
        }
        totalAmount();
      }

      function loadAccHeadDet(el,accid){
          //var itemid = $('#itemid_1').val()
          //alert(el+' '+accid)
          $.get('/acctrans/get-acchead/getdetails/'+accid+'/getfirst', function(data){
          item = data.data
          }).then(function(){
            id_arr = el
            id = id_arr.split("_")
            $('tr.duplicate').removeClass('duplicate')
            checkDuplicateAccHead(id, item)
            $('Debit_'+id[1]).focus();
            getDropdownInvoiceList(id[1],accid);
          })
      }

      function loadInvoiceDet(el,invid){
          var invVal = 0;
          var j = 1;
          //Checked the user qty
          $('.dAmount').each(function(){
              accinvoice = $('#AccInvoice_'+j).val();
              dr    = $('#Debit_'+j).val();
              //alert(j+'-'+item+' L-'+lot)
              if(parseFloat($(this).val())>0 && accinvoice == invid)
                  invVal += parseFloat($(this).val());
              j += 1;
          });

         //alert(el+'--'+invid+'acctrans/get-cust-invoice-inf/getdetails/'+invid+'/getfirst');
          $.ajax({  //create an ajax request to display.php
            type: "GET",
            url: '/acctrans/get-cust-invoice-inf/getdetails/'+invid+'/getfirst',
            success: function (data) {
              alert(' Invoice No : '+data.inv_no
              +'\n Invoice Date : '+ data.inv_date
              +'\n Customer : '+ data.cust_name
              +'\n Discount : '+ data.inv_disc_value
              +'\n VAT : '+ data.inv_vat_value
              +'\n Bill Amount : '+ data.inv_net_amt);
            }
          });
          id_arr = el;
          id = id_arr.split("_");
          accid = $('#AccHeadCodeId_'+id[1]).val();
          //alert('/accInvoiceBalanceLookup/'+compcode+'/'+accid+'/'+invid);
          $.ajax({  //create an ajax request to display.php
            type: "GET",
            url: '/accInvoiceBalanceLookup/'+compcode+'/'+accid+'/'+invid,
            success: function (response) {
                $.each(response, function(index, element) {
                    //alert(' Balance : '+element.balance);
                    invVal = parseFloat(element.balance) - parseFloat(invVal);
                    $('#AccInvoiceBal_' + id[1]).val(invVal);
                });
            }
          });


      }

      $(document).on('keypress', '.autocomplete_txt', function () {
              compcode = $('#company_code').val()
              transtype = $('#transtype').val()

            //  alert(compcode)
              el = $(this).attr('id')
              $(this).autocomplete({

                  source: function(req, res){
                      $.ajax({
                          url: "/get-acchead/all",
                          dataType: "json",
                          data:{'item':encodeURIComponent(req.term),
                              'compcode':encodeURIComponent(compcode),
                              'acc_type':encodeURIComponent(transtype) },

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
              if(flag[0]){
                  var duplicateItemId = flag[1]
                  $('#AccHeadDesc_'+duplicateItemId).parent().parent('tr').addClass('duplicate')
                  alert('You have selected duplicate Account!')
              }else{
              //alert(names.acc_origin);
              $('#AccHeadCodeId_' + id[1]).val(names.id);
              $('#AccHeadCode_' + id[1]).val(names.acc_code);
              $('#AccHeadDesc_' + id[1]).val(names.acc_origin);
              $('#Debit_'+id[1]).focus()
            }
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
                  if(amount > 0) row_increment();
              }
          }

          $(".addmore").on('click', function () {
              row_increment()
          });

          function row_increment() {

              var i = $('#acc_table tr').length ;
             // alert(i);
              html = '<tr >';
              html += '<td width="3%" class="text-center">' + i + '</td>';
              html += '<td width="2%" style="display: none"><input type="text" data-type="AccHeadCodeId" name="AccHeadCodeId[]" id="AccHeadCodeId_' + i + '"  class="form-control item_id_class" autocomplete="off"></td>';
              html += '<td style="display: none" width="2%"><input type="text" data-type="AccHeadCode" name="AccHeadCode[]" id="AccHeadCode_' + i + '" class="form-control" autocomplete="off"></td>';
              //html += '<td width="15%"><input type="text" data-type="AccHead" name="AccHead[]" id="AccHead_' + i + '" onkeydown="toggleQuantity(this.id, event)" id="AccHead_' + i + '" class="form-control input-sm autocomplete_txt" autocomplete="off"></td>';
              html += '<td width="15%">';
              html += '<div><select data-type="AccHead" name="AccHead[]"  id ="AccHead_' + i + '" class="chosen-select" onkeydown="toggleQuantity(this.id, event)" onchange="loadAccHeadDet(this.id,this.value)">';
              html += '<option value="" disabled selected>-- Select Account Head --</option>';
              html += '</select></div></td>';

                html += '<td width="10%">';
                html += '<div><select data-type="AccInvoice" name="AccInvoice[]"  id ="AccInvoice_' + i + '" class="form-control chosen-select" onchange="loadInvoiceDet(this.id,this.value)">';
                html += '<option value="0" selected>- Select Invoice -</option>';
                html += '</select></div>';
                html += '</td>';
                html += '<td width="10%"><input type="text" name="AccInvoiceBal[]" id="AccInvoiceBal_' + i + '" class="form-control input-sm" style="text-align: right;" autocomplete="off"></td>';

              html += '<td width="25%"><input type="text" data-type="AccHeadDesc" name="AccHeadDesc[]" id="AccHeadDesc_' + i + '" class="form-control" autocomplete="off" readonly></td>';
              html += '<td width="10%"><input type="text" name="Debit[]" id="Debit_' + i + '" onkeydown="search(this.value,1)" class="form-control input-sm changesDebit dAmount" style="text-align: right;" autocomplete="off"></td>';
              html += '<td width="10%"><input type="text" name="Credit[]" id="Credit_' + i + '" onkeydown="search(this.value,2)" class="form-control input-sm changesCredit cAmount" style="text-align: right;" autocomplete="off"></td>';
              html += '<td width="3%"><div class="btn-group btn-corner"><button type="button" tabindex="-1" class="btn btn-danger btn-xs delete" title="Delete This Row" onclick="removeRow(this)">Del</button></div></td>';
              html += '</tr>';

              $('#acc_table').append(html);
              getDropdownAccountList(i,0);
              document.getElementById('AccHead_'+i).focus();
              i++;
          }

          function removeRow  (el) {
              $(el).parents("tr").remove()
              totalAmount()
              //calcluteTotalBill();
          }

          function myFunction(id_arr,type) {
            id = id_arr.split("_");
            x = $(type + id[1]).val();
            // If x is Not a Number or less than one
            if (isNaN(x) || x < 1) {
              text = "Input not valid";
            //  alert(text);
              $(type + id[1]).val('');
            }
          }

          // this funciton is for summation of Debit amount
          $(document).on('change keyup blur', '.changesDebit', function () {

            id_arr = $(this).attr('id');
            id = id_arr.split("_");
            myFunction(id_arr,'#Debit_');
            debit = $('#Debit_' + id[1]).val();
            credit = $('#Credit_' + id[1]).val();
            invoiceValue = parseFloat($('#AccInvoiceBal_' + id[1]).val());
            invoiceNo = $('#AccInvoice_' + id[1]).val();
            if (invoiceNo != "0"){
              //alert(invoiceNo+'-'+invoiceValue+'-'+debit);
              if(parseFloat(invoiceValue)<parseFloat(debit)){
                $('#Debit_' + id[1]).val(invoiceValue.toFixed(2));
              }
            }
            //alert(credit);
            if(credit != '' && parseFloat(credit) > 0){
              $('#Debit_' + id[1]).val('0.00');
            }
            //$('#Debit_' + id[1]).val(0);
            //alert(debit);
            totalAmount();
          });

          // this funciton is for summation of Credit amount
          $(document).on('change keyup blur', '.changesCredit', function () {

            id_arr = $(this).attr('id');
            id = id_arr.split("_");
            myFunction(id_arr,'#Credit_');
            debit = $('#Debit_' + id[1]).val();
            credit = $('#Credit_' + id[1]).val();
            invoiceValue = parseFloat($('#AccInvoiceBal_' + id[1]).val());
            invoiceNo = $('#AccInvoice_' + id[1]).val();
            if (invoiceNo != "0"){
              //alert(invoiceNo+'-'+invoiceValue+'-'+credit);
              if(parseFloat(invoiceValue)<parseFloat(credit)){
                $('#Credit_' + id[1]).val(invoiceValue.toFixed(2));
              }
            }
            if(debit != '' && parseFloat(debit) > 0){
              $('#Credit_' + id[1]).val('0.00');
            }
            //alert('HHH' + debit);
            totalAmount();

          });

          // this funciton is for summation of Credit amount for Bill to Bill FORM
          $(document).on('change keyup blur', '.billtobillchangesCredit', function () {

            id_arr = $(this).attr('id');
            id = id_arr.split("_");
            myFunction(id_arr,'#Credit_');
            invoiceValue = parseFloat($('#AccInvoiceBal_' + id[1]).val());
            credit = parseFloat($('#Credit_' + id[1]).val());
            if(parseFloat(invoiceValue)<parseFloat(credit)){
              $('#Credit_' + id[1]).val(invoiceValue.toFixed(2));
            }

            credit = parseFloat($('#Credit_' + id[1]).val());
            if(credit) {
              $('#Due_' + id[1]).val((invoiceValue-credit).toFixed(2));
            }
            else {
              $('#Due_' + id[1]).val('0');
            }

            //alert('HHH' + debit);
            totalAmount();

          });


          function totalAmount()
          {
              // for Debit Amount
              var total_debitamount = 0;
              $('.dAmount').each(function(){
                  if(parseFloat($(this).val())>0)
                      total_debitamount += parseFloat($(this).val());
              })

              $('#total_debit').text(total_debitamount.toFixed(2));
              $('#total_debit_in').val(total_debitamount.toFixed(2));

              // for Credit Amount
              var total_creditamount = 0;
              $('.cAmount').each(function(){
                  if(parseFloat($(this).val())>0)
                      total_creditamount += parseFloat($(this).val());
              })

              $('#total_credit').text(total_creditamount.toFixed(2));
              $('#total_credit_in').val(total_creditamount.toFixed(2));

              // the below two is for bill to bill
              $('#total_cash').text(total_creditamount.toFixed(2));
              $('#total_cash_in').val(total_creditamount.toFixed(2));

              // for total invoice for Bill to Bill
              var total_invamount = 0;
              $('.invAmount').each(function(){
                  if(parseFloat($(this).val())>0)
                      total_invamount += parseFloat($(this).val());
              })
              $('#total_invamount').text(total_invamount.toFixed(2));

              // for total collection for Bill to Bill
              var total_collected = 0;
              $('.invAmountCol').each(function(){
                  if(parseFloat($(this).val())>0)
                      total_collected += parseFloat($(this).val());
              })
              $('#total_collected').text(total_collected.toFixed(2));

              // for total left to allocation for Bill to Bill
              var total_dueamount = 0;
              $('.invAmountBal').each(function(){
                  if(parseFloat($(this).val())>0)
                      total_dueamount += parseFloat($(this).val());
              })
              $('#total_dueamount').text(total_dueamount.toFixed(2));

          }


          function formSubmit()
          {
            var total_debit_in = $('#total_debit_in').val()
            var total_credit_in = $('#total_credit_in').val()
            //alert(total_debit_in +' :: '+total_credit_in)
            if(parseFloat(total_debit_in) == parseFloat(total_credit_in)){
              $('#acc_Form').submit()
            }else{
              alert('Debit & Credit Does Not Equal')
              //console.log('rifat')
            }
          }

          function formcheck() {
            var isSubmit = true;
            var fields = $(".ss-item-required")
            .find("select, textarea, input").serializeArray();

            $.each(fields, function(i, field) {
              if (!field.value){
                alert(field.name + ' is required');
                isSubmit = false;
              }
            });
            if(isSubmit) {
                $('.button-prevent-multiple_submit').attr('disabled',true); 
                formSubmit();
            }
            console.log(fields);
          }

           function formSubmit_billtoBill()
          {
            var total_credit_in = $('#total_credit_in').val()
            //alert(total_debit_in +' :: '+total_credit_in)
            if(parseFloat(total_credit_in) > 0){
              $('#acc_Form').submit()
            }else{
              alert('Credit Value Does Not 0.00')
              //console.log('rifat')
            }
          }

          function formcheck_billtoBill() {
            var isSubmit = true;
            var fields = $(".ss-item-required")
            .find("select, textarea, input").serializeArray();

            $.each(fields, function(i, field) {
              if (!field.value){
                alert(field.name + ' is required');
                isSubmit = false;
              }
            });
            if(isSubmit) {
                $('.button-prevent-multiple_submit').attr('disabled',true); 
                formSubmit_billtoBill();
            }
            console.log(fields);
          }

  </script>
@stop
