<html>
    <head>
      <style>
       body { margin: 0; font-size: 12px; font-family: "Arrial Narrow";}
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
               bottom: 25px;
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
                    <font size="5">House 5(1st Floor), Road 17/A, Sector:12, Uttara Model Town,
                    <br/>Dhaka-1230, Bangladesh, Phone: 0088 02 55086938 / 55086939<br/>
                    Phone : 01313-772676<br/>
                    Email: jistlifecare@gmail.com</font></p></td>
                  <td valign="middle" align="right"><font size="7"><b>Purchase Order</b></font></td>
                </tr>
              </table>
            </div>
          </div>

        </header>

        <footer>
          <div class="row">
            <div class="col-sm-12">
              <table border="0" cellspacing="0" cellpadding="0"
              style='font-family:"Arrial Narrow", Courier, monospace; font-size:10px'>

                  <tr>
                      <td valign="top" align="left"><img src="dist/img/ceo_signature.jpg" width="20%"/></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td height="40%">&nbsp;</td>
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
                      <td width="15%"><b>Date:</b></td><td>{{ date('d-m-Y', strtotime($rows_m->po_order_date)) }}</td>
                      <td width="15%"><b>PO&nbsp;No:</b></td><td>{{ $rows_m->po_order_no }}</td>
                  </tr>
                  <tr>
                    <td width="15%"><b>Supplier:</b></td><td>{{ $rows_m->supp_name }}</td>
                    <td width="15%"><b>PI&nbsp;No:</b></td><td>{{ $rows_m->po_pi_no }}</td>
                  </tr>
                  <tr>
                    <td width="15%"><b>Phone:</b></td><td>{{ $rows_m->supp_mobile }}</td>
                    @if($rows_m->po_type != '0')
                      <td width="15%"><b>{{ $rows_m->po_m_curr }} to BDT conversion:</b></td><td>{{ $rows_m->po_m_curr_rate }} ({{ $rows_m->po_m_curr }})</td>
                    @endif
                  </tr>
                  <tr>
                    <td width="15%"><b>Address:</b></td><td colspan="3">{{ $rows_m->supp_add1 }} {{ $rows_m->supp_add2 }}</td>
                  </tr>

                </table><br/>

                <table class="table table-bordered" border="1">
                 <tr>
                   <td align="center"><b>SL</b></td>
                   <td align="center"><b>Item&nbsp;Name</b></td>
                   <td align="center"><b>Qty</b></td>
                   <td align="center"><b>Price ({{ $rows_m->po_m_curr }})</b></td>
                   <td align="center"><b>Total ({{ $rows_m->po_m_curr }})</b></td>
                   <td align="center"><b>Remarks</b></td>
                </tr>
                  <?php $i=0; ?>
                  @foreach($rows_d as $p_details)
                  <?php
                    $po_amount = $p_details->po_item_qty*$p_details->po_item_price;
                    $po_amount_bdt = $p_details->po_item_qty*$p_details->po_item_price;
                  ?>
                  <tr>
                    <td align="center">{{ $i += 1 }}</td>
                    <td>{{ $p_details->item_name }}({{ $p_details->itm_cat_name }})</td>
                    <td align="right">{{ $p_details->po_item_qty }}</td>
                    <td align="right">{{ number_format($p_details->po_item_price, 2) }}</td>
                    <td align="right">{{ number_format($po_amount, 2) }}</td>
                    <td>{{ $p_details->po_item_remarks }}</td>
                  </tr>
                  @endforeach
                  <tr>
                    <td align="center" colspan="2">&nbsp;</td>
                    <td align="right"><b>{{ $rows_m->po_total_qty }}</b></td>
                    <td align="right">&nbsp;</td>   
                    <td align="right"><b>{{ number_format($rows_m->po_total_amount, 2) }}</b></td>
                    <td align="right">&nbsp;</td>
                  </tr>
                </table>
              </div>
            </div><br/>
             <div class="row justify-content-left">
               <div class="col-md-12">
                 {!! $rows_m->po_comments !!}
               </div>
            </div>
            </p>
        </main>
    </body>
</html>
