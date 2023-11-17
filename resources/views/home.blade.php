@extends('layouts.app')
@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" />

<style>
	.widget-title, .widget-title a {
		color: #D0C2B2!important; font-weight: 800;
	}
	.card-counter{
    box-shadow: 2px 2px 10px #DADADA;
    margin: 5px;
    padding: 20px 10px;
    background-color: #fff;
    height: 100px;
    border-radius: 5px;
    transition: .3s linear all;
  }

  .card-counter:hover{
    box-shadow: 4px 4px 20px #DADADA;
    transition: .3s linear all;
  }

  .card-counter.primary{
    background-color: #007bff;
    color: #FFF;
  }

  .card-counter.danger{
    background-color: #ef5350;
    color: #FFF;
  }  

  .card-counter.success{
    background-color: #66bb6a;
    color: #FFF;
  }  

  .card-counter.info{
    background-color: #26c6da;
    color: #FFF;
  }  

  .card-counter i{
    font-size: 5em;
    opacity: 0.2;
  }

  .card-counter .count-numbers{
    position: absolute;
    right: 35px;
    top: 20px;
    font-size: 32px;
    display: block;
  }

  .card-counter .count-name{
    position: absolute;
    right: 35px;
    top: 65px;
    font-style: italic;
    text-transform: capitalize;
    opacity: 0.5;
    display: block;
    font-size: 18px;
  }

</style>
@stop
@section('content')

