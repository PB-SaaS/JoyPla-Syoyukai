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
		line-height: 1.5em;
	}

	#detail-sheet p {
		line-height: normal;
	}

	/* 印刷1ページ用のコンテンツはここで定義 */
	#detail-sheet {
		box-sizing: border-box;
		font-size: 11pt;
		display: block !important;
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
		font-size: 6pt !important;
		padding : 2pt;
	}
	*+.uk-grid-margin, .uk-grid+.uk-grid, .uk-grid>.uk-grid-margin {
		margin-top: 0px !important;
	}
}
</style>