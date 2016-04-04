<div id="$ID" class="field ss-ace-block-manager-field" data-sort-link="$Link('sort')" data-securityid="$SecurityID">
    <div class="ss-ace-block-manager-field-dialog"></div>
    <div class="ss-ace-block-manager-field-loading"></div>

    <div class="ss-ace-block-manager-field-header">
        <h3>$Title</h3>
        <div class="ss-ace-block-manager-field-create">
            <select class="ss-ace-block-manager-field-create-class no-change-track">
                <option value="">
                    <%t ACEBlockManagerField.CreateBlockLabel "Create a block" %>&hellip;
                </option>
                <% loop $BlockTypes() %>
                <option value="$Top.ActionLink('new', $ClassName, 'edit', $Top.PageId())">$SingularName</option>
                <% end_loop %>
            </select>
            <button class="ss-ui-button ui-state-disabled ss-ace-block-manager-field-do-create" data-icon="add">
                <%t ACEBlockManagerField.CreateLabel "Create" %>
            </button>
        </div>
    </div>
    
    <% if $ProxiedObjects() %>
    <div class="ss-ace-block-manager-field-actions" data-href-order="$Link(order)">
        <% loop $ProxiedObjects() %>
        <div class="ss-ace-block-manager-field-action" data-id="$ID">
            <div class="ss-ace-block-manager-field-action-header">
                <div class="ss-ace-block-manager-field-action-drag"></div>
                <img src="$Icon" alt="$Title.ATT" class="ss-ace-block-manager-field-action-icon">
                <h4>$Title</h4>
                <div class="ss-ace-block-manager-field-action-buttons">
                    <a class="ss-ui-button ss-ace-block-manager-field-open-dialog<% if $canEdit %><% else %> ss-ace-block-manager-field-action-disabled<% end_if %>" href="$Top.ActionLink('item', $ID, 'edit', $ClassName)" data-icon="pencil">
                        <%t ACEBlockManagerField.EditAction "Edit" %>
                    </a>
                    <a href="$Top.ActionLink('item', $ID, 'delete', $ClassName)" data-securityid="$SecurityID" class="ss-ui-button ss-ace-block-manager-field-delete<% if $canDelete %><% else %> ss-ace-block-manager-field-action-disabled<% end_if %>" data-icon="cross-circle">
                        <%t ACEBlockManagerField.DeleteAction "Delete" %>
                    </a>
                </div>
            </div>
        </div>
        <% end_loop %>
    </div>
    <% end_if %>
</div>