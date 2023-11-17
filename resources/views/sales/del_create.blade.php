@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/blogic_css/sales_tb.css') }}" />

    <link href="{{ asset('assets/bootstrap/3.4.1/css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/bootstrap/3.4.1/js/bootstrap.min.js') }}"></script>
@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>
<div class="title">
  <div  style="background-color:#e0e0e0" class="widget-header widget-header-small">
    <h6 class="widget-title smaller">
    <font size="2" color="blue"><b>Delivery Entry Form</b></font>
    </h6>
    <div class="widget-toolbar">
      <a href="{{route('sales.delivery.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
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
    <div class="widget-body">
      <div class="widget-main">
        <form id="get_Form" action="{{ route('sales.delivery.select') }}" method="post"  class="delete_form">
        {!! csrf_field() !!}
          <div class="row"> 
            <div class="col-md-3">
              <div class="input-group ss-item-required">
                <select name="get_sales_order_no" class="chosen-select" id="get_sales_order_no" required>
                  <option value="" >--Select SO List--</option>
                    @if ($so_list->count())
                      @foreach($so_list as $list)
                        <option {{ $sales_order_id == $list->id ? 'selected' : '' }} value="{{$list->id}}" >{{ $list->so_order_no }} >> {{ $list->cust_name }}</option>
                      @endforeach
                    @endif
                </select>
                </div>
              </div>
            <div class="col-md-4">
              <div class="input-group">
                <button type="submit" class="btn btn-primary btn-sm" >Get Sales Order</button>
              </div>
            </div>
          </div>
        </form> 

        <form id="so_Form" action="{{route('sales.delivery.store')}}" method="post">
        {{ csrf_field() }}
        <input type="hidden" id="so_id" name="so_id" value="{{ $rows->id }}" class="form-control  input-sm" autocomplete="off" required/>
        <input type="hidden" id="so_del_to" name="so_del_to" value="{{ $rows->so_del_to }}" class="form-control  input-sm" autocomplete="off" required/>
        <input type="hidden" id="make_invoice" name="make_invoice" value="{{ $make_invoice }}" class="form-control  input-sm" autocomplete="off" required/>
        <input type="hidden" name="itm_warehouse" id="itm_warehouse" value="{{ $warehouse_id}}" class="form-control  input-sm" autocomplete="off" required/>

         <div class="row">
          <div class="col-md-3">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:70px">Delivery Date:</div>
                </div>
                <input type="text" size = "15" name="delivery_date" onclick="displayDatePicker('order_date');"  value={{ old('delivery_date') == "" ?  date('d-m-Y') :  date('d-m-Y',strtotime(old('delivery_date'))) }}  required />
                <a href="javascript:void(0);" onclick="displayDatePicker('delivery_date');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>

                <!-- input type="date" name="delivery_date" value="{{ old('delivery_date') }}" class="form-control  input-sm" autocomplete="off" required/ -->
            </div>
          </div>
          <div class="col-md-3">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:70px">Sales Order No:</div>
                </div>
                <input type="text" name="sales_order_no" value="{{ $rows->so_order_no }}" class="form-control  input-sm" autocomplete="off" readonly required/>
            </div>
          </div>
          <div class="col-md-4">
               <div class="input-group">
                 <div class="input-group-prepend">
                     <div class="input-group-text" style="min-width:130px">Company:</div>
                 </div>
                 <select name="company_code" class="form-control-sm autocomplete" id="company_code"  style="max-width:150px" required>
                 <option value="" >--Select--</option>
                     @if ($companies->count())
                         @foreach($companies as $company)
                             <option {{ $company_code == $company->comp_id ? 'selected' : '' }} value="{{$company->comp_id}}" >{{ $company->comp_id }}-{{ $company->name }}</option>
                         @endforeach
                     @endif
                 </select>
                </div>
           </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <div class="input-group">
            <div class="input-group-prepend">
              <div class="input-group-text" style="min-width:85px">Refernce:</div>
            </div>
            <input type="text" name="reference_no" id="reference_no" value="{{$rows->so_reference}}" class="form-control" required/>
          </div>
        </div>

        <div class="col-md-5">
            <div class="input-group">
              <select name="customer_id" id="customer_id" class="chosen-select"  onchange="getCustomerDetails(this.value)">
                  <option value="" disabled selected>- Select Customer -</option>
                  @foreach($customers as $customer)
                      <option {{ $customer->id == $rows->so_cust_id ? 'selected' : '' }} value="{{ $customer->id }}">{{ $customer->cust_name }}</option>
                  @endforeach
              </select>
              @error('customer_id')
              <span class="text-danger">{{ $message }}</span>
              @enderror
              </div>
          </div>

          <div class="col-md-3">
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text" style="min-width:70px">Customer Code:</div>
              </div>
              <input type="text" name="result_customer_id" id="result_customer_id" value="{{$rows->cust_code}}" class="form-control" readonly required/>
            </div>
          </div>

          <div class="col-md-6">
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text" style="min-width:70px">Remarks: </div>
              </div>
              <textarea type="text" name="remarks" class="form-control"></textarea>
            </div>
          </div>

          <div class="col-md-3">
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text" style="min-width:70px">Customer Balance:</div>
              </div>
              <input style="font-weight: bold; color : red" type="text" name="result_customer_id" id="result_customer_id" value="{{$cust_balacne}}" class="form-control" readonly/>
            </div>
          </div>

       </div>
       <div class="row">
         <div class="col-md-12">
           <ul class="nav nav-tabs">
             <li class="nav-item">
               <a href="#itemdetails" class="nav-link" role="tab" data-toggle="tab"></a>
             </li>
             <!-- <li class="nav-item">
               <a href="#deliveryinf" class="nav-link" role="tab" data-toggle="tab">Delivery Information</a>
             </li> -->
           </ul>
           <div class="tab-content">
               <div role="tabpanel" class="tab-pane active" id = "itemdetails">
                 <div class="row">
                   <div class="col-md-12 input-group">
                     <table id="salesTable" class="table table-striped table-data table-view">
                       <thead class="salesTable">
                         <th width="1.5%" class="text-center">&nbsp;&nbsp;</th>
                         <th width="2%" style="display: none" class="text-center">Id</th>
                         <th width="5%" style="display: none" class="text-center">Code</th>
                         <th width="12%" class="text-center">Category</th>
                         <th width="20%" class="text-center">Item Name</th>
                         <th width="18%" class="text-center">Item Desc</th>
                         <!-- <th width="5%" style="display: none" class="text-center">Storage</th>
                         <th width="12%"  style="display: none" class="text-center">Item LOT</th> -->
                        
                         <th width="8%" class="text-center">Stock<br/>(KG)</th>
                         <th width="8%" class="text-center">PQty<br/>(Pcs)</th>
                         <th width="7%" class="text-center">Size</th>
                         <th width="7%" class="text-center">Weight<br/>Pcs(GM)</th>
                         <th width="7%" class="text-center">Qty<br/>Pcs</th>
                         <th width="10%" class="text-center">Total<br/>Weight(KG)</th>
                         <th width="6%" class="text-center">Unit</th>
                         <th width="7%" class="text-center">Price</th>
                         <th width="4%" class="text-center">Disc<br/>(%)</th>
                         <th width="8%" class="text-center">Total<br/>Disc</th>
                         <th width="8%" class="text-center">Total</th>
                         <th width="3%" class="text-center">&nbsp;</th>
                     </thead>
                   <tbody class="salesTable" style="background-color: #ffffff;">
                    <?php $i = 1; ?>
                    @foreach($rows_d as $d)
                    <?php
                      if($d->so_item_unit == 'KG'){ 
                        $s_total_amt = $d->so_item_price*$d->so_order_bal_qty;
                      }else{
                        $s_total_amt = $d->so_item_price*$d->so_item_pcs;
                      }
                      
                      $total_disc =  ($s_total_amt*$d->so_order_disc)/100;
                      $total_amt = $s_total_amt - $total_disc;


                    ?>
                    <tr>
                      <td width="1.5%" class="text-center">{{$i}}</td>
                      <td width="2%" style="display: none"><input type="text" data-type="ItemCodeId" name="ItemCodeId[]" id="ItemCodeId_{{$i}}" value="{{$d->so_item_id}}" class="form-control item_id_class" autocomplete="off"></td>
                      <td width="5%" style="display: none"><input type="text" data-type="ItemCode" name="ItemCode[]" id="ItemCode_{{$i}}" value="{{$d->item_code}}" class="form-control autocomplete_txt" autocomplete="off"></td>
                      
                      <td width="12%"><input type="text" data-type="itmcategory" name="itmcategory[]" id="itmcategory_{{$i}}" value="{{ $d->itm_cat_origin }}{{ $d->itm_cat_name }}" class="form-control" autocomplete="off" readonly></td>

                      <!-- <td width="20%">
                        <div class="input-group ss-item-required"><select data-type="itemid" name="itemid[]"  id ="itemid_{{$i}}" class="form-control chosen-select" onchange="loadItemsDet(this.id,this.value)">
                          <option value="" disabled selected>- Select Item -</option>
                          @foreach($item_list as $cmb)
                              <option  {{ $cmb->id == $d->so_item_id ? 'selected' : '' }} value="{{ $cmb->id }}">{{ $cmb->item_name }}({{ $cmb->itm_cat_name }})</option>
                          @endforeach
                        </select></div>
                     </td> -->
                     <td width="20%">
                        <input type="text" data-type="itemid" name="itemid[]" id="itemid_{{$i}}" value="{{ $d->item_name }}({{ $d->itm_cat_name }})" class="form-control" autocomplete="off" readonly>
                     </td>

                      <td width="18%"><input type="text" data-type="ItemDesc" name="ItemDesc[]" id="ItemDesc_{{$i}}" value="{{$d->so_item_spec}}" class="form-control" autocomplete="off" ></td>
                      <!-- <td width="5%" align="center">
                        <div class="input-group ss-item-required"><select data-type="Storage" name="Storage[]"  id ="Storage_{{$i}}" class="form-control chosen-select" onchange="getDropdownLotList(this.id,this.value)">
                            <option value="" disabled selected>Load</option>
                            @foreach($stor_list as $stor)
                                <option  value="{{ $stor->id }}">{{ $stor->stor_code }}({{ $stor->stor_name }})</option>
                            @endforeach
                          </select></div>
                      </td> -->
                      <!-- <td width="10%">
                        <div class="input-group ss-item-required"><select data-type="lotno" name="lotno[]"  id ="lotno_{{$i}}" class="form-control chosen-select" onchange="loadLotDet(this.id,this.value)">
                          <option value="" disabled selected>- Item LOT1 -</option>
                        </select></div>
                      </td> -->
                      
                      <td width="8%" align="center"><input type="text" data-type="Stock" name="Stock[]" id="Stock_{{$i}}"  value="{{$d->stock}}" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" readonly/></td>
                      <td width="8%" align="center"><input type="text" data-type="SQty" name="SQty[]" id="SQty_{{$i}}" value="{{$d->so_order_bal_qty}}" onkeydown="enter(this.id,this.value)" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" readonly /></td>

                      <td width="7%" align="center"><input type="text" data-type="Size" name="Size[]" id="Size_{{$i}}" value="{{$d->so_item_size}}" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" readonly></td>

                       <td width="7%" align="center"><input type="text" data-type="QWeight" name="QWeight[]" id="QWeight_{{$i}}" value="{{$d->so_item_weight}}" class="form-control input-sm changesNo" style="font-weight:bold; text-align: center;" autocomplete="off" readonly></td>

                       <td width="7%" align="center"><input type="text" data-type="PCS" name="PCS[]" id="PCS_{{$i}}" class="form-control input-sm changesNo" style="font-weight:bold; text-align: center;" autocomplete="off" ></td>

                      <td width="10%" align="center"><input type="text" data-type="Qty" value="{{ $d->so_pd_order_conf_weight }}" name="Qty[]" id="Qty_{{$i}}" value="0" onkeydown="enter(this.id,this.value)" class="form-control input-sm changesNo iQty" style="font-weight:bold; text-align: center;" autocomplete="off" /></td>
                      <td width="6%" align="center"><input type="text" data-type="Unit" name="Unit[]" id="Unit_{{$i}}"  value="{{$d->so_item_unit}}" class="form-control input-sm"  autocomplete="off" readonly/></td>
                      <td width="7%" align="center"><input type="text" data-type="Price" name="Price[]" id="Price_{{$i}}" value="{{$d->so_item_price}}" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" readonly/></td>
                      
                      <td width="4%" align="right"><input type="text" data-type="Discp" name="Discp[]" id="Discp_{{$i}}" value="{{$d->so_order_disc}}" onkeydown="enter(this.value,this.value)" class="form-control input-sm changesNo iDiscp" style="font-weight:bold; text-align: center;" autocomplete="off" readonly/></td>
                      <td width="8%" align="right"><input type="text" data-type="Discount" name="Discount[]" id="Discount_{{$i}}" value="0" class="form-control input-sm iDiscount"  style="font-weight:bold; text-align: right;" autocomplete="off" readonly/></td>
                      <td width="8%" align="right"><input type="text" data-type="Total" name="Total[]" id="Total_{{$i}}" value="0" class="form-control input-sm iTotal" style="font-weight:bold; text-align: right;" autocomplete="off" readonly/></td>
                      <td width="3%"><div class="btn-group btn-corner"><button type="button" tabindex="-1" class="btn btn-danger btn-xs delete" title="Delete This Row" onclick="removeRow(this)"><i class="fa fa-trash"></i></button></div></td>

                   </tr>
                    <?php $i = $i + 1; ?>
                   @endforeach
                  </tbody>
                 </table>
               </div></div>
               <div class="row">
               <div class="col-md-12 input-group">
                 <table class="table table-striped table-data table-view">

                   <tbody style="background-color: #ffffff;">
                     <tr>
                        <td width="5%" align="center"><b>Total&nbsp;Qty:</b></td>
                        <td width="5%" align="center"><b>Total&nbsp;Item&nbsp;Disc:</b></td>
                        <td width="12%" align="center"><b>Sub&nbsp;Total:</b></td>
                        <td width="5%" align="center"><b>Disc(%)</b></td>
                        <td width="8%" align="center"><b>Disc&nbsp;Amt</b></td>
                        <td width="15%" align="center"><b>Total&nbsp;Disc</b></td>
                        <td width="15%" align="center" style="display: none"><b>Gr.&nbsp;Amt:</b></td>
                        <td width="5%" align="center" style="display: none"><b>VAT(%)</b></td>
                        <td width="12%" align="center" style="display: none"><b>VAT&nbsp;Amt</b></td>
                        <td width="15%" align="center"><b>Total&nbsp;Amt:</b></td>
                     </tr>

                    <tr>
                      <td width="5%" align="right"><input type="text" name="total_qty" id="total_qty" value="0" style="font-weight:bold; text-align: right;" readonly /></td>
                      <td width="5%" align="right"><input type="text" data-type="total_discount" name="total_discount" id="total_discount" value="0.00" style="font-weight:bold; text-align: right;" readonly></td>
                      <td width="12%"><input type="text" id="n_sub_total"  name="n_sub_total" value="{{ $rows->so_sub_total }}" style="background-color: rgba(255,246,125); font-weight:bold; text-align: right; font-size: 16px;" class="form-control input-sm" placeholder="Sub Total" autocomplete="off"/></td>
                      <td width="5%"><input type="text" id="n_disc_per" name="n_disc_per" value="{{ $rows->so_disc_per }}" class="form-control input-sm changesNo" placeholder="Disc(%)" autocomplete="off" readonly/></td>
                      <td width="8%"><input type="text" id="n_discount" name="n_discount" value="{{ $rows->so_disc_value }}" class="form-control input-sm changesNo" style="font-weight:bold; text-align: right; font-size: 16px;" placeholder="Discount" autocomplete="off" readonly /></td>
                      <td width="15%"><input type="text" id="n_total_disc" name="n_total_disc" value="{{ $rows->so_total_disc }}" style="background-color: rgba(255,246,125); font-weight:bold; text-align: right; font-size: 16px;" class="form-control input-sm" placeholder="Total Discount" autocomplete="off" readonly /></td>
                      <td width="15%" style="display: none"><input type="text" id="n_total_gross" name="n_total_gross" value="{{ $rows->so_gross_amt }}" style="background-color: rgba(195, 248, 150); font-weight:bold; text-align: right; font-size: 16px;" class="form-control input-sm" placeholder="Total Gross" autocomplete="off"/></td>
                      <td width="5%" style="display: none"><input type="text" id="n_vat_per" name="n_vat_per" value="{{ $rows->so_vat_per }}" class="form-control input-sm changesNo" placeholder="" autocomplete="off" readonly/></td>
                      <td width="12%" style="display: none"><input type="text" id="n_total_vat" name="n_total_vat" value="{{ $rows->so_vat_value }}" class="form-control input-sm" placeholder="Total VAT" style="font-weight:bold; text-align: right; font-size: 16px;" autocomplete="off" readonly/></td>

                      <td width="15%" ><input type="text" id="n_net_amount" name="n_net_amount" value="{{ $rows->so_net_amt}}" style="background-color: rgba(255,246,125); font-weight:bold; text-align: right; font-size: 16px;" class="form-control input-sm" placeholder="Net Amount" autocomplete="off" readonly/></td>
                   </tr>
                 </tbody>
                 </table>
               </div>

              </div>

