{*
 Extra fields for ACH Payment Processor
*}
<fieldset id="ach-direct-payment-extra" {if $BackendContribution}style = "display:none"{/if}>
  <legend>Payment Information</legend>
    <div class="ach-direct-payment-extra">
      <div class="crm-section routing-number-section">
        <div class="label">{$form.routing_number.label}</div>
        <div class="content">{$form.routing_number.html}</div>
        <div class="clear"></div>
      </div>

      <div class="crm-section account-number-number-section">
        <div class="label">{$form.account_number.label}</div>
        <div class="content">{$form.account_number.html}</div>
        <div class="clear"></div>
      </div>

      <div class="crm-section account-type-section">
        <div class="label">{$form.account_type.label}</div>
        <div class="content">{$form.account_type.html}</div>
        <div class="clear"></div>
      </div>
    </div>
</fieldset>
{literal}
<script type = 'text/javascript'>
 CRM.$(function($) {

var BackendContri = '{/literal}{$BackendContribution}{literal}';
 if(BackendContri) {
     //Inserting ACH Payment information after Credit Card Payment information for backend transaction
     $($('#ach-direct-payment-extra')).insertAfter('#payment_information .credit_card_info-group');
     var ACHProcessors = '{/literal}{$ACHProcessors}{literal}';
     ACHProcessors     = JSON.parse(ACHProcessors);

  }else{
    //Relpacing Credit card info only for transact mode
    $('.credit_card_info-group').replaceWith($('#ach-direct-payment-extra'));
  }

//Handling payment options on change of payment processor
$( "#payment_processor_id" ).change(function() {
   handlePaymentOptions($(this).val());
});

//Handling Payment Option on load
handlePaymentOptions($( "#payment_processor_id" ).val());

//Function to handle payment options depending on the payment processor selected.
function handlePaymentOptions (PPID) {
 if(BackendContri) {
    if($.isNumeric(ACHProcessors[PPID])) {
      $('#payment_information .credit_card_info-group').hide();
      $('#ach-direct-payment-extra').show();
    }else{
      $('#ach-direct-payment-extra').hide();
      $('#payment_information .credit_card_info-group').show();
      }
    }
  }
});
</script>
{/literal}
