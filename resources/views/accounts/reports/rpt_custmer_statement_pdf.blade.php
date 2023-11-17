<!DOCTYPE html>
<html lang="en" >
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
       <style>
        body { margin: 0; font-size: 18px; font-family: "Arrial Narrow";}
            /** Define the margins of your page **/
            @page {
                margin: 100px 55px;
            }

            header {
                position: fixed;
                top: -75px;
                left: 0px;
                right: 0px;
                height: 60px;
                /** Extra personal styles **/

                color: white;
                text-align: center;
                line-height: 65px;
            }

            footer {
                position: fixed;
                bottom: -75px;
                text-align: center;
            }

            table {
              width: 100%;
              border-collapse: collapse;
            }
            h1 {
              border-bottom-style: solid;
            }
        </style>
    </head>
    <body>
        <!-- Define header and footer blocks before your content -->
        <header>
          <div class="row justify-content-center">
            <div class="col-md-12">
              <table border="0" cellspacing="0" cellpadding="0">
                <tr><td width="14%" valign="top" align="left"><img src="dist/img/jist_logo.jpeg"/></td>
                  <td valign="middle"><font size="6"><b><p>&nbsp;</b></font><br/>
                    <font size="4">House 5(1st Floor), Road 17/A, Sector:12, Uttara Model Town,
                    <br/>Dhaka-1230, Bangladesh, Phone: 0088 02 55086938 / 55086939<br/>
                    Phone : 01313-772676<br/>
                    Email: jistlifecare@gmail.com</font></p></td>
                  <td valign="middle" align="right"><font size="4"><b>Customer Statement <br/> Date:
                  {{date('d-m-Y',strtotime($fromdate))}} To {{date('d-m-Y',strtotime($todate))}}</b></font></td>
                </tr>
              </table>
            </div>
          </div>
        </header>

        <footer>
          <div class="row">
            <div class="col-sm-12">
              <table border="0" cellspacing="0" cellpadding="0">
                  <tr>
                      <td align="right">{PAGENO}</td>
                  </tr>

              </table>
            </div>
          </div>
        </footer>
        <!-- Wrap the content of your PDF inside a main tag -->
        <main>
            <p>
              <div class="row justify-content-center">
                <div class="col-md-12">

                <table class="table table-bordered" border="1">
                  <tr>
                    <td align="left"><b>Name:</b></td>
                    <td align="left" colspan="5"><b>{{$cust_data->cust_name}}</b></td>
                </tr>
                <tr>
                    <td align="left"><b>Address:</b></td>
                    <td align="left" colspan="5"><b>{{$cust_data->cust_add1}} {{$cust_data->cust_add2}}</b></td>
                 </tr>
                 <tr>
                   <td width="12%" align="center"><b>Date</b></td>
                   <td width="12%" align="center"><b>Invoice&nbsp;No</b></td>
                   <td width="55%" align="left"><b>Description</b></td>
                   <td width="10%" align="center"><b>Debit</b></td>
                   <td width="10%" align="center"><b>Credit</b></td>
                   <td width="10%" align="center"><b>Balance</b></td>
                </tr>
                <tr>
                   <?php
                   $opening = $opening->debit - $opening->credit;
                   $total_Debit = 0;   $total_Credit = 0;
                   ?>
                   <td colspan="5"><b>Opening Balance :</b></td>
                   <td width="10%" align="right">&nbsp;{{ number_format($opening,2) }}</td>
                </tr>
                 @foreach($rows as $row)
                   <?php $balance = $opening + $row->d_amount - $row->c_amount;
                   $total_Debit = $total_Debit + $row->d_amount;
                   $total_Credit = $total_Credit + $row->c_amount; ?>
                   <tr>
                     <td>{{ date('d-m-Y', strtotime($row->voucher_date)) }}&nbsp;&nbsp;</td>
                     <td>&nbsp;{{ $row->trans_type }}-{{ $row->voucher_no }}</td>
                     <td>{{ $row->trans_type == 'SV'? 'Invoice No-':''}}{{$row->acc_invoice_no}}:<br/>{!! $row->t_narration !!}</n></td>
                     <td align="right">{{ number_format($row->d_amount,2)=='0.00'?'':number_format($row->d_amount,2) }}</td>
                     <td align="right">{{ number_format($row->c_amount,2)=='0.00'?'':number_format($row->c_amount,2) }}</td>
                     <td align="right">{{ number_format($balance,2) }}</td>
                   </tr>
                   <?php $opening = $balance; ?>
                 @endforeach
                   <tr>
                     <td colspan="3" align="right"><b>Total:&nbsp;&nbsp;</b></td>
                     <td align="right"><b>&nbsp;&nbsp;{{ number_format($total_Debit,2) }}</b></td>
                     <td align="right"><b>&nbsp;&nbsp;{{ number_format($total_Credit,2) }}</b></td>
                     <td align="right"><b>&nbsp;&nbsp;</b></td>
                   </tr>
                </table>
              </div>
             </div>
            </p>
        </main>
    </body>
</html>