<!--               
              <div class="row">
                <div class="col-md-12">
                 <table class="table table-striped table-data table-view">
                   <tbody style="background-color: #ffffff;">
                     <tr style="display: none">
                       <td width="2%" align="right"><input type="text" data-type="total_amount" name="total_amount" id="total_amount" value="0.00" style="font-weight:bold; text-align: right;" readonly></td>
                       <td width="3%"></td>
                     </tr>
                    </tbody>
                 </table>
               </div></div> -->
              
               <div class="row">
                <div class="col-md-12">
                 <table class="table table-striped table-data table-view">
                   <tbody style="background-color: #ffffff;">
                     <tr> 
                       <td colspan="12" align="right"><b>Carring Cost:</b></td>
                       <td width="15%" align="right"><input type="text" data-type="carring_cost" name="carring_cost" id="carring_cost" value="{{ number_format($rows->so_carring_cost,0) }}" class="form-control input-sm changesNo" style="background-color: rgba(255,246,125); font-weight:bold; text-align: right;" maxlength="23" size="23"></td> 
                     </tr> 
                     <tr> 
                       <td colspan="12" align="right"><b>Labour Cost:</b></td>
                       <td width="15%" align="right"><input type="text" data-type="labour_cost" name="labour_cost" id="labour_cost" value="{{ number_format($rows->so_labour_cost,0) }}" class="form-control input-sm changesNo" style="background-color: rgba(255,246,125); font-weight:bold; text-align: right;" maxlength="23" size="23"></td> 
                     </tr> 
                     <tr> 
                       <td colspan="12" align="right"><b>Load/Unload Cost:</b></td>
                       <td width="15%" align="right"><input type="text" data-type="load_unload_cost" name="load_unload_cost" id="load_unload_cost" value="{{ number_format($rows->so_load_unload_cost,0) }}" class="form-control input-sm changesNo" style="background-color: rgba(255,246,125); font-weight:bold; text-align: right;" maxlength="23" size="23"></td> 
                     </tr> 
                     <tr> 
                       <td colspan="12" align="right"><b>Service Charge:</b></td>
                       <td width="15%" align="right"><input type="text" data-type="service_charge" name="service_charge" id="service_charge" value="{{ number_format($rows->so_service_charge,0) }}" class="form-control input-sm changesNo" style="background-color: rgba(255,246,125); font-weight:bold; text-align: right;" maxlength="23" size="23"></td> 
                     </tr> 
                     <tr> 
                       <td colspan="12" align="right"><b>Other Cost:</b></td>
                       <td width="15%" align="right"><input type="text" data-type="other_cost" name="other_cost" id="other_cost" value="{{ number_format($rows->so_other_cost,0) }}" class="form-control input-sm changesNo" style="background-color: rgba(255,246,125); font-weight:bold; text-align: right;" maxlength="23" size="23"></td> 
                     </tr> 
                     <tr> 
                       <td colspan="12" align="right"><b>Net Amount:</b></td>
                       <td width="15%" align="right"><input type="text" data-type="t_n_net_amount" name="t_n_net_amount" id="t_n_net_amount" value="{{ number_format($rows->so_net_amt,0) }}" class="form-control input-sm" style="background-color: rgba(255,246,125); font-weight:bold; text-align: right;" maxlength="23" size="23" readonly></td> 
                     </tr>
                    </tbody>
                 </table>
               </div></div>

               </div>
               <div role="tabpanel" class="tab-pane" id = "deliveryinf">
                 <div class="row">
                   <div class="col-md-3.5">
                     <div class="input-group">
                         <div class="input-group-prepend ">
                             <div class="input-group-text" style="min-width:80px">Required Delivery Date:</div>
                         </div>
                         <input type="text" size = "15" name="delivery_req_date" onclick="displayDatePicker('delivery_req_date');"  value={{ old('delivery_date') == "" ?  date('d-m-Y') :  date('d-m-Y',strtotime($rows->so_req_del_date)) }}  required />
                         <a href="javascript:void(0);" onclick="displayDatePicker('delivery_req_date');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>

                         <!-- input type="date" name="delivery_req_date" value="{{ $rows->so_req_del_date }}" class="form-control  input-sm" autocomplete="off" required/ -->
                       </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-5">
                      <div class="input-group">
                          <select id="delivered_to" name="delivered_to" class="form-control" onchange="loadDeliveredDet(this.id,this.value)">
                              <option value="" >--Delivered To--</option>
                          </select>
                            @error('delivered_to')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                     </div>
                   </div>

                   <div class="row">
                     <div class="col-md-5">
                              <div class="input-group">
                                  <div class="input-group-prepend">
                                      <div class="input-group-text" style="min-width:80px">Address1:</div>
                                  </div>
                                  <textarea id="address1" name="address1" rows="2" cols="100" class="form-control config" placeholder="Narration" maxlength="500">{{ $rows->so_del_add }}</textarea>
                              </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-5">
                        <div class="input-group">
                            <div class="input-group-prepend ">
                                <div class="input-group-text" style="min-width:80px">Contact No:</div>
                            </div>
                            <input type="text" id="contact_no" name="contact_no" value="{{ $rows->so_cont_no}}" class="form-control  input-sm" autocomplete="off"/>
                          </div>
                       </div>
                     </div>

                     <div class="row">
                       <div class="col-md-5">
                         <div class="input-group ss-item-required">
                             <div class="input-group-prepend ">
                                 <div class="input-group-text" style="min-width:80px">Reference:</div>
                             </div>
                             <input type="text" name="cust_ref" value="{{$rows->so_del_ref}}" class="form-control  input-sm" autocomplete="off"/>
                           </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-5">
                            <div class="input-group">
                              <select name="courr_id" id="courr_id" class="chosen-select" >
                                  <option value="" disabled selected>- Select Courrier To -</option>
                                  @foreach($courr_list as $courr)
                                      <option {{ $rows->so_courrier_to == $courr->id ? 'selected' : '' }} value="{{ $courr->id }}">{{ $courr->courrier_to }}</option>
                                  @endforeach
                              </select>
                              @error('courr_id')
                              <span class="text-danger">{{ $message }}</span>
                              @enderror
                              </div>
                          </div>
                       </div>
                       <div class="row">
                         <div class="col-md-5">
                             <div class="input-group">
                               <select name="condition_tag" id="condition_tag" class="chosen-select">
                                   <option {{ $rows->so_courrier_cond == 'Non Condition'?'Selected':'' }}>Non Condition</option>
                                   <option {{ $rows->so_courrier_cond == 'Condition'?'Selected':'' }}>Condition</option>
                               </select>
                               </div>
                           </div>
                        </div>
                      <div class="row">
                        <div class="col-md-5">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text" style="min-width:80px">Comments:</div>
                                </div>
                                <textarea name="comments" rows="2" cols="100" class="form-control config" maxlength="500" required>{{$rows->so_comments}}</textarea>
                            </div>
                         </div>
                       </div>

               </div>
           </div>
         </div>
       </div>
      <div class="row justify-content-left">
          <div class="col-sm-12 text-left">
              <button class="btn btn-sm btn-success" id="btn1" type="button" onclick="formcheck(); return false"><i class="fa fa-save"></i> Save</button> 
              <a href="{{route('sales.delivery.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
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

