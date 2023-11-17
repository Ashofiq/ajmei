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
      <font size="2" color="blue"><b>{{$title}} </b></font>
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
  <form id="acc_Form" action="{{route('acctrans.store')}}" method="post">
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
              <th width="20%" class="text-center">Head of Accounting</th>
              @if($trans_type != 1 && $trans_type != 2)
                <th width="10%" class="text-center">Invoice</th>
                <th width="8%" class="text-center">Balance</th>
              @endif
              <th width="20%" class="text-center">Description</th>
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
              @if($trans_type != 1 && $trans_type != 2)
                <td width="10%">
                   <div><select data-type="AccInvoice" name="AccInvoice[]"  id ="AccInvoice_1" class="form-control chosen-select" onchange="loadInvoiceDet(this.id,this.value)">
                    <option value="0" selected>- Select Invoice -</option>
                  </select></div>
                </td>
                <td width="8%"><input type="text" name="AccInvoiceBal[]" id="AccInvoiceBal_1" class="form-control input-sm" style="text-align: right;" autocomplete="off"></td>
              @endif
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
                <a href="{{route('acctrans.cr.moneyRecive', $accTransactionId)}}" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-list"></i> Money RCPT.</a>
              @elseif($trans_type == 4)
                 <a href="{{route('acctrans.cp.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
              @elseif($trans_type == 5)
                  <a href="{{route('acctrans.br.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
                  <a href="{{route('acctrans.cr.moneyRecive', $accTransactionId)}}" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-list"></i> Money RCPT.</a>
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
  <script src="{{ asset('assets/blogic_js/acc_trans.js') }}"></script>
@stop
