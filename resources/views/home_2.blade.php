@extends('layouts.app')
@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" />

<style>.widget-title, .widget-title a {
	color: #D0C2B2!important; font-weight: 800;
}</style>
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
 
<div class="row">
    <div class="col-md-10">

          <div class="widget-box transparent">
              <div class="widget-header widget-header-flat">
                  <h4 class="widget-title lighter">
                      <i class="ace-icon fa fa-star orange"></i>
                      Today's Cash Statement
                  </h4>

                  <div class="widget-toolbar">
                      <a href="#" data-action="collapse">
                          <i class="ace-icon fa fa-chevron-up"></i>
                      </a>
                  </div>
              </div>

              <div class="widget-body" style="display: block;">
                  <div class="widget-main no-padding">
                      <table class="table table-responsive table-data table-view">
							<thead class="thead-light">
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
					<table class="table table-responsive table-data table-view">
						            <thead class="thead-light">
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
							<table class="table table-responsive table-data table-view">
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