<script>
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

  n_sub_total = $('#n_sub_total').val();
  if(parseFloat(n_sub_total) <= "0"){
    alert('Sub total Value can not be zero');
    isSubmit = false;
  }

  if(isSubmit) {
    $($('#btn1')).prop('disabled', true);
    formSubmit();

  }
  console.log(fields);
}

function formSubmit()
{
    $('#so_Form').submit()
}
</script>

<script>
  $(document).ready(function() {
    //getDropdownItemList(1);
    getDropdownDeliveredToList();
    totalQuantityCount();
    totalItemDiscount();
    totalAmount();
    totalDiscount();
    totalVat();
    totalNetAmount();
  });


$(document).on('change keyup blur', '.changesNo', function () {
  id_arr = $(this).attr('id');
  id = id_arr.split("_");
  //alert(id[0]);
  //total_qty = $('#total_qty').val();

  unit          = $('#Unit_' + id[1]).val();
  price         = $('#Price_' + id[1]).val();
  stockQuantity = $('#Stock_' + id[1]).val();
  orderqty      = $('#SQty_' + id[1]).val();
  // quantity      = $('#Qty_' + id[1]).val();
  perDisc       = $('#Discp_' + id[1]).val();
  valueDisc     = $('#Discount_' + id[1]).val();

  n_disc_per = $('#n_disc_per').val();
  n_discount = $('#n_discount').val();
  n_vat_per  = $('#n_vat_per').val();

  qweight    = $('#QWeight_' + id[1]).val();
  pcs        = $('#PCS_' + id[1]).val();
 
 
  if(parseFloat(qweight) > 0 && parseFloat(pcs) > 0 && (parseFloat(pcs) <= parseFloat(orderqty))){ 
      quantity = parseFloat(parseFloat(qweight) * parseFloat(pcs))/1000; 
      //alert(quantity +':'+ parseFloat(stockQuantity) + '*' + parseFloat(pcs));
      if (parseFloat(quantity) > parseFloat(stockQuantity)){
      // alert(quantity +':'+ parseFloat(stockQuantity));
        // $('#Qty_' + id[1]).val(0); 
      }else{
        // $('#Qty_' + id[1]).val(quantity); 
      } 
  }else{
      $('#PCS_' + id[1]).val(0);
      // $('#Qty_' + id[1]).val(0);
      $('#Amount_' + id[1]).val(0);
  }
  quantity = $('#Qty_' + id[1]).val();
  
  //if(pcs =='')  $('#PCS_' + id[1]).val(0);

  if(unit == 'KG') 
    price_qty = (parseFloat(price)*parseFloat(quantity)).toFixed(0);
  else
    price_qty = (parseFloat(price)*parseFloat(pcs)).toFixed(0);

  if (perDisc != '' && price_qty > 0){
    valueDisc = ((parseFloat(price_qty)*parseFloat(perDisc))/100).toFixed(0);
    $('#Discount_' + id[1]).val(valueDisc);
  }else{
    $('#Discount_' + id[1]).val("0.00");
  }

  // if(stockQuantity > 0 && orderqty > 0){
  //     if(parseFloat(quantity) > parseFloat(orderqty) || parseFloat(quantity) > parseFloat(stockQuantity)){
  //         $('#Qty_' + id[1]).val(0);
  //     }
  //     price_qty = (parseFloat(price)*parseFloat(quantity)).toFixed(2);
  //     if (perDisc != '' && price_qty > 0){
  //       valueDisc = ((parseFloat(price_qty)*parseFloat(perDisc))/100).toFixed(2);
  //       $('#Discount_' + id[1]).val(valueDisc);
  //     }else{
  //       $('#Discount_' + id[1]).val("0.00");
  //     }
  // }else{
  //     $('#Qty_' + id[1]).val(0);
  // }

  if (quantity != '' && price != ''){
    total = parseFloat(price_qty) - $('#Discount_' + id[1]).val();
    $('#Total_'+id[1]).val(parseFloat(total).toFixed(0));
  }else{
    $('#Total_'+id[1]).val(parseFloat(0).toFixed(0));
  }

  
  //totalVat();
  totalAmount();
  totalQuantityCount();
  totalItemDiscount();
  totalDiscount();
  totalAmount();
});

