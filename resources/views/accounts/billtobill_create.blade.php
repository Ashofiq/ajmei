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
  @if($trans_type == 3) <?php $title_color = "#FFFFE0";?> <?php // Cash Recived ?>
  @elseif($trans_type == 5) <?php $title_color = "#FFFF99";?> <?php // Bank Recived ?>
  @endif
  <div  style="background-color:<?php echo $title_color; ?>" class="widget-header widget-header-small">
  <h6 class="widget-title smaller">
  <font size="2" color="blue"><b>{{$title}}</b></font>
  </h6>
     <div class="widget-toolbar">

      @if($trans_type == 3)
        <a href="{{route('acctrans.cr.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
      @elseif($trans_type == 5)
           <a href="{{route('acctrans.br.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
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
  <form id="acc_Form" action="{{route('billtobill.store')}}" method="post">
    {{ csrf_field() }}
    <input type="hidden" id="transtype" name="transtype" value="{{$trans_type}}" class="form-control"/>
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
        <div class="row">
          <div class="col-md-2">
               <div class="input-group ss-item-required">
                    <select name="doc_type" id="doc_type" class="form-control chosen-select"  required>
                        @if ($accdoctype->count())
                            @foreach($accdoctype as $dcombo)
                                <option {{ request()->get('doc_type') == $dcombo->doc_type ? 'selected' : '' }} value="{{ $dcombo->doc_type  }}" >{{ $dcombo->doc_type }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
          </div>
          <div class="col-md-3">
               <div class="input-group ss-item-required">
                    <select name="AccountHead"  id ="AccountHead" class="form-control chosen-select"
                    onchange="loadAccHeadDetforBillToBill(this.id,this.value)">
                        <option value="" disabled selected>- Select Account Head -</option>
                            @foreach($accounthead as $head)
                                <option value="{{ $head->id  }}" >{{ $head->acc_head }}({{ $head->p_acc_head }})</option>
                            @endforeach
                    </select>
                </div>
           </div>
          <div class="col-md-2">
            <input type="text" name="total_credit_in" id="total_credit_in" class="form-control"  style="font-size: 15px;color: black; text-align: right;" autocomplete="off" required readonly>&nbsp;
          </div>
        @if($trans_type == 3)
          <div class="col-md-3">
              <div class="input-group ss-item-required">
                 <div class="input-group-prepend">
                   <div class="input-group-text" style="min-width:80px">Cash In Hand:</div>
                   </div>
                    <input type="text" name="total_cash_in" id="total_cash_in" class="form-control" style="font-size: 15px;color: black; text-align: right;"  autocomplete="off" readonly>
                 </div>
          </div>
        @elseif($trans_type == 5)
          <div class="col-md-3">
               <div class="input-group ss-item-required">
                    <select name="BankAccountHead"  id ="BankAccountHead" class="form-control chosen-select">
                        <option value="" disabled selected>- Select Bank Account Head -</option>
                            @foreach($bankaccounthead as $head)
                                <option value="{{ $head->id  }}" >{{ $head->acc_head }}({{ $head->p_acc_head }})</option>
                            @endforeach
                    </select>
                </div>
           </div>
           <div class="col-md-2">
             <input type="text" name="total_cash_in" id="total_cash_in" class="form-control"  style="font-size: 15px;color: black; text-align: right;" autocomplete="off" required readonly>&nbsp;
           </div>
        @endif
      </div>

     <div class="row justify-content-center">
       <div class="col-md-12">
        <table id="acc_table" class="table table-striped table-data table-report ">
          <thead class="accTable">
            <tr>
              <th width="8%" class="text-center">Date</th>
              <th width="10%" class="text-center">Invoice</th>
              <th width="10%" class="text-center">Invoice Amount</th>
              <th width="10%" class="text-center">Collected</th>
              <th width="10%" class="text-center">Return</th>
              <th width="10%" class="text-center">Left to Collection</th>
              <th width="10%" class="text-center">Collection</th>
              <th width="3%" class="text-center">&nbsp;</th>
            </tr>
          </thead>
          <tbody class="accTable" style="background-color: #ffffff;">

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
            <td width="15%" align="right"><span id="" style="font-size: 17px; color: black;"><b>&nbsp;</b></span></td>
            <td width="10%" align="right"><span id="total_invamount" style="font-size: 17px;color: black; text-align: right;"></span></td>
            <td width="10%" align="right"><span id="total_collected" style="font-size: 17px;color: black; text-align: right;"></span></td>
            <td width="10%" align="right"><span id="total_collected" style="font-size: 17px;color: black; text-align: right;"></span></td>
            <td width="10%" align="right"><span id="total_dueamount" style="font-size: 17px;color: black; text-align: right;"></span></td>
            <td width="10%" align="right"><span id="total_credit" style="font-size: 17px;color: black; text-align: right;"></span></td>
            <td width="3%" align="right"></td>
          </tr>
        </tbody>
      </table>
     </div>
   </div><br/>
    <div class="row justify-content-left">
          <div class="col-sm-6 text-left">
              <button class="btn btn-sm btn-success button-prevent-multiple_submit" type="button" onclick="formcheck_billtoBill(); return false"><i class="fa fa-save"></i> Save</button>
              @if($trans_type == 3)
                <a href="{{route('acctrans.cr.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
              @elseif($trans_type == 5)
                <a href="{{route('acctrans.br.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
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
<script src="{{ asset('assets/blogic_js/sel_box_search.js') }}"></script>
<script src="{{ asset('assets/blogic_js/acc_trans.js') }}"></script>
@stop
