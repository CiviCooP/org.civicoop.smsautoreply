<h3>{if $action eq 1}{ts}New Autoreply{/ts}{elseif $action eq 2}{ts}Edit Autoreply{/ts}{else}{ts}Delete Autoreply{/ts}{/if}</h3>
<div class="crm-block crm-form-block crm-job-form-block">
 <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div>

{if $action eq 8}
  <div class="messages status no-popup">  
      <div class="icon inform-icon"></div>{ts}Do you want to continue?{/ts}
  </div>
{else}
  <table class="form-layout-compressed">
    <tr class="crm-sms-autoreply-form-block-subject">
        <td class="label">{$form.subject.label}</td><td>{$form.subject.html}</td>
    </tr>
    <tr class="crm-sms-autoreply-form-block-keyword">
        <td class="label">{$form.keyword.label}</td><td>{$form.keyword.html}</td>
    </tr>
    <tr class="crm-sms-autoreply-form-block-reply">
        <td class="label">{$form.reply.label}</td><td>{$form.reply.html}</td>
    </tr>
    <tr class="crm-sms-autoreply-form-block-provider_id">
        <td class="label">{$form.provider_id.label}</td><td>{$form.provider_id.html}</td>
    </tr>
    <tr class="crm-sms-autoreply-form-block-is_active">
        <td></td><td>{$form.is_active.html}&nbsp;{$form.is_active.label}</td>
    </tr>
    <tr class="crm-sms-autoreply-form-block-charge">
        <td class="label">{$form.charge.label}</td><td>{$form.charge.html}</td>
    </tr>
    <tr class="crm-sms-autoreply-form-block-financial_type_id">
        <td class="label">{$form.financial_type_id.label}</td><td>{$form.financial_type_id.html}</td>
    </tr>
  </table>
{/if} 
</table>
       <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
  </fieldset>
</div>