function totalVat()
{
    n_vat_per  = $('#n_vat_per').val();
    if(n_vat_per!=''){
      n_total_gross = parseFloat($('#n_sub_total').val()) - parseFloat($('#n_discount').val());
      //alert(n_total_gross);
      n_total_vat = ((parseFloat(n_total_gross) * parseFloat(n_vat_per)) / 100).toFixed(0);
      $('#n_total_vat').val(parseFloat(n_total_vat).toFixed(0));
    }else{
      $('#n_total_vat').val(parseFloat(0).toFixed(0));
    }
}

function totalDiscount()
{
    n_disc_per = $('#n_disc_per').val();
    n_discount = $('#n_discount').val();
    n_sub_total = $('#n_sub_total').val();
    discount = 0;
    //alert(n_disc_per +' '+ n_sub_total);
    if (n_disc_per != '' && n_disc_per > 0){
        discount = ((n_sub_total * n_disc_per) / 100).toFixed(0);
        $('#n_discount').val(parseFloat(discount).toFixed(0));
    }else if (n_discount != '' && n_disc_per > 0){
        discount = parseFloat(n_sub_total) - parseFloat(n_discount);
        $('#n_discount').val(parseFloat(discount).toFixed(0));
        $('#n_disc_per').val(parseFloat(0).toFixed(0));
    }
    n_total_disc = parseFloat($('#total_discount').val()) + parseFloat($('#n_discount').val());
    $('#n_total_disc').val(parseFloat(n_total_disc).toFixed(0));
    n_total_gross = parseFloat(n_sub_total) - parseFloat(n_total_disc);
    $('#n_total_gross').val(parseFloat(n_total_gross).toFixed(0));
}

