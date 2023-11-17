<!doctype html>
<html lang="en">
<head>
<title>Items Barcodes</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
	<style type="text/css">
<!- CSS adapted from article: boulderinformationservices.wordpress.com/2011/08/25/print-avery-labels-using-css-and-html/ ->
		body {
			margin-top: 0in;
			margin-left: 0in;
		}
		.page {
			width: 8.5in;
			height: 10.5in;
			margin-top: 0.5in;
			margin-left: 0.25in;
		}
    .label {
			width: 2.1in;
			height: .9in;
			padding: .125in .3in 0;
			margin-right: 0.125in;
			float: left;
			text-align: center;
			overflow: hidden;
		}
    .page-break {
			clear: left;
			display:block;
			page-break-after:always;
		}
	</style>
</head>
<body>
<div width = 100%>
@php($qty = request('qty'))
@php($j = 5)
  <table class="no-spacing" cellspacing="0">
  <tr>
  @for ($i = 1; $i <= $qty; $i++)
    <td>
      {{$item->item_name}}<br/>({{$item->itm_cat_name}})
      {!! DNS1D::getBarcodeHTML($item->item_bar_code, "C128",1.4,22) !!}
      {{$item->item_bar_code}}
    </td><td>&nbsp;<td>
    @if($i == $j)
    <br/><br/><tr><td></tr><tr>
    @php($j = $i+5)
    @endif
  @endfor
  </tr>
  </table>
</div>
</body></html>
