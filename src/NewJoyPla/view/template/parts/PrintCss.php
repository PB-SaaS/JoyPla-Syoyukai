<style>
@media print{ 
	.print-width-1-1{
		width:100% !important;
	}
	table.uk-table-responsive{
	    display: table;
	} 
	table.uk-table-responsive tbody{
	    display: table-row-group;
	} 
	table.uk-table-responsive td, 
	table.uk-table-responsive th{
	    display: table-cell;
		padding: 16px 12px !important;
	} 
	table.uk-table-responsive .uk-table-link:not(:last-child)>a, 
	table.uk-table-responsive td:not(:last-child):not(.uk-table-link), 
	table.uk-table-responsive th:not(:last-child):not(.uk-table-link){
		padding: 16px 12px !important;
	}
	table.uk-table-responsive .uk-table-link:not(:first-child)>a, 
	table.uk-table-responsive td:not(:first-child):not(.uk-table-link), 
	table.uk-table-responsive th:not(:first-child):not(.uk-table-link){
		padding: 16px 12px !important;
	}

	table.uk-table-responsive tr {
	    display: table-row;
	}

	.uk-text-nowrap{
		white-space: normal;
		/*white-space:break-spaces !important;*/
	}
	.uk-table th{
		white-space: nowrap !important;
	}
	.no_print{
        display: none;
    }
	.printarea{
        page-break-after: always;
        font-size: 12px;
    }
    body{
    	zoom: 60%; /* Equal to scaleX(0.7) scaleY(0.7) */
    }
    
	/* Single Widths
	 ========================================================================== */
	/*
	 * 1. `max-width` is needed for the pixel-based core
	 */
	[class*='uk-width'] {
	  box-sizing: border-box;
	  width: 100%;
	  /* 1 */
	  max-width: 100%;
	}
	/* Halves */
	.uk-width-1-2,
	.uk-width-1-2\@s,
	.uk-width-1-2\@m,
	.uk-width-1-2\@l{
	  width: 50%;
	}
	/* Thirds */
	.uk-width-1-3,
	.uk-width-1-3\@s,
	.uk-width-1-3\@m,
	.uk-width-1-3\@l {
	  width: calc(100% * 1 / 3.001);
	}
	.uk-width-2-3,
	.uk-width-2-3\@s,
	.uk-width-2-3\@m,
	.uk-width-2-3\@l {
	  width: calc(100% * 2 / 3.001);
	}
	/* Quarters */
	.uk-width-1-4,
	.uk-width-1-4\@s,
	.uk-width-1-4\@m,
	.uk-width-1-4\@l {
	  width: 25%;
	}
	.uk-width-3-4,
	.uk-width-3-4\@s,
	.uk-width-3-4\@m,
	.uk-width-3-4\@l {
	  width: 75%;
	}
	/* Fifths */
	.uk-width-1-5,
	.uk-width-1-5\@s,
	.uk-width-1-5\@m,
	.uk-width-1-5\@l {
	  width: 20%;
	}
	.uk-width-2-5,
	.uk-width-2-5\@s,
	.uk-width-2-5\@m,
	.uk-width-2-5\@l {
	  width: 40%;
	}
	.uk-width-3-5,
	.uk-width-3-5\@s,
	.uk-width-3-5\@m,
	.uk-width-3-5\@l {
	  width: 60%;
	}
	.uk-width-4-5,
	.uk-width-4-5\@s,
	.uk-width-4-5\@m,
	.uk-width-4-5\@l {
	  width: 80%;
	}
	/* Sixths */
	.uk-width-1-6,
	.uk-width-1-6\@s,
	.uk-width-1-6\@m,
	.uk-width-1-6\@l {
	  width: calc(100% * 1 / 6.001);
	}
	.uk-width-5-6,
	.uk-width-5-6\@s,
	.uk-width-5-6\@m,
	.uk-width-5-6\@l {
	  width: calc(100% * 5 / 6.001);
	}
	/* Pixel */
	.uk-width-small {
	  width: 150px;
	}
	.uk-width-medium {
	  width: 300px;
	}
	.uk-width-large {
	  width: 450px;
	}
	.uk-width-xlarge {
	  width: 600px;
	}
	.uk-width-2xlarge {
	  width: 750px;
	}
	/* Auto */
	.uk-width-auto {
	  width: auto;
	}
	/* Expand */
	.uk-width-expand {
	  flex: 1;
	  min-width: 1px;
	} 
	.uk-table .fix .uk-button, .uk-table .no_fix .uk-button, .uk-table .labelCreate .uk-button{
		display: none;
	}
}
</style>