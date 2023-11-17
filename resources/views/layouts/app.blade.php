<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="ERP Management">
  <meta name="author" content="SYCORAX">
  <meta name="keyword" content="ERP Management">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'ERP | Application') }}</title>
  <!-- Google Font: Source Sans Pro -->
  <!-- link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet" -->
  <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:400,500" rel="stylesheet">
  <link rel="icon" href="{{ asset('assets/images/lh_favicon_1_6_XXXXX.ico') }}" type="image/png" sizes="16x16">
    <script>

        site='http://127.0.0.1:8000/';
        base='http://127.0.0.1:8000/';
        lang = {
            cancel:'Cancel',
            loading:'Loading...',
        }
    </script>
    <script type="text/javascript" src="{{ asset('assets/js/popper.min.js?v=1.3.3') }}"></script>

    <!-- script type="text/javascript" src="{{ asset('assets/js/jquery-3.1.1.min.js?v=1.3.3') }}"></script -->
    <script type="text/javascript" src="{{ asset('assets/js/jquery-2.1.4.min.js') }}"></script>

    @yield('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/calendar.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-datepicker.min.css?v=1.3.3') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/tabulator.min.css?v=1.3.3') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/autocomplete.min.css?v=1.3.3') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/materialdesignicons.min.css?v=1.3.3') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css?v=1.3.3') }}" />
    <link rel="stylesheet" href="{{ asset('assets/font-awesome/4.5.0/css/font-awesome.min.css') }}" />


    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-override.css?v=1.3.3') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-toggle.css?v=1.3.3') }}" />
    <!-- link rel="stylesheet" type="text/css" href="{{ asset('assets/css/jstree.min.css?v=1.3.3') }}" / -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-treeview.min.css') }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/treetable.css?v=1.3.3') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css?v=1.3.3') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/fullcalendar.min.css?v=1.3.3') }}" />
   
    <link rel="stylesheet" href="{{ asset('assets/css/ace.min.css') }}" class="ace-main-stylesheet" id="main-ace-style"/>
   
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style1.css') }}" />
    
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.css">
  
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.js"></script>


    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="e2a-animate">
   <div class="e2a-wrapper e2a-collapsible-sidebar e2a-collapsible-sidebar-collapsed">

  <nav class="navbar navbar-expand-md e2a-sub-navigation">
      <div class="navbar-collapse collapse show" id="e2a-sub-navigation-collapse">
          <div id="module-dropdown"></div>
                  <ul class="nav navbar-nav accounting" style="display:none">
                      <!--li class="nav-item" role="presentation">
                        <a href="#" data-url="https://erp2all.com/dashboard/welcome/dashboard" color="blue" id="dashboard" class="tab-link nav-link"><i class="mdi mdi-desktop-mac"></i> <span>Dashboard</span></a>
                      </li -->
                  
                    <li role="presentation" class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle " data-toggle="dropdown"><i class="fa mdi mdi-call-received"></i> Accounts Info <span class="caret"></span></a>
                        <div class="dropdown-menu">
                          @if(Auth::user()->role->name == 'admin' || in_array('106', json_decode(Auth::user()->role->permissions)))
                            <a href="{{ asset('/chartofacc/chartofacc/index') }}" data-url="https://erp2all.com/accounting/coa" color="red" id="coa" class="tab-link nav-link"><i class="mdi mdi-file-tree"></i> <span>Chart of Accounts</span></a>
                          @endif
                          <a href="{{ route('cust.add') }}" color="green" id="customer" class="tab-link nav-link"><i class="mdi mdi-account-multiple"></i> <span>Customers </span></a>
                          <a href="{{ route('suppliers.create') }}" color="sky" id="supplier" class="tab-link nav-link"><i class="mdi mdi-account"></i> <span>Suppliers</span></a> 

                          @if(Auth::user()->role->name == 'admin' || in_array('107', json_decode(Auth::user()->role->permissions)))
                            <a href="{{ asset('/finyeardec/index') }}" data-url="" color="green" id="config" class="tab-link nav-link"><i class="mdi mdi-clipboard-account"></i> <span>Financial Year Declaration</span></a>
                          @endif 
                        </div>
                    </li>
                     
                    <!-- @if(Auth::user()->role->name == 'admin' || in_array('101', json_decode(Auth::user()->role->permissions)))
                      <li class="nav-item" role="presentation">
                        <a href="{{ asset('/chartofacc/chartofacc/index') }}" data-url="https://erp2all.com/accounting/coa" color="red" id="coa" class="tab-link nav-link"><i class="mdi mdi-file-tree"></i> <span>Chart of Accounts</span></a>
                      </li>
                    @endif
                    @if(Auth::user()->role->name == 'admin' || in_array('102', json_decode(Auth::user()->role->permissions)))
                      <li class="nav-item" role="presentation">
                          <a href="{{ asset('/finyeardec/index') }}" data-url="" color="green" id="config" class="tab-link nav-link"><i class="mdi mdi-clipboard-account"></i> <span>Financial Year Declaration</span></a>
                      </li>
                    @endif -->
                    @if(Auth::user()->role->name == 'admin' || in_array('103', json_decode(Auth::user()->role->permissions)))
                      <li class="nav-item" role="presentation">
                          <a href="{{ asset('/acctrans/jv-create') }}" data-url="" color="green" id="journals" class="tab-link nav-link"><i class="mdi mdi-format-list-checkbox"></i> <span>Journal</span></a>
                      </li>
                    @endif
                    <!-- @if(Auth::user()->role->name == 'admin' || in_array('104', json_decode(Auth::user()->role->permissions)))
                      <li class="nav-item" role="presentation">
                          <a href="{{ asset('/billtobill-jv-create') }}" data-url="" color="green" id="journals" class="tab-link nav-link"><i class="mdi mdi-format-list-checkbox"></i> <span>Bill to Bill Journal</span></a>
                      </li>
                    @endif -->
                    @if(Auth::user()->role->name == 'admin' || in_array('105', json_decode(Auth::user()->role->permissions)))
                      <li class="nav-item" role="presentation">
                          <a href="{{ asset('/acctrans/con-create') }}" data-url="" color="purple" id="expense" class="tab-link nav-link"><i class="mdi mdi-cash-refund"></i> <span>Contra</span></a>
                      </li>
                    @endif
                      <li role="presentation" class="nav-item dropdown">
                          <a class="nav-link dropdown-toggle " data-toggle="dropdown"><i class="fa mdi mdi-call-received"></i> Receives <span class="caret"></span></a>
                          <div class="dropdown-menu">
                            <!-- @if(Auth::user()->role->name == 'admin' || in_array('108', json_decode(Auth::user()->role->permissions)))
                              <a href="{{ asset('/billtobill-cr-create') }}" color="seagreen" class="tab-link dropdown-item"><i class="fa fa-dollar"></i> <span>Bill to Bill Cash Received</span></a>
                            @endif
                            @if(Auth::user()->role->name == 'admin' || in_array('109', json_decode(Auth::user()->role->permissions)))
                              <a href="{{ asset('/billtobill-br-create') }}" color="seagreen" class="tab-link dropdown-item"><i class="fa fa-dollar"></i> <span>Bill to Bill Bank Received</span></a>
                            @endif -->
                            @if(Auth::user()->role->name == 'admin' || in_array('106', json_decode(Auth::user()->role->permissions)))
                              <a href="{{ asset('/acctrans/cr-create') }}" color="seagreen" class="tab-link dropdown-item"><i class="fa fa-user"></i> <span>Cash Received</span></a>
                            @endif
                            @if(Auth::user()->role->name == 'admin' || in_array('107', json_decode(Auth::user()->role->permissions)))
                              <a href="{{ asset('/acctrans/br-create') }}" color="seagreen" class="tab-link dropdown-item"><i class="fa fa-dollar"></i> <span>Bank Received</span></a>
                            @endif 
                          </div>
                      </li>

                     <li role="presentation" class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle " data-toggle="dropdown"><i class="fa mdi mdi-call-made"></i> Payments <span class="caret"></span></a>
                        <div class="dropdown-menu">
                          @if(Auth::user()->role->name == 'admin' || in_array('110', json_decode(Auth::user()->role->permissions)))
                            <a href="{{ asset('/acctrans/cp-create') }}" color="seagreen" modal="1" class="tab-link dropdown-item"><i class="fa fa-user"></i> <span>Cash Payment</span></a>
                          @endif
                          @if(Auth::user()->role->name == 'admin' || in_array('111', json_decode(Auth::user()->role->permissions)))
                            <a href="{{ asset('/acctrans/bp-create') }}" color="seagreen" class="tab-link dropdown-item"><i class="fa fa-user"></i> <span>Bank Payment</span></a>
                          @endif
                        </div>
                    </li>

                    <li role="presentation" class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="fa mdi mdi-chart-bar"></i> Reports <span class="caret"></span></a>
                      <div class="dropdown-menu">
                        @if(Auth::user()->role->name == 'admin' || in_array('112', json_decode(Auth::user()->role->permissions)))
                          <a href="{{route('acchead.tree.view.list')}}" color="blue" id="ledger" class="tab-link dropdown-item"><i class="mdi mdi-file-tree"></i> <span>Chart Of Accounts</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('113', json_decode(Auth::user()->role->permissions)))
                          <a href="{{route('rpt.vh.list')}}" color="blue" id="ledger" class="tab-link dropdown-item"><i class="mdi mdi-table-edit"></i> <span>Voucher Report</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('114', json_decode(Auth::user()->role->permissions)))
                          <a href="{{route('rpt.cash.sheet')}}" color="seagreen" id="profitloss" class="tab-link dropdown-item"><i class="mdi mdi-chart-areaspline"></i> <span>Daily Cash Statement</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('115', json_decode(Auth::user()->role->permissions)))
                          <a href="{{route('rpt.cash.sheet.summary')}}" color="seagreen" id="profitloss" class="tab-link dropdown-item"><i class="mdi mdi-chart-areaspline"></i> <span>Daily Cash Statement Summary</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('116', json_decode(Auth::user()->role->permissions)))
                          <a href="{{route('rpt.sub.ledger')}}" color="red" id="ledger" class="tab-link dropdown-item"><i class="mdi mdi-format-list-checkbox"></i> <span>Subsidiary Ledger</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('117', json_decode(Auth::user()->role->permissions)))
                          <a href="{{route('rpt.con.sub.ledger')}}" color="red" id="ledger" class="tab-link dropdown-item"><i class="mdi mdi-format-list-checkbox"></i> <span>Control Wise Subsidiary Ledger</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('118', json_decode(Auth::user()->role->permissions)))
                          <a href="{{route('rpt.trial.bal1')}}" color="red" id="trial" class="tab-link dropdown-item"><i class="mdi mdi-scale-balance"></i> <span>Trial Balance</span></a>
                        @endif   

                        @if(Auth::user()->role->name == 'admin' || in_array('118', json_decode(Auth::user()->role->permissions)))
                          <a href="{{route('rpt.trial.bal3')}}" color="red" id="trial" class="tab-link dropdown-item"><i class="mdi mdi-scale-balance"></i> <span>Manufacturing To Balance Sheet</span></a>
                        @endif

                        @if(Auth::user()->role->name == 'admin' || in_array('119', json_decode(Auth::user()->role->permissions)))
                          <a href="{{route('rpt.trial.bal2')}}" color="red" id="trial" class="tab-link dropdown-item"><i class="mdi mdi-scale-balance"></i> <span>Trial Balance(Multiple Column)</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('119', json_decode(Auth::user()->role->permissions)))
                          <a href="{{route('rpt.trial.bal2')}}" color="red" id="trial" class="tab-link dropdown-item"><i class="mdi mdi-scale-balance"></i> <span>Trading To Balance Sheet</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('120', json_decode(Auth::user()->role->permissions)))
                          <a href="{{route('rpt.liquid.cash.sheet')}}" data-url="" color="green" id="liquidcash" class="tab-link dropdown-item"><i class="mdi mdi-chart-timeline"></i><span>Liquid Cash Report</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('121', json_decode(Auth::user()->role->permissions)))
                          <a href="{{route('rpt.cust.statement')}}" data-url="" color="green" id="" class="tab-link dropdown-item"><i class="mdi mdi-currency-usd"></i> <span>Customer Statement</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('122', json_decode(Auth::user()->role->permissions)))
                          <!-- a href="{{route('rpt.cond.inv.list')}}" data-url="" color="orange" id="dispatch" class="tab-link dropdown-item"><i class="mdi mdi-truck-fast"></i> <span>Conditional Report</span></a -->
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('123', json_decode(Auth::user()->role->permissions)))
                          <a href="{{route('rpt.cust.dues')}}" data-url="" color="navyblue" id="tally" class="tab-link dropdown-item"><i class="fa fa-bank"></i> <span>Customer Due Report</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('121', json_decode(Auth::user()->role->permissions)))
                          <a href="{{route('rpt.cust.statement.summary')}}" data-url="" color="green" id="" class="tab-link dropdown-item"><i class="mdi mdi-currency-usd"></i> <span>Customer Summary Report</span></a>
                        @endif
                          <!-- a href="#" data-url="https://erp2all.com/accounting/reports/profitlosscost" color="green" id="profitlosttcost" class="tab-link dropdown-item"><i class="mdi mdi-chart-timeline"></i> <span>Profit & Loss by Cost Center</span></a>
                          <a href="#" data-url="https://erp2all.com/accounting/reports/balancesheet" color="navyblue" id="balancesheet" class="tab-link dropdown-item"><i class="mdi mdi-scale"></i> <span>Balance Sheet</span></a>                                                                        
                          <a href="#" data-url="https://erp2all.com/accounting/reports/vatreturn" color="green" id="" class="tab-link dropdown-item"><i class="mdi mdi-currency-usd"></i> <span>VAT Return</span></a>
                          <a href="#" data-url="https://erp2all.com/accounting/reports/vatreturn/uk" color="green" id="" class="tab-link dropdown-item"><i class="mdi mdi-currency-usd"></i> <span>VAT Return UK</span></a>                                                                        
                          <a href="#" data-url="https://erp2all.com/accounting/reports/audittrail" color="red" id="audittrail" class="tab-link dropdown-item"><i class="fa fa-exchange"></i> <span>Audit Trail</span></a>
                          <a href="#" data-url="https://erp2all.com/accounting/reports/vataudit" color="red" id="vataudit" class="tab-link dropdown-item"><i class="fa fa-exchange"></i> <span>VAT Audit Report</span></a>                                                                        
                          <a href="#" data-url="https://erp2all.com/accounting/reports/daybook" color="green" id="daybook" class="tab-link dropdown-item"><i class="fa fa-exchange"></i> <span>Day Book Report</span></a>
                          <a href="#" data-url="https://erp2all.com/accounting/reports/receivepayment" color="red" id="receivepayment" class="tab-link dropdown-item"><i class="fa fa-exchange"></i> <span>Receive Payment Report</span></a>                                                                        
                          <a href="#" data-url="https://erp2all.com/accounting/reports/bankanalysis/0" color="purple" id="bankanalysis" class="tab-link dropdown-item"><i class="fa fa-bank"></i> <span>Bank Analysis</span></a>
                          <a href="#" data-url="https://erp2all.com/accounting/reports/tally" color="navyblue" id="tally" class="tab-link dropdown-item"><i class="fa fa-bank"></i> <span>Tally Book</span></a -->                                                                
                        </div>
                    </li>

                   <li role="presentation" class="nav-item dropdown">
                       <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="mdi mdi-cog-outline"></i> Config <span class="caret"></span></a>
                     <div class="dropdown-menu">
                        @if(Auth::user()->role->name == 'admin' || in_array('125', json_decode(Auth::user()->role->permissions)))
                         <a href="{{route('acctrans.currency')}}" data-url="" color="seagreen" id="pick" class="tab-link dropdown-item"><i class="fa fa-dollar"></i> <span>Currency</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('126', json_decode(Auth::user()->role->permissions)))
                         <a href="{{route('acctrans.check')}}" data-url="" color="seagreen" id="pick" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Cross Check</span></a>
                        @endif
                      </div>
                   </li>
            </ul>
            
            <ul class="nav navbar-nav crm" style="display:none">
                <!-- @if(Auth::user()->role->name == 'admin' || in_array('401', json_decode(Auth::user()->role->permissions)))
                    <li class="nav-item" role="presentation">
                      <a href="{{ route('sales.quot.create') }}" tooltip="Sales Quotation/ Revision-Quote" color="blue" id="sq" class="tab-link nav-link"><i class="mdi mdi-note-multiple-outline"></i> <span>Sales Quotation</span></a>
                    </li>
                @endif -->
                <!-- @if(Auth::user()->role->name == 'admin' || in_array('402', json_decode(Auth::user()->role->permissions)))
                    <li class="nav-item" role="presentation">
                        <a href="{{route('sales.order.create')}}" color="green" id="so" class="tab-link nav-link"><i class="mdi mdi-note-multiple"></i> <span>Sales Order</span></a>
                    </li>
                @endif -->
                
                <!-- @if(Auth::user()->role->name == 'admin' || in_array('403', json_decode(Auth::user()->role->permissions)))
                    <li class="nav-item" role="presentation">
                        <a href="{{route('sales.order.index')}}"  color="purple" id="merchandising" class="tab-link nav-link"><i class="mdi mdi-cart"></i> <span>Order Confirmation</span></a>
                    </li>
                @endif -->
                    <li class="nav-item" role="presentation">
                        <a href="{{route('sales.prod.index')}}" color="red" id="so" class="tab-link nav-link"><i class="mdi mdi-cart-outline"></i> <span>Production List</span></a>
                    </li>

                    <li class="nav-item" role="presentation">
                        <a href="{{route('sales.pp.prod.index')}}"   color="red" id="so" class="tab-link nav-link"><i class="mdi mdi-cart-outline"></i> <span>PP Production List</span></a>
                    </li>

                    <li class="nav-item" role="presentation">
                        <a href="{{route('sales.jute.prod.index')}}"   color="red" id="so" class="tab-link nav-link"><i class="mdi mdi-cart-outline"></i> <span>Jute Production List</span></a>
                    </li>

                    <li role="presentation" class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="fa mdi mdi-chart-bar"></i> Reports <span class="caret"></span></a>
                      @if(Auth::user()->role->name == 'admin' || in_array('404', json_decode(Auth::user()->role->permissions)))
                        <div class="dropdown-menu">
                          <a href="#" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Sales Quotation List</span></a>
                          <a href="{{ route('sales.prod.report.daily') }}" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Production Report Daily</span></a>

                          <a href="{{route('rpt.sales.order.list.pending')}}" data-url="" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Sales Order List (Pending)</span></a>
                        </div>
                      @endif

                    </li>
                  
                   
              </ul>

                <ul class="nav navbar-nav sales" style="display:none">
                    <!--li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/sales/welcome" color="red" id="dashboard" class="tab-link nav-link"><i class="mdi mdi-database"></i> <span>Dashboard</span></a>
                    </li -->
                    <!--@if(Auth::user()->role->name == 'admin' || in_array('301', json_decode(Auth::user()->role->permissions)))-->
                    <!--  <li class="nav-item" role="presentation">-->
                    <!--    <a href="{{ route('sales.persons.index') }}" color="green" id="customer" class="tab-link nav-link"><i class="mdi mdi-account-multiple"></i> <span>Sales Person</span></a>-->
                    <!--  </li>-->
                    <!--@endif-->
                    @if(Auth::user()->role->name == 'admin' || in_array('302', json_decode(Auth::user()->role->permissions)))
                      <li class="nav-item" role="presentation">
                        <a href="{{ route('cust.add') }}" color="green" id="customer" class="tab-link nav-link"><i class="mdi mdi-account-multiple"></i> <span>Customers </span></a>
                      </li>
                    @endif
                    <!--@if(Auth::user()->role->name == 'admin' || in_array('303', json_decode(Auth::user()->role->permissions)))-->
                    <!--  <li class="nav-item" role="presentation">-->
                    <!--    <a href="{{route('direct.order.create')}}"   color="red" id="so" class="tab-link nav-link"><i class="mdi mdi-cart-outline"></i> <span>Direct Invoice</span></a>-->
                    <!--  </li>-->
                    <!--@endif-->
                    <!--@if(Auth::user()->role->name == 'admin' || in_array('303', json_decode(Auth::user()->role->permissions)))-->
                    <!--  <li class="nav-item" role="presentation">-->
                    <!--    <a href="{{route('direct.delivery.create')}}"   color="red" id="so" class="tab-link nav-link"><i class="mdi mdi-cart-outline"></i> <span>Direct Delivery</span></a>-->
                    <!--  </li>-->
                    <!--@endif-->
                    @if(Auth::user()->role->name == 'admin' || in_array('304', json_decode(Auth::user()->role->permissions))) 
                    <li class="nav-item" role="presentation">
                        <a href="{{route('sales.order.index')}}"   color="red" id="so" class="tab-link nav-link"><i class="mdi mdi-cart-outline"></i> <span>Sales Order</span></a>
                    </li>
                    @endif
                    <!-- @if(Auth::user()->role->name == 'admin' || in_array('330', json_decode(Auth::user()->role->permissions))) 
                    <li class="nav-item" role="presentation">
                        <a href="{{route('sales.prod.index')}}" color="red" id="so" class="tab-link nav-link"><i class="mdi mdi-cart-outline"></i> <span>Production List</span></a>
                    </li>
                    @endif
                    @if(Auth::user()->role->name == 'admin' || in_array('331', json_decode(Auth::user()->role->permissions))) 
                    <li class="nav-item" role="presentation">
                        <a href="{{route('sales.pp.prod.index')}}"   color="red" id="so" class="tab-link nav-link"><i class="mdi mdi-cart-outline"></i> <span>PP Production List</span></a>
                    </li>
                    @endif
                    @if(Auth::user()->role->name == 'admin' || in_array('332', json_decode(Auth::user()->role->permissions))) 
                    <li class="nav-item" role="presentation">
                        <a href="{{route('sales.jute.prod.index')}}"   color="red" id="so" class="tab-link nav-link"><i class="mdi mdi-cart-outline"></i> <span>Jute Production List</span></a>
                    </li>
                    @endif -->
                    
                    <!--@if(Auth::user()->role->name == 'admin' || in_array('305', json_decode(Auth::user()->role->permissions)))-->
                    <!--<li class="nav-item" role="presentation">-->
                    <!--    <a href="{{route('sales.order.pending')}}" data-url="" color="purple" id="jobsheets" class="tab-link nav-link"><i class="mdi mdi-cart"></i> <span>Sales Delivery</span></a>-->
                    <!--</li>-->
                    <!--@endif-->
                    @if(Auth::user()->role->name == 'admin' || in_array('306', json_decode(Auth::user()->role->permissions)))
                    <li class="nav-item" role="presentation">
                        <a href="{{route('sales.invoice.index')}}" data-url="" color="green" id="si" class="tab-link nav-link"><i class="mdi mdi-note"></i> <span>Sales Invoice</span></a>
                    </li>
                    @endif 

                    @if(Auth::user()->role->name == 'admin' || in_array('306', json_decode(Auth::user()->role->permissions)))
                    <li class="nav-item" role="presentation">
                        <a href="{{route('sales.order.direct.create')}}" data-url="" color="green" id="si" class="tab-link nav-link"><i class="mdi mdi-note"></i> <span> Direct Sales</span></a>
                    </li>
                    @endif 


                    @if(Auth::user()->role->name == 'admin' || in_array('307', json_decode(Auth::user()->role->permissions)))
                    <li class="nav-item" role="presentation">
                        <a href="{{route('sales.return.index')}}" data-url="" color="green" id="si" class="tab-link nav-link"><i class="mdi mdi-note"></i> <span>Sales Return</span></a>
                    </li>
                    @endif
                    @if(Auth::user()->role->name == 'admin' || in_array('327', json_decode(Auth::user()->role->permissions)))
                      <li class="nav-item" role="presentation">
                        <a href="{{route('sales.invoice.locked')}}"   color="red" id="so" class="tab-link nav-link"><i class="mdi mdi-cart-outline"></i> <span>Invoice Locked</span></a>
                      </li>
                    @endif
                <!--<li role="presentation" class="nav-item dropdown">-->
                <!--      <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="mdi mdi-cash"></i> Loan <span class="caret"></span></a>-->
                <!--    <div class="dropdown-menu">-->
                <!--        @if(Auth::user()->role->name == 'admin' || in_array('328', json_decode(Auth::user()->role->permissions)))-->
                <!--          <a href="{{route('sales.loan.issue')}}" data-url="" color="seagreen" id="pick" class="tab-link dropdown-item"><i class="mdi mdi-note"></i><span>Temporary Loan Receive</span></a>-->
                <!--        @endif -->
                <!--        @if(Auth::user()->role->name == 'admin' || in_array('329', json_decode(Auth::user()->role->permissions)))-->
                <!--          <a href="{{route('sales.loan.return')}}" data-url="" color="seagreen" id="pick" class="tab-link dropdown-item"><i class="mdi mdi-note"></i> <span>Loan Recovery</span></a>-->
                <!--        @endif                -->
                <!--    </div>-->
                <!--</li>-->
                <li role="presentation" class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="fa mdi mdi-chart-bar"></i> Reports <span class="caret"></span></a>
                    <div class="dropdown-menu">
                        @if(Auth::user()->role->name == 'admin' || in_array('321', json_decode(Auth::user()->role->permissions)))
                            <a href="{{route('rpt.customer.list')}}" data-url="" color="red" id="customer" class="tab-link dropdown-item"><i class="mdi mdi-account-star"></i> <span>Customer List</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('308', json_decode(Auth::user()->role->permissions)))
                            <a href="{{route('rpt.inv.date.list')}}" data-url="" color="seagreen" id="pick" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Date Wise Report</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('309', json_decode(Auth::user()->role->permissions)))
                            <a href="{{route('rpt.inv.date.summ.list')}}" data-url="" color="seagreen" id="pick" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Date Wise Summary Report</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('310', json_decode(Auth::user()->role->permissions)))
                            <a href="{{route('rpt.inv.list')}}" data-url="" color="seagreen" id="pick" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Invoice Wise Report</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('311', json_decode(Auth::user()->role->permissions)))
                            <a href="{{route('rpt.inv.itm.list')}}" data-url="" color="seagreen" id="pick" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Item Wise Sales Report</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('312', json_decode(Auth::user()->role->permissions)))
                        
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('313', json_decode(Auth::user()->role->permissions)))
                            <a href="{{route('rpt.inv.cust.list')}}" data-url="" color="seagreen" id="pick" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Customer Wise Sales Report</span></a>
                        @endif

                        @if(Auth::user()->role->name == 'admin' || in_array('313', json_decode(Auth::user()->role->permissions)))
                            <a href="{{route('rpt.cust.order.statement')}}" data-url="" color="seagreen" id="pick" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Customer Ledger Sheet</span></a>
                        @endif

                        @if(Auth::user()->role->name == 'admin' || in_array('314', json_decode(Auth::user()->role->permissions)))
                            <a href="{{route('rpt.so.list')}}" data-url="" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Sales Order Report</span></a>
                        @endif

                        @if(Auth::user()->role->name == 'admin' || in_array('314', json_decode(Auth::user()->role->permissions)))
                            <a href="{{route('rpt.sales.order.list.pending')}}" data-url="" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Sales Order List (Pending)</span></a>
                        @endif

                        @if(Auth::user()->role->name == 'admin' || in_array('315', json_decode(Auth::user()->role->permissions)))
                            <a href="{{route('rpt.del.list')}}" data-url="" color="orange" id="dispatch" class="tab-link dropdown-item"><i class="mdi mdi-truck-fast"></i> <span>Delivery Report</span></a>
                        @endif
                        <!--@if(Auth::user()->role->name == 'admin' || in_array('316', json_decode(Auth::user()->role->permissions)))-->
                        <!--    <a href="{{route('rpt.cond.inv.list')}}" data-url="" color="orange" id="dispatch" class="tab-link dropdown-item"><i class="mdi mdi-truck-fast"></i> <span>Conditional Report</span></a>-->
                        <!--@endif-->
                         
                        <!--@if(Auth::user()->role->name == 'admin' || in_array('317', json_decode(Auth::user()->role->permissions)))-->
                        <!--    <a href="{{route('rpt.inv.comm.list')}}" data-url="" color="seagreen" id="pick" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Commission Report</span></a>-->
                        <!--@endif-->
                        <!--@if(Auth::user()->role->name == 'admin' || in_array('318', json_decode(Auth::user()->role->permissions)))-->
                        <!--    <a href="{{route('rpt.top.item.sales.qty')}}" data-url="" color="seagreen" id="pick" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Sales Qty(Top 20 Item)</span></a>-->
                        <!--@endif-->
                        <!--@if(Auth::user()->role->name == 'admin' || in_array('319', json_decode(Auth::user()->role->permissions)))-->
                        <!--    <a href="{{route('rpt.top.item.sales.volume')}}" data-url="" color="seagreen" id="pick" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Sales Volume(Top 20 Item)</span></a>-->
                        <!--@endif-->
                        @if(Auth::user()->role->name == 'admin' || in_array('321', json_decode(Auth::user()->role->permissions)))
                          <a href="{{route('rpt.inv.wise.profit_loss')}}" data-url="" color="seagreen" id="pick" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Invoice wise Profit and Loss</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('322', json_decode(Auth::user()->role->permissions)))
                          <a href="{{route('rpt.item.wise.profit_loss')}}" data-url="" color="seagreen" id="pick" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Item wise Profit and Loss</span></a>
                        @endif  
                    </div>
                  </li>
                  <!--<li role="presentation" class="nav-item dropdown">-->
                  <!--   <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="fa mdi mdi-chart-bar"></i> Customer Reports <span class="caret"></span></a>-->
                  <!--   <div class="dropdown-menu">-->
                        <!--@if(Auth::user()->role->name == 'admin' || in_array('320', json_decode(Auth::user()->role->permissions)))-->
                        <!--    <a href="{{route('rpt.salesperson.list')}}" data-url="" color="red" id="customer" class="tab-link dropdown-item"><i class="mdi mdi-account-star"></i> <span>Sales Person List</span></a>-->
                        <!--@endif-->
                       
                        <!--@if(Auth::user()->role->name == 'admin' || in_array('322', json_decode(Auth::user()->role->permissions)))-->
                        <!--    <a href="{{route('rpt.pending.sp.cust.list')}}" data-url="" color="red" id="customer" class="tab-link dropdown-item"><i class="mdi mdi-account-star"></i> <span>Customer List(Pending SP)</span></a>-->
                        <!--@endif-->
                        <!--@if(Auth::user()->role->name == 'admin' || in_array('323', json_decode(Auth::user()->role->permissions)))-->
                        <!--    <a href="{{route('rpt.sp.wise.cust.list')}}" data-url="" color="red" id="customer" class="tab-link dropdown-item"><i class="mdi mdi-account-star"></i> <span>Sales Person Wise Customer List</span></a>-->
                        <!--@endif-->
                  <!--      @if(Auth::user()->role->name == 'admin' || in_array('324', json_decode(Auth::user()->role->permissions)))-->
                  <!--          <a href="{{route('rpt.cust.wise.price.list')}}" data-url="" color="red" id="customer" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Customer Wise Price List</span></a>-->
                  <!--      @endif-->
                        <!--@if(Auth::user()->role->name == 'admin' || in_array('325', json_decode(Auth::user()->role->permissions)))-->
                        <!--    <a href="{{route('rpt.cust.wise.sales.stm')}}" data-url="" color="red" id="customer" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Sales Person Wise Customer Sales</span></a>-->
                        <!--@endif-->
                        <!--@if(Auth::user()->role->name == 'admin' || in_array('326', json_decode(Auth::user()->role->permissions)))-->
                        <!--    <a href="{{route('rpt.salesperson.wise.sales.stm')}}" data-url="" color="red" id="customer" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Sales Person Wise Summary Statement</span></a>-->
                        <!--@endif-->
                  <!--  </div>-->
                  <!-- </li>-->
                   
                </ul>
                <ul class="nav navbar-nav pos" style="display:none">
                    <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/pos/invoices/si/insert" color="red" id="sales" class="tab-link nav-link"><i class="mdi mdi-cart-outline"></i> <span>Sales Register</span></a>                
                    </li>
                    <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/pos/invoices/si" color="green" id="history" class="tab-link nav-link"><i class="mdi mdi-history"></i> <span>Sales History</span></a>                
                    </li>
                </ul>
                <ul class="nav navbar-nav material" style="display:none">
                    <li class="nav-item" role="presentation">
                          <a href="{{ route('suppliers.create') }}" color="sky" id="supplier" class="tab-link nav-link"><i class="mdi mdi-account"></i> <span>Suppliers</span></a> 
                    </li>
                      
                    <li role="presentation" class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown"><i class="mdi mdi-tag-multiple"></i> Items <span class="caret"></span></a>
                        <div class="dropdown-menu">
                        
                        <!--@if(Auth::user()->role->name == 'admin' || in_array('201', json_decode(Auth::user()->role->permissions)))-->
                        <!--  <a href="{{ route('suppliers.create') }}" color="sky" id="supplier" class="tab-link nav-link"><i class="mdi mdi-account"></i> <span>Suppliers</span></a> -->
                        <!--@endif-->

                        @if(Auth::user()->role->name == 'admin' || in_array('209', json_decode(Auth::user()->role->permissions))) 
                            <a href="{{ route('itm.cat.index') }}" color="green" id="pmo" class="tab-link nav-link"><i class="mdi mdi-calendar-text-outline"></i> <span>Item Category</span></a>
                        @endif
                        
                        @if(Auth::user()->role->name == 'admin' || in_array('210', json_decode(Auth::user()->role->permissions))) 
                            <a href="{{ route('itm.create') }}" color="purple" id="ppo" class="tab-link nav-link"><i class="mdi mdi-cube-outline"></i> <span>Items</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('211', json_decode(Auth::user()->role->permissions)))
                        
                          <!-- <a href="{{ route('itm.barcode') }}" color="orange" id="inventory" class="tab-link nav-link"><i class="mdi mdi-tag-multiple"></i> <span>Item Barcode</span></a> -->
                        
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('212', json_decode(Auth::user()->role->permissions)))
                         
                            <a href="{{ route('itm.op.index') }}" color="orange" id="inventory" class="tab-link nav-link"><i class="fa fa-cubes"></i> <span>Item Opening</span></a>
                           
                        @endif 
                        </div>
                  </li>
                    <li role="presentation" class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle " data-toggle="dropdown"><i class="mdi mdi-tag-multiple"></i> Inventory <span class="caret"></span></a>
                        <div class="dropdown-menu">
                          
                            @if(Auth::user()->role->name == 'admin' || in_array('206', json_decode(Auth::user()->role->permissions)))
                                <a href="{{ route('itm.inv.dam.index') }}" color="seagreen" class="tab-link dropdown-item"><i class="mdi mdi-view-sequential"></i> <span>Damages</span></a>
                            @endif
                            @if(Auth::user()->role->name == 'admin' || in_array('207', json_decode(Auth::user()->role->permissions)))
                                <a href="{{ route('itm.inv.short.index') }}" color="seagreen" class="tab-link dropdown-item"><i class="mdi mdi-view-sequential"></i> <span>Shortages</span></a>
                            @endif
                            @if(Auth::user()->role->name == 'admin' || in_array('208', json_decode(Auth::user()->role->permissions)))
                                <a href="{{ route('itm.inv.exp.index') }}" color="seagreen" class="tab-link dropdown-item"><i class="mdi mdi-view-sequential"></i> <span>Process Loss</span></a>
                            @endif
                        </div>
                    </li>
                    
                    @if(Auth::user()->role->name == 'admin' || in_array('222', json_decode(Auth::user()->role->permissions))) 
                      <li class="nav-item" role="presentation">
                      <a href="{{ route('raw.itm.receive.index') }}" color="seagreen" class="tab-link nav-link"><i class="mdi mdi-view-sequential"></i> <span>Raw Material Receive</span></a>
                      </li>
                    @endif

                    @if(Auth::user()->role->name == 'admin' || in_array('223', json_decode(Auth::user()->role->permissions))) 
                      <li class="nav-item" role="presentation">
                      <a href="{{ route('raw.itm.issue.prod.index') }}" color="seagreen" class="tab-link nav-link"><i class="mdi mdi-view-sequential"></i> <span>Issue To Prod</span></a>
                      </li>
                    @endif
                    
                    @if(Auth::user()->role->name == 'admin' || in_array('225', json_decode(Auth::user()->role->permissions))) 
                      <li class="nav-item" role="presentation">
                      <a href="{{ route('itm.consumable.index') }}" color="seagreen" class="tab-link nav-link"><i class="mdi mdi-view-sequential"></i> <span>Item Consumed</span></a>
                      </li>
                    @endif
                  
                    @if(Auth::user()->role->name == 'admin' || in_array('224', json_decode(Auth::user()->role->permissions))) 
                        <li class="nav-item" role="presentation">
                        <a href="{{ route('fin.goods.rec.index') }}" color="seagreen" class="tab-link nav-link"><i class="mdi mdi-view-sequential"></i> <span>Finish Goods Receive</span></a></li>
                    @endif
                  
                    <!--@if(Auth::user()->role->name == 'admin' || in_array('209', json_decode(Auth::user()->role->permissions))) -->
                    <!--<li class="nav-item" role="presentation">-->
                    <!--  <a href="{{ route('itm.cat.index') }}" color="green" id="pmo" class="tab-link nav-link"><i class="mdi mdi-calendar-text-outline"></i> <span>Item Category</span></a>-->
                    <!--</li>-->
                    <!--@endif-->
                    <!--@if(Auth::user()->role->name == 'admin' || in_array('210', json_decode(Auth::user()->role->permissions))) -->
                    <!--<li class="nav-item" role="presentation">-->
                    <!--  <a href="{{ route('itm.create') }}" color="purple" id="ppo" class="tab-link nav-link"><i class="mdi mdi-cube-outline"></i> <span>Items</span></a>-->
                    <!--</li>-->
                    <!--@endif-->
                    <!--@if(Auth::user()->role->name == 'admin' || in_array('211', json_decode(Auth::user()->role->permissions)))-->
                    <!--<li class="nav-item" role="presentation">-->
                    <!--  <a href="{{ route('itm.barcode') }}" color="orange" id="inventory" class="tab-link nav-link"><i class="mdi mdi-tag-multiple"></i> <span>Item Barcode</span></a>-->
                    <!--</li>-->
                    <!--@endif-->
                    <!--@if(Auth::user()->role->name == 'admin' || in_array('212', json_decode(Auth::user()->role->permissions)))-->
                    <!--<li class="nav-item" role="presentation">-->
                    <!--  <a href="{{ route('itm.op.index') }}" color="orange" id="inventory" class="tab-link nav-link"><i class="fa fa-cubes"></i> <span>Item Opening</span></a>-->
                    <!--</li>-->
                    <!--@endif-->
                     @if(Auth::user()->role->name == 'admin' || in_array('214', json_decode(Auth::user()->role->permissions)))
                    <li class="nav-item" role="presentation">
                        <a href="{{route('sales.order.pending')}}" data-url="" color="purple" id="jobsheets" class="tab-link nav-link"><i class="mdi mdi-cart"></i> <span>Sales Delivery</span></a>
                    </li>
                    @endif

                    <li class="nav-item" role="presentation">
                        <a href="{{route('sales.order.pending_item')}}" data-url="" color="purple" id="jobsheets" class="tab-link nav-link"><i class="mdi mdi-cart"></i> <span>Delivery Pending Items</span></a>
                    </li>

                    <li role="presentation" class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="fa mdi mdi-chart-bar"></i> Reports <span class="caret"></span></a>
                      <div class="dropdown-menu">
                        <!--@if(Auth::user()->role->name == 'admin' || in_array('213', json_decode(Auth::user()->role->permissions)))-->
                        <!--    <a href="{{ route('itm.barcode.view.rpt') }}" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Item Barcode List</span></a>-->
                        <!--@endif-->
                        @if(Auth::user()->role->name == 'admin' || in_array('214', json_decode(Auth::user()->role->permissions)))
                            <a href="{{ route('itm.tree.view.rpt') }}" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Item Tree View</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('215', json_decode(Auth::user()->role->permissions)))
                          <a href="{{ route('itm.opening.rpt') }}" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-note"></i> <span>Item Opening</span></a>
                        @endif

                        @if(Auth::user()->role->name == 'admin' || in_array('216', json_decode(Auth::user()->role->permissions)))
                          <a href="{{ route('itm.stock.rpt') }}" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-note"></i> <span>Item Stock</span></a>
                        @endif
                        <!-- a href="{{ route('itm.stock.ledger.det.rpt') }}" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-note"></i> <span>Item Ledger (Details)</span></a>
                        <a href="{{ route('itm.stock.ledger.rpt') }}" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-note"></i> <span>Item Ledger (Summary)</span></a -->
                        @if(Auth::user()->role->name == 'admin' || in_array('217', json_decode(Auth::user()->role->permissions)))
                          <a href="{{ route('itm.stock.ledger1.rpt') }}" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-note"></i> <span>Item Ledger</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('218', json_decode(Auth::user()->role->permissions)))
                          <a href="{{ route('itm.wise.purchase.rpt') }}" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-note"></i> <span>Item Wise Purchase Report</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('219', json_decode(Auth::user()->role->permissions)))
                            <a href="{{ route('itm.sup.wise.purchase.rpt') }}" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-note"></i> <span>Supplier Wise Purchase Report</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('220', json_decode(Auth::user()->role->permissions)))
                            <a href="{{ route('itm.dt.wise.damages.rpt') }}" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-note"></i> <span>Date Wise Damage Report</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('221', json_decode(Auth::user()->role->permissions)))
                            <a href="{{ route('itm.dt.wise.shortages.rpt') }}" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-note"></i> <span>Date Wise Shortage Report</span></a>
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('222', json_decode(Auth::user()->role->permissions)))
                            <a href="{{ route('itm.dt.wise.expired.rpt') }}" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-note"></i> <span>Date Wise Process Loss Report</span></a>
                        @endif

                        @if(Auth::user()->role->name == 'admin' || in_array('215', json_decode(Auth::user()->role->permissions)))
                          <a href="{{ route('rpt.daily.order.statement') }}" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-note"></i> <span>Daily Cash Book</span></a>
                        @endif

                        <a href="{{ route('sales.delivery.prod.report.daily') }}" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Delivery Report</span></a>

                      </div>
                    </li>

                    <li role="presentation" class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="fa mdi mdi-chart-bar"></i>SP Reports <span class="caret"></span></a>
                      <div class="dropdown-menu">
                        <!--@if(Auth::user()->role->name == 'admin' || in_array('213', json_decode(Auth::user()->role->permissions)))-->
                        <!--    <a href="{{ route('itm.barcode.view.rpt') }}" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Item Barcode List</span></a>-->
                        <!--@endif-->
                        <!-- @if(Auth::user()->role->name == 'admin' || in_array('214', json_decode(Auth::user()->role->permissions)))
                            <a href="{{ route('itm.stock.ledger2.rpt') }}" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Finish Goods Stock</span></a>
                        @endif -->

                        @if(Auth::user()->role->name == 'admin' || in_array('214', json_decode(Auth::user()->role->permissions)))
                            <a href="{{ route('yearlyReports') }}" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Yearly Finish Goods Report</span></a>
                        @endif

                        @if(Auth::user()->role->name == 'admin' || in_array('214', json_decode(Auth::user()->role->permissions)))
                            <a href="{{ route('month.wise.report') }}" color="blue" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Month Wise Report</span></a>
                        @endif

                      </div>
                    </li>


                </ul>
                  <ul class="nav navbar-nav srm" style="display:none">
                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/srm/welcome" color="red" id="dashboard" class="tab-link nav-link"><i class="mdi mdi-database"></i> <span>Dashboard</span></a>                </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/buying/newrequisitions" color="blue" id="mr" class="tab-link nav-link"><i class="fa fa-cubes"></i> <span>Material Requisitions</span></a>                </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/srm/people/eoi" color="red" id="eoi" class="tab-link nav-link"><i class="mdi mdi-account-heart"></i> <span>Express of Interest</span></a>                </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/srm/audit" color="seagreen" id="audit" class="tab-link nav-link"><i class="mdi mdi-marker"></i> <span>Audits</span></a>                </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/srm/audit/qualified" color="seagreen" id="qualified" class="tab-link nav-link"><i class="mdi mdi-marker-check"></i> <span>Qualified Suppliers</span></a>                </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/srm/orders/srm/pq" tooltip="Purchase Quotation/ Revision-Quote" color="blue" id="pq" class="tab-link nav-link"><i class="mdi mdi-note-multiple-outline"></i> <span>Purchase Quotation</span></a>                </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/srm/orders/srm/po" color="orange" id="po" class="tab-link nav-link"><i class="mdi mdi-note-multiple"></i> <span>Purchase Orders</span></a>                </li>
                                              <li role="presentation" class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="fa mdi mdi-chart-bar"></i> Reports <span class="caret"></span></a>
                      <div class="dropdown-menu">
                                                  <a href="#" data-url="https://erp2all.com/srm/welcome/report" color="red" id="suppliers" class="tab-link dropdown-item"><i class="mdi mdi-account-star"></i> <span>Top Supplier Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/srm/audit/report" color="seagreen" id="audit" class="tab-link dropdown-item"><i class="mdi mdi-marker"></i> <span>Comparative Statement</span></a>                                                                        <a href="#" data-url="https://erp2all.com/srm/audit/report/qualified" color="seagreen" id="qualified" class="tab-link dropdown-item"><i class="mdi mdi-marker-check"></i> <span>Qualified Suppliers list</span></a>                                                                </div>
                  </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/srm/config" color="blue" id="config" class="tab-link nav-link"><i class="mdi mdi-cog-outline blue"></i> <span>Config</span></a>                </li>
                                      </ul>
                  <ul class="nav navbar-nav buying" style="display:none">
                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/buying/requisitions" color="blue" id="requisitions" class="tab-link nav-link"><i class="fa fa-cubes"></i> <span>Material Requisition</span></a>                </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/srm/orders/bp/po" color="orange" id="po" class="tab-link nav-link"><i class="mdi mdi-note-multiple"></i> <span>Purchase Order</span></a>                </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/accounting/invoices/bp/pi" color="seagreen" id="pi" class="tab-link nav-link"><i class="mdi mdi-note"></i> <span>Purchase Invoices</span></a>                </li>
                                              <li role="presentation" class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="fa mdi mdi-chart-bar"></i> Reports <span class="caret"></span></a>
                      <div class="dropdown-menu">
                                                  <a href="#" data-url="https://erp2all.com/accounting/reports/aged/supplier" color="red" id="aged" class="tab-link dropdown-item"><i class="mdi mdi-book-variant"></i> <span>Supplier Aged Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/srm/orders/receivesreport" color="red" id="receives" class="tab-link dropdown-item"><i class="mdi mdi-view-list"></i> <span>Good Receipt List</span></a>                                                                </div>
                  </li>
                  </ul>

                  <ul class="nav navbar-nav hrm" style="display:none">
                     <!-- <li class="nav-item" role="presentation">
                       <a href="#" data-url="https://erp2all.com/hrm/recruitment" color="red" id="recruitment" class="tab-link nav-link"><i class="mdi mdi-account-arrow-right"></i> <span>Recruitment</span></a>  
                      </li> -->
                    @if(Auth::user()->role->name == 'admin' || in_array('500', json_decode(Auth::user()->role->permissions)))
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('sys.dropdown.index') }}" color="red" id="employee" class="tab-link nav-link"><i class="fa fa-th-list"></i> <span>System Information</span></a>
                    </li>
                    @endif
                    @if(Auth::user()->role->name == 'admin' || in_array('501', json_decode(Auth::user()->role->permissions)))   
                          <li class="nav-item" role="presentation">  
                            <a href="{{ route('employees.index') }}" color="seagreen" id="employee" class="tab-link nav-link"><i class="mdi mdi-account-supervisor"></i> <span>Employees</span></a>                
                          </li>
                    @endif
                    @if(Auth::user()->role->name == 'admin' || in_array('502', json_decode(Auth::user()->role->permissions)))  
                      <li class="nav-item" role="presentation">
                      <a href="{{ route('attendance.index') }}" class="tab-link nav-link"><i class="mdi mdi-account-group"></i> <span>Attendance Entry</span></a>  
                      </li>
                    @endif
                     @if(Auth::user()->role->name == 'admin' || in_array('503', json_decode(Auth::user()->role->permissions)))  
                      <li class="nav-item" role="presentation">
                      <a href="{{route('leave.index')}}" class="tab-link nav-link"><i class="mdi mdi-account-group"></i> <span>Leave Requests</span></a>  
                      </li>
                    @endif
                    <!-- @if(Auth::user()->role->name == 'admin' || in_array('505', json_decode(Auth::user()->role->permissions)))  
                        <li class="nav-item" role="presentation">
                          <a href="{{route('attendance.process')}}" color="red" id="payroll" class="tab-link nav-link"><i class="mdi mdi-play-circle"></i> <span>Process Attendance</span></a>
                        </li>
                    @endif -->
                    @if(Auth::user()->role->name == 'admin' || in_array('508', json_decode(Auth::user()->role->permissions)))  
                        <li class="nav-item" role="presentation">
                          <a href="{{route('salary.process')}}" color="red" id="payroll" class="tab-link nav-link"><i class="mdi mdi-play-circle"></i> <span>Process Salary</span></a>
                        </li>
                    @endif
                <!--       
                    <li class="nav-item" role="presentation"> 
                                      
                      <a href="#" data-url="https://erp2all.com/hrm/training" color="blue" id="training" class="tab-link nav-link"><i class="mdi mdi-account-convert"></i> <span>Training</span></a>                
                      </li>
                    <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/hrm/jobposting/transfer" color="orange" id="transfer" class="tab-link nav-link"><i class="mdi mdi-transit-transfer"></i> <span>Transfer</span></a>                
                     </li>
                    <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/payroll/attendance/leaverequests" color="seagreen" id="leaves" class="tab-link nav-link"><i class="mdi mdi-account-arrow-left"></i> <span>Leave Process</span></a> 
                     </li>
                    <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/hrm/appraisal" color="red" id="appraisal" class="tab-link nav-link"><i class="mdi mdi-account-star"></i> <span>Appraisal</span></a>                
                     </li>
                    <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/hrm/promotion" color="red" id="promotion" class="tab-link nav-link"><i class="mdi mdi-account-star"></i> <span>Promotion</span></a>                
                     </li>
                    <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/hrm/notice" color="red" id="noticeboard" class="tab-link nav-link"><i class="mdi mdi-notification-clear-all"></i> <span>Notice</span></a>                
                     </li>
                    <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/hrm/disciplinaryaction" color="red" id="disciplinary" class="tab-link nav-link"><i class="mdi mdi-scale-balance"></i> <span>Disciplinary Action</span></a>
                     </li>
                -->   
                    <li role="presentation" class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="fa mdi mdi-chart-bar"></i> Reports <span class="caret"></span></a>
                       
                      <div class="dropdown-menu">
                        @if(Auth::user()->role->name == 'admin' || in_array('520', json_decode(Auth::user()->role->permissions)))
                            <a href="{{route('get.dept.sec.list')}}" color="seagreen" id="employeereport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> 
                            <span>Department & Section Wise Listing of Employees</span></a>
                        @endif 
                        <!-- @if(Auth::user()->role->name == 'admin' || in_array('521', json_decode(Auth::user()->role->permissions))) 
                            <a href="{{route('get.pend.machine.list')}}" color="seagreen" id="transferreport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i><span>Pending Machine Id</span></a>      
                        @endif 
                        @if(Auth::user()->role->name == 'admin' || in_array('522', json_decode(Auth::user()->role->permissions))) 
                            <a href="{{route('get.raw.attendance.data')}}" color="seagreen" id="transferreport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i><span>Card Punching Log</span></a>      
                        @endif 
                        @if(Auth::user()->role->name == 'admin' || in_array('525', json_decode(Auth::user()->role->permissions))) 
                            <a href="{{route('get.attend.error.data')}}" color="seagreen" id="transferreport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i><span>Error Attendance Data</span></a>      
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('523', json_decode(Auth::user()->role->permissions))) 
                            <a href="{{route('get.attend.present.data')}}" color="seagreen" id="transferreport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i><span>Present Report</span></a>            
                        @endif 
                        @if(Auth::user()->role->name == 'admin' || in_array('524', json_decode(Auth::user()->role->permissions))) 
                            <a href="{{route('get.attend.absent.data')}}" color="seagreen" id="transferreport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i><span>Absent Report</span></a>      
                        @endif -->
                        @if(Auth::user()->role->name == 'admin' || in_array('526', json_decode(Auth::user()->role->permissions))) 
                            <a href="{{route('get.emp.leave.data')}}" color="seagreen" id="transferreport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i><span>Leave Report</span></a>      
                        @endif
                        @if(Auth::user()->role->name == 'admin' || in_array('527', json_decode(Auth::user()->role->permissions))) 
                            <a href="{{route('get.emp.leave.summary.data')}}" color="seagreen" id="transferreport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i><span>Leave Summary Report</span></a>      
                        @endif
                        <!-- @if(Auth::user()->role->name == 'admin' || in_array('529', json_decode(Auth::user()->role->permissions))) 
                            <a href="{{route('get.job.card.report')}}" color="seagreen" id="transferreport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i><span>Job Card Report</span></a>      
                        @endif -->
                        <!-- @if(Auth::user()->role->name == 'admin' || in_array('530', json_decode(Auth::user()->role->permissions))) 
                            <a href="{{route('get.monthly.sal.report')}}" color="seagreen" id="transferreport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i><span>Monthly Salary Report</span></a>      
                        @endif -->

                        @if(Auth::user()->role->name == 'admin' || in_array('530', json_decode(Auth::user()->role->permissions))) 
                            <a href="{{route('attendance.list')}}" color="seagreen" id="transferreport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i><span>Salary &  Wages Sheet</span></a>      
                        @endif
                        <!-- a href="#" data-url="https://erp2all.com/payroll/reports/individualleavereportview" color="seagreen" id="individualleavereport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> 
                            <span>Individual Leave Report</span></a>
                        <a href="#" data-url="https://erp2all.com/hrm/jobposting/transfertemplate" color="seagreen" id="transferreport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> 
                            <span>Transfer History</span></a -->                                         
                      </div>
                    </li>
                  
                    <li role="presentation" class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="fa mdi mdi-cog-outline seagreen"></i> Config <span class="caret"></span></a>
                      <div class="dropdown-menu">
                       @if(Auth::user()->role->name == 'admin' || in_array('504', json_decode(Auth::user()->role->permissions)))
                            <a href="{{route('leave.types.index')}}" color="seagreen" id="jobgrade" class="tab-link dropdown-item"><i class="mdi mdi-settings"></i> <span>Leave Types</span></a> 
                       @endif 
                       @if(Auth::user()->role->name == 'admin' || in_array('506', json_decode(Auth::user()->role->permissions)))
                            <a href="{{route('timesheet.index')}}" color="seagreen" id="jobgrade" class="tab-link dropdown-item"><i class="mdi mdi-settings"></i> <span>Office Timing</span></a> 
                       @endif
                       @if(Auth::user()->role->name == 'admin' || in_array('507', json_decode(Auth::user()->role->permissions)))
                            <a href="{{route('holiday.index')}}" color="seagreen" id="jobgrade" class="tab-link dropdown-item"><i class="mdi mdi-settings"></i> <span>Holiday Information</span></a> 
                       @endif  
                     </div>
                    </li>
                
                </ul>
                
                
                  <ul class="nav navbar-nav payroll" style="display:none">
                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/hrm/employees/payroll" color="red" id="employee" class="tab-link nav-link"><i class="mdi mdi-account-plus"></i> <span>Employees</span></a>                </li>
                                              <li role="presentation" class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="fa mdi mdi-account-group"></i> Roster & Attendance <span class="caret"></span></a>
                      <div class="dropdown-menu">
                                                  <a href="#" data-url="https://erp2all.com/payroll/attendance/manual" class="tab-link dropdown-item"><i class="mdi mdi-account-group"></i> <span>Attendance Entry</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/attendance/corrections" class="tab-link dropdown-item"><i class="mdi mdi-account-group"></i> <span>Attendance Corrections</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/attendance/rosters" class="tab-link dropdown-item"><i class="mdi mdi-account-group"></i> <span>Rosters Plans</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/attendance/rosterchanges" class="tab-link dropdown-item"><i class="mdi mdi-account-group"></i> <span>Roster Changes</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/attendance/leaverequests" class="tab-link dropdown-item"><i class="mdi mdi-account-group"></i> <span>Leave Requests</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/attendance/otrequests" class="tab-link dropdown-item"><i class="mdi mdi-account-group"></i> <span>Overtime Requests</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/config/calendar" class="tab-link dropdown-item"><i class="mdi mdi-calendar"></i> <span>Calendar</span></a>                                                                </div>
                  </li>
                      <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/payroll/expenses" color="purple" id="expense" class="tab-link nav-link"><i class="mdi mdi-cash-refund"></i> <span>Expense Claim</span></a>
                     </li>
                                              <li role="presentation" class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="fa mdi mdi-cogs"></i> Production <span class="caret"></span></a>
                      <div class="dropdown-menu">
                                                  <a href="#" data-url="https://erp2all.com/payroll/financial/productionitem" color="green" id="productionitem" class="tab-link dropdown-item"><i class="mdi mdi-settings-box"></i> <span>Production Item</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/financial/production" color="green" id="productionquantity" class="tab-link dropdown-item"><i class="mdi mdi-settings-box"></i> <span>Production</span></a>                                                                </div>
                  </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/payroll/financial/loan" color="navyblue" id="loan" class="tab-link nav-link"><i class="mdi mdi-cash"></i> <span>Loan</span></a>                </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/payroll/payroll/regular" color="red" id="payroll" class="tab-link nav-link"><i class="mdi mdi-play-circle"></i> <span>Run Payroll</span></a>                </li>
                                              <li role="presentation" class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="fa mdi mdi-chart-bar"></i> Reports <span class="caret"></span></a>
                      <div class="dropdown-menu">
                                                  <a href="#" data-url="https://erp2all.com/payroll/reports/payroll" id="payroll" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Payroll Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/reports/reconcile" id="reconcile" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Reconcile Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/reports/costcenter" id="costcenter" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Cost Center Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/reports/attendance" id="attendance" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Attendance Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/reports/leaves" id="leaves" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Leaves Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/reports/shiftwise" id="shiftwise" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Shiftwise Attendance</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/reports/bonusReport" color="seagreen" id="bonusreport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Bonus Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/reports/overtimereport" color="seagreen" id="overtimereport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Individual Overtime Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/reports/totalovertimereport" color="seagreen" id="totalovertimereport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Total Overtime Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/providentfund/activitytemplate" color="seagreen" id="providentfundreport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Individual PF Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/reports/totalpfactivityview" color="seagreen" id="totalprovidentfundreport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Total PF Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/reports/incrementreportview" color="seagreen" id="incrementreport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Increment Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/reports/insurancereportview" color="seagreen" id="insurancereport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Total Insurance Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/reports/shiftreportview" color="seagreen" id="shiftreport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Shift Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/reports/individualshiftreportview" color="seagreen" id="individualshiftreport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Individual Shift Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/reports/individualattendancereportview" color="seagreen" id="individualattendance" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Individual Attendance Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/reports/totalattendancereportview" color="seagreen" id="totalattendance" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Total Attendance Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/reports/individualabsentview" color="seagreen" id="individualabsent" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Individual Absent Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/reports/individualleavereportview" color="seagreen" id="individualleavereport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Individual Leave Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/demo/pfstatement" color="seagreen" id="individualleavereport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>PF Statement</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/demo/pfinvestmentlist" color="seagreen" id="individualleavereport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>PF Investment List</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/demo/pfinvestmentregister" color="seagreen" id="individualleavereport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>PF Investment Register</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/demo/pfincome" color="seagreen" id="individualleavereport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>PF Income Schedule</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/demo/basicsalary" color="seagreen" id="individualleavereport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Basic Salary History</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/demo/gratuityeligibility" color="seagreen" id="individualleavereport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>Gratuity Eligibility Report</span></a>                                                                        <a href="#" data-url="https://erp2all.com/payroll/demo/wppfeligibility" color="seagreen" id="individualleavereport" class="tab-link dropdown-item"><i class="mdi mdi-chart-bar"></i> <span>WPPF Eligibility Report</span></a>                                                                </div>
                  </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/payroll/config" color="seagreen" id="config" class="tab-link nav-link"><i class="mdi mdi-cog-outline"></i> <span>Config</span></a>                </li>
                                      </ul>
                  <ul class="nav navbar-nav productions" style="display:none">
                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/master/bom/production" color="green" id="orders" class="tab-link nav-link"><i class="mdi mdi-robot-industrial"></i> <span>Production Orders</span></a>                </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/master/bom/production?show=requisitions" color="sky" id="requisitions" class="tab-link nav-link"><i class="mdi mdi-basket-fill"></i> <span>RM Requisitions</span></a>                </li>
                                      </ul>
                  <ul class="nav navbar-nav assets" style="display:none">
                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/assets/fixedassets/normal" color="red" id="fixedassets" class="tab-link nav-link"><i class="mdi mdi-factory"></i> <span>Fixed Assets Register</span></a>                </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/assets/fixedassets/assets/types" color="green" id="fixedassetchart" class="tab-link nav-link"><i class="mdi mdi-file-tree"></i> <span>Chart Of Fixed Asset</span></a>                </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/assets/fixedassets/depreciationrun/yearly" color="navyblue" id="depreciationrun" class="tab-link nav-link"><i class="mdi mdi-play-circle"></i> <span>Depreciation Run</span></a>                </li>
                                              <li role="presentation" class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="fa mdi mdi-cog-outline"></i> Config <span class="caret"></span></a>
                      <div class="dropdown-menu">
                                                  <a href="#" data-url="https://erp2all.com/assets/config" color="seagreen" id="config" class="tab-link dropdown-item"><i class="mdi mdi-settings"></i> <span>Depreciation Method Config</span></a>
                                                  <a href="#" data-url="https://erp2all.com/assets/config/depreciationrun" color="seagreen" id="config" class="tab-link dropdown-item"><i class="mdi mdi-settings"></i> <span>Depreciation Run Config</span></a>                                                                </div>
                  </li>
                  <li role="presentation" class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="fa mdi mdi-chart-bar"></i> Reports <span class="caret"></span></a>
                      <div class="dropdown-menu">
                          <a href="#" data-url="https://erp2all.com/assets/fixedassets/summary/0" color="seagreen" id="report" class="tab-link dropdown-item"><i class="mdi mdi-view-list"></i> <span>Fixed Assets Schedule</span></a>
                          <a href="#" data-url="https://erp2all.com/assets/fixedassets/depreciation/calculation" color="seagreen" id="depreciation" class="tab-link dropdown-item"><i class="mdi mdi-view-quilt"></i> <span>Depreciation Schedule Per Asset</span></a>
                          <a href="#" data-url="https://erp2all.com/assets/fixedassets/depreciationrun/runtime" color="seagreen" id="depreciation" class="tab-link dropdown-item"><i class="mdi mdi-view-sequential"></i> <span>Book Value Report</span></a>
                      </div>
                  </li>
                </ul>
                  <ul class="nav navbar-nav lcm" style="display:none">
                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/lcm/lc/export" color="seagreen" id="export" class="tab-link nav-link"><i class="fa fa-file-text-o"></i> <span>Export LCs</span></a>                </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/lcm/lc/back" color="seagreen" id="back" class="tab-link nav-link"><i class="fa fa-file-text-o"></i> <span>B2B LCs</span></a>                </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/lcm/lc/import" color="seagreen" id="import" class="tab-link nav-link"><i class="fa fa-file-text-o"></i> <span>Import LCs</span></a>                </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/lcm/config" color="seagreen" id="config" class="tab-link nav-link"><i class="fa fa-file-text-o"></i> <span>Config</span></a>                </li>
                                      </ul>
                  <ul class="nav navbar-nav sysinfo" style="display:none">
                      <!-- li class="nav-item" role="presentation">
                        <a href="#" data-url="https://erp2all.com/dashboard/welcome/dashboard" color="silver" id="sysinfo" class="tab-link nav-link"><i class="fa fa-file-text-o"></i> <span>Company Information</span></a>
                      </li -->
                      @if(Auth::user()->role->name == 'admin' || in_array('705', json_decode(Auth::user()->role->permissions)))
                        <li role="presentation" class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="mdi mdi-account-multiple"></i> User Settings <span class="caret"></span></a>
                            <div class="dropdown-menu">
                                <a href="{{ route('role.index') }}" color="seagreen" id="depreciation" class="tab-link dropdown-item"><i class="mdi mdi-view-sequential"></i> <span>Mapping Role</span></a>
                                <a href="{{ route('user.index') }}" color="seagreen" id="depreciation" class="tab-link dropdown-item"><i class="mdi mdi-view-quilt"></i> <span>Create User</span></a>
                            </div>
                        </li>
                      @endif
                      @if(Auth::user()->role->name == 'admin' || in_array('701', json_decode(Auth::user()->role->permissions))) 
                      <li class="nav-item" role="presentation">
                        <a href="{{ asset('/company/company/index') }}" data-url="https://erp2all.com/dashboard/welcome/dashboard" color="silver" id="sysinfo" class="tab-link nav-link">
                          <i class="mdi mdi-desktop-mac"></i> <span>Company Information</span></a>
                      </li>
                      @endif
                      @if(Auth::user()->role->name == 'admin' || in_array('702', json_decode(Auth::user()->role->permissions)))
                      <li class="nav-item" role="presentation">
                        <a href="{{ route('companyassign.index') }}" data-url="https://erp2all.com/dashboard/welcome/dashboard" color="silver" id="test" class="tab-link nav-link"><i class="mdi mdi-desktop-mac"></i> <span>Company Assign</span></a>
                      </li>
                      @endif
                      <!-- @if(Auth::user()->role->name == 'admin' || in_array('703', json_decode(Auth::user()->role->permissions)))
                      <li class="nav-item" role="presentation">
                          <a href="{{ route('sys.dropdown.index') }}" color="red" id="diary" class="tab-link nav-link"><i class="fa fa-th-list"></i> <span>Dropdown Master</span></a>
                      </li>
                      @endif -->
                      @if(Auth::user()->role->name == 'admin' || in_array('704', json_decode(Auth::user()->role->permissions)))
                      <li class="nav-item" role="presentation">
                        <a href="{{ route('setting.accounts.create') }}" data-url="" color="blue" id="config" class="tab-link nav-link"><i class="mdi mdi-cog-outline blue"></i> <span>Config</span></a>
                      </li>
                      @endif
                  </ul>

                  <ul class="nav navbar-nav diary" style="display:none">
                              <li class="nav-item" role="presentation">
                      <a href="#" color="red" id="diary" class="tab-link nav-link"><i class="fa fa-th-list"></i> <span>Diary</span></a>                </li>
                  </ul>
                  <ul class="nav navbar-nav master" style="display:none">
                    <li class="nav-item" role="presentation">
                      <a href="{{ route('cust.index') }}" color="green" id="customer" class="tab-link nav-link"><i class="mdi mdi-account-multiple"></i> <span>Customers</span></a>
                    </li>


                    <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/hrm/employees/master" color="red" id="master" class="tab-link nav-link"><i class="mdi mdi-account-supervisor"></i> <span>Employees</span></a>                </li>
                    
                      <li class="nav-item" role="presentation">

                      <a href="{{ asset('/chartofacc/chartofacc/index') }}" data-url="https://erp2all.com/accounting/coa" color="red" id="coa" class="tab-link nav-link"><i class="mdi mdi-file-tree"></i> <span>Chart of Accounts</span></a>                </li>
                                      </ul>
                  <ul class="nav navbar-nav transactions" style="display:none">
                    <li class="nav-item" role="presentation">
                      <a href="#" onclick="return showSubMenu('accounting')" color="green" id="back" class="tab-link nav-link"><i class="mdi mdi-arrow-left"></i> <span></span></a>
                    </li>

                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/accounting/invoices/transactions/si" color="seagreen" id="si" class="tab-link nav-link"><i class="mdi mdi-login"></i> <span>Sales Invoices</span></a>                </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/accounting/invoices/transactions/pi" color="red" id="pi" class="tab-link nav-link"><i class="mdi mdi-logout"></i> <span>Purchase Invoices</span></a>                </li>
                  <li role="presentation" class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="fa mdi mdi-call-received"></i> Receives <span class="caret"></span></a>
                      <div class="dropdown-menu">
                          <a href="#" data-url="https://erp2all.com/accounting/invoices/si/payments" color="seagreen" class="tab-link dropdown-item"><i class="fa fa-user"></i> <span>Receives From Customer</span></a>
                          <a href="#" data-url="https://erp2all.com/accounting/invoices/si/advances" color="seagreen" class="tab-link dropdown-item"><i class="fa fa-user"></i> <span>Receives On Account / Advances</span></a>
                          <a href="#" data-url="https://erp2all.com/accounting/payment/list/customer" color="seagreen" modal="1" class="tab-link dropdown-item"><i class="fa fa-sign-in"></i> <span>Receives from Other</span></a>                                                                        <a href="#" data-url="https://erp2all.com/accounting/payment/list/refundsupplier" color="seagreen" class="tab-link dropdown-item"><i class="fa fa-sign-in"></i> <span>Receives from Supplier / Refunds</span></a>
                          <a href="#" data-url="https://erp2all.com/accounting/payment/list/employeereceive" color="seagreen" class="tab-link dropdown-item"><i class="fa fa-sign-in"></i> <span>Receives from Employee</span></a>                                                                </div>
                  </li>

                 <li role="presentation" class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="fa mdi mdi-call-made"></i> Payments <span class="caret"></span></a>
                    <div class="dropdown-menu">
                      <a href="#" data-url="https://erp2all.com/accounting/invoices/pi/payments" color="seagreen" modal="1" class="tab-link dropdown-item"><i class="fa fa-user"></i> <span>Payments to Supplier</span></a>
                      <a href="#" data-url="https://erp2all.com/accounting/invoices/pi/advances" color="seagreen" class="tab-link dropdown-item"><i class="fa fa-user"></i> <span>Payments On Account / Advances</span></a>
                      <a href="#" data-url="https://erp2all.com/accounting/payment/list/supplier" color="seagreen" modal="1" class="tab-link dropdown-item"><i class="fa fa-sign-in"></i> <span>Payments to Other</span></a>                                                                        <a href="#" data-url="https://erp2all.com/accounting/payment/list/refundcustomer" color="seagreen" class="tab-link dropdown-item"><i class="fa fa-sign-in"></i> <span>Payments to Customer / Refunds</span></a>
                      <a href="#" data-url="https://erp2all.com/accounting/payment/list/expense" color="seagreen" class="tab-link dropdown-item"><i class="fa fa-dollar"></i> <span>Petty Expenses</span></a>                                                                        <a href="#" data-url="https://erp2all.com/accounting/payment/list/employeeadvance" color="seagreen" class="tab-link dropdown-item"><i class="fa fa-sign-in"></i> <span>Payment to Employee</span></a>
                    </div>
                </li>

                  <li role="presentation" class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="fa mdi mdi-bank-transfer"></i> Fund Transfer <span class="caret"></span></a>
                      <div class="dropdown-menu">
                                                  <a href="#" data-url="https://erp2all.com/accounting/payment/list/withdraw" color="seagreen" class="tab-link dropdown-item"><i class="fa fa-dollar"></i> <span>Cash Withdraws</span></a>                                                                        <a href="#" data-url="https://erp2all.com/accounting/payment/list/deposit" color="seagreen" class="tab-link dropdown-item"><i class="fa fa-dollar"></i> <span>Cash Deposits</span></a>                                                                        <a href="#" data-url="https://erp2all.com/accounting/payment/list/banktransfer" color="seagreen" class="tab-link dropdown-item"><i class="mdi mdi-bank-transfer-out"></i> <span>Bank Transfer</span></a>                                                                        <a href="#" data-url="https://erp2all.com/accounting/payment/list/cashtransfer" color="seagreen" class="tab-link dropdown-item"><i class="mdi mdi-cash-refund"></i> <span>Cash Transfer</span></a>                                                                </div>
                  </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/accounting/contras" color="purple" tooltip="BalanceTransfer/Adjustment between Customer & Supplier ledgers" id="contras" class="tab-link nav-link"><i class="mdi mdi-perspective-more"></i> <span>Balance Transfer</span></a>                </li>
                                              <li class="nav-item" role="presentation">
                      <a href="#" data-url="https://erp2all.com/accounting/budget" color="red" id="budget" class="tab-link nav-link"><i class="mdi mdi-chart-pie"></i> <span>Budget Entry</span></a>                </li>
                                      </ul>
              </div>


              <div>
                <ul class="nav navbar float-right e2a-user-nav">
                    <li class="nav-item" role="presentation">
                      <a href="/" data-url="" color="blue" id="dashboard" class="tab-link nav-link"><i class="mdi mdi-desktop-mac"></i> <span>Dashboard</span></a>
                    </li>
                    <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" color="blue" role="button" aria-expanded="false"><i><img class="img img-responsive" src="{{asset('assets/images/Ajmeri-Golden-Fiber.jpg')}}" width="180" height="180"></i><font size="3" color="red">&nbsp;Ajmeri Golden Fiber</font></a>

                      <!--<a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-expanded="false"><i class="mdi mdi-account-circle"></i> <span class="user-name"></span></a>-->
                      <div class="dropdown-menu" role="menu">
                          <div class="user-info">
                              <div class="user-name">SYCORAX</div>
                          </div>
                          <a class="dropdown-item tab-link" href="#"><span class="icon mdi mdi-home-account"></span>Profile</a>
                          <a class="dropdown-item" href="{{ route('logout') }}"
                             onclick="event.preventDefault();
                                           document.getElementById('logout-form').submit();"><span class="icon mdi mdi-power">
                              {{ __('Logout') }}
                          </span></a>
                          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                              @csrf
                          </form>
                          <a class="dropdown-item tab-link" href="{{route('password.edit')}}">
                              <span  class="ace-icon fa fa-lock"></span>&nbsp;&nbsp;Change Password</a>
                      </div>
                    </li>
                </ul>

             </div>
  </nav>

  <!-- Content Wrapper. Contains page content -->
  <div id="e2a-tab-content" class="tab-content container">
        @yield('content')
  </div>

