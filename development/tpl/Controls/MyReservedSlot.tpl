{if $Slot->IsPending()}
	{assign var=class value='pending'}
{/if}
<td colspan="{$Slot->PeriodSpan()}" class="reserved {$class} mine clickres slot" id="{$Slot->Id()}|{$Slot->Date()->Format('Ymd')}">{$Slot->Label()}</td>