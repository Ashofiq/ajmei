@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/blogic_css/sales_quot_tb.css') }}" />
    <!-- script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>tinymce.init({ selector:'textarea' });</script -->

 <!-- include libraries(jQuery, bootstrap) -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">

<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

@stop

@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="CRM@1" class="form-control" required>
<div class="title">
  <div  style="background-color:#e0e0e0" class="widget-header widget-header-small">
    <h6 class="widget-title smaller">
    <font size="2" color="blue"><b>Quotation</b></font>
    </h6>
    <div class="widget-toolbar">
      <a href="{{route('sales.quot.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
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

  <form id="quot_Form" action="{{route($action)}}" method="post">
    <input type="text" name="id" value="{{$id}}" class="form-control  input-sm" autocomplete="off" required/>
    {{ csrf_field() }}
    <div class="widget-body">
      <div class="widget-main">
         <div class="row">
          <div class="col-md-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                      <div class="input-group-text" style="min-width:80px">Existing Customer:</div>
                  </div>
                  <select name="existing_cust" id="existing_cust" class="form-control-sm autocomplete" onchange="getCustomer(this.value)" required>
                    <option value="" >--Select--</option>
                    <option {{ $rows_m->quot_exit_cust == 1 ? 'selected' : '' }} value="1" >Yes</option>
                    <option {{ $rows_m->quot_exit_cust == 0 ? 'selected' : '' }} value="0" >No</option>
                  </select>
                 </div>
          </div>
          <div class="col-md-3">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:70px">Quotation Ref:</div>
                </div>
                <input type="text" name="quot_ref" value="{{$rows_m->quot_ref_no}}" class="form-control  input-sm" autocomplete="off" required readonly/>
           </div>
          </div>
          <div class="col-md-3">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:70px">Quotation Date:</div>
                </div>
                <input type="text" size = "15" name="quot_date" onclick="displayDatePicker('quot_date');"  value="{{ date('d-m-Y',strtotime($rows_m->quot_date)) }}"  required />
                <a href="javascript:void(0);" onclick="displayDatePicker('quot_date');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>

                <!-- input type="date" name="quot_date" value="{{$rows_m->quot_date}}" class="form-control  input-sm" autocomplete="off" required/ -->
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
                             <option {{ $company_code == $rows_m->quot_comp_id ? 'selected' : '' }} value="{{$company->comp_id}}" >{{ $company->comp_id }}-{{ $company->name }}</option>
                         @endforeach
                     @endif
                 </select>
                </div>
          </div>
      </div>

     <div class="row">
        <div class="col-md-12">
          <div class="input-group">
            <input type="text" name="quot_to" value="{{$rows_m->quot_to}}" class="form-control" placeholder="To" required/>
           </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="input-group">
            <input type="text" name="quot_writing_to" value="{{$rows_m->quot_writ_to}}" class="form-control" placeholder="Managing Director"  required/>
          </div>
        </div>
      </div>

    <div class="exit_customer" {{ $rows_m->quot_exit_cust == 1 ?: 'hidden' }}>
      <div class="row">
        <div class="col-md-12">
          <div class="input-group">
            <select data-type="exit_customer_id" name="exit_customer_id"  id ="exit_customer_id" class="form-control chosen-select" onchange="fetchAddress(this.value)">
              <option value="" disabled selected>- Select Customer -</option>
              @foreach($customers as $cmb)
                  <option  {{ $rows_m->quot_cust_id == $cmb->id ? 'selected' : '' }} value="{{ $cmb->id }}">{{ $cmb->cust_name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
   </div>

    <div class="nonexit_customer" {{ $rows_m->quot_exit_cust == 0 ?: 'hidden' }}>
      <div class="row">
        <div class="col-md-12">
          <div class="input-group">
            <input type="text" name="quot_customer" value="{{$rows_m->quot_cust_name}}" class="form-control" placeholder="Customer Name"  required/>
           </div>
        </div>
      </div>
    </div>

    <div class="row">
        <div class="col-md-12">
          <div class="input-group">
            <input type="text" name="quot_add" id = "quot_add" value="{{$rows_m->quot_cust_add}}" class="form-control" placeholder="Customer Address"  required/>
           </div>
        </div>
    </div>

      <div class="row">
        <div class="col-md-12">
          <div class="input-group">
            <input type="text" name="quot_subj" value="{{$rows_m->quot_subj}}" class="form-control" placeholder="Subject "  required/>
           </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
            <!--div class="summernote">summernote 1</div-->
            <textarea class="summernote" rows="5" cols="40" name="quot_body" id="quot_body">{!! $rows_m->quot_body !!}</textarea>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <ul class="nav nav-tabs">
            <li class="nav-item">
              <a href="#Category1" class="nav-link" role="tab" data-toggle="tab">Cate-1</a>
            </li>
            <li class="nav-item">
              <a href="#Category2" class="nav-link" role="tab" data-toggle="tab">Cate-2</a>
            </li>
            <li class="nav-item">
              <a href="#Category3" class="nav-link" role="tab" data-toggle="tab">Cate-3</a>
            </li>
            <li class="nav-item">
              <a href="#Category4" class="nav-link" role="tab" data-toggle="tab">Cate-4</a>
            </li>
            <li class="nav-item">
              <a href="#Category5" class="nav-link" role="tab" data-toggle="tab">Cate-5</a>
            </li>
            <li class="nav-item">
              <a href="#Category6" class="nav-link" role="tab" data-toggle="tab">Cate-6</a>
            </li>
            <li class="nav-item">
              <a href="#Category7" class="nav-link" role="tab" data-toggle="tab">Cate-7</a>
            </li>
            <li class="nav-item">
              <a href="#Category8" class="nav-link" role="tab" data-toggle="tab">Cate-8</a>
            </li>
            <li class="nav-item">
              <a href="#Category9" class="nav-link" role="tab" data-toggle="tab">Cate-9</a>
            </li>
            <li class="nav-item">
              <a href="#Category10" class="nav-link" role="tab" data-toggle="tab">Cate-10</a>
            </li>
            <li class="nav-item">
              <a href="#Category11" class="nav-link" role="tab" data-toggle="tab">Terms & Cond.</a>
            </li>
          </ul>
         <div class="tab-content">
         <?php // Category 1------------------   ?>
           <div role="tabpanel" class="tab-pane active" id = "Category1">
             <table id="salesTable" class="table table-striped table-data table-report ">
               <thead class="salesTable">
                 <tr>
                   <th width="1%" class="text-center">&nbsp;&nbsp;</th>
                   <th width="64%" class="text-center">Item Category</th>
                   <th width="15%" class="text-center">Qty</th>
                   <th width="20%" class="text-center">Amount</th>
                 </tr>
               </thead>
               <tbody class="salesTable" style="background-color: #ffffff;">
                 <tr>
                   <td width="1%" class="text-center">1</td>
                   <td width="64%">
                     <select data-type="itemid1" name="itemid1"  id ="itemid1_1" class="form-control chosen-select" onchange="loadItemsDet(this.id,this.value,1)">
                       <option value="" selected>- Select Category 1 -</option>
                       @foreach($itm_cat as $cmb)
                           <option {{ $cmb->tab == 1 ? 'selected' : '' }}  value="{{ $cmb->id }}">{{ $cmb->itm_cat_name }}</option>
                       @endforeach
                     </select>
                  </td>
                  <?php
                  $quot_qty = '';
                  $quot_amount = '';
                  $quot_note = '';
                  $quot_cate_id = 0;
                  ?>
                  @foreach($itm_cat as $cmb)
                    <?php
                    if($cmb->tab == 1){
                      $quot_qty = $cmb->quot_qty;
                      $quot_amount = $cmb->quot_amount;
                      $quot_note = $cmb->quot_note;
                      $quot_cate_id = $cmb->quot_cate_id;
                    }
                    ?>
                  @endforeach
                   <td width="15%"><input type="text" data-type="qty1" name="qty1" id="qty1_1" value="{{ $quot_qty }}" class="form-control" autocomplete="off" ></td>
                   <td width="20%" align="center"><input type="text" data-type="amount1" name="amount1" value="{!! $quot_amount !!}" id="amount1_1" class="form-control input-sm" autocomplete="off" ></td>
                   <td width="5%" align="center"><input type="checkbox" data-type="delete1" name="delete1" id="delete1_1" value="1"> DEL</td>
                   <td style="display: none"><input type="text" name="tabno1" value="1"/></td>
               </tr>
                 <tr><td colspan="4"><textarea class="summernote" rows="5" cols="40" name="quot_note1" id="quot_note1_1">{!! $quot_note !!}</textarea></td></tr>
              </tbody>
              </table>

              <table id="salesTable_1" class="table table-striped table-data table-report ">
                <thead class="salesTable">
                </thead>
                <tbody class="salesTable" style="background-color: #ffffff;">
                   <div>
                    <?php $i = 1; ?>
                    @foreach($rows_d as $rowsd)
                    @if ($quot_cate_id == $rowsd->item_ref_cate_id)
                     <tr id="row1{{$i}}" class="dynamic-added">
                     <td width="3%">{{$i}}</td>
                     <td style="display: none"><input type="text" name="parameterid1[]" id="parameterid1_{{$i}}" value="{{$rowsd->quot_itm_id}}" placeholder="" class="form-control name_list" /></td>
                     <td><input type="text" name="parameter1[]" id="parameter1_{{$i}}" value="{{$rowsd->item_name}}" placeholder="Enter Parameter" class="form-control name_list" readonly /></td>
                     <td><input type="text" name="test1[]" id="test1_{{$i}}" value="{{$rowsd->quot_test_price}}" placeholder="Enter Price (Taka) /Test" class="form-control name_list" /></td>
                     <td><input type="text" name="kit1[]" id="kit1_{{$i}}" value="{{$rowsd->quot_kit_price}}" placeholder="Enter Price/Kit (25 Test)" class="form-control name_list" /></td>
                     <td><button type="button" name="remove" value="1" id="{{$i}}" class="btn btn-danger btn_remove">X</button></td>
                    </tr>
                     <?php $i += 1; ?>
                    @endif
                    @endforeach
                  </div>
                 </tbody>
              </table>
           </div>
     <?php // Category 2------------------   ?>
         <div role="tabpanel" class="tab-pane " id = "Category2">
           <table id="salesTable" class="table table-striped table-data table-report ">
             <thead class="salesTable">
               <tr>
                 <th width="1%" class="text-center">&nbsp;&nbsp;</th>
                 <th width="64%" class="text-center">Item Category</th>
                 <th width="15%" class="text-center">Qty</th>
                 <th width="20%" class="text-center">Amount</th>
               </tr>
             </thead>
             <tbody class="salesTable" style="background-color: #ffffff;">
               <tr>
                 <td width="1%" class="text-center">1</td>
                 <td width="64%">
                 <select  chosen width="'100%'"  data-type="itemid2" name="itemid2"  id ="itemid2_1" class="form-control chosen-select" onchange="loadItemsDet(this.id,this.value,2)">
                     <option value="" selected>- Select Category 2 -</option>
                     @foreach($itm_cat as $cmb)
                         <option {{ $cmb->tab == 2 ? 'selected' : '' }} value="{{ $cmb->id }}">{{ $cmb->itm_cat_name }}</option>
                     @endforeach
                   </select>
                </td>
                <?php
                $quot_qty = '';
                $quot_amount = '';
                $quot_note = '';
                $quot_cate_id = 0;
                ?>
                @foreach($itm_cat as $cmb)
                  <?php
                  if($cmb->tab == 2){
                    $quot_qty = $cmb->quot_qty;
                    $quot_amount = $cmb->quot_amount;
                    $quot_note = $cmb->quot_note;
                    $quot_cate_id = $cmb->quot_cate_id;
                  }
                  ?>
                  @endforeach
                 <td width="15%"><input type="text" data-type="qty2" name="qty2" id="qty2_1" value="{{ $quot_qty }}" class="form-control" autocomplete="off" ></td>
                 <td width="20%" align="center"><input type="text" data-type="amount2" name="amount2" id="amount2_1" value="{{ $quot_amount }}" class="form-control input-sm" autocomplete="off" ></td>
                 <td width="5%" align="center"><input type="checkbox" data-type="delete2" name="delete2" id="delete2_1" value="1"> DEL</td>
                 <td style="display: none"><input type="text" name="tabno2" value="2"/></td>
               </tr>
               <tr><td colspan="4"><textarea class="summernote" rows="5" cols="40" name="quot_note2">{!! $quot_note !!}</textarea></td></tr>
             </tbody>
           </table>
           <table id="salesTable_2" class="table table-striped table-data table-report ">
             <thead class="salesTable">
             </thead>
             <tbody class="salesTable" style="background-color: #ffffff;">
               <div>
                <?php $i = 1; ?>
                @foreach($rows_d as $rowsd)
                @if ($quot_cate_id == $rowsd->item_ref_cate_id)
                 <tr id="row2{{$i}}" class="dynamic-added">
                 <td width="3%">{{$i}}</td>
                 <td style="display: none"><input type="text" name="parameterid2[]" id="parameterid2_{{$i}}" value="{{$rowsd->quot_itm_id}}" placeholder="" class="form-control name_list" /></td>
                 <td><input type="text" name="parameter2[]" id="parameter2_{{$i}}" value="{{$rowsd->item_name}}" placeholder="Enter Parameter" class="form-control name_list" readonly /></td>
                 <td><input type="text" name="test2[]" id="test2_{{$i}}" value="{{$rowsd->quot_test_price}}" placeholder="Enter Price (Taka) /Test" class="form-control name_list" /></td>
                 <td><input type="text" name="kit2[]" id="kit2_{{$i}}" value="{{$rowsd->quot_kit_price}}" placeholder="Enter Price/Kit (25 Test)" class="form-control name_list" /></td>
                 <td><button type="button" name="remove" value="2" id="{{$i}}" class="btn btn-danger btn_remove">X</button></td>
                </tr>
                 <?php $i += 1; ?>
                @endif
                @endforeach
              </div>
              </tbody>
           </table>
         </div>

     <?php // Category 3------------------   ?>
         <div role="tabpanel" class="tab-pane " id = "Category3">
           <table id="salesTable" class="table table-striped table-data table-report ">
             <thead class="salesTable">
               <tr>
                 <th width="1%" class="text-center">&nbsp;&nbsp;</th>
                 <th width="64%" class="text-center">Item Category</th>
                 <th width="15%" class="text-center">Qty</th>
                 <th width="20%" class="text-center">Amount</th>
               </tr>
             </thead>
             <tbody class="salesTable" style="background-color: #ffffff;">
               <tr>
                 <td width="1%" class="text-center">1</td>
                 <td width="64%">
                 <select  chosen width="'100%'"  data-type="itemid3" name="itemid3"  id ="itemid3_1" class="form-control chosen-select" onchange="loadItemsDet(this.id,this.value,3)">
                     <option value="" selected>- Select Category 3 -</option>
                     @foreach($itm_cat as $cmb)
                         <option {{ $cmb->tab == 3 ? 'selected' : '' }} value="{{ $cmb->id }}">{{ $cmb->itm_cat_name }}</option>
                     @endforeach
                   </select>
                </td>
                <?php
                $quot_qty = '';
                $quot_amount = '';
                $quot_note = '';
                $quot_cate_id = 0;
                ?>
                @foreach($itm_cat as $cmb)
                  <?php
                  if($cmb->tab == 3){
                    $quot_qty = $cmb->quot_qty;
                    $quot_amount = $cmb->quot_amount;
                    $quot_note = $cmb->quot_note;
                    $quot_cate_id = $cmb->quot_cate_id;
                  }
                  ?>
                @endforeach
                 <td width="15%"><input type="text" data-type="qty3" name="qty3" id="qty3_1" value="{{ $quot_qty }}" class="form-control" autocomplete="off" ></td>
                 <td width="20%" align="center"><input type="text" data-type="amount3" name="amount3" id="amount3_1" value="{{ $quot_amount }}" class="form-control input-sm" autocomplete="off" ></td>
                 <td width="5%" align="center"><input type="checkbox" data-type="delete3" name="delete3" id="delete3_1" value="1"> DEL</td>
                 <td style="display: none"><input type="text" name="tabno3" value="3"/></td>
             </tr>
               <tr><td colspan="4"><textarea class="summernote" rows="5" cols="40" name="quot_note3" >{!! $quot_note !!}</textarea></td></tr>
              </tbody>
           </table>
           <table id="salesTable_3" class="table table-striped table-data table-report ">
             <thead class="salesTable">
             </thead>
             <tbody class="salesTable" style="background-color: #ffffff;">
               <div>
                <?php $i = 1; ?>
                @foreach($rows_d as $rowsd)
                @if ($quot_cate_id == $rowsd->item_ref_cate_id)
                 <tr id="row3{{$i}}" class="dynamic-added">
                 <td width="3%">{{$i}}</td>
                 <!-- td style="display: none"><input type="text" name="itemid3[]"  id ="itemid3_{{$i}}" value="{{$rowsd->item_ref_cate_id}}"/></td -->
                 <td style="display: none"><input type="text" name="parameterid3[]" id="parameterid3_{{$i}}" value="{{$rowsd->quot_itm_id}}" placeholder="" class="form-control name_list" /></td>
                 <td><input type="text" name="parameter3[]" id="parameter3_{{$i}}" value="{{$rowsd->item_name}}" placeholder="Enter Parameter" class="form-control name_list" readonly /></td>
                 <td><input type="text" name="test3[]" id="test3_{{$i}}" value="{{$rowsd->quot_test_price}}" placeholder="Enter Price (Taka) /Test" class="form-control name_list" /></td>
                 <td><input type="text" name="kit3[]" id="kit3_{{$i}}" value="{{$rowsd->quot_kit_price}}" placeholder="Enter Price/Kit (25 Test)" class="form-control name_list" /></td>
                 <td><button type="button" name="remove" value="3" id="{{$i}}" class="btn btn-danger btn_remove">X</button></td>
                </tr>
                 <?php $i += 1; ?>
                @endif
                @endforeach
              </div>
              </tbody>
           </table>
         </div>
         <?php // Category 4------------------   ?>
             <div role="tabpanel" class="tab-pane " id = "Category4">
               <table id="salesTable" class="table table-striped table-data table-report ">
                 <thead class="salesTable">
                   <tr>
                     <th width="1%" class="text-center">&nbsp;&nbsp;</th>
                     <th width="64%" class="text-center">Item Category</th>
                     <th width="15%" class="text-center">Qty</th>
                     <th width="20%" class="text-center">Amount</th>
                   </tr>
                 </thead>
                 <tbody class="salesTable" style="background-color: #ffffff;">
                   <tr>
                     <td width="1%" class="text-center">1</td>
                     <td width="64%">
                     <select  chosen width="'100%'"  data-type="itemid4" name="itemid4[]"  id ="itemid4_1" class="form-control chosen-select" onchange="loadItemsDet(this.id,this.value,4)">
                         <option value="" disabled selected>- Select Category 4 -</option>
                         @foreach($itm_cat as $cmb)
                             <option {{ $cmb->tab == 4 ? 'selected' : '' }} value="{{ $cmb->id }}">{{ $cmb->itm_cat_name }}</option>
                         @endforeach
                       </select>
                    </td>
                    <?php
                    $quot_qty = '';
                    $quot_amount = '';
                    $quot_note = '';
                    $quot_cate_id = 0;
                    ?>
                    @foreach($itm_cat as $cmb)
                      <?php
                      if($cmb->tab == 4){
                        $quot_qty = $cmb->quot_qty;
                        $quot_amount = $cmb->quot_amount;
                        $quot_note = $cmb->quot_note;
                        $quot_cate_id = $cmb->quot_cate_id;
                      }
                      ?>
                    @endforeach
                     <td width="15%"><input type="text" data-type="qty4" name="qty4" id="qty4_1" value="{{ $quot_qty }}" class="form-control" autocomplete="off" ></td>
                     <td width="20%" align="center"><input type="text" data-type="amount4" name="amount4" id="amount4_1" value="{{ $quot_amount }}" class="form-control input-sm" autocomplete="off" ></td>
                     <td width="5%" align="center"><input type="checkbox" data-type="delete4" name="delete4" id="delete4_1" value="1"> DEL</td>
                     <td style="display: none"><input type="text" name="tabno4" value="4"/></td>
                 </tr>
                   <tr><td colspan="4"><textarea class="summernote" rows="5" cols="40" name="quot_note4">{!! $quot_note !!}</textarea></td></tr>
                  </tbody>
               </table>
               <table id="salesTable_4" class="table table-striped table-data table-report ">
                 <thead class="salesTable">
                 </thead>
                 <tbody class="salesTable" style="background-color: #ffffff;">
                   <div>
                    <?php $i = 1; ?>
                    @foreach($rows_d as $rowsd)
                    @if ($quot_cate_id == $rowsd->item_ref_cate_id)
                     <tr id="row4{{$i}}" class="dynamic-added">
                     <td width="3%">{{$i}}</td>

                     <!--td style="display: none"><input type="text" name="itemid4[]"  id ="itemid4_{{$i}}" value="{{$rowsd->item_ref_cate_id}}"/></td -->
                     <td style="display: none"><input type="text" name="parameterid4[]" id="parameterid4_{{$i}}" value="{{$rowsd->quot_itm_id}}" placeholder="" class="form-control name_list" /></td>
                     <td><input type="text" name="parameter4[]" id="parameter4_{{$i}}" value="{{$rowsd->item_name}}" placeholder="Enter Parameter" class="form-control name_list" readonly /></td>
                     <td><input type="text" name="test4[]" id="test4_{{$i}}" value="{{$rowsd->quot_test_price}}" placeholder="Enter Price (Taka) /Test" class="form-control name_list" /></td>
                     <td><input type="text" name="kit4[]" id="kit4_{{$i}}" value="{{$rowsd->quot_kit_price}}" placeholder="Enter Price/Kit (25 Test)" class="form-control name_list" /></td>
                     <td><button type="button" name="remove" value="4" id="{{$i}}" class="btn btn-danger btn_remove">X</button></td>
                    </tr>
                     <?php $i += 1; ?>
                    @endif
                    @endforeach
                  </div>
                  </tbody>
               </table>
             </div>
             <?php // Category 5------------------   ?>
                 <div role="tabpanel" class="tab-pane " id = "Category5">
                   <table id="salesTable" class="table table-striped table-data table-report ">
                     <thead class="salesTable">
                       <tr>
                         <th width="1%" class="text-center">&nbsp;&nbsp;</th>
                         <th width="64%" class="text-center">Item Category</th>
                         <th width="15%" class="text-center">Qty</th>
                         <th width="20%" class="text-center">Amount</th>
                       </tr>
                     </thead>
                     <tbody class="salesTable" style="background-color: #ffffff;">
                       <tr>
                         <td width="1%" class="text-center">1</td>
                         <td width="64%">
                         <select  chosen width="'100%'"  data-type="itemid5" name="itemid5"  id ="itemid5_1" class="form-control chosen-select" onchange="loadItemsDet(this.id,this.value,5)">
                             <option value="" disabled selected>- Select Category 5 -</option>
                             @foreach($itm_cat as $cmb)
                                 <option {{ $cmb->tab == 5 ? 'selected' : '' }} value="{{ $cmb->id }}">{{ $cmb->itm_cat_name }}</option>
                             @endforeach
                           </select>
                        </td>
                        <?php
                        $quot_qty = '';
                        $quot_amount = '';
                        $quot_note = '';
                        $quot_cate_id = 0;
                        ?>
                        @foreach($itm_cat as $cmb)
                          <?php
                          if($cmb->tab == 5){
                            $quot_qty = $cmb->quot_qty;
                            $quot_amount = $cmb->quot_amount;
                            $quot_note = $cmb->quot_note;
                            $quot_cate_id = $cmb->quot_cate_id;
                          }
                          ?>
                        @endforeach
                         <td width="15%"><input type="text" data-type="qty5" name="qty5" id="qty5_1" value="{{ $quot_qty }}" class="form-control" autocomplete="off" ></td>
                         <td width="20%" align="center"><input type="text" data-type="amount5" name="amount5" id="amount5_1" value="{{ $quot_amount }}" class="form-control input-sm" autocomplete="off" ></td>
                         <td width="5%" align="center"><input type="checkbox" data-type="delete5" name="delete5" id="delete5_1" value="1"> DEL</td>
                         <td style="display: none"><input type="text" name="tabno5" value="5"/></td>
                     </tr>
                       <tr><td colspan="4"><textarea class="summernote" rows="5" cols="40" name="quot_note5">{!! $quot_note !!}</textarea></td></tr>
                      </tbody>
                   </table>
                   <table id="salesTable_5" class="table table-striped table-data table-report ">
                     <thead class="salesTable">
                     </thead>
                     <tbody class="salesTable" style="background-color: #ffffff;">
                       <div>
                        <?php $i = 1; ?>
                        @foreach($rows_d as $rowsd)
                        @if ($quot_cate_id == $rowsd->item_ref_cate_id)
                         <tr id="row5{{$i}}" class="dynamic-added">
                        <td width="3%">{{$i}}</td>
                         <!--td style="display: none"><input type="text" name="itemid5[]"  id ="itemid5_{{$i}}" value="{{$rowsd->item_ref_cate_id}}"/></td -->
                         <td style="display: none"><input type="text" name="parameterid5[]" id="parameterid5_{{$i}}" value="{{$rowsd->quot_itm_id}}" placeholder="" class="form-control name_list" /></td>
                         <td><input type="text" name="parameter5[]" id="parameter5_{{$i}}" value="{{$rowsd->item_name}}" placeholder="Enter Parameter" class="form-control name_list" readonly /></td>
                         <td><input type="text" name="test5[]" id="test5_{{$i}}" value="{{$rowsd->quot_test_price}}" placeholder="Enter Price (Taka) /Test" class="form-control name_list" /></td>
                         <td><input type="text" name="kit5[]" id="kit5_{{$i}}" value="{{$rowsd->quot_kit_price}}" placeholder="Enter Price/Kit (25 Test)" class="form-control name_list" /></td>
                         <td><button type="button" name="remove" value="5" id="{{$i}}" class="btn btn-danger btn_remove">X</button></td>
                        </tr>
                         <?php $i += 1; ?>
                        @endif
                        @endforeach
                      </div>
                      </tbody>
                   </table>
                 </div>

                 <?php // Category 6------------------   ?>
                     <div role="tabpanel" class="tab-pane " id = "Category6">
                       <table id="salesTable" class="table table-striped table-data table-report ">
                         <thead class="salesTable">
                           <tr>
                             <th width="1%" class="text-center">&nbsp;&nbsp;</th>
                             <th width="64%" class="text-center">Item Category</th>
                             <th width="15%" class="text-center">Qty</th>
                             <th width="20%" class="text-center">Amount</th>
                           </tr>
                         </thead>
                         <tbody class="salesTable" style="background-color: #ffffff;">
                           <tr>
                             <td width="1%" class="text-center">1</td>
                             <td width="64%">
                             <select  chosen width="'100%'"  data-type="itemid6" name="itemid6"  id ="itemid6_1" class="form-control chosen-select" onchange="loadItemsDet(this.id,this.value,6)">
                                 <option value="" disabled selected>- Select Category 6 -</option>
                                 @foreach($itm_cat as $cmb)
                                     <option {{ $cmb->tab == 6 ? 'selected' : '' }} value="{{ $cmb->id }}">{{ $cmb->itm_cat_name }}</option>
                                 @endforeach
                               </select>
                            </td>
                            <?php
                            $quot_qty = '';
                            $quot_amount = '';
                            $quot_note = '';
                            $quot_cate_id = 0;
                            ?>
                            @foreach($itm_cat as $cmb)
                              <?php
                              if($cmb->tab == 6){
                                $quot_qty = $cmb->quot_qty;
                                $quot_amount = $cmb->quot_amount;
                                $quot_note = $cmb->quot_note;
                                $quot_cate_id = $cmb->quot_cate_id;
                              }
                              ?>
                            @endforeach
                             <td width="15%"><input type="text" data-type="qty6" name="qty6" id="qty6_1" value="{{ $quot_qty }}" class="form-control" autocomplete="off" ></td>
                             <td width="20%" align="center"><input type="text" data-type="amount6" name="amount6" id="amount6_1" value="{{ $quot_amount }}" class="form-control input-sm" autocomplete="off" ></td>
                             <td width="5%" align="center"><input type="checkbox" data-type="delete6" name="delete6" id="delete6_1" value="1"> DEL</td>
                              <td><input type="text" name="tabno6" value="6"/></td>
                         </tr>
                           <tr><td colspan="4"><textarea class="summernote" rows="5" cols="40" name="quot_note6">{!! $quot_note !!}</textarea></td></tr>

                          </tbody>
                       </table>
                       <table id="salesTable_6" class="table table-striped table-data table-report ">
                         <thead class="salesTable">
                         </thead>
                         <tbody class="salesTable" style="background-color: #ffffff;">
                           <div>
                            <?php $i = 1; ?>
                            @foreach($rows_d as $rowsd)
                            @if ($quot_cate_id == $rowsd->item_ref_cate_id)
                             <tr id="row6{{$i}}" class="dynamic-added">
                             <td><input type="text" value="{{$i}}"/></td>
                             <!--td><input type="text" name="itemid6[]"  id ="itemid6_{{$i}}" value="{{$rowsd->item_ref_cate_id}}"/></td -->
                             <td><input type="text" name="parameterid6[]" id="parameterid6_{{$i}}" value="{{$rowsd->quot_itm_id}}" placeholder="" class="form-control name_list" /></td>
                             <td><input type="text" name="parameter6[]" id="parameter6_{{$i}}" value="{{$rowsd->item_name}}" placeholder="Enter Parameter" class="form-control name_list" readonly /></td>
                             <td><input type="text" name="test6[]" id="test6_{{$i}}" value="{{$rowsd->quot_test_price}}" placeholder="Enter Price (Taka) /Test" class="form-control name_list" /></td>
                             <td><input type="text" name="kit6[]" id="kit6_{{$i}}" value="{{$rowsd->quot_kit_price}}" placeholder="Enter Price/Kit (25 Test)" class="form-control name_list" /></td>
                             <td><button type="button" name="remove" value="6" id="{{$i}}" class="btn btn-danger btn_remove">X</button></td>
                            </tr>
                             <?php $i += 1; ?>
                            @endif
                            @endforeach
                          </div>
                          </tbody>
                       </table>
                     </div>

                     <?php // Category 7------------------   ?>
                         <div role="tabpanel" class="tab-pane " id = "Category7">
                           <table id="salesTable" class="table table-striped table-data table-report ">
                             <thead class="salesTable">
                               <tr>
                                 <th width="1%" class="text-center">&nbsp;&nbsp;</th>
                                 <th width="64%" class="text-center">Item Category</th>
                                 <th width="15%" class="text-center">Qty</th>
                                 <th width="20%" class="text-center">Amount</th>
                               </tr>
                             </thead>
                             <tbody class="salesTable" style="background-color: #ffffff;">
                               <tr>
                                 <td width="1%" class="text-center">1</td>
                                 <td width="64%">
                                 <select  chosen width="'100%'"  data-type="itemid7" name="itemid7"  id ="itemid7_1" class="form-control chosen-select" onchange="loadItemsDet(this.id,this.value,7)">
                                     <option value="" disabled selected>- Select Category 7 -</option>
                                     @foreach($itm_cat as $cmb)
                                         <option {{ $cmb->tab == 7 ? 'selected' : '' }} value="{{ $cmb->id }}">{{ $cmb->itm_cat_name }}</option>
                                     @endforeach
                                   </select>
                                </td>
                                <?php
                                $quot_qty = '';
                                $quot_amount = '';
                                $quot_note = '';
                                $quot_cate_id = 0;
                                ?>
                                @foreach($itm_cat as $cmb)
                                  <?php
                                  if($cmb->tab == 7){
                                    $quot_qty = $cmb->quot_qty;
                                    $quot_amount = $cmb->quot_amount;
                                    $quot_note = $cmb->quot_note;
                                    $quot_cate_id = $cmb->quot_cate_id;
                                  }
                                  ?>
                                @endforeach
                                 <td width="15%"><input type="text" data-type="qty7" name="qty7" id="qty7_1" value="{{ $quot_qty }}" class="form-control" autocomplete="off" ></td>
                                 <td width="20%" align="center"><input type="text" data-type="amount7" name="amount7" id="amount7_1" value="{{ $quot_amount }}" class="form-control input-sm" autocomplete="off" ></td>
                                 <td width="5%" align="center"><input type="checkbox" data-type="delete7" name="delete7" id="delete7_1" value="1"> DEL</td>
                                 <td><input type="text" name="tabno7" value="7"/></td>
                               </tr>
                               <tr><td colspan="4"><textarea class="summernote" rows="5" cols="40" name="quot_note7">{!! $quot_note !!}</textarea></td></tr>

                              </tbody>
                           </table>
                           <table id="salesTable_7" class="table table-striped table-data table-report ">
                             <thead class="salesTable">
                             </thead>
                             <tbody class="salesTable" style="background-color: #ffffff;">
                               <div>
                                <?php $i = 1; ?>
                                @foreach($rows_d as $rowsd)
                                @if ($quot_cate_id == $rowsd->item_ref_cate_id)
                                 <tr id="row7{{$i}}" class="dynamic-added">
                                 <td><input type="text" value="{{$i}}"/></td>
                                 <!--td><input type="text" name="itemid7[]"  id ="itemid7_{{$i}}" value="{{$rowsd->item_ref_cate_id}}"/></td -->
                                 <td><input type="text" name="parameterid7[]" id="parameterid7_{{$i}}" value="{{$rowsd->quot_itm_id}}" placeholder="" class="form-control name_list" /></td>
                                 <td><input type="text" name="parameter7[]" id="parameter7_{{$i}}" value="{{$rowsd->item_name}}" placeholder="Enter Parameter" class="form-control name_list" readonly /></td>
                                 <td><input type="text" name="test7[]" id="test7_{{$i}}" value="{{$rowsd->quot_test_price}}" placeholder="Enter Price (Taka) /Test" class="form-control name_list" /></td>
                                 <td><input type="text" name="kit7[]" id="kit7_{{$i}}" value="{{$rowsd->quot_kit_price}}" placeholder="Enter Price/Kit (25 Test)" class="form-control name_list" /></td>
                                 <td><button type="button" name="remove" value="7" id="{{$i}}" class="btn btn-danger btn_remove">X</button></td>
                                </tr>
                                 <?php $i += 1; ?>
                                @endif
                                @endforeach
                              </div>
                              </tbody>
                           </table>
                         </div>
           <?php // Category 8------------------   ?>
             <div role="tabpanel" class="tab-pane " id = "Category8">
                 <table id="salesTable" class="table table-striped table-data table-report ">
                   <thead class="salesTable">
                     <tr>
                       <th width="1%" class="text-center">&nbsp;&nbsp;</th>
                       <th width="64%" class="text-center">Item Category</th>
                       <th width="15%" class="text-center">Qty</th>
                       <th width="20%" class="text-center">Amount</th>
                     </tr>
                   </thead>
                   <tbody class="salesTable" style="background-color: #ffffff;">
                   <tr>
                     <td width="1%" class="text-center">1</td>
                     <td width="64%">
                       <select  chosen width="'100%'"  data-type="itemid8" name="itemid8"  id ="itemid8_1" class="form-control chosen-select" onchange="loadItemsDet(this.id,this.value,8)">
                         <option value="" disabled selected>- Select Category 8 -</option>
                         @foreach($itm_cat as $cmb)
                         <option {{ $cmb->tab == 8 ? 'selected' : '' }} value="{{ $cmb->id }}">{{ $cmb->itm_cat_name }}</option>
                         @endforeach
                       </select>
                     </td>
                     <?php
                     $quot_qty = '';
                     $quot_amount = '';
                     $quot_note = '';
                     $quot_cate_id = 0;
                     ?>
                     @foreach($itm_cat as $cmb)
                       <?php
                       if($cmb->tab == 8){
                         $quot_qty = $cmb->quot_qty;
                         $quot_amount = $cmb->quot_amount;
                         $quot_note = $cmb->quot_note;
                         $quot_cate_id = $cmb->quot_cate_id;
                       }
                       ?>
                     @endforeach
                     <td width="15%"><input type="text" data-type="qty8" name="qty8" id="qty8_1" value="{{ $quot_qty }}" class="form-control" autocomplete="off" ></td>
                     <td width="20%" align="center"><input type="text" data-type="amount8" name="amount8" id="amount8_1" value="{{ $quot_amount }}" class="form-control input-sm" autocomplete="off" ></td>
                     <td width="5%" align="center"><input type="checkbox" data-type="delete8" name="delete8" id="delete8_1" value="1"> DEL</td>
                     <td><input type="text" name="tabno8" value="8"/></td>
                   </tr>
                   <tr><td colspan="4"><textarea class="summernote" rows="5" cols="40" name="quot_note8">{!! $quot_note !!}</textarea></td></tr>

                 </tbody>
             </table>
             <table id="salesTable_8" class="table table-striped table-data table-report ">
               <thead class="salesTable">
               </thead>
               <tbody class="salesTable" style="background-color: #ffffff;">
                 <div>
                  <?php $i = 1; ?>
                  @foreach($rows_d as $rowsd)
                  @if ($quot_cate_id == $rowsd->item_ref_cate_id)
                   <tr id="row8{{$i}}" class="dynamic-added">
                   <td><input type="text" value="{{$i}}"/></td>
                   <!-- td><input type="text" name="itemid8[]"  id ="itemid8_{{$i}}" value="{{$rowsd->item_ref_cate_id}}"/></td -->
                   <td><input type="text" name="parameterid8[]" id="parameterid8_{{$i}}" value="{{$rowsd->quot_itm_id}}" placeholder="" class="form-control name_list" /></td>
                   <td><input type="text" name="parameter8[]" id="parameter8_{{$i}}" value="{{$rowsd->item_name}}" placeholder="Enter Parameter" class="form-control name_list" readonly /></td>
                   <td><input type="text" name="test8[]" id="test8_{{$i}}" value="{{$rowsd->quot_test_price}}" placeholder="Enter Price (Taka) /Test" class="form-control name_list" /></td>
                   <td><input type="text" name="kit8[]" id="kit8_{{$i}}" value="{{$rowsd->quot_kit_price}}" placeholder="Enter Price/Kit (25 Test)" class="form-control name_list" /></td>
                   <td><button type="button" name="remove" value="8" id="{{$i}}" class="btn btn-danger btn_remove">X</button></td>
                  </tr>
                   <?php $i += 1; ?>
                  @endif
                  @endforeach
                </div>
                </tbody>
             </table>
           </div>

           <?php // Category 9------------------   ?>
             <div role="tabpanel" class="tab-pane" id = "Category9">
                 <table id="salesTable" class="table table-striped table-data table-report ">
                   <thead class="salesTable">
                     <tr>
                       <th width="1%" class="text-center">&nbsp;&nbsp;</th>
                       <th width="64%" class="text-center">Item Category</th>
                       <th width="15%" class="text-center">Qty</th>
                       <th width="20%" class="text-center">Amount</th>
                     </tr>
                   </thead>
                   <tbody class="salesTable" style="background-color: #ffffff;">
                   <tr>
                     <td width="1%" class="text-center">1</td>
                     <td width="64%">
                       <select  chosen width="'100%'"  data-type="itemid9" name="itemid9"  id ="itemid9_1" class="form-control chosen-select" onchange="loadItemsDet(this.id,this.value,9)">
                         <option value="" disabled selected>- Select Category 9 -</option>
                         @foreach($itm_cat as $cmb)
                         <option {{ $cmb->tab == 9 ? 'selected' : '' }} value="{{ $cmb->id }}">{{ $cmb->itm_cat_name }}</option>
                         @endforeach
                       </select>
                     </td>
                     <?php
                     $quot_qty = '';
                     $quot_amount = '';
                     $quot_note = '';
                     $quot_cate_id = 0;
                     ?>
                     @foreach($itm_cat as $cmb)
                       <?php
                       if($cmb->tab == 9){
                         $quot_qty = $cmb->quot_qty;
                         $quot_amount = $cmb->quot_amount;
                         $quot_note = $cmb->quot_note;
                         $quot_cate_id = $cmb->quot_cate_id;
                       }
                       ?>
                     @endforeach
                     <td width="15%"><input type="text" data-type="qty9" name="qty9" id="qty9_1" value="{{ $quot_qty }}" class="form-control" autocomplete="off" ></td>
                     <td width="20%" align="center"><input type="text" data-type="amount9" name="amount9" id="amount9_1" value="{{ $quot_amount }}" class="form-control input-sm" autocomplete="off" ></td>
                     <td width="5%" align="center"><input type="checkbox" data-type="delete9" name="delete9" id="delete9_1" value="1"> DEL</td>
                     <td><input type="text" name="tabno9" value="9"/></td>
                   </tr>
                   <tr><td colspan="4"><textarea class="summernote" rows="5" cols="40" name="quot_note9">{!! $quot_note !!}</textarea></td></tr>

                 </tbody>
             </table>
             <table id="salesTable_9" class="table table-striped table-data table-report ">
               <thead class="salesTable">
               </thead>
               <tbody class="salesTable" style="background-color: #ffffff;">
                 <div>
                  <?php $i = 1; ?>
                  @foreach($rows_d as $rowsd)
                  @if ($quot_cate_id == $rowsd->item_ref_cate_id)
                   <tr id="row9{{$i}}" class="dynamic-added">
                   <td><input type="text" value="{{$i}}"/></td>
                   <!-- td><input type="text" name="itemid9[]"  id ="itemid9_{{$i}}" value="{{$rowsd->item_ref_cate_id}}"/></td -->
                   <td><input type="text" name="parameterid9[]" id="parameterid9_{{$i}}" value="{{$rowsd->quot_itm_id}}" placeholder="" class="form-control name_list" /></td>
                   <td><input type="text" name="parameter9[]" id="parameter9_{{$i}}" value="{{$rowsd->item_name}}" placeholder="Enter Parameter" class="form-control name_list" readonly /></td>
                   <td><input type="text" name="test9[]" id="test9_{{$i}}" value="{{$rowsd->quot_test_price}}" placeholder="Enter Price (Taka) /Test" class="form-control name_list" /></td>
                   <td><input type="text" name="kit9[]" id="kit9_{{$i}}" value="{{$rowsd->quot_kit_price}}" placeholder="Enter Price/Kit (25 Test)" class="form-control name_list" /></td>
                   <td><button type="button" name="remove" value="9" id="{{$i}}" class="btn btn-danger btn_remove">X</button></td>
                  </tr>
                   <?php $i += 1; ?>
                  @endif
                  @endforeach
                </div>
                </tbody>
             </table>
           </div>

           <?php // Category 10------------------   ?>
             <div role="tabpanel" class="tab-pane " id = "Category10">
                 <table id="salesTable" class="table table-striped table-data table-report ">
                   <thead class="salesTable">
                     <tr>
                       <th width="1%" class="text-center">&nbsp;&nbsp;</th>
                       <th width="64%" class="text-center">Item Category</th>
                       <th width="15%" class="text-center">Qty</th>
                       <th width="20%" class="text-center">Amount</th>
                     </tr>
                   </thead>
                   <tbody class="salesTable" style="background-color: #ffffff;">
                   <tr>
                     <td width="1%" class="text-center">1</td>
                     <td width="64%">
                       <select  chosen width="'100%'"  data-type="itemid10" name="itemid10"  id ="itemid10_1" class="form-control chosen-select" onchange="loadItemsDet(this.id,this.value,10)">
                         <option value="" disabled selected>- Select Category 10 -</option>
                         @foreach($itm_cat as $cmb)
                         <option {{ $cmb->tab == 10 ? 'selected' : '' }} value="{{ $cmb->id }}">{{ $cmb->itm_cat_name }}</option>
                         @endforeach
                       </select>
                     </td>
                     <?php
                     $quot_qty = '';
                     $quot_amount = '';
                     $quot_note = '';
                     $quot_cate_id = 0;
                     ?>
                     @foreach($itm_cat as $cmb)
                       <?php
                       if($cmb->tab == 10){
                         $quot_qty = $cmb->quot_qty;
                         $quot_amount = $cmb->quot_amount;
                         $quot_note = $cmb->quot_note;
                         $quot_cate_id = $cmb->quot_cate_id;
                       }
                       ?>
                     @endforeach
                     <td width="15%"><input type="text" data-type="qty10" name="qty10" id="qty10_1" value="{{ $quot_qty }}" class="form-control" autocomplete="off" ></td>
                     <td width="20%" align="center"><input type="text" data-type="amount10" name="amount10" id="amount10_1" value="{{ $quot_amount }}" class="form-control input-sm" autocomplete="off" ></td>
                     <td width="5%" align="center"><input type="checkbox" data-type="delete10" name="delete10" id="delete10_1" value="1"> DEL</td>
                     <td><input type="text" name="tabno10" value="10"/></td>
                   </tr>
                   <tr><td colspan="4"><textarea class="summernote" rows="5" cols="40" name="quot_note10">{!! $quot_note !!}</textarea></td></tr>
                 </tbody>
             </table>
             <table id="salesTable_10" class="table table-striped table-data table-report ">
               <thead class="salesTable">
               </thead>
               <tbody class="salesTable" style="background-color: #ffffff;">
                 <div>
                  <?php $i = 1; ?>
                  @foreach($rows_d as $rowsd)
                  @if ($quot_cate_id == $rowsd->item_ref_cate_id)
                   <tr id="row10{{$i}}" class="dynamic-added">
                   <td><input type="text" value="{{$i}}"/></td>
                   <!-- td><input type="text" name="itemid10[]"  id ="itemid10_{{$i}}" value="{{$rowsd->item_ref_cate_id}}"/></td -->
                   <td><input type="text" name="parameterid10[]" id="parameterid10_{{$i}}" value="{{$rowsd->quot_itm_id}}" placeholder="" class="form-control name_list" /></td>
                   <td><input type="text" name="parameter10[]" id="parameter10_{{$i}}" value="{{$rowsd->item_name}}" placeholder="Enter Parameter" class="form-control name_list" readonly /></td>
                   <td><input type="text" name="test10[]" id="test10_{{$i}}" value="{{$rowsd->quot_test_price}}" placeholder="Enter Price (Taka) /Test" class="form-control name_list" /></td>
                   <td><input type="text" name="kit10[]" id="kit10_{{$i}}" value="{{$rowsd->quot_kit_price}}" placeholder="Enter Price/Kit (25 Test)" class="form-control name_list" /></td>
                   <td><button type="button" name="remove" value="10" id="{{$i}}" class="btn btn-danger btn_remove">X</button></td>
                  </tr>
                   <?php $i += 1; ?>
                  @endif
                  @endforeach
                </div>
                </tbody>
             </table>
           </div>
           <?php // Category Terms & Conditions------------------   ?>
             <div role="tabpanel" class="tab-pane col-md-12" id = "Category11">
               <div class="row">
                 <div class="col-md-12">
                     <textarea class="summernote" rows="5" cols="40" name="quot_terms_conds" id="quot_terms_conds">{!! $rows_m->quot_term_cond !!}</textarea>
                 </div>
               </div>
           </div>
           <?php // end category ?>
       </div>
     </div>
    </div>
    <div class="row justify-content-left">
          <div class="col-sm-12 text-left">
              <!-- button class="btn btn-sm btn-success" type="button" value="draft" onclick="formcheck(); return false"><i class="fa fa-save"></i> Save As Draft</button -->
              <button class="btn btn-sm btn-success" type="button" value="final" onclick="formcheck(); return false"><i class="fa fa-save"></i> Save</button>
              <a href="{{route('sales.quot.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
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
  $('.summernote').summernote();
//  $(".exit_customer").fadeOut();
//  $(".nonexit_customer").fadeIn();

  $(document).on('click', '.btn_remove', function(){
      var button_id = $(this).attr("id");
      var fired_button = $(this).val();
      //alert(fired_button);
      //alert(button_id );
      $('#row'+fired_button+button_id+'').remove();
  });

  /*$('#add').click(function(e){
       var i = $('#salesTable tr').length;
       e.preventDefault();
         html  = '<tr id="row'+i+'">';
         html += '<td width="1%" class="text-center">' + i + '</td>';
         html += '<td width="2%"><input type="text" data-type="ItemCodeId" name="ItemCodeId[]" id="ItemCodeId_' + i + '" class="form-control item_id_class" autocomplete="off"></td>';
         html += '<td width="20%">';
         html += '<div><select data-type="itemid" name="itemid[]"  id ="itemid_' + i + '" class="form-control chosen-select" onchange="loadItemsDet(this.id,this.value)">';
         html += '<option value="" disabled selected>- Select Item -</option>';
         html += '</select></div></td>';
         html += '<td width="5%"><input type="text" data-type="ItemDesc" name="ItemDesc[]" id="ItemDesc_' + i + '" class="form-control" autocomplete="off" ></td>';
         html += '<td width="8%" align="center"><input type="text" data-type="Price" name="Price[]" id="Price_' + i + '" onkeydown="search(this.value,1)" class="form-control input-sm" autocomplete="off" ></td>';
         html += '<td width="2%"><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td>&nbsp;';
         html += '<td width="3%"><button type="button" name="items_add" id="add_items" class="btn btn-success">&nbsp;Items</button></td> </tr>';
         html += '<tr id="row'+i+'"><td colspan="6"><textarea class="summernote" rows="5" cols="40" name="description2"></textarea></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td> </tr>';
         html += '<tr id="row'+i+'"><td colspan="6"><div></div></td></tr>';
         //var html = '<tr id="row'+i+'" class="dynamic-added"><td><input type="text" name="name[]" placeholder="Enter Account Head" class="form-control name_list" /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>';

       $('#salesTable').append(html);
       $('#salesTable').appendto($('.summernote').summernote());
  });*/

});
</script>