<!--footer class="e2a-footer">
    <button id="btn-add-tab" type="button" class="add-button e2a-shortcut" data-placement="top" data-popover-content="#a1" data-html="true">+</button>
    <div class="hidden" id="a1" style="display:none">
        <div class="popover-heading"></div>
        <div class="popover-body">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link tab-link" href="#" id="https://erp2all.com/accounting/journals/insert"><i class="mdi mdi-pencil"></i> New Journal</a></li>
                <li class="nav-item"><a class="nav-link tab-link" href="#" data-url="https://erp2all.com/accounting/invoices/transactions/si/insert"><i class="mdi mdi-pencil"></i> New Sales Invoice</a></li>
                <li class="nav-item"><a class="nav-link tab-link" href="#" data-url="https://erp2all.com/accounting/invoices/transactions/pi/insert"><i class="mdi mdi-pencil"></i> New Purchase Invoice</a></li>
                <li class="nav-item"><a class="nav-link tab-link" href="#" data-url="https://erp2all.com/accounting/invoices/si/payment"><i class="mdi mdi-pencil"></i> New Receive From Customer</a></li>
                <li class="nav-item"><a class="nav-link tab-link" href="#" data-url="https://erp2all.com/accounting/invoices/pi/payment"><i class="mdi mdi-pencil"></i> New Payment To Supplier</a></li>
            </ul>
        </div>
    </div>
    <ul id="e2a-tab-list" class="nav nav-tabs e2a-tab-navs" role="tablist">
    <!-- <li>
        <a href="#e2a-tab-1" role="tab" data-toggle="tab" class="active show">Dashboard
            <button type="button" class="btn-close"><i class="mdi mdi-close"></i></button>
        </a>
    </li> -->
    <!-- /ul>
    <div class="scroll-btn-wrapper">
        <button class="scroll-btn scroll-btn-prev"><i class="mdi mdi-menu-left"></i></button>
        <button class="scroll-btn scroll-btn-next"><i class="mdi mdi-menu-right"></i></button>
    </div>
 </footer -->
