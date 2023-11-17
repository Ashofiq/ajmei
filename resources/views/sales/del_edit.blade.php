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
  <input type="hidden" name="menu_selection" id="menu_selection" value="SD@1" class="form-control" required>
<div class="title">
  <div  style="background-color:#e0e0e0" class="widget-header widget-header-small">
    <h6 class="widget-title smaller">
    <font size="2" color="blue"><b>Edit:Delivery Entry Form</b></font>
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
  <form id="so_Form" action="{{route('sales.delivery.update')}}" method="post">
    {{ csrf_field() }}
    <input type="hidden" id="so_id" name="so_id" value="{{ $rows->so_id }}" class="form-control  input-sm" autocomplete="off" required/> 
    <input type="hidden" id="del_id" name="del_id" value="{{ $rows->id }}" class="form-control  input-sm" autocomplete="off" required/>  
    <input type="hidden" id="so_inv_no" name="so_inv_no" value="{{ $rows_inv->inv_no }}" class="form-control  input-sm" autocomplete="off" required/>
    <input type="hidden" id="so_inv_id" name="so_inv_id" value="{{ $rows_inv->inv_id  }}" class="form-control  input-sm" autocomplete="off" required/>

    <input type="hidden" id="make_invoice" name="make_invoice" value="{{ $make_invoice }}" class="form-control  input-sm" autocomplete="off" required/>
    <input type="hidden" name="itm_warehouse" id="itm_warehouse" value="{{ $rows->del_m_warehouse_id}}" class="form-control  input-sm" autocomplete="off" required/>

    <div class="widget-body">
      <div class="widget-main">
         <div class="row">
          <div class="col-md-3">
            <div class="input-group ss-item-required">
              <div class="input-group-prepend ">
                  <div class="input-group-text" style="min-width:70px">Delivery Date:</div>
              </div>
              <input type="text" size = "10" name="delivery_date" onclick="displayDatePicker('delivery_date');"  value="{{ date('d-m-Y',strtotime($rows->del_date)) }}"  required />
              <a href="javascript:void(0);" onclick="displayDatePicker('delivery_date');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
           </div>
          </div>
          <div class="col-md-2">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:70px">Delivery No:</div>
                </div>
                <input type="text" name="del_no" value="{{ $rows->del_no }}" class="form-control  input-sm" autocomplete="off" required readonly/>
           </div>
          </div>
          <div class="col-md-2">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:70px">Sales Order No:</div>
                </div>
                <input type="text" name="so_order_no" value="{{ $rows->so_order_no }}" class="form-control  input-sm" autocomplete="off" required readonly/>
           </div>
          </div>
          <div class="col-md-3">
               <div class="input-group">
                 <div class="input-group-prepend">
                     <div class="input-group-text" style="min-width:80px">Company:</div>
                 </div>
                 <select name="company_code" class="form-control-sm autocomplete" id="company_code"  style="max-width:180px" required>
                 <option value="" >--Select--</option>
                      @if ($companies->count())
                         @foreach($companies as $company)
                             <option {{ $rows->del_comp_id == $company->comp_id ? 'selected' : '' }} value="{{$company->comp_id}}" >{{ $company->comp_id }}-{{ $company->name }}</option>
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
              <div class="input-group-text" style="min-width:70px">Refernce:</div>
            </div>
            <input type="text" name="reference_no" id="reference_no" value="{{$rows->del_po_no}}" class="form-control" required/>
          </div>
        </div>

        <div class="col-md-5">
            <div class="input-group">
              <select name="customer_id" id="customer_id" class="chosen-select"  onchange="getCustomerDetails(this.value)">
                  <option value="" disabled selected>- Select Customer -</option>
                  @foreach($customers as $customer)
                      <option {{ $rows->del_cust_id == $customer->id ? 'selected' : '' }} value="{{ $customer->id }}">{{ $customer->cust_name }}</option>
                  @endforeach
              </select>
              @error('customer_id')
              <span class="text-danger">{{ $message }}</span>
              @enderror
              </div>
          </div>

          <!-- <div class="col-md-3">
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text" style="min-width:70px">Outstanding:</div>
              </div>
              <input type="hidden" name="result_customer_id" id="result_customer_id" value="{{ $rows->del_cust_id }}" class="form-control" readonly required/>
              <input type="text" name="result_cust_outstanding" id="result_cust_outstanding" value="" class="form-control" readonly />
            </div>
          </div> -->

       </div>

       <div class="row"> 
          <div class="col-md-4">
            <div class="input-group ss-item-required">
               
              <input type="hidden" name="result_storage_id" id="result_storage_id" value="{{$rows->del_m_warehouse_id}}" class="form-control" readonly required/> 
              <!-- <div class="input-group-text" style="min-width:80px">Customer Disc(%):&nbsp;</div>
              <input type="text" size="15" name="result_cust_own_comm" id="result_cust_own_comm" value="0" class="form-control" readonly/>
              &nbsp;<div class="input-group-text" style="min-width:80px">SP Comm(%):&nbsp;</div><input type="text" size="15" name="result_cust_comm" id="result_cust_comm" value="0" class="form-control" readonly/> --> 
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
                      if($d->del_item_unit == 'KG'){ 
                        $s_total_amt = $d->del_item_price*$d->del_qty;
                      }else{
                        $s_total_amt = $d->del_item_price*$d->del_item_pcs;
                      }
                      $total_disc   = ($s_total_amt*$d->del_disc)/100;
                      $total_amt    = $s_total_amt - $total_disc;
                    ?>
                     <tr>
                       <td width="1.5%" class="text-center">{{$i}}</td>
                       <td width="2%" style="display: none"><input type="text" data-type="ItemCodeId" name="ItemCodeId[]" id="ItemCodeId_{{$i}}" value="{{$d->del_item_id}}" class="form-control item_id_class" autocomplete="off"></td>
                       <td width="5%" style="display: none"><input type="text" data-type="ItemCode" name="ItemCode[]" id="ItemCode_{{$i}}" value="{{$d->item_code}}" class="form-control autocomplete_txt" autocomplete="off"></td>
                       <td width="12%"><input type="text" data-type="itmcategory" name="itmcategory[]" id="itmcategory_{{$i}}" value="{{ $d->itm_cat_origin }}{{ $d->itm_cat_name }}" class="form-control" autocomplete="off" readonly></td>
                       <td width="20%">

                      <input type="text" data-type="itemid" name="itemid[]" id="itemid_{{$i}}" value="{{ $d->item_name }}({{ $d->itm_cat_name }})" class="form-control" autocomplete="off" readonly>
                      </td>
                       <!-- <td width="20%"> 
                       <div><select data-type="itemid" name="itemid[]"  id ="itemid_{{$i}}" class="form-control chosen-select" onchange="loadItemsDet(this.id,this.value)">
                           <option value="" disabled selected>- Select Item -</option>
                           @foreach($item_list as $cmb)
                               <option {{ $cmb->id == $d->del_item_id ? 'selected' : '' }} value="{{ $cmb->id }}">{{ $cmb->item_name }}({{ $cmb->itm_cat_name }})</option>
                           @endforeach
                         </select></div>
                      </td> -->
                      <td width="18%"><input type="text" data-type="ItemDesc" name="ItemDesc[]" id="ItemDesc_{{$i}}" value="{{$d->del_item_spec}}" class="form-control" autocomplete="off" ></td> 
                       <!-- <td width="5%" style="display: none" align="center">
                         <div><select data-type="Storage" name="Storage[]"  id ="Storage_{{$i}}" class="form-control chosen-select">
                             @foreach($stor_list as $stor)
                                 <option {{ $stor->id == $d->del_storage_id ? 'selected' : '' }} value="{{ $stor->id }}">{{ $stor->stor_code }}({{ $stor->stor_name }})</option>
                             @endforeach
                           </select></div>
                       </td>
                       <td width="10%">
                         <div><select data-type="lotno" name="lotno[]"  id ="lotno_{{$i}}" class="form-control chosen-select" onchange="loadLotDet(this.id,this.value)">
                           <option value="{{ $d->so_lot_no }}">{{$d->del_lot_no}}</option>
                         </select></div>
                       </td> -->
                      

                    <td width="8%" align="center"><input type="text" data-type="Stock" name="Stock[]" id="Stock_{{$i}}"  value="{{$d->stock + $d->del_qty}}" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" readonly></td>
                    <td width="8%" align="center"><input type="text" data-type="SQty" name="SQty[]" id="SQty_{{$i}}" value="{{$d->so_order_bal_qty + $d->del_item_pcs}}" onkeydown="enter(this.id,this.value)" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" readonly /></td>

                    <td width="7%" align="center"><input type="text" data-type="Size" name="Size[]" id="Size_{{$i}}" value="{{$d->del_item_size}}" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" readonly></td>

                    <td width="7%" align="center"><input type="text" data-type="QWeight" name="QWeight[]" id="QWeight_{{$i}}" value="{{$d->del_item_weight}}" class="form-control input-sm changesNo" style="font-weight:bold; text-align: center;" autocomplete="off" readonly></td>

                    <td width="7%" align="center"><input type="text" data-type="PCS" name="PCS[]" id="PCS_{{$i}}" value="{{$d->del_item_pcs}}" class="form-control input-sm changesNo" style="font-weight:bold; text-align: center;" autocomplete="off" ></td>

                    <td width="10%" align="center"><input type="text" data-type="Qty" name="Qty[]" id="Qty_{{$i}}" value="{{$d->del_qty}}" onkeydown="enter(this.id,this.value)" class="form-control input-sm changesNo iQty" style="font-weight:bold; text-align: center;" autocomplete="off"></td>
                    <td width="6%" align="center"><input type="text" data-type="Unit" name="Unit[]" id="Unit_{{$i}}"  value="{{$d->del_item_unit}}" class="form-control input-sm"  autocomplete="off" readonly></td>
                    <td width="7%" align="center"><input type="text" data-type="Price" name="Price[]" id="Price_{{$i}}" value="{{$d->del_item_price}}" class="form-control input-sm changesNo iPrice" style="font-weight:bold; text-align: center;" autocomplete="off" readonly></td>
                    <td width="4%" align="right"><input type="text" data-type="Discp" name="Discp[]" id="Discp_{{$i}}" value="{{$d->del_disc}}" onkeydown="enter(this.id,this.value)" class="form-control input-sm changesNo iDiscp" style="font-weight:bold; text-align: center;" autocomplete="off" readonly></td>
                    <td width="8%" align="right"><input type="text" data-type="Discount" name="Discount[]" id="Discount_{{$i}}" value="{{number_format($total_disc,0)}}" class="form-control input-sm iDiscount"  style="font-weight:bold; text-align: right;" autocomplete="off" readonly></td>
                    <td width="8%" align="right"><input type="text" data-type="Total" name="Total[]" id="Total_{{$i}}" value="{{number_format($total_amt,0)}}" class="form-control input-sm iTotal" style="font-weight:bold; text-align: right;" autocomplete="off" readonly></td>
                    @if($i == 1)
                      <td width="3%"></td>
                    @else
                      <td width="3%"><div class="btn-group btn-corner"><button type="button" tabindex="-1" class="btn btn-danger btn-xs delete" title="Delete This Row" onclick="removeRow(this)"><i class="fa fa-trash"></i></button></div></td>
                    @endif
                   </tr>
                   <?php $i = $i + 1; ?>
                   @endforeach
                  </tbody>
                 </table>
               </div> 
              </div>

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
                      <td width="12%"><input type="text" id="n_sub_total"  name="n_sub_total" value="{{ $rows->del_sub_total }}" style="background-color: rgba(255,246,125); font-weight:bold; text-align: right; font-size: 16px;" class="form-control input-sm" placeholder="Sub Total" autocomplete="off" readonly/></td>
                      <td width="5%"><input type="text" id="n_disc_per" name="n_disc_per" value="{{ $rows->del_disc_per }}" class="form-control input-sm changesNo" placeholder="Disc(%)" autocomplete="off"/></td>
                      <td width="8%"><input type="text" id="n_discount" name="n_discount" value="{{ number_format($rows->del_disc_value,0) }}" class="form-control input-sm changesNo" style="font-weight:bold; text-align: right; font-size: 16px;" placeholder="Discount" autocomplete="off"/></td>
                      <td width="15%"><input type="text" id="n_total_disc" name="n_total_disc" value="{{ $rows->del_total_disc }}" style="background-color: rgba(255,246,125); font-weight:bold; text-align: right; font-size: 16px;" class="form-control input-sm" placeholder="Total Discount" autocomplete="off" readonly/></td>

                      <td width="15%" style="display: none"><input type="text" id="n_total_gross" name="n_total_gross" value="{{ $rows->del_gross_amt }}" style="background-color: rgba(195, 248, 150); font-weight:bold; text-align: right; font-size: 16px;" class="form-control input-sm" placeholder="Total Gross" autocomplete="off"/></td>
                      <td width="5%" style="display: none"><input type="text" id="n_vat_per" name="n_vat_per" value="{{ $rows->del_vat_per }}" class="form-control input-sm changesNo" placeholder="" autocomplete="off" readonly/></td>
                      <td width="12%" style="display: none"><input type="text" id="n_total_vat" name="n_total_vat" value="{{ $rows->so_vat_value }}" class="form-control input-sm" placeholder="Total VAT" style="font-weight:bold; text-align: right; font-size: 16px;" autocomplete="off" readonly/></td>

                      <td width="15%" ><input type="text" id="n_net_amount" name="n_net_amount" value="{{ $rows->del_net_amt}}" style="background-color: rgba(255,246,125); font-weight:bold; text-align: right; font-size: 16px;" class="form-control input-sm" placeholder="Net Amount" autocomplete="off" readonly/></td> 
                    </tr>
                 </tbody>
                 </table>
               </div>
              </div>

              <!-- <div class="row">
                <div class="col-md-10">
                 <table class="table table-striped table-data table-view">
                   <tbody style="background-color: #ffffff;">
                     <tr style="display: none">
                       <td colspan="10" align="right"><input type="text" data-type="total_qty" name="total_qty" id="total_qty" value="0" style="font-weight:bold; text-align: right;" readonly></td>
                       <td width="2%" align="right"><input type="text" data-type="total_discount" name="total_discount" id="total_discount" value="0.00" style="font-weight:bold; text-align: right;" readonly></td>
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
                       <td width="15%" align="right"><input type="text" data-type="carring_cost" name="carring_cost" id="carring_cost" value="{{ number_format($rows->del_carring_cost,0) }}" class="form-control input-sm changesNo" style="background-color: rgba(255,246,125); font-weight:bold; text-align: right;" maxlength="23" size="23"></td> 
                     </tr> 
                     <tr> 
                       <td colspan="12" align="right"><b>Labour Cost:</b></td>
                       <td width="15%" align="right"><input type="text" data-type="labour_cost" name="labour_cost" id="labour_cost" value="{{ number_format($rows->del_labour_cost,0) }}" class="form-control input-sm changesNo" style="background-color: rgba(255,246,125); font-weight:bold; text-align: right;" maxlength="23" size="23"></td> 
                     </tr> 
                     <tr> 
                       <td colspan="12" align="right"><b>Load/Unload Cost:</b></td>
                       <td width="15%" align="right"><input type="text" data-type="load_unload_cost" name="load_unload_cost" id="load_unload_cost" value="{{ number_format($rows->del_load_unload_cost,0) }}" class="form-control input-sm changesNo" style="background-color: rgba(255,246,125); font-weight:bold; text-align: right;" maxlength="23" size="23"></td> 
                     </tr> 
                     <tr> 
                       <td colspan="12" align="right"><b>Service Charge:</b></td>
                       <td width="15%" align="right"><input type="text" data-type="service_charge" name="service_charge" id="service_charge" value="{{ number_format($rows->del_service_charge,0) }}" class="form-control input-sm changesNo" style="background-color: rgba(255,246,125); font-weight:bold; text-align: right;" maxlength="23" size="23"></td> 
                     </tr> 
                     <tr> 
                       <td colspan="12" align="right"><b>Other Cost:</b></td>
                       <td width="15%" align="right"><input type="text" data-type="other_cost" name="other_cost" id="other_cost" value="{{ number_format($rows->del_other_cost,0) }}" class="form-control input-sm changesNo" style="background-color: rgba(255,246,125); font-weight:bold; text-align: right;" maxlength="23" size="23"></td> 
                     </tr> 
                     <tr> 
                       <td colspan="12" align="right"><b>Net Amount:</b></td>
                       <td width="15%" align="right"><input type="text" data-type="t_n_net_amount" name="t_n_net_amount" id="t_n_net_amount" value="{{ number_format($rows->del_net_amt,0) }}" class="form-control input-sm" style="background-color: rgba(255,246,125); font-weight:bold; text-align: right;" maxlength="23" size="23" readonly></td> 
                     </tr>
                    </tbody>
                 </table>
               </div></div>

               </div>
               
               <!-- <div role="tabpanel" class="tab-pane" id = "deliveryinf">
                 <div class="row">
                   <div class="col-md-3.5">
                     <div class="input-group">
                         <div class="input-group-prepend ">
                             <div class="input-group-text" style="min-width:80px">Required Delivery Date:</div>
                         </div>
                         <input type="text" size = "15" name="delivery_date" onclick="displayDatePicker('delivery_date');"  value="{{ date('d-m-Y',strtotime($rows->del_req_date)) }}"  required />
                         <a href="javascript:void(0);" onclick="displayDatePicker('delivery_date');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>  
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
                                  <textarea id="address1" name="address1" rows="2" cols="100" class="form-control config" placeholder="Narration" maxlength="500">{{ $rows->del_add }}</textarea>
                              </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-5">
                        <div class="input-group">
                            <div class="input-group-prepend ">
                                <div class="input-group-text" style="min-width:80px">Contact No:</div>
                            </div>
                            <input type="text" id="contact_no" name="contact_no" value="{{ $rows->del_cont_no }}" class="form-control  input-sm" autocomplete="off"/>
                          </div>
                       </div>
                     </div>

                     <div class="row">
                       <div class="col-md-5">
                         <div class="input-group">
                             <div class="input-group-prepend ">
                                 <div class="input-group-text" style="min-width:80px">Reference:</div>
                             </div>
                             <input type="text" name="cust_ref" value="{{ $rows->del_cust_ref }}" class="form-control  input-sm" autocomplete="off"/>
                           </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-5">
                            <div class="input-group">
                              <select name="courr_id" id="courr_id" class="chosen-select" >
                                  <option value="" disabled selected>- Select Courrier To -</option>
                                  @foreach($courr_list as $courr)
                                      <option {{ $rows->del_courrier_to == $courr->id ? 'selected' : '' }} value="{{ $courr->id }}">{{ $courr->courrier_to }}</option>
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
                                 <select name="condition_tag" id="condition_tag" class="chosen-select" onchange="getAutoComments(this.id,this.value)">
                                     <option {{ $rows->del_courrier_cond == 'Non Condition'?'Selected':'' }}>Non Condition</option>
                                     <option {{ $rows->del_courrier_cond == 'Condition'?'Selected':'' }}>Condition</option>
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
                                <textarea name="comments" rows="2" cols="100" class="form-control config" maxlength="500">{{ $rows->del_comments }}</textarea>
                            </div>
                         </div>
                       </div>

               </div> -->
           </div>
         </div>
       </div>
      <div class="row justify-content-left">
          <div class="col-sm-12 text-left">
            <button class="btn btn-sm btn-success" type="button" id='btn1' ><i class="fa fa-save"></i> Update</button>
            <!-- button class="btn btn-sm btn-success" type="button" id='btn3'><i class="fa fa-save"></i>Print</button -->
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
  $(document).ready(function() {
    //getDropdownItemList(1);
    //getDropdownDeliveredToList();
    totalQuantityCount();
    totalDiscountCount();
    totalAmountCount();
  });

  var form = document.getElementById('so_Form');

  document.getElementById('btn1').onclick = function() {
    form.action = '{{route("sales.delivery.update")}}';
    if(formcheck()){
      form.submit();
    }
  }

  // document.getElementById('btn4').onclick = function() {
  //   var del_id = $('#del_id').val();
  //   if(del_id != null) {
  //     url = "{{url('delivery-challan-pdf')}}/"+del_id;
  //     form.action = url;
  //     form.target = "_blank";
  //     form.submit();
  //   }else{
  //     alert("Challan can't be empty");
  //   }
  // }

  // document.getElementById('btn6').onclick = function() {

  //   var customer_id = $('#customer_id').val();
  //   var company_code = $('#company_code').val();
  //   var order_date = $('#delivery_date').val();

  //   //alert(customer_id + company_code + order_date);

  //   if(customer_id != null) {
  //     url = "{{url('/rpt/sub-ledger1/')}}/"+order_date+'/'+company_code+'/'+customer_id;
  //     form.action = url;
  //     form.target = "_blank";
  //     form.submit();
  //   }else{
  //     alert("Customer can't be empty");
  //   }
  // }

  // document.getElementById('btn5').onclick = function() {
  //   var del_id = $('#del_id').val();
  //   if(del_id != null) {
  //     url = "{{url('delivery-invoice-pdf')}}/"+del_id;
  //     form.action = url;
  //     form.target = "_blank";
  //     form.submit();
  //   }else{
  //     alert("Invoice can't be empty");
  //   }
  // }

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
  if(isSubmit) formSubmit();
  console.log(fields);
}

