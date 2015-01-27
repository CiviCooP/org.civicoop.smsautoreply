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
        <td class="label">{$form.reply.label}</td>
        <td>
            <div id='char-count-message'></div>
            <span class="helpIcon" id="helptext">
                <a href="#" onClick="return showToken('Text', 1);">{$form.token1.label}</a>
                {help id="id-token-text" file="CRM/Contact/Form/Task/SMS.hlp"}
                <div id='tokenText' style="display: none">
                    <input  style="border:1px solid #999999;" type="text" id="filter1" size="20" name="filter1" onkeyup="filter(this, 1)"/><br />
                    <span class="description">{ts}Begin typing to filter list of tokens{/ts}</span><br/>
                    {$form.token1.html}
                </div>
            </span>
            <div class="clear"></div>
            <div class='text'>
                {$form.text_message.html}
            </div>
            {* code below is needed for the insert tookens javascript *}
            <div id="editMessageDetails"></div>
            <div id="template"></div>
            <div id="saveDetails"></div>
            <div id="updateDetails"></div>
            <input type="checkbox" name="saveTemplate" style="display: none">
            <input type="checkbox" name="updateTemplate" style="display: none">
            <input type="text" name="saveTemplateName" id="saveTemplateName" style="display: none">
        </td>
    </tr>
    <tr class="crm-sms-autoreply-form-block-provider_id">
        <td class="label">{$form.provider_id.label}</td><td>{$form.provider_id.html}</td>
    </tr>
    <tr class="crm-sms-autoreply-form-block-weight">
        <td class="label">{$form.weight.label}</td><td>{$form.weight.html}</td>
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
    <tr class="crm-sms-autoreply-form-block-aksjon_id">
        <td class="label">{$form.aksjon_id.label}</td><td>{$form.aksjon_id.html}</td>
    </tr>
    <tr class="crm-sms-autoreply-form-block-earmarking">
        <td class="label">{$form.earmarking.label}</td><td>{$form.earmarking.html}</td>
    </tr>
  </table>
{/if} 
</table>
       <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
  </fieldset>
</div>

{include file="CRM/Mailing/Form/InsertTokens.tpl"}
<script type="text/javascript">
{literal}
maxCharInfoDisplay();

cj('#text_message').bind({
  change: function() {
   maxLengthMessage();
  },
  keyup:  function() {
   maxCharInfoDisplay();
  }
});

function maxLengthMessage()
{
   var len = cj('#text_message').val().length;
   var maxLength = {/literal}{$max_sms_length}{literal};
   if (len > maxLength) {
      cj('#text_message').crmError({/literal}'{ts escape="js"}SMS body exceeding limit of 160 characters{/ts}'{literal});
      return false;
   }
return true;
}

function maxCharInfoDisplay(){
   var maxLength = {/literal}{$max_sms_length}{literal};
   var enteredCharLength = cj('#text_message').val().length;
   var count = maxLength - enteredCharLength;

   if( count < 0 ) {
      cj('#text_message').val(cj('#text_message').val().substring(0, maxLength));
      count = 0;
   }
   cj('#char-count-message').text( "You can insert upto " + count + " characters" );
}
{/literal}
</script>