 <style> 
 
 .error {
			border : red 2px solid;
		}
	.uk-button-link {
		color: #1e87f0;
	}
table .smp-row-data {
  border-top: 1px solid #e5e5e5;
}
table .smp-row-data{
  border-bottom: 1px solid #e5e5e5;
}
.uk-input[type="number"]{
	text-align: right;
}
table.uk-table-divider tr:last-of-type{
  border-bottom: 1px solid #e5e5e5;
}
table#tbl-Items tr td{
	padding: 12px 0px 12px 12px !important;
}
.uk-navbar-container{
	border-bottom : solid 2px #98CB00;
}
.bk-application-color{
	background : #98CB00;
}

table td.active{
    background-color: #AACC44 !important;
}
table td{
	vertical-align: middle !important;
}
table.uk-table td, table.uk-table th{
	font-size: 1.0em;
}

.uk-navbar-container{
	border-bottom : solid 2px #98CB00;
}
.bk-application-color{
	background : #98CB00;
}

.uk-table th{
	text-transform: none !important;
}

.resultarea{
	display:none;
}
/*
 * Primary
 */
.uk-button-primary {
  background-color: #7AAE36;
  color: #fff;
  border: 1px solid transparent;
}
.uk-text-primary {
  color: #7AAE36 !important;
}
/* Hover + Focus */
.uk-button-primary:hover,
.uk-button-primary:focus {
  background-color: #93BD5B;
  color: #fff;
}
.uk-button-primary:disabled:hover,
.uk-button-primary:disabled:focus {
  background-color: transparent;
  color: #999;
}
/* OnClick + Active */
.uk-button-primary:active,
.uk-button-primary.uk-active {
  background-color: #B2D08B;
  color: #fff;
}
/*
 * Success
 */
.uk-label-success {
  background-color: #7AAE36;
  color: #fff;
}

.uk-table tr:last-child{
	border-bottom: 1px solid #e5e5e5;
}

.uk-table tr.tr-gray{
  background: #e5e5e5;
}

.title_spacing {
	letter-spacing : 1em;
	text-indent: 1em;
}

.jp-min-width-150 {
  min-width : 150px;
}
.animsition {
	opacity: 0;
}

/* -----------------------------
Switch */

.uk-switch {
  position: relative;
  display: inline-block;
  height: 34px;
  width: 60px;
}

/* Hide default HTML checkbox */
.uk-switch input {
  display:none;
}
/* Slider */
.uk-switch-slider {
  background-color: rgba(0,0,0,0.22);
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  border-radius: 500px;
  bottom: 0;
  cursor: pointer;
  transition-property: background-color;
	transition-duration: .2s;
  box-shadow: inset 0 0 2px rgba(0,0,0,0.07);
}
/* Switch pointer */
.uk-switch-slider:before {
  content: '';
  background-color: #fff;
  position: absolute;
  width: 30px;
  height: 30px;
  left: 2px;
  bottom: 2px;
  border-radius: 50%;
  transition-property: transform, box-shadow;
	transition-duration: .2s;
}
/* Slider active color */
input:checked + .uk-switch-slider {
  background-color: #7AAE36 !important;
}
/* Pointer active animation */
input:checked + .uk-switch-slider:before {
  transform: translateX(26px);
}

/* Modifiers */
.uk-switch-slider.uk-switch-on-off {
  background-color: #f0506e;
}
input:checked + .uk-switch-slider.uk-switch-on-off {
  background-color: #32d296 !important;
}

/* Style Modifier */
.uk-switch-slider.uk-switch-big:before {
  transform: scale(1.2);
  box-shadow: 0 0 6px rgba(0,0,0,0.22);
}
.uk-switch-slider.uk-switch-small:before {
  box-shadow: 0 0 6px rgba(0,0,0,0.22);
}
input:checked + .uk-switch-slider.uk-switch-big:before {
  transform: translateX(26px) scale(1.2);
}

/* Inverse Modifier - affects only default */
.uk-light .uk-switch-slider:not(.uk-switch-on-off) {
  background-color: rgba(255,255,255,0.22);
}

.change {
  background-color: rgb(255, 204, 153);
  color: rgb(68, 68, 68)
}
</style>