function totalQuantityCount()
{
    var total_qty = 0;
    $('.iQty').each(function(){
        if(parseFloat($(this).val())>0)
            total_qty += parseFloat($(this).val());
    });
    $('#total_qty').val(total_qty);
}

function totalItemDiscount()
{
    var total_discount = 0;
    $('.iDiscount').each(function(){
        if(parseFloat($(this).val())>0)
            total_discount += parseFloat($(this).val());
    });
    $('#total_discount').val(total_discount.toFixed(0));

    n_discount = $('#n_discount').val();
    if(n_discount>0) discount = parseFloat(total_discount) + parseFloat(n_discount);
    else discount = total_discount;
    $('#n_total_disc').val(parseFloat(discount).toFixed(0));
}

function totalAmount()
{
    var total_amount = Number('0');
    $('.iTotal').each(function(){
        //alert($(this).val());
        if(parseFloat($(this).val())>0)
            total_amount += parseFloat($(this).val().replace(/,/g, ""));

    });
    $('#total_amount').val(parseFloat(total_amount).toFixed(0));
    $('#n_sub_total').val(parseFloat(total_amount).toFixed(0));

    n_discount = $('#n_discount').val();
    n_total_disc = $('#n_total_disc').val();

    if (n_total_disc != ''){
      n_sub_total = $('#n_sub_total').val();
      //n_net_amount = parseFloat(n_sub_total) - parseFloat(n_total_disc);
      n_net_amount = parseFloat(n_sub_total) - parseFloat(n_discount);
      $('#n_total_gross').val(parseFloat(n_net_amount).toFixed(0));
    }else{
        n_net_amount  = total_amount;
    }

    n_total_vat = $('#n_total_vat').val()==""?'0.00':$('#n_total_vat').val();
    if (n_total_vat != ''){
      n_total_gross = $('#n_total_gross').val();
      n_net_amount = parseFloat(n_total_gross) + parseFloat(n_total_vat);
    }else{
        n_net_amount  = parseFloat(n_total_gross);
    }
     
    total_n_net_amount = 0;

    $('#n_net_amount').val(parseFloat(n_net_amount).toFixed(0));
    $('#t_n_net_amount').val(parseFloat(n_net_amount).toFixed(0));

    n_carring_cost = $('#carring_cost').val()==''?'0.00':parseFloat($('#carring_cost').val());
    n_labour_cost = $('#labour_cost').val()==''?'0.00':parseFloat($('#labour_cost').val());
    n_load_unload_cost  = $('#load_unload_cost').val()==''?'0.00':parseFloat($('#load_unload_cost').val());
    n_service_charge = $('#service_charge').val()==''?'0.00':parseFloat($('#service_charge').val());
    n_other_cost = $('#other_cost').val()==''?'0.00':parseFloat($('#other_cost').val()); 
    
    // total_n_net_amount = parseFloat(n_carring_cost) + 
    // parseFloat(n_labour_cost) + 
    // parseFloat(n_load_unload_cost) + 
    // parseFloat(n_service_charge) + 
    // parseFloat(n_other_cost) + 
    // parseFloat(n_net_amount);

    total_n_net_amount = parseFloat(n_net_amount);
    // $('#n_net_amount').val(parseFloat(total_n_net_amount).toFixed(0));
    $('#t_n_net_amount').val(parseFloat(total_n_net_amount).toFixed(0));
}

