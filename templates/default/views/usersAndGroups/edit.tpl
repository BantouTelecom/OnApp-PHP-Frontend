{include file="default/views/header.tpl"}


<h1>{'USER_DETAILS'|onapp_string}</h1>
    <form action='{$_ALIASES["users_and_groups"]}' method="post">
        <div class="div_page">
            <dl>
                <dt><label for="login_field">{'LOGIN_'|onapp_string}</label></dt>
                <dd>
                    <input id="login_field_hidden" type="hidden" name="user[_login]" value="{$user_obj->_login}" />
                    <input id="login_field" type="text" name="user[_login]" value="{$user_obj->_login}" disabled=true />
                </dd>
            </dl>
            <dl>
                <dt><label for="first_name">{'FIRST_NAME'|onapp_string}</label></dt>
                <dd><input id="first_name" type="text" name="user[_first_name]" value="{$user_obj->_first_name}" /></dd>
            </dl>
            <dl>
                <dt><label for="last_name">{'LAST_NAME'|onapp_string}</label></dt>
                <dd><input id="last_name" type="text" name="user[_last_name]" value="{$user_obj->_last_name}" /></dd>
            </dl>
            <dl>
                <dt><label for="e_mail">{'E_MAIL'|onapp_string}</label></dt>
                <dd><input id="e_mail" type="text" name="user[_email]" value="{$user_obj->_email}" /></dd>
            </dl>
            <dl>
                <dt><label for="user_time_zone">{'TIME_ZONE'|onapp_string}</label></dt>
                <dd>
                    <select id="user_time_zone" name="user[_time_zone]">
                        <option value=""></option>
                    {foreach from=$time_zones key=zone_key item=zone_value}
                        <option value="{$zone_key}" {if $zone_key == $user_obj->_time_zone}selected="selected"{/if}>{$zone_value}</option>
                    {/foreach}
                        
                    </select>
                </dd>
            </dl>
           
         </div>

<h1>{'LOGIN_PASSWORD'|onapp_string}</h1>
         <div class="div_page">
            <dl>
                <dt><label for="password_field">{'PASSWORD_'|onapp_string}</label></dt>
                <dd><input id="password_field" type="password" name="user[_password]" value="" /></dd>
            </dl>
            <dl>
                <dt><label for="repeat_password">{'REPEAT_PASSWORD'|onapp_string}</label></dt>
                <dd><input id="repeat_password" type="password" name="user[_password_confirmation]" value="" /></dd>
            </dl>
            
         </div>
<h1>{'BILLING_PLAN'|onapp_string}</h1>
        <div class="div_page">
            <dl>
                <dt><label for="billing_plan_field">{'BILLING_PLAN'|onapp_string}</label></dt>
                <dd>
                    <select id="billing_plan_field" name="user[_billing_plan_id]">
                        <option value=""></option>
                    {foreach from=$billing_plans_obj item=plan}
                        <option value="{$plan->_id}" {if $billing_plan_obj->_id == $plan->_id}selected=true{/if}>{$plan->_label} [{$plan->_currency_code}]</option>
                    {/foreach}
                    </select>
                </dd>
            </dl>
        </div>

<h1>{'USER_ROLES'|onapp_string}</h1>
         <div class="div_page">
         {foreach from=$role_obj item=role}
             <dl>
                <dt>

                </dt>
                <dd>
                    <input type="hidden" name="user[_role_ids][]" value="0" />
                    <input value="{$role->_id}" type="checkbox" name="user[_role_ids][]" {if in_array($role->_id, $user_role_ids)}checked="true"{/if}/>
                    {$role->_label}
                </dd>
            </dl>
         {/foreach}
         </div>

<h1>{'USER_GROUP'|onapp_string}</h1>
        <div class="div_page">
            <dl>
                <dt><label for="user_group_select">{'USER_GROUP'|onapp_string}</label></dt>
                <dd>
                    <select id="user_group_select" name="user[_user_group_id]">
                        <option value=""></option>
                    {foreach from=$user_group_obj item=group}
                        <option value="{$group->_id}" {if $user_obj->_user_group_id == $group->_id}selected=true{/if}>{$group->_label}</option>
                    {/foreach}
                    </select>
                </dd>
            </dl>
        </div>

<!-- TODO 
<h1>{'AUTO_SUSPENDING'|onapp_string}</h1>
        <div class="div_page">
            <dl>
                <dt><label for="suspend_field">{'SUSPEND_AT'|onapp_string}</label></dt>
                <dd><input id="login_field" type="text" name="user[_suspend_at]" value="{$user_obj->_suspend_at}" /> YYYY-MM-DD hh:mm:ss (UTC)</dd>
            </dl>
            <dl>
                <dt>

                </dt>
                <dd>
                    <input type="hidden" name="user[_role_ids][]" value="0" />
                    <input value="{$role->_id}" type="checkbox" name="user[_role_ids][]" {if in_array($role->_id, $user_role_ids)}checked="true"{/if}/>
                    {$role->_label}
                </dd>
            </dl>
        </div>
-->
        
        <input type="hidden" name = "id" value="{$user_id}" />
        <input type="hidden" name = "action" value="edit" />
        <input type="submit" value="{'SAVE_'|onapp_string}" />
    </form>


{include file="default/views/navigation.tpl"}
{include file="default/views/footer.tpl"}