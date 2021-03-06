{include file="default/views/header.tpl"}

<h1>{'PROPERTIES_'|onapp_string}</h1>

    <form action='{$_ALIASES["users_and_groups"]}' method="post">
        <div class="div_page">
            <dl>
                <dt><label for="description_field">{'DESCRIPTION_'|onapp_string}</label></dt>
                <dd><input id="description_field" type="text" name="role[_label]" value="" /></dd>
            </dl>
        </div>

<h1>{'PERMISSIONS_'|onapp_string}</h1>
     {literal}
        <script type="text/javascript">
        document.write('\n\
            <div id="all_roles" class="div_page"></div> \n\
            <div style="position:inherit" id="pagination"></div>'
        )
        </script>
     {/literal}

    <noscript>
        <div class="div_page">
		{foreach from=$permission_obj item=permission}
		         <dl>
		             <dt>
		                 </dt>
		                    <dd>
		                        <input type="hidden" name="role[_permission_ids][]" value="0" />
		                        <input value="{$permission->_id}" type="checkbox" name="role[_permission_ids][]" />
		                        <span style="font-weight:normal">{$permission->_label}</span> <b>({$permission->_identifier})</b>
		                    </dd>
	            </dl>
		{/foreach}
		</div>

    </noscript>

        <input type="hidden" name = "id" value="{$id}" />
        <input type="hidden" name = "action" value="role_edit" />
        <input type="submit" value="{'SAVE_'|onapp_string}" />
    </form>

<script type="text/javascript">
    var items_per_page = {$items_per_page};
    var permission_array = {$permission_array}
    var checked_role_ids_js = {$checked_role_ids_js}
    var pagescount = {$pages_quantity}
    document.write("<body onload='paginate( 1 )'>");
</script>

{literal}
    <script type="text/javascript" src="templates/default/views/usersAndGroups/pagination.js"></script>
{/literal}


{include file="default/views/navigation.tpl"}
{include file="default/views/footer.tpl"}