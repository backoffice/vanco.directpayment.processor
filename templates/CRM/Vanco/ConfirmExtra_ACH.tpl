{*
 Extra fields in Confirmation Screen
*}
<div class="crm-group ach_payment-group">
  <div class="header-dark"> Payment Detail </div>
 <div id="vancoach-direct-payment">
   <div class="crm-section routing-number-field">
     <div class="label">{$form.routing_number.label}</div>
     <div class="content">{$form.routing_number.html}</div>
     <div class="clear"></div>
   </div>
   <div class="crm-section account-number-field">
    <div class="label">{$form.account_number.label}</div>
    <div class="content">{$form.account_number.html}</div>
    <div class="clear"></div>
   </div>
   <div class="crm-section account-type-field">
    <div class="label">{$form.account_type.label}</div>
    <div class="content">{$form.account_type.html}</div>
    <div class="clear"></div>
   </div>
 </div>
</div>
{if $vancoRegionEvent}
{literal}
<script type = 'text/javascript'>
 CRM.$(function($) {
    $('.credit_card-group').replaceWith($('.ach_payment-group'));
});
</script>
{/literal}
{/if}