function totalNetAmount()
{
  n_sub_total = $('#n_sub_total').val();
  n_discount = $('#n_discount').val();
  n_total_disc = $('#n_total_disc').val();
  n_total_vat = $('#n_total_vat').val();
  n_total_gross =  parseFloat(n_sub_total) - parseFloat(n_discount);
  n_net_amount =  parseFloat(n_total_gross) + parseFloat(n_total_vat); 
  $('#n_total_gross').val(parseFloat(n_total_gross).toFixed(0));
  $('#n_net_amount').val(parseFloat(n_net_amount).toFixed(0));
}

function loadItemsDet(el,itemid){
    var so_id  = $('#so_id').val();
    //alert(el+' Item:'+itemid+' SO:'+so_id)
    $.get('/get-item-del-code/getdetails/'+itemid+'/'+so_id+'/getfirst', function(data){
    item = data.data
    }).then(function(){
      id_arr = el
      id = id_arr.split("_")
      $('tr.duplicate').removeClass('duplicate')
      checkDuplicateItem(id, item,false)
      totalQuantityCount()
      totalItemDiscount()
      totalAmount()
    })
}


function loadDeliveredDet(el,delid){
    //alert(delid);
    $.ajax({  //create an ajax request to display.php
      type: "GET",
      url: 'get-delivered-inf/getdetails/'+delid+'/getfirst',
      success: function (data) {
        //alert(data.deliv_add +'::'+ data.deliv_mobile);
        $("#address1").val(data.deliv_add);
        $("#contact_no").val(data.deliv_mobile);
      }
    });
}

function getCustomerDetails(custid){
  var customer_id = $('#customer_id').val();
  $('#result_customer_id').val(customer_id);
  getDropdownItemList(1);
  getDropdownDeliveredToList();
}

function loadLotDet(el,lotno){
    i = el.split("_")[1];
    var storgae_id = $('#Storage_'+i).val()
    var itemid = $('#ItemCodeId_'+i).val();
    //alert(i+'-'+itemid+'-'+lotno)
    $.ajax({  //create an ajax request to display.php
      type: "GET",
      url: '/get-stock-inf/'+storgae_id+'/'+itemid+'/'+lotno+'/getfirst',
      success: function (data) {
        //alert(data.stock);
        $("#Stock_"+i).val(data.stock);
        $('#Qty_'+i).focus();
      }
    });
}

function enter(id,amount) {
    if(event.keyCode == 13) {
        field = id.split("_")[0];
        i = id.split("_")[1];
        if(amount > 0 && field == 'Qty') row_increment();
        else if(field == 'Discp') row_increment();
        //alert(i);
        //getDropdownItemList(i);
    }
}


function removeRow(el) {
    $(el).parents("tr").remove();
    totalQuantityCount();
    totalItemDiscount();
    totalAmount();
}