<script>

function getCustomer(value){
  //alert(value);
  if(value == 1) {
    $(".exit_customer").fadeIn();
    $(".nonexit_customer").fadeOut();
  }else{
    $(".exit_customer").fadeOut();
    $(".nonexit_customer").fadeIn();
  }
}

function loadItemsDet(el,itemid,tab){

    //var itemid = $('#itemid_1').val()
    var i = $('#salesTable'+tab+' tr').length;
  //  i += 2;
   // alert(i+' '+el+' '+itemid)
    id = el.split("_")
    //alert(id[1])
    $.get('/get-quot-item-code/getdetails/'+itemid+'/getfirst', function(response){
      $('#salesTable_'+tab+ ' tbody').empty();
      $.each(response, function(index, element) {
        //alert(i+' '+element.id+ ' SD ' +element.item_name);
        html = '<tr id="row'+tab+i+'" class="dynamic-added">';
        html += '<td style="display: none"><input type="text" value="'+ i +'"/></td>';
        //html += '<td style="display: none"><input type="text" name="tabno'+tab+'[]" id="tabno'+tab+'_' + i + '" value="'+ tab +'"/></td>';
        //html += '<td style="display: none"><input type="text" name="itemid'+tab+'[]"  id ="itemid'+tab+'_' + i + '" value="'+ element.item_ref_cate_id +'"/></td>';
        html += '<td style="display: none"><input type="text" name="parameterid'+tab+'[]" id="parameterid'+tab+'_' + i + '" value="'+ element.id +'" placeholder="" class="form-control name_list" /></td>';
        html += '<td><input type="text" name="parameter'+tab+'[]" id="parameter'+tab+'_' + i + '" value="'+ element.item_name +'" placeholder="Enter Parameter" class="form-control name_list" readonly /></td>';
        html += '<td><input type="text" name="test'+tab+'[]" id="test'+tab+'_' + i + '" value="'+ element.base_price +'" placeholder="Enter Price (Taka) /Test" class="form-control name_list" /></td>';
        html += '<td><input type="text" name="kit'+tab+'[]" id="kit'+tab+'_' + i + '" value="'+ element.size*element.base_price +'" placeholder="Enter Price/Kit (25 Test)" class="form-control name_list" /></td>';
        html += '<td><button type="button" name="remove" value="'+tab+'" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>';
        $('#salesTable_'+tab).append(html);
        //selectList.append('<option value="' + element.id + '">' + element.item_name + '</option>');
        i++;
      });
    });
}

  function fetchAddress(customer_id){
     //alert(customer_id);
       $.ajax({
         url: '/get-quot-cust-add/getcustAddress/'+customer_id,
         type: 'get',
         dataType: 'json',
         error: function (request, error) {
            console.log(arguments);
            alert( error +" Can't do because: " +  console.log(arguments));
         },
         success: function(data){
           var len = 0;
           //alert(data.cust_add1);
           $("#quot_add").val(data.cust_add1 +' '+ data.cust_add2);
         }
       });

  }

  function formcheck() {
    //alert('1111');
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
    var total_debit_in = 0;
    var total_credit_in = 0;
    //alert(total_debit_in +' :: '+total_credit_in)
    if(parseFloat(total_debit_in) == parseFloat(total_credit_in)){
      $('#quot_Form').submit()
    }else{
      alert('Debit & Credit Does Not Equal')
      //console.log('rifat')
    }
  }