<!-- Main content -->
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="HOME@1" class="form-control" required>
<div class="container-fluid">
  <div class="page-header">
      <h1 style="font-size: 18px;">
          <i class="fa fa-tachometer"></i> Dashboard/Today Summary
      </h1>
  </div>


  <div class="row">
      <div class="col-md-12">
          <div class="info-container">

		<div class="col-md-12 text-center">
			<span style="font-size:30px"><b>PP UNIT</b></span>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="card-counter primary">
					<i class="fa fa-code-fork"></i>
					<span class="count-numbers">{{ number_format($totalOrderPPUnit, 2) }}</span>
					<span class="count-name">Total Order</span>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card-counter danger">
					<i class="fa fa-ticket"></i>
					<span class="count-numbers">{{ number_format($monthlyOrderPPUnit, 2) }}</span>
					<span class="count-name">Monthly Order</span>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card-counter success">
					<i class="fa fa-database"></i>
					<span class="count-numbers">
						<?php 
							$totalPPUnit = 0;
						?>
						@foreach($todayOrderPPUnit as $row) 
							<?php 
								if($row->so_item_unit == 'KG'){
									$totalPPUnit += $row->kgAmount;
								}else{
									$totalPPUnit += $row->pcsAmount;
								}
							?>
						@endforeach

						{{ number_format($totalPPUnit, 2) }}</span>
					<span class="count-name">Today Order</span>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card-counter info">
					<i class="fa fa-users"></i>
					<span class="count-numbers">0</span>
					<span class="count-name"></span>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card-counter info">
					<i class="fa fa-users"></i>
					<span class="count-numbers">{{ number_format($totalPPUnitSales, 2) }}</span>
					<span class="count-name">Total Sales</span>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card-counter primary">
					<i class="fa fa-code-fork"></i>
					<span class="count-numbers">{{ number_format($monthlyPPUnitSales, 2) }}</span>
					<span class="count-name">Monthly Sales</span>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card-counter danger">
					<i class="fa fa-code-fork"></i>
					<span class="count-numbers">{{ number_format($todayPPUnitSales, 2) }}</span>
					<span class="count-name">Today Sales</span>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card-counter success">
					<i class="fa fa-code-fork"></i>
					<span class="count-numbers">0</span>
					<span class="count-name"></span>
				</div>
			</div>

		</div>

  		<br>
		<div class="col-md-12 text-center">
			<span style="font-size:30px"><b>JUTE UNIT</b></span>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="card-counter primary">
					<i class="fa fa-code-fork"></i>
					<span class="count-numbers">{{ number_format($totalOrderjuteUnit, 2) }}</span>
					<span class="count-name">Total Order</span>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card-counter danger">
					<i class="fa fa-ticket"></i>
					<span class="count-numbers">{{ number_format($monthlyOrderJuteUnit, 2) }}</span>
					<span class="count-name">Monthly Order</span>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card-counter success">
					<i class="fa fa-database"></i>
					<span class="count-numbers">
						<?php 
							$totalJuteUnit = 0;
						?>
						@foreach($todayOrderJutenit as $row) 
							<?php 
								if($row->so_item_unit == 'KG'){
									$totalJuteUnit += $row->kgAmount;
								}else{
									$totalJuteUnit += $row->pcsAmount;
								}
							?>
						@endforeach
					{{ number_format($totalJuteUnit, 2) }}</span>
					<span class="count-name">Today Order</span>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card-counter info">
					<i class="fa fa-users"></i>
					<span class="count-numbers">0</span>
					<span class="count-name"></span>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card-counter info">
					<i class="fa fa-users"></i>
					<span class="count-numbers">{{ number_format($totalJuteUnitSales, 2) }}</span>
					<span class="count-name">Total Sales</span>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card-counter primary">
					<i class="fa fa-code-fork"></i>
					<span class="count-numbers">{{ number_format($monthlyJuteUnitSales, 2) }}</span>
					<span class="count-name">Monthly Sales</span>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card-counter danger">
					<i class="fa fa-code-fork"></i>
					<span class="count-numbers">{{ number_format($todayJuteUnitSales, 2) }}</span>
					<span class="count-name">Today Sales</span>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card-counter success">
					<i class="fa fa-code-fork"></i>
					<span class="count-numbers">0</span>
					<span class="count-name"></span>
				</div>
			</div>

		</div>

		<br>
		<div class="col-md-12 text-center">
			<span style="font-size:30px"><b>Outstanding</b></span>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="card-counter primary">
					<i class="fa fa-code-fork"></i>
					<span class="count-numbers"> {{ number_format($todaycollection, 2)}}</span>
					<span class="count-name">Total Collection</span>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card-counter danger">
					<i class="fa fa-ticket"></i>
					<span class="count-numbers"> {{number_format($monthlycollection, 2)}}</span>
					<span class="count-name">Monthly Collection</span>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card-counter success">
					<i class="fa fa-database"></i>
					<span class="count-numbers"> {{number_format($todayoutstanding, 2)}}</span>
					<span class="count-name">Today Outstanding</span>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card-counter info">
					<i class="fa fa-users"></i>
					<span class="count-numbers">{{number_format($montlhyoutstanding, 2)}}</span>
					<span class="count-name">Monthly Outstanding</span>
				</div>
			</div>
		</div>

  <!-- <div class="row">
	 <div class="col-md-12 text-center">
	 	<span style="font-size:30px"><b>PP UNIT</b></span>
	 </div>
      <div class="col-md-1 col-xs-12 col-sm-12 "></div>
			<div class="col-md-2 col-xs-12 col-sm-6">
				<div class="infobox infobox-orange">
					<div class="infobox-icon">
						<i class="ace-icon fa fa-hand-grab-o"></i>
					</div>
					<br/>
					<div class="infobox-data">
						<span class="infobox-data-number counter" style="font-weight: 800;font-size:25px; margin-top: 30px;">
							{{number_format($totalSalesOrder, 2)}}
						</span>
						<div class="infobox-content">
							<h2>Total Order</h2>
						</div>
					</div>
				</div>
			</div>

      <div class="col-md-2 col-xs-12 col-sm-6">
          <div class="infobox infobox-green">
							<div class="infobox-icon">
									<i class="ace-icon fa fa-money"></i>
							</div><br/>

              <div class="infobox-data">
                  <span class="infobox-data-number counter"
                      style="font-weight: 800;font-size:25px; margin-top: 30px;">{{number_format($todaySale, 2)}}</span>
                  <div class="infobox-content">
                      <h2>Today Sale</h2>
                  </div>
              </div>
          </div>
      </div>

      <div class="col-md-2 col-xs-12 col-sm-6">
          <div class="infobox infobox-grey">
              <div class="infobox-icon">
                  <i class="ace-icon fa fa-newspaper-o"></i>
              </div><br/>

              <div class="infobox-data">
                  <span class="infobox-data-number counter"
                      style="font-weight: 800;font-size:25px; margin-top: 30px;">{{number_format($monthSale, 2)}}</span>
                  <div class="infobox-content">
                      <h2>Monthly Sale</h2>
                  </div>
              </div>
          </div>
      </div>

	<div class="col-md-2 col-xs-12 col-sm-6">
			<div class="infobox infobox-green2">
					<div class="infobox-icon">
							<i class="ace-icon fa fa-credit-card"></i>
					</div><br/>

					@foreach($totalstock as $row)
					<div class="infobox-data">
							<span class="infobox-data-number counter"
									style="font-weight: 800;font-size:25px; margin-top: 30px; text-align: center;">{{number_format($row->AMOUNT,2)}}</span>
							<div class="infobox-content">
									<h2>Total Stock</h2>
							</div>
					</div>
					@endforeach
			</div>
	</div>

  </div> 
   -->
  <br/>
	<!-- <div class="row">
      <div class="col-md-1 col-xs-12 col-sm-12"></div>
		<div class="col-md-2 col-xs-12 col-sm-6">
				<div class="infobox infobox-red">
						<div class="infobox-icon">
								<i class="ace-icon fa fa-money"></i>
						</div>
              <div class="infobox-data">
                  <span class="infobox-data-number counter"
                      style="font-weight: 800;font-size:25px; margin-top: 30px; text-align: center;">
					  {{number_format($todaycollection, 2)}}</span>
                  <div class="infobox-content">
                      <h2>Today Collection&nbsp;&nbsp;&nbsp;</h2>
                  </div>
              </div>
          </div>
      </div>

      <div class="col-md-2 col-xs-12 col-sm-6">
          <div class="infobox infobox-blue">
							<div class="infobox-icon">
									<i class="ace-icon fa fa-money"></i>
							</div>

              <div class="infobox-data">
                  <span class="infobox-data-number counter"
                      style="font-weight: 800;font-size:25px; margin-top: 30px; text-align: center;">
					  {{number_format($monthlycollection, 2)}}</span>
                  <div class="infobox-content">
                      <h2>Monthly Collection</h2>
                  </div>
              </div>
          </div>
      </div>

      <div class="col-md-2 col-xs-12 col-sm-6">
				<div class="infobox infobox-orange2">
						<div class="infobox-icon">
								<i class="ace-icon fa fa-hand-grab-o"></i>
						</div>

              <div class="infobox-data">
                  <span class="infobox-data-number counter"
                      style="font-weight: 800;font-size:25px; margin-top: 30px; text-align: center;">
					  {{number_format($todayoutstanding, 2)}}</span>
                  <div class="infobox-content">
                      <h2>Today Outstanding</h2>
                  </div>
              </div>
          </div>
      </div>

			<div class="col-md-2 col-xs-12 col-sm-6">
          <div class="infobox infobox-orange">
              <div class="infobox-icon">
                  <i class="ace-icon fa fa-newspaper-o"></i>
              </div>

              <div class="infobox-data">
                  <span class="infobox-data-number counter"
                      style="font-weight: 800;font-size:25px; margin-top: 30px; text-align: center;">
					  {{number_format($montlhyoutstanding, 2)}}</span>
                  <div class="infobox-content">
                      <h2>Monthly Outstanding</h2>
                  </div>
              </div>
          </div>
      </div>

  </div> -->

  <div class="row">
	  	<!-- today order PP unit  -->
		<div class="col-md-6">
				<div class="widget-box transparent">
						<div class="widget-header widget-header-flat">
								<h4 class="widget-title lighter">
										<i class="ace-icon fa fa-star orange"></i>
										Today Order PP Unit ({{date('D')}})
								</h4>

								<div class="widget-toolbar">
										<a href="#" data-action="collapse">
												<i class="ace-icon fa fa-chevron-up"></i>
										</a>
								</div>
						</div>
						<div class="widget-body" style="display: block;">
								<div class="widget-main no-padding">
									<table class="table table-theme table-data table-view table-theme v-middle" id="datatable">
										<thead class=" thead-dark">
											<th class="text-center" scope="col">Order No</th>
											<th class="text-center" scope="col">Quantity</th>
											<th class="text-center" scope="col">Price</th>
											<th class="text-center" scope="col">Amount</th>
										</thead>
										<tbody>
											<?php 
											$totalPPUnit = 0;
											?>
											@foreach($todayOrderPPUnit as $row) 
											<?php 
												if($row->so_item_unit == 'KG'){
													$totalPPUnit += $row->kgAmount;
												}else{
													$totalPPUnit += $row->pcsAmount;
												}
											?>
											<tr>
												<td align="right">{{ $row->so_order_no }}</td>
												<td align="right">{{ $row->qty }}</td>
												<td align="right">{{ $row->price }}</td>
												@if($row->so_item_unit == 'KG')
													<td align="right">{{ $row->kgAmount }}</td>
												@else
													<td align="right">{{ $row->pcsAmount }}</td>
												@endif
											</tr>
											@endforeach
											<tr>
												<td colspan="3" align="right"><b>Total</b></td>
												<td align="right"><b><b>{{ $totalPPUnit }}</b></td>
											</tr>
										</tbody>
									</table>
								</div><!-- /.widget-main -->
						</div><!-- /.widget-body -->
				</div>
		</div>

		<!-- today order Jute Unit -->
		<div class="col-md-6">
				<div class="widget-box transparent">
						<div class="widget-header widget-header-flat">
								<h4 class="widget-title lighter">
										<i class="ace-icon fa fa-star orange"></i>
										Today Order Jute Unit ({{date('D')}})
								</h4>

								<div class="widget-toolbar">
										<a href="#" data-action="collapse">
												<i class="ace-icon fa fa-chevron-up"></i>
										</a>
								</div>
						</div>
						<div class="widget-body" style="display: block;">
								<div class="widget-main no-padding">
								<table class="table table-theme table-data table-view table-theme v-middle" id="datatable">
										<thead class=" thead-dark">
											<th class="text-center" scope="col">Order No</th>
											<th class="text-center" scope="col">Quantity</th>
											<th class="text-center" scope="col">Price</th>
											<th class="text-center" scope="col">Amount</th>
										</thead>
										<tbody>
											<?php 
											$totalJuteUnit = 0;
											?>
											@foreach($todayOrderJutenit as $row) 
											<?php 
												if($row->so_item_unit == 'KG'){
													$totalJuteUnit += $row->kgAmount;
												}else{
													$totalJuteUnit += $row->pcsAmount;
												}
											?>
											<tr>
												<td align="right">{{ $row->so_order_no }}</td>
												<td align="right">{{ $row->qty }}</td>
												<td align="right">{{ $row->price }}</td>
												@if($row->so_item_unit == 'KG')
													<td align="right">{{ $row->kgAmount }}</td>
												@else
													<td align="right">{{ $row->pcsAmount }}</td>
												@endif
											</tr>
											@endforeach
											<tr>
												<td colspan="3" align="right"><b>Total</b></td>
											
												<td align="right"><b><b>{{ $totalJuteUnit }}</b></td>
											</tr>
										</tbody>
									</table>
								</div><!-- /.widget-main -->
						</div><!-- /.widget-body -->
				</div>
		</div>
	</div>
  
  <div class="hr hr32 hr-dotted"></div>
  	<div class="row">
	  	<!-- PP unit  -->
		<div class="col-md-6">
				<div class="widget-box transparent">
						<div class="widget-header widget-header-flat">
								<h4 class="widget-title lighter">
										<i class="ace-icon fa fa-star orange"></i>
										Monthly Sale Summary PP Unit ({{date('F')}})
								</h4>

								<div class="widget-toolbar">
										<a href="#" data-action="collapse">
												<i class="ace-icon fa fa-chevron-up"></i>
										</a>
								</div>
						</div>
						<div class="widget-body" style="display: block;">
								<div class="widget-main no-padding">
									<table class="table table-theme table-data table-view table-theme v-middle" id="datatable">
										<thead class=" thead-dark">
											<th class="text-center" scope="col">Invoice&nbsp;Date</th>
											<th class="text-center" scope="col">Total Amount</th>
											<th class="text-center" scope="col">Total Discount</th>
											<th class="text-center" scope="col">Total VAT</th>
											<th class="text-center" scope="col">Net Amount</th>
										</thead>
										<tbody>
											<?php $inv_sub_total = 0; $inv_disc_value = 0;
											$inv_vat_value = 0; $inv_net_amt = 0; ?>
											@foreach($monthlyDatewiseSalePPUnit as $row) 
											<?php $inv_sub_total += $row->inv_sub_total;
											$inv_disc_value += $row->inv_itm_disc_value + $row->inv_disc_value;
											$inv_vat_value += $row->inv_vat_value;
											$inv_net_amt += $row->inv_net_amt; ?>
											<tr>
												<td align="right">{{ date('d-m-Y', strtotime($row->inv_date)) }}</td>
												<td align="right">{{ number_format($row->inv_sub_total,2) }}</td>
												<td align="right">{{ number_format($row->inv_itm_disc_value + $row->inv_disc_value,2) }}</td>
												<td align="right">{{ number_format($row->inv_vat_value,2) }}</td>
												<td align="right">{{ number_format($row->inv_net_amt,2) }}</td>
											</tr>
											@endforeach
											<tr>
												<td align="right"><b>Total</b></td>
												<td align="right"><b>{{ number_format($inv_sub_total,2) }}</b></td>
												<td align="right"><b>{{ number_format($inv_disc_value,2) }}</b></td>
												<td align="right"><b>{{ number_format($inv_vat_value,2) }}</b></td>
												<td align="right"><b><b>{{ number_format($inv_net_amt,2) }}</b></td>
											</tr>
											</tbody>
										</table>
								</div><!-- /.widget-main -->
						</div><!-- /.widget-body -->
				</div>
		</div>

		<!-- Jute Unit -->
		<div class="col-md-6">
				<div class="widget-box transparent">
						<div class="widget-header widget-header-flat">
								<h4 class="widget-title lighter">
										<i class="ace-icon fa fa-star orange"></i>
										Monthly Sale Summary Jute Unit ({{date('F')}})
								</h4>

								<div class="widget-toolbar">
										<a href="#" data-action="collapse">
												<i class="ace-icon fa fa-chevron-up"></i>
										</a>
								</div>
						</div>
						<div class="widget-body" style="display: block;">
								<div class="widget-main no-padding">
									<table class="table table-theme table-data table-view table-theme v-middle" id="datatable">
										<thead class="thead-dark">
											<th class="text-center" scope="col">Invoice&nbsp;Date</th>
											<th class="text-center" scope="col">Total Amount</th>
											<th class="text-center" scope="col">Total Discount</th>
											<th class="text-center" scope="col">Total VAT</th>
											<th class="text-center" scope="col">Net Amount</th>
										</thead>
										<tbody>
											<?php $inv_sub_total = 0; $inv_disc_value = 0;
											$inv_vat_value = 0; $inv_net_amt = 0; ?>
											@foreach($monthlyDatewiseSaleJuteUnit as $row) 
											<?php $inv_sub_total += $row->inv_sub_total;
											$inv_disc_value += $row->inv_itm_disc_value + $row->inv_disc_value;
											$inv_vat_value += $row->inv_vat_value;
											$inv_net_amt += $row->inv_net_amt; ?>
											<tr>
												<td align="right">{{ date('d-m-Y', strtotime($row->inv_date)) }}</td>
												<td align="right">{{ number_format($row->inv_sub_total,2) }}</td>
												<td align="right">{{ number_format($row->inv_itm_disc_value + $row->inv_disc_value,2) }}</td>
												<td align="right">{{ number_format($row->inv_vat_value,2) }}</td>
												<td align="right">{{ number_format($row->inv_net_amt,2) }}</td>
											</tr>
											@endforeach
											<tr>
												<td align="right"><b>Total</b></td>
												<td align="right"><b>{{ number_format($inv_sub_total,2) }}</b></td>
												<td align="right"><b>{{ number_format($inv_disc_value,2) }}</b></td>
												<td align="right"><b>{{ number_format($inv_vat_value,2) }}</b></td>
												<td align="right"><b><b>{{ number_format($inv_net_amt,2) }}</b></td>
											</tr>
											</tbody>
										</table>
								</div><!-- /.widget-main -->
						</div><!-- /.widget-body -->
				</div>
		</div>
	</div>

