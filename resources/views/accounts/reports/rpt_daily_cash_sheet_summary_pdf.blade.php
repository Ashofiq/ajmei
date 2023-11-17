<!DOCTYPE html>
<html lang="en" >
<head>
    <title>Daily Cash Statement</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
       <style>
        body { margin: 0; font-size: 8px; font-family: "Arrial Narrow";}
            /** Define the margins of your page **/
            @page {
                margin: 100px 55px;
            }

            header {
                position: fixed;
                top: -45px;
                left: 0px;
                right: 0px;
                height: 60px;
                /** Extra personal styles **/

                color: white;
                text-align: center;
                line-height: 35px;
            }

            footer {
                position: fixed;
                bottom: -30px;
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
              <table>
                <thead>
                  <tr><th align="center" colspan="5"><font size="6"><b>{{$comp_name}}</b><font></th></tr>
                  <tr><th align="center" colspan="5"><b>Daily Cash Statement Summary</b></th></tr>
                  <tr><th align="center" colspan="5"><font size="3"><b>Date:&nbsp;{{ date('d-m-Y', strtotime($fromdate)) }}</b><font></th></tr>
                </thead>
              </table>
            </div>
          </div>
        </header>
        <!-- Wrap the content of your PDF inside a main tag -->
        <main>
            <p>
              <div class="row justify-content-center">
                <div class="col-md-12">
                <table class="table table-bordered" border="1"  width="100%">
                 <tr>
                   <td width="3%" align="center"><b>SL</b></td>
                   <td width="55%" align="center"><b>Head&nbsp;of&nbsp;Accounts</b></td>
                   <td width="13%" align="center"><b>Received</b></td>
                   <td width="13%" align="center"><b>Payment</b></td>
                   <td width="13%" align="center"><b>Balance</b></td>
                 </tr>
                 <tr>
                   @foreach($openings as $opening)
                   <?php $i=0;
                     $balance = 0;
                     $opening1 =  $opening->debit - $opening->credit;
                     $opening =  $opening->debit - $opening->credit;
                     $total_Debit = 0;   $total_Credit = 0;
                   ?>
                   @endforeach
                   <td colspan="4"><b>Opening Balance :</b></td>
                   <td width="10%" align="right">&nbsp;{{ number_format($opening,2) }}</td>
                 </tr>

                 @foreach($rows_bank_rec as $row)
                   <?php $balance = $opening + $row->c_amount - $row->d_amount;
                   $total_Debit = $total_Debit + $row->d_amount;
                   $total_Credit = $total_Credit + $row->c_amount; ?>
                   <tr>
                     <td>{{ $i +=1 }}</td>
                     <td>&nbsp;{{ $row->acc_head }}</td>
                     <td align="right">{{ number_format($row->c_amount,2)=='0.00'?'0.00':number_format($row->c_amount,2) }}</td>
                     <td align="right">{{ number_format($row->d_amount,2)=='0.00'?'0.00':number_format($row->d_amount,2) }}</td>
                     <td align="right">{{ number_format($balance,2) }}</td>
                   </tr>
                   <?php $opening = $balance; ?>
                 @endforeach

                 @foreach($rows_cash_rec as $row)
                   <?php $balance = $opening + $row->c_amount - $row->d_amount;
                   $total_Debit = $total_Debit + $row->d_amount;
                   $total_Credit = $total_Credit + $row->c_amount; ?>
                   <tr>
                     <td>{{ $i +=1 }}</td>
                     <td>&nbsp;{{ $row->acc_head }}</td>
                     <td align="right">{{ number_format($row->c_amount,2)=='0.00'?'0.00':number_format($row->c_amount,2) }}</td>
                     <td align="right">{{ number_format($row->d_amount,2)=='0.00'?'0.00':number_format($row->d_amount,2) }}</td>
                     <td align="right">{{ number_format($balance,2) }}</td>
                   </tr>
                   <?php $opening = $balance; ?>
                 @endforeach

                 @foreach($rows_payment as $row)
                   <?php $balance = $opening + $row->c_amount - $row->d_amount;
                   $total_Debit = $total_Debit + $row->d_amount;
                   $total_Credit = $total_Credit + $row->c_amount; ?>
                   <tr>
                     <td>{{ $i +=1 }}</td>
                     <td>&nbsp;{{ $row->acc_head }}</td>
                     <td align="right">{{ number_format($row->c_amount,2)=='0.00'?'0.00':number_format($row->c_amount,2) }}</td>
                     <td align="right">{{ number_format($row->d_amount,2)=='0.00'?'0.00':number_format($row->d_amount,2) }}</td>
                     <td align="right">{{ number_format($balance,2) }}</td>
                   </tr>
                   <?php $opening = $balance; ?>
                 @endforeach

               <tr>
                 <td colspan="2" align="right"><b>Total:&nbsp;&nbsp;</b></td>
                 <td align="right"><b>&nbsp;&nbsp;{{ number_format($total_Credit,2) }}</b></td>
                 <td align="right"><b>&nbsp;&nbsp;{{ number_format($total_Debit,2) }}</b></td>
                 <td align="right"><b>&nbsp;&nbsp;</b></td>
               </tr>

            <tr><td style="border-left:1px solid white; border-right:1px solid white" colspan="5">&nbsp;</td></tr>


             <tr>
               <td align="center"><b>SL</b></td>
               <td align="center"><b>Head&nbsp;of&nbsp;Accounts</b></td>
               <td align="center"><b>Amount</b></td>
               <td align="center"><b>Remarks</b></td>
             </tr>

             <?php $i=0; $Total_AMT=0; ?>
             @foreach($data as $d)
               <?php
                   $amount = $d->d_amount -  $d->c_amount;
                   $Total_AMT += $amount;
               ?>
               @if($amount != '0')
                   <tr>
                       <td>{{ $i +=1 }} </td>
                       <td>{{ $d->acc_head }}</td>
                       <td align="right">&nbsp;</td>
                       <td align="right">{{ number_format($amount,2)=='0.00'?'0.00':number_format($amount,2) }}</td>
                       <td align="right">&nbsp;</td>
                   </tr>
               @endif
             @endforeach
               <tr>
                   <td colspan="3" align="right"><b>Total Advance:</b></td>
                   <td align="right"><b>{{ number_format($Total_AMT,2) }}</b></td>
                   <td align="right">&nbsp;</td>
               </tr>
               <tr>
                   <td colspan="3" align="right"><b>Cash in Hand:</b></td>
                   <td align="right"><b>{{ number_format($CashinHand->CashinHand,2) }}</b></td>
                   <td align="right">&nbsp;</td>
               </tr>
               <tr>
                 <td style="border-left:1px solid white; border-right:1px solid white;
                 border-bottom:1px solid white;" colspan="1">&nbsp;</td>
                 <td style="border-left:1px solid white; border-right:1px solid white;
                 border-left:1px solid white;" colspan="4">&nbsp;</td></tr>

               <tr>
                 <td style="border-left:0px solid white;border-bottom:0px solid white;border-top:0px solid white;"></td>
                 <td><b>Opening Balance</b></td>
                 <td>&nbsp;</td>
                 <td align="right">{{ number_format($opening1,2) }}</td>
                 <td>&nbsp;</td>
               </tr>
               <tr>
                 <td style="border-left:0px solid white;border-bottom:0px solid white;border-top:0px solid white;"></td>
                 <td><b>Total Received</b></td>
                 <td>&nbsp;</td>
                 <td align="right">{{ number_format($total_Credit,2) }}</td>
                 <td>&nbsp;</td>
               </tr>
               <?php $subtotal = $opening1 + $total_Credit; ?>
               <tr>
                 <td style="border-left:0px solid white;border-bottom:0px solid white;border-top:0px solid white;"></td>
                 <td><b>Sub-Total</b></td>
                 <td>&nbsp;</td>
                 <td align="right">{{ number_format($subtotal,2) }}</td>
                 <td>&nbsp;</td>
               </tr>

               <tr>
                 <td style="border-left:0px solid white;border-bottom:0px solid white;border-top:0px solid white;"></td>
                 <td><b>Expenses</b></td>
                 <td>&nbsp;</td>
                 <td align="right">{{ number_format($total_Debit,2) }}</td>
                 <td>&nbsp;</td>
               </tr>

               <?php $balance = $subtotal - $total_Debit; ?>
               <tr>
                 <td style="border-left:0px solid white;border-bottom:0px solid white;border-top:0px solid white;"></td>
                 <td><b>Balance as per cash book</b></td>
                 <td>&nbsp;</td>
                 <td align="right">{{ number_format($balance,2) }}</td>
                 <td>&nbsp;</td>
               </tr>
                </table>
              </div>
             </div>
            </p>
        </main>

        <footer>
          <div class="row">
            <div class="col-sm-12">
              <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td style="border-right:1px solid white;border-left:1px solid white;border-bottom:1px solid white;text-align:center;">Accounts  Office</td>
                  <td style="border-right:1px solid white;border-left:1px solid white;border-bottom:1px solid white;text-align:center;">Senior Accounts</td>
                  <td style="border-right:1px solid white;border-left:1px solid white;border-bottom:1px solid white;text-align:center;">CEO</td>
                  <td style="border-right:1px solid white;border-left:1px solid white;border-bottom:1px solid white;text-align:center;">Managing Director</td>
                </tr>
              </table>
            </div>
          </div>
        </footer>
    </body>
</html>