function formSubmit()
{
    $('#so_Form').submit()
}
</script>

<script>
$(document).on('change keyup blur', '.changesNo', function () {
  id_arr = $(this).attr('id');
  id = id_arr.split("_");
  //alert(id[0]);
  //total_qty = $('#total_qty').val();

  unit          = $('#Unit_' + id[1]).val();
  price         = $('#Price_' + id[1]).val();
  stockQuantity = $('#Stock_' + id[1]).val();
  orderqty      = $('#SQty_' + id[1]).val();
  quantity      = $('#Qty_' + id[1]).val(); 
  perDisc       = $('#Discp_' + id[1]).val();
  valueDisc     = $('#Discount_' + id[1]).val();

  n_disc_per = $('#n_disc_per').val()==''?'0.00':$('#n_disc_per').val();
  n_discount = $('#n_discount').val()==''?'0.00':$('#n_discount').val();
  n_vat_per  = $('#n_vat_per').val()==''?'0.00':$('#n_vat_per').val();

  qweight    = $('#QWeight_' + id[1]).val();
  pcs        = $('#PCS_' + id[1]).val();


  if(parseFloat(qweight) > 0 && parseFloat(pcs) > 0 && (parseFloat(pcs) <= parseFloat(orderqty))){ 
      quantity = parseFloat(parseFloat(qweight) * parseFloat(pcs))/1000; 
      //alert(quantity +':'+ parseFloat(stockQuantity) + '*' + parseFloat(pcs));
      if (parseFloat(quantity) > parseFloat(stockQuantity)){
      // alert(quantity +':'+ parseFloat(stockQuantity));
        // $('#Qty_' + id[1]).val(0);
      }else{
        $('#Qty_' + id[1]).val(quantity); 
      } 
  }else{
      $('#PCS_' + id[1]).val(0);
      $('#Qty_' + id[1]).val(0);
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

  // price_qty = (parseFloat(price)*parseFloat(quantity)).toFixed(0);
  // if(stockQuantity > 0){ 
  //   if(parseFloat(quantity) > parseFloat(orderqty) || 
  //         parseFloat(quantity) > parseFloat(stockQuantity)){
  //         $('#Qty_' + id[1]).val(0);
  //     }
  //     if (perDisc != '' && price_qty > 0){
  //       valueDisc = ((parseFloat(price_qty)*parseFloat(perDisc))/100).toFixed(0);
  //       $('#Discount_' + id[1]).val(valueDisc);
  //     }else{
  //       $('#Discount_' + id[1]).val(0);
  //     }
  // }else{
  //     $('#Qty_' + id[1]).val(0);
  // }
  //alert(n_disc_per);
  if (quantity != '' && price != ''){
    total = parseFloat(price_qty) - $('#Discount_' + id[1]).val();
    $('#Total_'+id[1]).val(parseFloat(total).toFixed(0));
  }else{
    $('#Total_'+id[1]).val(parseFloat(0).toFixed(0));
  }


  if (n_disc_per != '' && n_disc_per > 0){
      n_sub_total = $('#n_sub_total').val();
      discount = ((n_sub_total * n_disc_per) / 100).toFixed(0);
      //alert(discount);
      $('#n_discount').val(parseFloat(discount).toFixed(0));
  }else if (n_discount != '' && n_disc_per > 0){
      n_sub_total = $('#n_sub_total').val();
      discount = parseFloat(n_sub_total) - parseFloat(n_discount);
      $('#n_discount').val(parseFloat(discount).toFixed(0));
      $('#n_disc_per').val(parseFloat(0).toFixed(0));
  }

  if(n_vat_per!=''){
    n_total_gross = $('#n_total_gross').val();
    //n_total_gross = parseFloat($('#n_sub_total').val()) - parseFloat($('#n_discount').val());
    n_total_vat = ((parseFloat(n_total_gross) * parseFloat(n_vat_per)) / 100).toFixed(0);
    $('#n_total_vat').val(parseFloat(n_total_vat).toFixed(0));
  }else{
    $('#n_total_vat').val(parseFloat(0).toFixed(0));
  }
 
  //totalAmountCount();
  totalQuantityCount(); 
  totalDiscountCount();
  totalAmountCount();
 
});

function totalQuantityCount()
{
    var total_qty = 0;
    $('.iQty').each(function(){
        if(parseFloat($(this).val())>0)
            total_qty += parseFloat($(this).val());
    });
    $('#total_qty').val(total_qty);
}

// function totalItemDiscount()
// {
//     var total_discount = 0;
//     $('.iDiscount').each(function(){
//         if(parseFloat($(this).val())>0)
//             total_discount += parseFloat($(this).val());
//     });
//     $('#total_discount').val(total_discount.toFixed(2));

//     n_discount = $('#n_discount').val();
//     if(n_discount>0) discount = parseFloat(total_discount) + parseFloat(n_discount);
//     else discount = total_discount;
//     $('#n_total_disc').val(parseFloat(discount).toFixed(2));
// }

function totalDiscountCount()
{
    var total_discount = 0;
    $('.iDiscount').each(function(){
        if(parseFloat($(this).val())>0)
            // total_discount += parseFloat($(this).val());
            total_discount += parseFloat($(this).val().replace(',', ''));
    });
    $('#total_discount').val(total_discount.toFixed(0));

    n_discount = $('#n_discount').val();
    if(n_discount>0) discount = parseFloat(total_discount) + parseFloat(n_discount);
    else discount = total_discount;
    $('#n_total_disc').val(parseFloat(discount).toFixed(0));
}

function totalAmountCount()
{
    var total_amount = 0;
    $('.iTotal').each(function(){
        if(parseFloat($(this).val())>0)
          //  total_amount += parseFloat($(this).val());
            total_amount += parseFloat($(this).val().replace(/,/g, ""));
    });
    $('#total_amount').val(parseFloat(total_amount).toFixed(0));
    $('#n_sub_total').val(parseFloat(total_amount).toFixed(0));

    n_total_disc = $('#n_total_disc').val();
    if (n_total_disc != ''){
      n_sub_total = $('#n_sub_total').val();
      n_discount = $('#n_discount').val();
      //n_net_amount = parseFloat(n_sub_total) - parseFloat(n_total_disc);
      n_net_amount = parseFloat(n_sub_total) - parseFloat(n_discount);
      $('#n_total_gross').val(parseFloat(n_net_amount).toFixed(0));
    }else{
        n_net_amount  = total_amount;
    }

    n_total_vat = $('#n_total_vat').val()==""?'0.00':$('#n_total_vat').val();
    if (n_total_vat != ''){
      n_total_gross = $('#n_total_gross').val();
      n_net_amount = parseFloat(n_total_gross);// + parseFloat(n_total_vat);
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
    
    total_n_net_amount =  parseFloat(n_net_amount);
    // $('#n_net_amount').val(parseFloat(total_n_net_amount).toFixed(0));
    $('#t_n_net_amount').val(parseFloat(total_n_net_amount).toFixed(0));


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
   // alert($('#total_discount').val() + '+' + $('#n_discount').val());
    n_total_disc = parseFloat($('#total_discount').val()) + parseFloat($('#n_discount').val());
    $('#n_total_disc').val(parseFloat(n_total_disc).toFixed(0));
    n_total_gross = parseFloat(n_sub_total) - parseFloat(n_total_disc);
    $('#n_total_gross').val(parseFloat(n_total_gross).toFixed(0));
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


function getStorageByWearId(w_house){
    var comp_code = $('#company_code').val();
    var total_qty = $('#total_qty').val();
    var result_wh_id = $('#result_wh_id').val();

    if(total_qty>0){
      alert("Can't change warehoue if LOT No/Qty is already Defined");
      getDropdownWarehouseList(result_wh_id);
      return false;
    }
  //  alert('get-storage-inf/getdetails/'+comp_code+'/'+w_house+'/getfirst');

    $.ajax({  //create an ajax request to display.php
      type: "GET",
      url: 'get-storage-inf/getdetails/'+comp_code+'/'+w_house+'/getfirst',
      success: function (data) {
        $("#result_storage_id").val(data.id)
        $("#result_wh_id").val(w_house)
        getDropdownStorageList(1)
      }
    });
}

function loadLotDet(el,lotno){
    i = el.split("_")[1];
    var storgae_id = $('#Storage_'+i).val();
    var itemid = $('#ItemCodeId_'+i).val();
    var result_cust_own_comm = $('#result_cust_own_comm').val();
    var qty = 0;
    var j = 1;

    //Checked the user qty
    $('.iQty').each(function(){
        item = $('#ItemCodeId_'+j).val();
        lot    = $('#lotno_'+j).val();
        //alert(j+'-'+item+' L-'+lot)
        if(parseFloat($(this).val())>0 && item == itemid && lotno == lot)
            qty += parseFloat($(this).val());
        j += 1;
    });
    //alert(qty);
    //alert(i+'-'+itemid+'-'+lotno)
    $.ajax({  //create an ajax request to display.php
      type: "GET",
      url: '/get-stock-inf/'+storgae_id+'/'+itemid+'/'+lotno+'/getfirst',
      success: function (data) {
        //alert(data.stock);
        qty = parseFloat(data.stock) - parseFloat(qty);
        $("#Stock_"+i).val(qty);
        $('#Qty_'+i).focus();
        $("#Discp_"+i).val(result_cust_own_comm);
      }
    });
}

function loadItemsDet(el,itemid){
    i = el.split("_")[1];
    var customer_id = $('#customer_id').val();
    var warehouse_id = $("#itm_warehouse").val();
    var storgae_id = $('#Storage_'+i).val();
    //alert(customer_id+'W'+warehouse_id+'S'+storgae_id);
    if (warehouse_id == ''){
      alert("Please Select Customer & Warehoue");
      return false;
    }

    $.get('/get-item-code/getdetails/'+itemid+'/'+customer_id+'/getfirst', function(data){
    item = data.data
    }).then(function(){
      id_arr = el
      id = id_arr.split("_")
      $('tr.duplicate').removeClass('duplicate')
      checkDuplicateItem(id, item,false)
      getDropdownStorageList(id[1])
      getDropdownLotList(id[1],item.id,storgae_id,false)
    })

}


function loadDeliveredDet(el,delid){
    //alert(delid);
    $.ajax({  //create an ajax request to display.php
      type: "GET",
      url: '/get-delivered-inf/getdetails/'+delid+'/getfirst',
      success: function (data) {
        //alert(data.deliv_add +'::'+ data.deliv_mobile);
        $("#address1").val(data.deliv_add);
        $("#contact_no").val(data.deliv_mobile);
      }
    });
}

function getDeliveredInformByCustId(custid){
    //alert(custid);
    $.ajax({  //create an ajax request to display.php
      type: "GET",
      url: '/get-delivered-inf_d/getdetails/'+custid+'/getfirst',
      success: function (data) {
        //alert(data.deliv_add +'::'+ data.deliv_mobile);
        $("#address1").val(data.deliv_add);
        $("#contact_no").val(data.deliv_mobile);
      }
    });
}

function getCustomerOutstanding(compid,custid){
    //alert(compid+'--'+custid);
    $.ajax({  //create an ajax request to display.php
      type: "GET",
      url: '/get-cust-oustanding-inf/getdetails/'+compid+'/'+custid+'/getfirst',
      success: function (data) {
        //alert(data.outstanding);
        $("#result_cust_outstanding").val(data.outstanding);
      }
    });
}

function getCustomerVAT(compid,custid){
    //alert(compid+'--'+custid);
    $.ajax({  //create an ajax request to display.php
      type: "GET",
      url: '/get-cust-vat-inf/getdetails/'+compid+'/'+custid+'/getfirst',
      success: function (data) {
        //alert(data.cust_own_comm);
        $("#n_vat_per").val(data.cust_VAT);
        $("#result_cust_comm").val(data.cust_overall_comm);
        $("#result_cust_own_comm").val(data.cust_own_comm);
      }
    });
}

function getCustomerDetails(custid){
  var customer_id = $('#customer_id').val();
  var company_code = $('#company_code').val();
  $('#result_customer_id').val(customer_id);
  getDropdownItemList(1);
  getDropdownDeliveredToList();
  getDeliveredInformByCustId(customer_id);
  getCustomerOutstanding(company_code,customer_id);
  getCustomerVAT(company_code,customer_id);
}

function getAutoComments(el,contag){
  var n_net_amount = $('#n_net_amount').val();
  //alert(n_net_amount +' :: '+contag);
  if(contag == 'Condition'){
    $('#comments').val(n_net_amount);
  }else{
    $('#comments').val('');
  }
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
    totalDiscountCount();
    totalAmountCount();
}

function row_increment() {

    var i = $('#salesTable tr').length;
    html = '<tr>';
    html += '<td width="1.5%" class="text-center">' + i + '</td>';
    html += '<td width="2%" style="display: none"><input type="text" data-type="ItemCodeId" name="ItemCodeId[]" id="ItemCodeId_' + i + '"  class="form-control item_id_class" autocomplete="off"></td>';
    html += '<td width="5%" style="display: none"><input type="text" data-type="ItemCode" name="ItemCode[]" id="ItemCode_' + i + '" class="form-control autocomplete_txt" autocomplete="off"></td>';
    html += '<td width="7%"><input type="text" data-type="ItemBarCode" name="ItemBarCode[]" id="ItemBarCode_' + i + '" class="form-control autocomplete_barcode_txt" autocomplete="off"></td>';
    html += '<td width="23%">';
    html += '<div><select data-type="itemid" name="itemid[]"  id ="itemid_' + i + '" class="chosen-select" onchange="loadItemsDet(this.id,this.value)">';
    html += '<option value="" disabled selected>-- Select Item --</option>';
    html += '</select></div></td>';
    html += '<td width="8%" style="display: none"><input type="text" data-type="ItemDesc" name="ItemDesc[]" id="ItemDesc_' + i + '" class="form-control" autocomplete="off" readonly></td>';

    html += '<td width="5%" style="display: none">';
    html += '<div><select data-type="Storage" name="Storage[]"  id ="Storage_' + i + '" class="chosen-select" >';
    html += '</select></div></td>';
    html += '<td width="10%">';
    html += '<div><select data-type="lotno" name="lotno[]"  id ="lotno_' + i + '" class="form-control chosen-select" onchange="loadLotDet(this.id,this.value)">';
    html += '<option value="" disabled selected>--Item LOT' + i + '--</option>';
    html += '</select></div></td>';

    html += '<td width="6%"><input type="text" data-type="Price" name="Price[]" id="Price_' + i + '" class="form-control input-sm changesNo iPrice" style="font-weight:bold; text-align: center;" autocomplete="off"></td>';
    html += '<td width="8%"><input type="text" data-type="Stock" name="Stock[]" id="Stock_' + i + '" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" readonly></td>';
    html += '<td width="5%"><input type="text" data-type="Qty" name="Qty[]" id="Qty_' + i + '"  onkeydown="enter(this.id,this.value)" class="form-control input-sm changesNo iQty" style="font-weight:bold; text-align: center;" autocomplete="off"></td>';
    html += '<td width="4%"><input type="text" data-type="Unit" name="Unit[]" id="Unit_' + i + '" class="form-control" autocomplete="off" readonly></td>';
    html += '<td width="4%"><input type="text" data-type="Discp" name="Discp[]" id="Discp_' + i + '" onkeydown="enter(this.id,this.value)" class="form-control input-sm changesNo iDiscp" style="font-weight:bold; text-align: center;" autocomplete="off"></td>';
    html += '<td width="8%"><input type="text" data-type="Discount" name="Discount[]" id="Discount_' + i + '" class="form-control input-sm iDiscount" style="font-weight:bold; text-align: right;" autocomplete="off" readonly></td>';
    html += '<td width="8%"><input type="text" data-type="Total" name="Total[]" id="Total_' + i + '" class="form-control input-sm iTotal" style="font-weight:bold; text-align: right;" autocomplete="off" readonly></td>';
    html += '<td width="3%"><div class="btn-group btn-corner"><button type="button" tabindex="-1" class="btn btn-danger btn-xs delete" title="Delete This Row" onclick="removeRow(this)"><i class="fa fa-trash"></i></button></div></td>';
    html += '</tr>';

    $('#salesTable').append(html);
    getDropdownItemList(i,0);
    getDropdownStorageList(i);
    document.getElementById('ItemCode_'+i).focus();
    i++;
}

$(document).on('keypress', '.autocomplete_barcode_txt', function () {
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
})

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
 })

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
    //     names.vUnitName+'@'+names.item_bal_stock+'@'+names.item_unit+'@'+
      //    names.item_price);

        $('#ItemCodeId_' + id[1]).val(names.id);
        $('#ItemCode_' + id[1]).val(names.item_code);
        $('#ItemBarCode_' + id[1]).val(names.item_bar_code);
      //  $('#itemid_' + id[1]).val(names.item_name);
        $('#ItemDesc_' + id[1]).val(names.item_desc);
        $('#Price_' + id[1]).val(names.item_price);
        $('#Unit_' + id[1]).val(names.item_unit);
      //  $('#Stock_' + id[1]).val(names.item_bal_stock);
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

    function getDropdownWarehouseList(w_id){
      var comp_code = $('#company_code').val();
      //alert(comp_code+' A '+w_id);
      //i = parseInt(i);
      $.get('{{ url('/') }}/warehouseLookup1/'+comp_code, function(response) {
        var selectList = $('select[id="itm_warehouse"]');
        selectList.chosen();
        selectList.empty();
        $.each(response, function(index, element) {
          if (w_id ==  element.w_ref_id){
            selectList.append('<option value="' + element.w_ref_id + '" selected>' + element.ware_name +'</option>');
          }else{
            selectList.append('<option value="' + element.w_ref_id + '">' + element.ware_name +'</option>');
          }
        });
        selectList.trigger('chosen:updated');
      });
    }



    function getDropdownLotList(i,itemid,storageid,oldLot){
      i = parseInt(i);
    //  alert(i + ","+ itemid);
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

    function getDropdownStorageList(i){
      i = parseInt(i);
      var comp_code = $('#company_code').val();
    //  var w_house = $('#Warehouse_'+i).val();
      var w_house = $('#itm_warehouse').val();
    //alert(i);
      $.get('{{ url('/') }}/storageLookup/'+comp_code+'/'+w_house, function(response) {
        var selectList = $('select[id="Storage_'+i+'"]');
        selectList.chosen();
        selectList.empty();
        $.each(response, function(index, element) {
          selectList.append('<option value="' + element.id + '">' +element.stor_code +'('+ element.stor_name +')</option>');
        });
        selectList.trigger('chosen:updated');
      });
    }

    function getDropdownItemList(i,oldItem){
      var compcode = $('#company_code').val();
      var custid = $('#customer_id').val();
      i = parseInt(i);
      //alert(i + ",CO:"+ compcode + ",Cu:" + custid);
      $.get('{{ url('/') }}/itemLookup/'+compcode, function(response) {
        var selectList = $('select[id="itemid_'+i+'"]');
        selectList.chosen();
        selectList.empty();
        //$('#itemid_' + i).append('<option value="">--Select Item--</option>');
        selectList.append('<option value="" disabled selected>--Select Item'+i+'+--</option>');
        $.each(response, function(index, element) {
          //alert(element.id+ ' SD ' +element.item_name);
          if (oldItem ==  element.id){
            selectList.append('<option value="' + element.id + '" selected>' + element.item_desc +':'+ element.item_name +' ('+ element.itm_cat_name +')</option>');
          }else{
            selectList.append('<option value="' + element.id + '">' + element.item_desc +':'+ element.item_name +' ('+ element.itm_cat_name +')</option>');
          }

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
