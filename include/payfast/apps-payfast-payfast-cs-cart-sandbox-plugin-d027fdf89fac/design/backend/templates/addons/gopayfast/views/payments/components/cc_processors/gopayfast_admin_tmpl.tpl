{include file="common/subheader.tpl" title=__("addons.gopayfast_config_header") target="#gopayfast_merchant_configurations"}
<div id="gopayfast_merchant_configurations">    
    <div class="control-group">
        <label class="control-label cm-required" id="lbl_gopayfast_merchant_id" for="gopayfast_merchant_id">{__("addons.gopayfast_merchant_id")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][gopayfast_merchant_id]" id="gopayfast_merchant_id" size="32" value="{$processor_params.gopayfast_merchant_id}" >
        </div>
    </div>

    <div class="control-group">
        <label class="control-label cm-required" id="lbl_gopayfast_secured_key" for="gopayfast_secured_key">{__("addons.gopayfast_secured_key")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][gopayfast_secured_key]" id="gopayfast_secured_key" size="32" value="{$processor_params.gopayfast_secured_key}" >
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" id="lbl_gopayfast_store_id" for="gopayfast_gopayfast_store_id">{__("addons.gopayfast_gopayfast_store_id")}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][gopayfast_store_id]" id="gopayfast_store_id" size="32"
             value="{$processor_params.gopayfast_store_id}" >
        </div>
    </div>
</div>