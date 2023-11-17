<!DOCTYPE html>
<html lang="en" >
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
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
                <tr><td width="15%" valign="top" align="left"><img src="dist/img/jist_logo.jpeg"/></td>
                  <td valign="middle"><font size="7"><b><p>
                    Phone : 01313-772676<br/>
                    Email: jistlifecare@gmail.com</font></p></b></td>
                  <td valign="middle" align="right"><font size="7">&nbsp;</td>
                </tr>
              </table>
            </div>
          </div>
        </header>

        <!-- Wrap the content of your PDF inside a main tag -->
        <main>
            <p>
              <div class="row justify-content-center">
                <div class="col-md-12">

                  <table width="100%">
                    @foreach($rows_m as $m)
                    <thead>
                      <tr><td align="left" colspan="4">Ref:&nbsp;{{$m->quot_ref_no}}</td></tr>
                      <tr><td align="left" colspan="4">Date:&nbsp;{{ date('d/m/Y', strtotime($m->quot_date)) }}</b><font></td></tr>
                      <tr><td align="left" colspan="4">&nbsp;</td></tr>
                      <tr><td align="left" colspan="4">{{$m->quot_to}}</td></tr>
                      <tr><td align="left" colspan="4">{{$m->quot_writ_to}}</td></tr>
                      <tr><td align="left" colspan="4">{{$m->quot_cust_name}}</td></tr>
                      <tr><td align="left" colspan="4">{{$m->quot_cust_add}}</td></tr>
                      <tr><td align="left" colspan="4">&nbsp;</td></tr>
                      <tr><td align="left" colspan="4">{!! $m->quot_subj !!}</td></tr>
                      <tr><td align="left" colspan="4">&nbsp;</td></tr>
                      <tr><td align="left" colspan="4">{!! $m->quot_body !!}</td></tr>
                    </thead>
                    <?php $quot_term_cond = $m->quot_term_cond; ?>
                    @endforeach
                  </table>
              <table width="100%" border="1" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
              <tbody>
                  <?php $tag = 0; $i=1;?>
                  @foreach($rows_d as $d)
                  @if($tag == 0 || $tag != $d->quot_cate_id)
                  <tr><td align="left" colspan="4">&nbsp;</td></tr>
                  <tr>
                    <td align="center"><b>Sl No</b></td>
                    <td align="center"><b>Description of Equipment</b></td>
                    <td align="center"><b>Qty</b></td>
                    <td align="center"><b>Amount BDT.</b></td>
                  </tr>
                  <tr>
                    <td align="center">0{{$i++}}</td>
                    <td align="center">{!! $d->itm_cat_name !!}</td>
                    <td align="center">{!! $d->quot_qty !!}</td>
                    <td align="center">{!! $d->quot_amount !!}</td>
                  </tr>
                  <tr><td colspan="4">{!! $d->quot_note !!}</td></tr> 
                  @if($d->item_name != '')
                  <tr><td colspan="4">&nbsp;</td></tr>
                  <tr><td colspan="4"><b>Reagent Price:</b></td></tr>
                  <tr>
                    <td align="center"><b>Sl No</b></td>
                    <td align="center"><b>Parameter</b></td>
                    <td align="center" colspan="2"><b>Price (Taka) /Test</b></td>
                    <!-- td align="center"><b>Price/Kit (25Test)</b></td -->
                  </tr><br/>
                  @endif
                  <?php $tag = $d->quot_cate_id; $j=1;?>
                  @endif
                  @if($d->item_name != '')
                  <tr>
                    <td align="center">{{$j++}}</td>
                    <td align="center">{!! $d->item_name !!}</td>
                    <td align="center" colspan="2">{!! $d->quot_test_price !!}</td>
                    <!--td align="center">{!! $d->quot_kit_price !!}</td -->
                  </tr>
                  @endif
                  @endforeach
                  <tr><td colspan="4">&nbsp;</td></tr>
                  <tr><td align="left" colspan="4"><b>Terms & Conditions:</b></td></tr>
                  <tr><td colspan="4">{!! $quot_term_cond !!}</td></tr>
                  </tbody>
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
                    <td valign="middle"><p>
                    <font size="5">House 5(1st Floor), Road 17/A, Sector:12, Uttara Model Town,
                    <br/>Dhaka-1230, Bangladesh, Phone: 0088 02 55086938 / 55086939</font></p></td>
                  </tr>

              </table>
            </div>
          </div>
        </footer>
    </body>
</html>