<div class="row">
      <div class="col-md-6">
          <div class="widget-box transparent">
              <div class="widget-header widget-header-flat">
                  <h4 class="widget-title lighter">
                      <i class="ace-icon fa fa-star blue"></i>
                      Today's Liquid Cash/Bank Report
                  </h4>

                  <div class="widget-toolbar">
                      <a href="#" data-action="collapse">
                          <i class="ace-icon fa fa-chevron-up"></i>
                      </a>
                  </div>
              </div>

              <div class="widget-body" style="display: block;">
                  <div class="widget-main no-padding">
                      <table class="table table-data table-view">
                        <thead >
	          			  <tr class="thead-dark">
                            <th class="text-center">Account Name</th>
                            <th class="text-center" colspan="2">Balance</th>
                          </tr>
                          <tr>
                            <th class="text-center" width="55%"></th>
                            <th class="text-center" width="13%">Debit</th>
                            <th class="text-center" width="13%">Credit</th>
                          </tr>
                        </thead>
                        <tbody>
                         <?php
                          $total_op = 0; $total_bal = 0;
                          $total_tr_debit = 0; $total_tr_credit = 0;
                          $total_bal_debit = 0; $total_bal_credit = 0;
                         ?>
                          @foreach($todayLiquidCash as $row)
                          <?php
                            $op  = $row->op_debit - $row->op_credit;
                            $bal = $op + $row->tr_debit - $row->tr_credit;

                            $total_op = $total_op + $op;
                            $total_bal  = $total_bal + $bal;

                            $total_tr_debit = $total_tr_debit + $row->tr_debit;
                            $total_tr_credit = $total_tr_credit + $row->tr_credit;

                            if($bal>0) $total_bal_debit = $total_bal_debit + $bal;
                            if($bal<0) $total_bal_credit = $total_bal_credit + $bal;

                            ?>
                          <tr>
                             <td>{{ $row->acc_head }}</td>
                             <td align="right">{{ $bal > 0 ? number_format($bal,2):'0.00'}}</td>
                             <td align="right">{{ $bal < 0 ? number_format(abs($bal),2) :'0.00'}}</td>
                          </tr>

                          @endforeach
                          <tr>
                            <td align="right"><b>Total:</b></td>
                            <td align="right"><b>{{ number_format($total_bal_debit,2) }}</b></td>
                            <td align="right"><b>{{ number_format(abs($total_bal_credit),2) }}</b></td>
                          </tr>
                          </tbody>
                    </table>
                  </div><!-- /.widget-main -->
              </div><!-- /.widget-body -->
          </div>
      </div>

      <!-- div class="col-md-6">
          <div class="widget-box transparent">
              <div class="widget-header widget-header-flat">
                  <h4 class="widget-title lighter">
                      <i class="ace-icon fa fa-star blue"></i>
                      Item Report
                  </h4>

                  <div class="widget-toolbar">
                      <a href="#" data-action="collapse">
                          <i class="ace-icon fa fa-chevron-up"></i>
                      </a>
                  </div>
              </div>

              <div class="widget-body" style="display: block;">
                  <div class="widget-main no-padding">
                      <table class="table table-bordered table-striped" id="datatable">
                          <thead>