function row_increment() {
    var i = $('#salesTable tr').length;
    html = '<tr>';
    html += '<td width="1.5%" class="text-center">' + i + '</td>';
    html += '<td width="2%" style="display: none"><input type="text" data-type="ItemCodeId" name="ItemCodeId[]" id="ItemCodeId_' + i + '"  class="form-control item_id_class" autocomplete="off"/></td>';
    html += '<td width="5%" style="display: none"><input type="text" data-type="ItemCode" name="ItemCode[]" id="ItemCode_' + i + '" class="form-control autocomplete_txt" autocomplete="off"/></td>';
    html += '<td width="8%"><input type="text" data-type="ItemBarCode" name="ItemBarCode[]" id="ItemBarCode_' + i + '"  class="form-control autocomplete_barcode_txt" autocomplete="off"/></td>';
    html += '<td width="22%">';
    html += '<div><select data-type="itemid" name="itemid[]"  id ="itemid_' + i + '" class="chosen-select" onchange="loadItemsDet(this.id,this.value)">';
    html += '<option value="" disabled selected>-- Select Item --</option>';
    html += '</select></div></td>';
    html += '<td width="8%" style="display: none"><input type="text" data-type="ItemDesc" name="ItemDesc[]" id="ItemDesc_' + i + '" class="form-control" autocomplete="off" readonly/></td>';
    html += '<td width="5%" >';
    html += '<div><select data-type="Storage" name="Storage[]"  id ="Storage_' + i + '" class="chosen-select" onchange="getDropdownLotList(this.id,this.value)">';
    html += '</select></div></td>';
    html += '<td width="10%">';
    html += '<div><select data-type="lotno" name="lotno[]"  id ="lotno_' + i + '" class="form-control chosen-select" onchange="loadLotDet(this.id,this.value)">';
    html += '<option value="" disabled selected>--Item LOT' + i + '--</option>';
    html += '</select></div></td>';
    html += '<td width="6%"><input type="text" data-type="Price" name="Price[]" id="Price_' + i + '" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" readonly/></td>';
    html += '<td width="8%"><input type="text" data-type="Stock" name="Stock[]" id="Stock_' + i + '" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" readonly/></td>';
    html += '<td width="5%"><input type="text" data-type="SQty" name="SQty[]" id="SQty_' + i + '"  class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" readonly/></td>';
    html += '<td width="5%"><input type="text" data-type="Qty" name="Qty[]" id="Qty_' + i + '"  onkeydown="enter(this.id,this.value)" class="form-control input-sm changesNo iQty" style="font-weight:bold; text-align: center;" autocomplete="off"/></td>';
    html += '<td width="4%"><input type="text" data-type="Unit" name="Unit[]" id="Unit_' + i + '" class="form-control" autocomplete="off" readonly></td>';
    html += '<td width="4%"><input type="text" data-type="Discp" name="Discp[]" id="Discp_' + i + '" onkeydown="enter(this.id,this.value)" class="form-control input-sm changesNo iDiscp" style="font-weight:bold; text-align: center;" autocomplete="off" readonly/></td>';
    html += '<td width="8%"><input type="text" data-type="Discount" name="Discount[]" id="Discount_' + i + '" class="form-control input-sm iDiscount" style="font-weight:bold; text-align: right;" autocomplete="off" readonly/></td>';
    html += '<td width="8%"><input type="text" data-type="Total" name="Total[]" id="Total_' + i + '" class="form-control input-sm iTotal" style="font-weight:bold; text-align: right;" autocomplete="off" readonly/></td>';
    html += '<td width="3%"><div class="btn-group btn-corner"><button type="button" tabindex="-1" class="btn btn-danger btn-xs delete" title="Delete This Row" onclick="removeRow(this)"><i class="fa fa-trash"></i></button></div></td>';
    html += '</tr>';

    $('#salesTable').append(html);
    getDropdownItemList(i);
    getDropdownStorageList(i)
    document.getElementById('ItemCode_'+i).focus();
    i++;
}

$(document).on('keypress', '.autocomplete_barcode_txt', function () {
    custid  = $('#customer_id').val()
    soid  = $('#so_id').val();
    //  alert(compcode)
    el = $(this).attr('id')
    //alert(el)
    $(this).autocomplete({
      source: function(req, res){
      $.ajax({
          url: "/get-item-del-code/all",
          dataType: "json",
          data:{'itemcode':encodeURIComponent(req.term),
                'custsid':encodeURIComponent(custid),
                'soid':encodeURIComponent(soid) },

          error: function (request, error) {
             console.log(arguments);
             alert(" Can't do because: " +  console.log(arguments));
          },

        success: function (data) {
          res($.map(data.data, function (item) {
            //alert('IQII:'+item.acc_head)
            return {
              label: item.item_bar_code,
              value: item.item_bar_code,
              itm_id: item.id,
              el:el,
            };
          }));
        }
      });
    },
      autoFocus:true,
      select: function(event, ui){
      //alert(ui.item.itm_id)
      $.get('/get-item-del-code/getdetails/'+ui.item.itm_id+'/'+soid+'/getfirst', function(data){
      item = data.data
      }).then(function(){

        id_arr = ui.item.el
        id = id_arr.split("_")
        $('tr.duplicate').removeClass('duplicate')
        checkDuplicateItem(id, item,true)
        $('Qty_'+id[1]).focus()
        totalQuantityCount()
        totalItemDiscount()
        totalAmount()
      })
    }
  })
})

