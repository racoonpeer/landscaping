<{if !empty($arHistory)}>
    <{section name=i loop=$arHistory}>
        <tr>
            <td width="135" align="left"><{$arHistory[i].date|date_format:"%d.%m.%Y %H:%M:%S"}></td>
            <td width="290"  align="left"><{$arHistory[i].action}></td>
            <td align="left"><{$arHistory[i].comment}></td>
        </tr>
    <{/section}>
<{/if}>

