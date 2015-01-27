{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Smsautoreply/Form/Autoreplies.tpl"}
{else}

{if $rows}
    {if $action ne 1 and $action ne 2}
        <div class="action-link">
        <a href="{crmURL q="action=add&reset=1"}" id="newAutoreply" class="button"><span><div class="icon add-icon"></div>{ts}Add New Autoreply{/ts}</span></a>
        </div>
    {/if}

<div id="help">
    {ts}The autoreplies are sorted by their weight. When an SMS is received it will match it contents to the keyword and the first match is send back to the sender{/ts}
</div>

    <div id="ltype">
    {strip}
    {* handle enable/disable actions*}
        {include file="CRM/common/enableDisable.tpl"}
        <br/>
        <table class="selector">        
            <tr class="columnheader">
                <th >{ts}Subject{/ts}</th>
                <th >{ts}Keyword{/ts}</th>
                <th >{ts}Reply{/ts}</th>
                <th >{ts}Provider{/ts}</th>
                <th >{ts}Charge{/ts}</th>
                <th >{ts}Financial type{/ts}</th>
                <th >{ts}Aksjon ID{/ts}</th>
                <th >{ts}Earmarking{/ts}</th>
                <th >{ts}Weight{/ts}</th>
                <th >{ts}Action{/ts}</th>
            </tr>
            {foreach from=$rows item=row}
                <tr id="row_{$row.id}" class="crm-autoreply {cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
                    <td class="crm-autoreply-subject">{$row.subject}</td>
                    <td class="crm-autoreply-keyword">{$row.keyword}</td>
                    <td class="crm-autoreply-reply">{$row.reply}</td>
                    <td class="crm-autoreply-provider">{$row.provider_id}</td>
                    <td class="crm-autoreply-charge">{$row.charge}</td>
                    <td class="crm-autoreply-financial_type">{$row.financial_type_id}</td>
                    <td class="crm-autoreply-aksjon_id">{$row.aksjon_id}</td>
                    <td class="crm-autoreply-earmarking">{$row.earmarking}</td>
                    <td class="crm-autoreply-weight">{$row.weight}</td>
                    <td>{$row.action|replace:'xx':$row.id}</td>
                </tr>
            {/foreach}
        </table>
    {/strip}
    </div>

{elseif $action ne 1}
    <div class="messages status no-popup">
      <div class="icon inform-icon"></div>
        {ts}There are no autoreplies configured.{/ts}
     </div>
    <div class="action-link">
    <a href="{crmURL q="action=add&reset=1"}" id="newAutoreply" class="button"><span><div class="icon add-icon"></div>{ts}Add New Autoreply{/ts}</span></a>
    </div>

{/if}
{/if}