</div>
<script type="text/javascript" src="{{ asset('assets/js/bootstrap.min.js?v=1.3.3') }}s"></script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js?v=1.3.3') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/bootstrap-datepicker.min.js?v=1.3.3') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/calendar.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/bootbox.min.js?v=1.3.3') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/tabulator.min.js?v=1.3.3') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/autocomplete.min.js?v=1.3.3') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.select.autocomplete.js?v=1.3.3') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/bootstrap-toggle.min.js?v=1.3.3') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/imageMapResizer.min.js?v=1.3.3') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.highlite.js?v=1.3.3') }}"></script> <!-- not found -->

<!-- script type="text/javascript" src="{{ asset('assets/js/jstree.min.js?v=1.3.3') }}"></script -->
<script type="text/javascript" src="{{ asset('assets/js/bootstrap-treeview.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/js/Chart.bundle.min.js?v=1.3.3') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.ddslick.min.js?v=1.3.3') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.panzoom.min.js?v=1.3.3') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.treetable.js?v=1.3.3') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/common.js?v=1.3.3') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/xlsx/shim.min.js?v=1.3.3') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/xlsx/xlsx.full.min.js?v=1.3.3') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/xlsx/Blob.js?v=1.3.3') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/xlsx/FileSaver.js?v=1.3.3') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/fullcalendar.min.js?v=1.3.3') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/datetimepicker_css.js?v=1.3.3') }}"></script>
<script>


  // var s = "<?php echo (Request::is('/sys-dropdown-index') ? 'active' : 'fv'); ?>";

   menu_selection = $('#menu_selection').val();
   var accSelected = false; var crmSelected = false;
   var sdSelected = false; var posSelected = false;
   var mmSelected = false; var hrmSelected = false;
   var payrollSelected = false;  var homeSelected = false;

   var menu = menu_selection.split("@")[0];
   var menuFlag = menu_selection.split("@")[1];
   //alert(menu+''+menuFlag);
   if(menu == 'ACC') accSelected = true;
   else if(menu == 'CRM') crmSelected = true;
   else if(menu == 'SD') sdSelected = true;
   else if(menu == 'POS') posSelected = true;
   else if(menu == 'MM') mmSelected = true;
   else if(menu == 'HRM') hrmSelected = true;
   else if(menu == 'PAYROLL') payrollSelected = true;
   else homeSelected = true;

    var modules = [
        { text: 'ACCOUNTS', value: 'accounting', selected:accSelected, description: 'Accounting & Finance', imageSrc: '{{ asset('assets/images/icons/af.png') }}'},
        { text: 'Sales', value: 'sales', selected:sdSelected, description: 'Sales & Distributions', imageSrc: '{{ asset('assets/images/icons/sd.png') }}'},
        { text: 'PRODUCTION', value: 'crm', selected:crmSelected, description: 'PRODUCTION', imageSrc: '{{ asset('assets/images/icons/crm.png') }}'},
        { text: 'Stock & Delivery', value: 'material', selected:mmSelected, description: 'Material Management', imageSrc: '{{ asset('assets/images/icons/mm.png') }}'},
        { text: 'HRM', value: 'hrm', selected:hrmSelected, description: 'Human Resource Management', imageSrc: '{{ asset('assets/images/icons/hrm.png') }}'},
        //{ text: 'PAYROLL', value: 'payroll', selected:payrollSelected, description: 'Payroll Management', imageSrc: '{{ asset('assets/images/icons/payroll.png') }}'},
        //{ text: 'COMM', value: 'pos', selected:posSelected, description: 'Commercial', imageSrc: '{{ asset('assets/images/icons/pos.png') }}'},
        { text: 'SYS INFO', value: 'sysinfo', selected:homeSelected, description: 'System Information', imageSrc: '{{ asset('assets/images/icons/pm.png') }}'},
    ];

    var dashboards = {
     accounting : 'dashboard/welcome/dashboard',
     crm : 'crm/welcome',
     sales : 'sales/welcome',
     // pos : '',
     material : '',
     //srm : 'srm/welcome',
     // buying : '',
     hrm : 'hrm/welcome/dashboard',
     payroll : 'payroll/welcome/dashboard',
     //productions : '',
     //assets : '',
     //lcm : '',
     sysinfo : '',
     //diary : '',
    };
</script>

<script>

//  $(".chosen-select").chosen(); 


$('.item-nav').click(function (event) {
   // Avoid the link click from loading a new page
   event.preventDefault();

   // Load the content from the link's href attribute
   $('.content').load($(this).attr('href'));
});
</script>
</body>
@yield('pagescript')
</html>