/*
$(document).on('keypress', '.autocomplete_txt', function () {
    compcode = $('#company_code').val()
    custid  = $('#customer_id').val()
    //  alert(compcode)
    el = $(this).attr('id')
    //alert(el)
    $(this).autocomplete({
      source: function(req, res){
      $.ajax({
          url: "/get-item-code/all",
          dataType: "json",
          data:{'itemcode':encodeURIComponent(req.term),
                'custsid':encodeURIComponent(custid),
                'compcode':encodeURIComponent(compcode) },

          error: function (request, error) {
             console.log(arguments);
             alert(" Can't do because: " +  console.log(arguments));
          },

        success: function (data) {
          res($.map(data.data, function (item) {
            //alert('IQII:'+item.acc_head)
            return {
              label: item.item_code,
              value: item.item_code,
              itm_id: item.id,
              el:el,
            };
          }));
        }
      });
    },
      autoFocus:true,
      select: function(event, ui){
      //alert(ui.item.itm_id)
      $.get('get-item-code/getdetails/'+ui.item.itm_id+'/getfirst', function(data){
      item = data.data
      }).then(function(){

        id_arr = ui.item.el
        id = id_arr.split("_")
        $('tr.duplicate').removeClass('duplicate')
        checkDuplicateItem(id, item,true)
        $('Qty_'+id[1]).focus()
        //  calcluteTotalBill()
        //  totalQuantityCount()
      })
    }
  })
}) */

 function checkDuplicateItem(id, names,s_tag){
        //alert(id);
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
            $('#ItemCode_'+duplicateItemId).parent().parent('tr').addClass('duplicate')
            alert('You have selected duplicate Item!')
        }else{

      //  alert(names.id+'@'+names.item_name+'@'+names.item_desc+'@'+
      //   names.vUnitName+'@'+names.item_bal_stock+'@'+names.item_unit+'@Price:'+
      //   names.item_price+'@ordQty:'+names.item_ord_qty+'@Disc:'+names.item_ord_disc);

        $('#ItemCodeId_' + id[1]).val(names.id);
        $('#ItemCode_' + id[1]).val(names.item_code);
        $('#ItemBarCode_' + id[1]).val(names.item_bar_code);
        //$('#itemid_' + id[1]).val(names.item_name);
        $('#ItemDesc_' + id[1]).val(names.item_desc);
        $('#Price_' + id[1]).val(names.item_price);
        $('#Unit_' + id[1]).val(names.item_unit);
        $('#Stock_' + id[1]).val(names.item_bal_stock);
        $('#SQty_' + id[1]).val(names.item_ord_qty);
        $('#Qty_' + id[1]).val(names.item_ord_qty);
        $('#Discp_' + id[1]).val(names.item_ord_disc);
        if(s_tag) setDropdownItemList(names.id,id[1]); // this is for selection item code
        else $('#ItemCode_' + id[1]).val(names.item_code); // this is for selection item box
        $('#Qty_'+id[1]).focus()
      }
    }

    function inArray(needle, haystack) {
        var length = haystack.length;
        /*for(var i = 0; i < length; i++) {
            if(haystack[i].item == needle) return [true, haystack[i].id];
        }*/
        return [false];
    }

    function setDropdownItemList(itemid,id){
      //alert(document.getElementById('itemid_'+id)+':::');
      //alert(itemid+':::'+id);
      $("#itemid_"+id+" > [value=" + itemid + "]").attr("selected", "true").trigger('chosen:updated');
    //  $("#delivered_to").attr("selected", "true").trigger('chosen:updated');

    }

    function getDropdownStorageList(i){
      var comp_code = $('#company_code').val();
      var w_house = $('#itm_warehouse').val();
      i = parseInt(i);
      //alert(i+','+comp_code+','+w_house);
      $.get('{{ url('/') }}/storageLookup/'+comp_code+'/'+w_house, function(response) {
        var selectList = $('select[id="Storage_'+i+'"]');
        selectList.chosen();
        selectList.empty();
        selectList.append('<option value="" disabled selected>LOAD</option>');
        $.each(response, function(index, element) {
          selectList.append('<option value="' + element.id + '">' +element.stor_code +'('+ element.stor_name +')</option>');
        });
        selectList.trigger('chosen:updated');
      });
    }

    function getDropdownLotList(el,oldLot){
      //i = parseInt(i);
      i = el.split("_")[1];
      //alert(i);
      var storageid = $('#Storage_'+i).val();
      var itemid = $('#ItemCodeId_'+i).val();

    //  alert(i+','+itemid+','+storageid+','+oldLot);
      $.get('{{ url('/') }}/LotLookup/'+itemid+'/'+storageid, function(response) {
        var selectList = $('select[id="lotno_'+i+'"]');
        selectList.chosen();
        selectList.empty();
        //$('#itemid_' + i).append('<option value="">--Select Item--</option>');
        selectList.append('<option value="" disabled selected>--Item LOT'+i+'--</option>');
        $.each(response, function(index, element) {
          //alert(element.id+ ' SD ' +element.item_name);
          if (oldLot ==  element.item_lot_no){
            selectList.append('<option value="' + element.item_lot_no + '" selected>' + element.item_lot_no +' ('+ element.stock +')</option>');
          }else{
            selectList.append('<option value="' + element.item_lot_no + '">' + element.item_lot_no +' ('+ element.stock +')</option>');
          }
        });
        selectList.trigger('chosen:updated');
      });
    }

    function getDropdownItemList(i){
      var so_id  = $('#so_id').val();
      i = parseInt(i);
      //alert(i + ","+ so_id);
      $.get('{{ url('/') }}/itemOrderLookup/'+so_id, function(response) {
        var selectList = $('select[id="itemid_'+i+'"]');
        selectList.chosen();
        selectList.empty();
        //$('#itemid_' + i).append('<option value="">--Select Item--</option>');
        selectList.append('<option value="" disabled selected>--Select Item1--</option>');
        $.each(response, function(index, element) {
          //alert(element.id+ ' SD ' +element.item_name);
          selectList.append('<option value="' + element.id + '">' + element.item_desc +':' + element.item_name +' ('+ element.itm_cat_name +')</option>');
        });
        selectList.trigger('chosen:updated');
      });
    }

    function getDropdownDeliveredToList(){
      var custid = $('#customer_id').val();
      var deliv_to = $('#so_del_to').val();
      //alert(custid);
      $.get('{{ url('/') }}/deliveredToLookup/' + custid, function(response) {
        var selectList = $('select[id="delivered_to"]');
        selectList.chosen();
        selectList.empty();
        selectList.append('<option value="">--Delivered To--</option>');
        $.each(response, function(index, element) {
        //  alert(element.id + "," + element.deliv_to);
          if(deliv_to == element.id){
            selectList.append('<option value="' + element.id + '" selected>' + element.deliv_to + '</option>');
          }else{
            selectList.append('<option value="' + element.id + '">' + element.deliv_to + '</option>');
          }
       });
        selectList.trigger('chosen:updated');
      });

    }
  </script>
@stop