</script>

<script>
/*
function customer(custid){
  getDropdownItemList(1);
}

function enter(id,amount) {
    if(event.keyCode == 13) {
        if(amount > 0) row_increment();
        i = id.split("_")[1];
        //alert(i);
      //  getDropdownItemList(i);
    }
}

function removeRow(el) {
  alert(el)
    $(el).parents("tr").remove()
  //  totalAmount()
    //calcluteTotalBill();
}


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
        alert(id);
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

        alert(names.id+'@'+names.item_name+'@'+names.item_desc+'@'+
          names.vUnitName+'@'+names.item_bal_stock+'@'+names.item_unit+'@'+
          names.item_price);

        $('#ItemCodeId_' + id[1]).val(names.id);
        $('#itemid_' + id[1]).val(names.item_name);
        $('#ItemDesc_' + id[1]).val(names.item_desc);
        $('#Price_' + id[1]).val(names.item_price);
        $('#Unit_' + id[1]).val(names.item_unit);
        $('#Stock_' + id[1]).val(names.item_bal_stock);
        if(s_tag) setDropdownItemList(names.id,id[1]); // this is for selection item code
        else $('#ItemCode_' + id[1]).val(names.item_code); // this is for selection item box
        $('#Qty_'+id[1]).focus()
      }
    }

    function inArray(needle, haystack) {
        var length = haystack.length;
        for(var i = 0; i < length; i++) {
            if(haystack[i].item == needle) return [true, haystack[i].id];
        }
        return [false];
    }

    function setDropdownItemList(itemid,id){
      //alert(document.getElementById('itemid_'+id)+':::');
      //alert(itemid+':::'+id);
      $("#itemid_"+id+" > [value=" + itemid + "]").attr("selected", "true").trigger('chosen:updated');

    }

    function getDropdownItemList(i){
      var compcode = $('#company_code').val();
      var custid = $('#customer_id').val();
      i = parseInt(i);
    //  alert(i + ","+ compcode + "," + custid);
      $.get('{{ url('/') }}/itemLookup/'+compcode+'/'+custid, function(response) {
        var selectList = $('select[id="itemid_'+i+'"]');
        selectList.chosen();
        selectList.empty();
        //$('#itemid_' + i).append('<option value="">--Select Item--</option>');
        selectList.append('<option value="" disabled selected>--Select Item1--</option>');
        $.each(response, function(index, element) {
          //alert(element.id+ ' SD ' +element.item_name);
          selectList.append('<option value="' + element.id + '">' + element.item_name + '</option>');
        });
        selectList.trigger('chosen:updated');
      });
    }*/
  </script>
@stop
