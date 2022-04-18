<style>

#detail-sheet {
	display: none;
}

@page {
  size: A4 portrait; /* 横の場合はlandscape */
  margin: 5mm 10mm;
}

@media print{
	.no_print {
		display: none;
	}
		
	body {
    	width: 190mm; /* needed for Chrome */
		-webkit-print-color-adjust: exact;
		color-adjust: exact;
		line-height: 1.5em;
	}

	#detail-sheet p {
		line-height: normal;
	}

	/* 印刷1ページ用のコンテンツはここで定義 */
	#detail-sheet {
		width: 210mm; /* 用紙の横幅を改めて指定 */
		page-break-after: always;
		box-sizing: border-box;
		font-size: 11pt;
	}

	#detail-sheet {
		display: block !important;
	}
	#detail-sheet .sheet {
		page-break-after: always;
	}

	#detail-sheet .print-text-xsmall {
		font-size: 6pt;
	}
	#detail-sheet .print-text-small {
		font-size: 8pt;
	}
	#detail-sheet .print-text-default {
		font-size: 11pt;
	}
	#detail-sheet .print-text-large {
		font-size: 13pt;
	}
	#detail-sheet .print-text-xlarge {
		font-size: 20pt;
	}
	#detail-sheet .print-table {
		border-collapse: collapse;
		border-spacing: 0;
		width : 100%;
		margin-top: 10pt;
	}
	#detail-sheet .print-table tr {
		border-spacing: 0;
	}
	#detail-sheet .print-table th, 
	#detail-sheet .print-table td {
		border: 1.2px gray solid;
		border-spacing: 0;
		font-size: 6pt;
		padding : 2pt;
	}
}
</style>