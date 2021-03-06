{include file="default_web20cart/views/header.tpl"}
<h1>{'IDENTIFICATION_'|onapp_string}</h1>

    <form action='{$_ALIASES["virtual_machines"]}' method="post">
        <div class="div_page">
            <dl>
                <dt>
                    <label for="network_interface_label">{'LABEL_'|onapp_string}</label>
                </dt>
                <dd>
                    <input id="network_interface_label" type="text" name="network_interface[_label]" value="{$network_interface_obj->_label}" />
                </dd>
            </dl>
        </div>
        
    <h1>{'CONNECTIVITY_'|onapp_string}</h1>
        <div class="div_page">
            <dl>
                <dt><label for="network_join_id">{'PHYSICAL_NETWORK'|onapp_string}</label></dt>
                
            <dd>
                <select  name="network_interface[_network_join_id]">
                    {foreach from=$network_obj key=network_join_id item=network}
                        <option value="{$network_join_id}" {if $network_interface_obj->_network_join_id == $network_join_id}selected="true"{/if}>
                            {$network->_label} ( {$target_labels[$network_join_id]} )
                        </option>
                    {/foreach}
                </select>
            </dd>
            </dl>
        
            <dl>
                <dt>
                    <label for="rate_limit">{'PORT_SPEED'|onapp_string}</label>
                </dt>
                <dd>
                    <input id="rate_limit" type="text" name="network_interface[_rate_limit]" value="{$network_interface_obj->_rate_limit}" />
                </dd>
            </dl>
            <dl>
                <dt>
                     
                </dt>
                <dd>
                    <input type="hidden" name="network_interface[_primary]" value="0" />
                    <input value="1" type="checkbox" name="network_interface[_primary]" {if $network_interface_obj->_primary == 1}checked="true"{/if}/>
                    {'IF_PRIMARY_INTERFACE'|onapp_string}
                </dd>
            </dl>
        </div>
        <input type="submit" value="{'SAVE_NETWORK_INTERFACE'|onapp_string}" />
        <input type="hidden" name="network_interface[_id]" value="{$network_interface_obj->_id}" />
        <input type="hidden" name="network_interface[_virtual_machine_id]" value="{$network_interface_obj->_virtual_machine_id}" />
        <input type="hidden" name="action" value="network_interface_edit" />
    </form>
     
{include file="default_web20cart/views/navigation.tpl"}
{include file="default_web20cart/views/footer.tpl"}

