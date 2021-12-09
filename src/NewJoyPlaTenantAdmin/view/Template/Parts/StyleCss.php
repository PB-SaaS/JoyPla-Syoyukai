<style>
    
    .uk-label-success {
      background-color: #7AAE36;
      color: #fff;
    }

    /*
     * Primary
     */
    .uk-button-primary {
      background-color: #7AAE36;
      color: #fff;
      border: 1px solid transparent;
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
    
    .uk-tab > .uk-active > a {
        color: #333;
        border-color: #7AAE36;
    }
    
    
    h1 {
        /*margin: 0 !important;*/
    }
    .uk-input[type="number"]{
    	text-align: right;
    }
    
    ul.uk-nav-sub li {
        margin-bottom: 12px;
    }
    
    table .smp-row-data {
        border-top: 1px solid #e5e5e5;
    }
    table .smp-row-data{
        border-bottom: 1px solid #e5e5e5;
    }
    .uk-text-break {
        word-break: break-word !important;
    }
    .uk-vertical-center {
        vertical-align: middle;
    }
    .uk-tab>*>a {
        display: flex;
        align-items: center;
        column-gap: 0.25em;
        justify-content: center;
        padding: 9px 20px;
        color: #999;
        border-bottom: 2px solid transparent;
        font-size: 12px;
        text-transform: uppercase;
        transition: color .1s ease-in-out;
        line-height: 20px;
    }
dl.cf{
    padding-bottom: 20px;
    border-bottom: 1px solid #e5e5e5;
}

@media (min-width: 960px){
  .uk-form-horizontal .uk-form-controls {
    margin-left: 400px !important;
  }
}
</style>