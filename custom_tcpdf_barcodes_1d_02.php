<?php
require_once './tcpdf/tcpdf_barcodes_1d.php';

class CustomTCPDFBarcode extends TCPDFBarcode {
	public function getBarcodeCustomHTML() {
		$w=1;
		$h=38;
		$color='black';
		$html = '<div style="float:right;font-size:0;position:relative;width:'.($this->barcode_array['maxw'] * $w).'px;height:'.($h).'px;">'."\n";
		// print bars
		$x = 0;
		foreach ($this->barcode_array['bcode'] as $k => $v) {
			$bw = round(($v['w'] * $w), 3);
			$bh = round(($v['h'] * $h / $this->barcode_array['maxh']), 3);
			if ($v['t']) {
				$y = round(($v['p'] * $h / $this->barcode_array['maxh']), 3);
				// draw a vertical bar
				$html .= '<div class="barcode'.$bw.'px" style="left:'.$x.'px;top:'.$y.'px;">&nbsp;</div>'."\n";
			}
			$x += $bw;
		}
		$html .= '</div>'."\n";
		return $html;
	}
}
