@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/blogic_css/acc_tb.css') }}" />
@stop

@section('content')
<div class="row">
<input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>
  <div class="col-sm-12">
         <div class="widget-header widget-header-small">
                <h6 class="widget-title smaller">
                    <i class="fa fa-pencil-square"></i> Barcode List
                  </h6>

                  <div class="widget-toolbar">
                      <a href="" class="white"><i class="fa fa-list"></i> List</a>
                  </div>
              </div>
              <div class="widget-body">
                  <div class="widget-main">
                  <form action="{{route('itm.search.barcode')}}" id="acc_form" method="post">
                  {{ csrf_field() }}
                    <div class="row">
                     <div class="col-md-4">
                          <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text" style="min-width:110px">Company:</div>
                            </div>
                            &nbsp;<select name="company_code" class="autocomplete" id="company_code"  style="max-width:150px" required>
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
                          <div class="input-group ss-item-required"> 
                                  <select name="item_id" class="col-xs-10 col-sm-8 chosen-select" id="item_id" onchange="item()" required>
                                      <option value="" disabled selected>- Select Item -</option>
                                      @foreach($item_list as $list)
                                          <option {{ old('item_id') == $list->id ? 'selected' : '' }} value="{{ $list->id }}">{{ $list->item_name }}-{{ $list->itm_cat_name }}</option>
                                      @endforeach
                                  </select>
                                  @error('customer_id')
                                  <span class="text-danger">{{ $message }}</span>
                                  @enderror
                         </div>
                      </div>
                      <div class="col-md-2">
                        <button type="submit" name="submit" value='html'  class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button>
                      </div>
                    </div>
                  </form>
                      <div class="row">
                          <div class="col-sm-12">
                            @foreach($items as $key => $item)
                            <div id="modal-import{{ $item->id }}" class="modal" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                              <h4 class="blue bigger"><i class="fa fa-upload"></i> Item Quantity</h4>
                                        </div>
                                          <div class="modal-body">
                                              <div class="row">
                                                  <form class="form-horizontal" action="{{ route('barcode.print',$item->id) }}" method="get" role="form" enctype="multipart/form-data">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label for="inputError" class="col-xs-12 col-sm-3 col-md-3 control-label"> Print Quantity</label>
                                                                <div class="col-xs-12 col-sm-6">
                                                                  <input type="number" name="qty" class="form-control">
                                                                </div>
                                                          </div>
                                                          <div class="form-group">
                                                              <label for="inputError" class="col-xs-12 col-sm-3 col-md-3 control-label"></label>
                                                                  <div class="col-xs-12 col-sm-6">
                                                                            <button class="btn btn-success btn-xs"><i class="fa fa-print"></i> Print</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <div class="col-sm-2">
                                        <a href="#modal-import{{ $item->id }}" role="button" class="blue" data-toggle="modal"><i class="fa fa-print"></i></a>
                                        {!! DNS1D::getBarcodeHTML($item->item_bar_code, "C39",1.3,44) !!}
                                        <p>{{ $item->item_name }} ({{ $item->itm_cat_name }})<br>
                                        {{ $item->item_bar_code }}</p>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                      <div class="card-tools">
                          <ul class="pagination pagination-sm float-right">
                            <p class="pull-right">
                             {{ $items->render("pagination::bootstrap-4") }} 
                            </p>
                          </ul>
                        </div>
                      </div>
                </div>

    </div>
@endsection

@section('pagescript')
  <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace.min.js') }}"></script>
  <script src="{{ asset('assets/blogic_js/sel_box_search.js') }}"></script>
@stop