dark              <tr>
                                  <th>Item Code</th>
                                  <th>Item Name</th>
                                  <th>Item Quantity</th>
                              </tr>
                          </thead>
                          <tbody>

                          </tbody>
                      </table>
                  </div> 
              </div> 
          </div>
      </div-->
  </div>

<!-- <div class="row">
	<div class="col-md-8">
		<h4 class="widget-title lighter">
				<i class="ace-icon fa fa-star orange"></i>
				Sales Person Wise Summary ({{date('F')}})
		</h4>
		<div class="widget-body" style="display: block;">
				<div class="widget-main no-padding">
						<table class="table table-bordered table-view">
							<thead class=" thead-dark">
								<th class="text-center" scope="col">Sales Person</th>
			          <th class="text-center" scope="col">Total Sales</th>
			          <th class="text-center" scope="col">Received</th>
			          <th class="text-center" scope="col">VAT</th>
			          <th class="text-center" scope="col">Outstanding</th>
							</thead>
							<tbody>
			            <?php
			            $inv_net_amt = 0;
			            $collection = 0;
			            $inv_vat_value = 0;
			            $outstanding = 0;
			         ?>
			          @foreach($spwiseSalesSumm as $row)
			          <?php

			            $inv_net_amt += $row->inv_net_amt;
			            $collection += $row->collection;
			            $inv_vat_value += $row->inv_vat_value;
			            $outstanding += $row->outstanding;

			          ?>
			          <tr>
			            <td style=display:none;></td>
			            <td>{{ $row->sales_name }}</td>
			            <td align="right">{{ number_format($row->inv_net_amt,2) }}</td>
			            <td align="right">{{ number_format($row->collection,2) }}</td>
			            <td align="right">{{ number_format($row->inv_vat_value,2) }}</td>
			            <td align="right">{{ number_format($row->outstanding,2) }}</td>
			          </tr>
			          @endforeach
			          <tr>
			            <td colspan="1"><b>Total:</b></td>
			            <td align="right"><b>{{ number_format($inv_net_amt,2) }}</b></td>
			            <td align="right"><b>{{ number_format($collection,2) }}</b></td>
			            <td align="right"><b>{{ number_format($inv_vat_value,2) }}</b></td>
			            <td align="right"><b>{{ number_format($outstanding,2) }}</b></td>
			          </tr>
			          </tbody>
						</table>
				</div>
		</div>
	</div>
