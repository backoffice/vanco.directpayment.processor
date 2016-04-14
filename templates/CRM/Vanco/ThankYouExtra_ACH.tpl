{*
 Extra fields in Thank You Screen
*}
<div id='vanco-ach-thankyou-extra'>
<div class="header-dark"> Payment Information </div>
  <div class="display-block">
Rounting Number: {$form.routing_number.html}<br />
Account Number: {$form.account_number.html}<br />
Account Type: {$form.account_type.html}<br />
  </div>
</div>

{* Replacing ACH thank you block with credit card block *}
{literal}
<script type = 'text/javascript'>
CRM.$(function($) {
$('.credit_card-group').replaceWith($('#vanco-ach-thankyou-extra'));
});
</script>
{/literal}
{* Replacing ends *}