</div> -->


<div class="row">
    <div class="col-md-10">

          <div class="widget-box transparent">
              <div class="widget-header widget-header-flat">
                  <h4 class="widget-title lighter">
                      <i class="ace-icon fa fa-star orange"></i>
                      Today's Receive Payment Statement
                  </h4>

                  <div class="widget-toolbar">
                      <a href="#" data-action="collapse">
                          <i class="ace-icon fa fa-chevron-up"></i>
                      </a>
                  </div>
              </div>

              <div class="widget-body" style="display: block;">
                  <div class="widget-main no-padding">
                      <table class="table table-data table-view">
							<thead class="thead-dark">
						              <tr>
						                <th class="text-center">SL</th>
						                <th class="text-center">Head of Accounts</th>
						                <th class="text-center">Particulars</th>
						                <th class="text-center">Received</th>
						                <th class="text-center">Payment</th>
						                <th class="text-center">Balance</th>
						              </tr>
						            </thead>
												<tbody>
													<tr>
														@foreach($dailyCashSheet['openings'] as $opening)
														<?php
														$opening1 = $opening->debit - $opening->credit;
														$opening = $opening->debit - $opening->credit;
														$total_Debit = 0;   $total_Credit = 0;?>
														@endforeach
														<td colspan="5">Opening Balance :</td>
														<td width="10%" align="right">{{ number_format($opening,2) }}</td>
													</tr>
													<?php  $i=0; ?>

													@foreach($dailyCashSheet['rows_bank_rec'] as $row)
													<?php $balance = $opening + $row->c_amount - $row->d_amount;
													$total_Debit = $total_Debit + $row->d_amount;
													$total_Credit = $total_Credit + $row->c_amount; ?>
													<tr>
														<td>{{ $i +=1 }}</td>
														<td>&nbsp;{{ $row->acc_head }}</td>
														<td>{{ $row->t_narration }}</td>
														<td align="right">{{ number_format($row->c_amount,2)=='0.00'?'0.00':number_format($row->c_amount,2) }}</td>
														<td align="right">{{ number_format($row->d_amount,2)=='0.00'?'0.00':number_format($row->d_amount,2) }}</td>
														<td align="right">{{ number_format($balance,2) }}</td>
													</tr>
													<?php $opening = $balance; ?>
													@endforeach

													@foreach($dailyCashSheet['rows_cash_rec'] as $row)
													<?php
													$balance = $opening + $row->c_amount - $row->d_amount;
													$total_Debit = $total_Debit + $row->d_amount;
													$total_Credit = $total_Credit + $row->c_amount; ?>
													<tr>
														<td width="3%">{{ $i +=1 }} </td>
														<td width="25%">{{ $row->acc_head }}</td>
														<td width="42%">{{ $row->t_narration }}</td>
														<td width="10%" align="right">{{ number_format($row->c_amount,2)=='0.00'?'0.00':number_format($row->c_amount,2) }}</td>
														<td width="10%" align="right">{{ number_format($row->d_amount,2)=='0.00'?'0.00':number_format($row->d_amount,2) }}</td>
														<td width="10%" align="right">{{ number_format($balance,2) }}</td>
													</tr>
													<?php $opening = $balance; ?>
													@endforeach

													@foreach($dailyCashSheet['rows_payment'] as $row)
													<?php
													$balance = $opening + $row->c_amount - $row->d_amount;
													$total_Debit = $total_Debit + $row->d_amount;
													$total_Credit = $total_Credit + $row->c_amount; ?>
													<tr>
														<td width="3%">{{ $i +=1 }} </td>
														<td width="25%">{{ $row->acc_head }}</td>
														<td width="42%">{{ $row->t_narration }}</td>
														<td width="10%" align="right">{{ number_format($row->c_amount,2)=='0.00'?'0.00':number_format($row->c_amount,2) }}</td>
														<td width="10%" align="right">{{ number_format($row->d_amount,2)=='0.00'?'0.00':number_format($row->d_amount,2) }}</td>
														<td width="10%" align="right">{{ number_format($balance,2) }}</td>
													</tr>
													<?php $opening = $balance; ?>
													@endforeach

													<tr>
														<td colspan="3" align="right"><b>Total:</b></td>
														<td width="8%" align="right"><b>{{ number_format($total_Credit,2) }}</b></td>
														<td width="8%" align="right"><b>{{ number_format($total_Debit,2) }}</b></td>
														<td width="8%" align="right"><b></b></td>
													</tr>
													</tbody>
                      </table>
											<table class="table table-data table-view">
						            <thead class=" thead-dark">
						              <tr>
						                <th class="text-center">SL</th>
						                <th class="text-center">Head of Accounts</th>
						                <th class="text-center">Amount</th>
						                <th class="text-center">Remarks</th>
						              </tr>
						            </thead>
						            <tbody>
						              <?php $i=0; $Total_AMT=0; ?>
						              @foreach($dailyCashSheet['data'] as $d)
						              <?php
						                $amount = $d->d_amount -  $d->c_amount;
						                $Total_AMT += $amount;
						              ?>
						                @if($amount != '0')
						              <tr>
						                <td width="3%">{{ $i +=1 }} </td>
						                <td width="67%">{{ $d->acc_head }}</td>
						                <td width="10%" align="right">{{ number_format($amount,2)=='0.00'?'0.00':number_format($amount,2) }}</td>
						                <td width="10%" align="right">&nbsp;</td>
						              </tr>
						              @endif
						              @endforeach
						                <tr>
						                    <td colspan="2" align="right"><b>Total Advance:</b></td>
						                    <td align="right"><b>{{ number_format($Total_AMT,2) }}</b></td>
						                    <td align="right">&nbsp;</td>
						                </tr>
						                <tr>
						                    <td colspan="2" align="right"><b>Cash in Hand:</b></td>
						                    <!-- td align="right"><b>{{ number_format($opening + $Total_AMT,2) }}</b></td -->
						                    <td align="right"><b>{{ number_format($dailyCashSheet['CashinHand']->CashinHand,2) }}</b></td>

						                    <td align="right">&nbsp;</td>
						                </tr>
						              </tbody>
						            </table>
												<table class="table table-data table-view">
							            <tbody>
							              <tr>
							                <td width="3%">&nbsp;</td>
							                <td width="67%">Opening Balance</td>
							                <td width="10%">&nbsp;</td>
							                <td width="10%" align="right">{{ number_format($opening1,2) }}</td>
							                <td width="10%">&nbsp;</td>
							              </tr>

							              <tr>
							                <td width="3%">&nbsp;</td>
							                <td width="67%">Total Received</td>
							                <td width="10%">&nbsp;</td>
							                <td width="10%" align="right">{{ number_format($total_Credit,2) }}</td>
							                <td width="10%">&nbsp;</td>
							              </tr>
							              <?php $subtotal = $opening1 + $total_Credit; ?>
							              <tr>
							                <td width="3%">&nbsp;</td>
							                <td width="67%">Sub-Total</td>
							                <td width="10%">&nbsp;</td>
							                <td width="10%" align="right">{{ number_format($subtotal,2) }}</td>
							                <td width="10%">&nbsp;</td>
							              </tr>

							              <tr>
							                <td width="3%">&nbsp;</td>
							                <td width="67%">Expenses</td>
							                <td width="10%">&nbsp;</td>
							                <td width="10%" align="right">{{ number_format($total_Debit,2) }}</td>
							                <td width="10%">&nbsp;</td>
							              </tr>

							              <?php $balance = $subtotal - $total_Debit; ?>
							              <tr>
							                <td width="3%">&nbsp;</td>
							                <td width="67%">Balance as per cash book</td>
							                <td width="10%">&nbsp;</td>
							                <td width="10%" align="right">{{ number_format($balance,2) }}</td>
							                <td width="10%">&nbsp;</td>
							              </tr>

							              </tbody>
							            </table>
                  </div><!-- /.widget-main -->
              </div><!-- /.widget-body -->
          </div>
      </div>
  </div>



  	<!-- <div class="row">
			<div class="col-md-8">
				<div class="widget-box transparent">
						<div class="widget-header widget-header-flat">
								<h4 class="widget-title lighter">
										<i class="ace-icon fa fa-star blue"></i>
										Today's Control Wise Subsidiary Balance (Sales)
								</h4>

								<div class="widget-toolbar">
										<a href="#" data-action="collapse">
												<i class="ace-icon fa fa-chevron-up"></i>
										</a>
								</div>
						</div>

						<div class="widget-body" style="display: block;">
								<div class="widget-main no-padding">
										<table class="table table-data table-view">
											<thead>
						 						dark
							            <th class="text-center"><b>SL No</th>
							            <th class="text-center"><b>Account Name</th>
							            <th class="text-center" colspan="2"><b>Transaction</b></th>
							          </tr>
							          <tr>
							            <th class="text-center" width="5%"></th>
							            <th class="text-center" width="19%"></th>
							            <th class="text-center" width="13%"><b>Debit</b></th>
							            <th class="text-center" width="13%"><b>Credit</b></th>
							          </tr>
											</thead>
											<tbody>
							         <?php
							          $total_op = 0;   $total_bal = 0;
							          $total_tr_debit = 0;
							          $total_tr_credit = 0;
							          $i = 0;
							          $total_bal_debit = 0;
							          $total_bal_credit = 0;
							         ?>
							          @foreach($consubsidiaryLedger as $row)
							          <?php
							            $op  = $row->op_debit - $row->op_credit;
							            $bal = $op + $row->tr_debit -  $row->tr_credit;

							            $total_op = $total_op + $op;
							            $total_bal  = $total_bal + $bal;

							            $total_tr_debit = $total_tr_debit + $row->tr_debit;
							            $total_tr_credit = $total_tr_credit + $row->tr_credit;

							            if($bal>0) $total_bal_debit = $total_bal_debit + $bal;
							            if($bal<0) $total_bal_credit = $total_bal_credit + $bal;

							            ?>
							          @if($row->tr_debit > 0 || $row->tr_credit>0)
							          <tr>
							             <td align="center">{{ $i += 1 }}</td>
							             <td>{{ $row->acc_head }}</td>
							             <td align="right">{{ number_format($row->tr_debit,2)=='0.00'?'':number_format($row->tr_debit,2) }}</td>
							             <td align="right">{{ number_format($row->tr_credit,2)=='0.00'?'':number_format(abs($row->tr_credit),2) }}</td>
							          </tr>
							          @endif
							          @endforeach
							          <tr>
							            <td align="right" colspan="2"><b>Total:</b></td>
							            <td align="right"><b>{{ number_format($total_tr_debit,2) }}</b></td>
							            <td align="right"><b>{{ number_format(abs($total_tr_credit),2) }}</b></td>
							          </tr>
							          </tbody>
									</table>
								</div>
						</div>
				</div>
			</div>
	</div> -->

	<div class="row">
	<div class="col-md-6">
		<h4 class="widget-title lighter">
				<i class="ace-icon fa fa-star orange"></i>
				Warehouse Stock ({{date('F')}})
		</h4>
		<div class="widget-body" style="display: block;">
				<div class="widget-main no-padding">
						<table class="table   table-bordered table-report">
							<thead class="thead-dark">
								<th class="text-center" scope="col">Warehouse</th>
								<th class="text-center" scope="col">Current Stock</th>
								<th class="text-center" scope="col">Stock Value</th>
							</thead>
							<tbody>
							    <?php
									$BAL = 0;
									$AMOUNT = 0;
								?>
								@foreach($warehousestock as $row)
								<?php
									$BAL += $row->BAL;
									$AMOUNT += $row->AMOUNT;
								?>
								<tr>
									<td align="center">{{ $row->ware_name }}</td>
									<td align="right">{{ number_format($row->BAL,2) }}</td>
									<td align="right">{{ number_format($row->AMOUNT,2) }}</td>
								</tr>
								@endforeach
								<tr>
									<td align="center"><b>Total</b></td>
									<td align="right"><b>{{ number_format($BAL,2) }}</b></td>
									<td align="right"><b>{{ number_format($AMOUNT,2) }}</b></td>
								</tr>
								</tbody>
						</table>
				</div><!-- /.widget-main -->
		</div><!-- /.widget-body -->
	</div>
</div>
	

	</div>
</div>
</div>

</div><!-- /.container-fluid -->
</section> <!-- /.content -->

@stop
@section('pagescript')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

  <script>
    var form = document.getElementById('myform');
    document.getElementById('btn2').onclick = function() {
      form.target = '_blank';
      form.submit();
    }

    $(document).ready(()=>{
        $('#datatable').dataTable();
      //  $('#dataCustomer').dataTable();
      //  $('#dataSupplier').dataTable();
    })
  </script>


@stop
