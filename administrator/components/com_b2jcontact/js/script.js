var JText = [];
JText['COM_B2JCONTACT_DYNAMIC_FIELD_FIELD_GROUP_LBL'] = '<?php echo JText::_("COM_B2JCONTACT_DYNAMIC_FIELD_FIELD_GROUP_LBL"); ?>';
JText['COM_B2JCONTACT_DYNAMIC_FIELD_NEW_GROUP_LBL'] = '<?php echo JText::_("COM_B2JCONTACT_DYNAMIC_FIELD_NEW_GROUP_LBL"); ?>';
JText['COM_B2JCONTACT_DYNAMIC_FIELD_GROUP_LBL'] = '<?php echo JText::_("COM_B2JCONTACT_DYNAMIC_FIELD_GROUP_LBL"); ?>';
JText['COM_B2JCONTACT_DYNAMIC_DELETE_GORUP_QUESTION'] = '<?php echo JText::_("COM_B2JCONTACT_DYNAMIC_DELETE_GORUP_QUESTION"); ?>';
JText['COM_B2JCONTACT_DYNAMIC_DELETE_ITEM_QUESTION'] = '<?php echo JText::_("COM_B2JCONTACT_DYNAMIC_DELETE_ITEM_QUESTION"); ?>';
JText['COM_B2JCONTACT_DYNAMIC_FIELD_DELETE_BTN'] = '<?php echo JText::_("COM_B2JCONTACT_DYNAMIC_FIELD_DELETE_BTN"); ?>';
JText['COM_B2JCONTACT_DYNAMIC_FIELD_EDIT_BTN'] = '<?php echo JText::_("COM_B2JCONTACT_DYNAMIC_FIELD_EDIT_BTN"); ?>';

var defaultEmail;

function showAddField($type)
{
	if(jQuery("#b2jFieldsCon").html().length > 0 && !$type){
		return false;
	}

	jQuery.ajax({   
		type:"POST",
		url:"index.php",
		data: ({
			option: "com_b2jcontact",
			no_html: 1,
			task : "showAddField",
			dataType: 'json',
			type: $type,
			defaultEmail: defaultEmail
			}),
		success: function(res){
			var obj = jQuery.parseJSON(res);
			if(obj.type == 'type'){
				jQuery("#b2jFieldsCon").html('');
				jQuery("#b2jTypeCon").html(obj.formHtml);
			}else{

				groups =  JSON.parse(jQuery("#jform_params_itemgroups").val());
				
				var orderingGroups = new Array();
				for(key in groups){

					orderingGroups[groups[key][0].ordering] = groups[key][0];
					orderingGroups[groups[key][0].ordering].val = key;
				}		
				
				len = getObjectArrayLength(orderingGroups);
				orderingGroups.reverse();
				if(len == 1){
					groupHtml = '<div class="control-group" style="display:none">'
				}else{
					groupHtml = '<div class="control-group">'
				}
					
				groupHtml +=		'<div class="control-label">'
				groupHtml +=			'<label title="" for="b2jNewFieldGroup">'+JText['COM_B2JCONTACT_DYNAMIC_FIELD_FIELD_GROUP_LBL']+' *</lable>'
				groupHtml +=		'</div>'
				groupHtml +=		'<div class="controls">'
				groupHtml +=			'<select id="b2jNewFieldGroup" onChange="b2jGroupToggle();" name="b2jNewFieldGroup">'
				
				for(key in orderingGroups){
					if(typeof orderingGroups[key] !== "function"){
						groupHtml +=	'<option value="'+orderingGroups[key].val+'">'+orderingGroups[key].title+'</option>'
					}
				}
				
				groupHtml +=			'</select>'
				groupHtml +=		'</div>'
				groupHtml += '</div>'

				if(len > 1){
					groupHtml += '<div class="control-group" id="b2jNewGroupCon" style="display:none;">'
				}
				else{
					groupHtml += '<div class="control-group" id="b2jNewGroupCon" >'
				}
				groupHtml +=  	'<div class="control-label">'
				groupHtml +=		'<label title="" for="b2jNewGroupName">'+JText['COM_B2JCONTACT_DYNAMIC_FIELD_NEW_GROUP_LBL']+' *</lable>'
				groupHtml +=	'</div>'
				groupHtml +=	'<div class="controls">'
				groupHtml +=		'<input id="b2jNewGroupName" type="text" value="" size="26" name="b2jNewGroupName" class="">'
				groupHtml +=	'</div>'
				groupHtml += '</div>';
				
				html = obj.formHtml + groupHtml + obj.formHtmlButtons;
				jQuery("#b2jFieldsCon").html(html);
			}
			jQuery('select').chosen({
				disable_search_threshold : 10,
				allow_single_deselect : true
			});
			jQuery('.hasTooltip').tooltip();                     
		}
	 });
}
function getObjectArrayLength(obj){
	keys = [];
		var k;
		var len=0;
		for (k in obj)
		{
		    if (obj.hasOwnProperty(k))
		    {
		        len++;
		    }
		}
	return len;	
}
function getFormByType(sel){
	showAddField(sel.value);
}
function b2jGroupToggle(){
	if(jQuery("select#b2jNewFieldGroup").val() == '0'){
		jQuery("#b2jNewGroupCon").css('display','block');
	}else{
		jQuery("#b2jNewGroupName").val('');
		jQuery("#b2jNewGroupCon").css('display','none');
	}
}
function escapeHtml(string) {
	if(string){
    	string = string.replace(/"/g, "");
   		string = string.replace(/'/g, "");
   	}
   	return string;
 }
function checkDefaultEmail() {
	if(window.value !== undefined){
		jQuery('.b2jDefaultEmailCon').each(function(){	
			id = jQuery(this).attr('id');
			jQuery("#" + id + " .b2jDefaultEmailControlsCon .radiobutton").removeClass('disabled');
		});
		for(key in value){
			if(value[key] !== undefined && value[key].type == "b2jDynamicEmail"){
				if(value[key].b2jFieldRadio == "1"){
					jQuery('.b2jDefaultEmailCon').each(function(){	
						if(jQuery(this).attr('id') != 'b2j_'+ value[key].b2jFieldKey){
							id = jQuery(this).attr('id');
							jQuery("#" + id + " .b2jDefaultEmailControlsCon input:radio[value='0']").click();
							jQuery("#" + id + " .b2jDefaultEmailControlsCon .radiobutton").addClass('disabled');
						}
					});
					return false;
				}
			}
		}
		return true;
	}else{
		return true;
	}
}

function submitB2jNewField(){
	removeNotValid();
	$type 	     	= jQuery("#b2jNewFieldType").val();
	$fieldName 	    = jQuery("#b2jNewFieldName").val();
	if(!textValidation(jQuery("#b2jNewFieldName"))){
		return false;
	}

	$fieldState		= jQuery("#b2jNewFieldState").val();
	$fieldGroup 	= jQuery("#b2jNewFieldGroup").val();

	if($type == 'b2jDynamicDropdown'){
		$fieldItems = jQuery("#b2jNewFieldItems").val();	
		if(!textValidation(jQuery("#b2jNewFieldItems"))){
			return false;
		}	
	}else{
		$fieldItems = false;	
	}
	if($type == 'b2jDynamicEmail'){
		$fieldRadio = jQuery("input:radio[name=b2jNewFieldRadio]:checked").val();	
	}else{
		$fieldRadio = false;	
	}
	if($type == 'b2jDynamicText' || $type == 'b2jDynamicEmail' || $type == 'b2jDynamicTextarea' || $type == 'b2jDynamicCheckbox' || $type == 'b2jDynamicDropdown' || $type == 'b2jDynamicDate'){
		$defaultValue = jQuery("#b2jNewFieldDefault").val();		
	}else{
		$defaultValue = false;	
	}

	if($fieldGroup == '0'){

		$newGroupName = jQuery("#b2jNewGroupName").val();
		if(!textValidation(jQuery("#b2jNewGroupName"))){
			return false;
		}
		$groups =  jQuery("#jform_params_itemgroups").val();
		groups = JSON.parse($groups);
		for(key in groups){
			groups[key] = groups[key][0];
		}
		
		len = getObjectArrayLength(groups);

		$fieldGroup = len;
		
		if(groupValue[$fieldGroup] === undefined) groupValue[$fieldGroup]=new Array();
		
        groupValue[$fieldGroup]['title'] = escapeHtml($newGroupName);
		groupValue[$fieldGroup]['ordering'] = $fieldGroup;
		groupValue[$fieldGroup]['class'] = "";
		groupValue[$fieldGroup]['state'] = "1";
		var res = stringify(groupValue);
	
		jQuery("#"+hiddenGroupId).attr("value",res);
	}else{
		$newGroupName = false;
	}
	if(jQuery('.b2jFields .control-group').length){
		var key=0;
		jQuery('.b2jFields').each(function(){
			var chKey = jQuery(this).attr('id').substr(4);
			chKey = parseInt(chKey);
			if(chKey>key) key = chKey;
		});
		$key = ++key;
	}else{
		$key = 0;
	}

	if(value[$key] === undefined) value[$key]=new Array();
	if($type == 'b2jDynamicDropdown'){
		value[$key]['b2jFieldItems'] = escapeHtml($fieldItems);
	}
	if($type == 'b2jDynamicEmail'){
		value[$key]['b2jFieldRadio'] = escapeHtml($fieldRadio);
	}
	if($type == 'b2jDynamicText' || $type == 'b2jDynamicEmail' || $type == 'b2jDynamicTextarea' || $type == 'b2jDynamicCheckbox' || $type == 'b2jDynamicDropdown' || $type == 'b2jDynamicDate'){
		value[$key]['b2jDefaultValue'] = escapeHtml($defaultValue);
	}
	value[$key]['b2jFieldKey'] = $key;
	value[$key]['type'] = $type;
	value[$key]['b2jFieldName'] = escapeHtml($fieldName);
	value[$key]['b2jFieldValue'] = '';
	value[$key]['b2jFieldState'] = $fieldState;
	value[$key]['b2jFieldGroup'] = $fieldGroup;
	value[$key]['b2jFieldOrdering'] = $key;

	//$groups =  jQuery("#jform_params_itemgroups").val();
	jQuery.ajax({   
		type:"POST",
		url:"index.php",
		data: ({
			option: "com_b2jcontact",
			no_html: 1,
			task : "saveNewField",
			dataType: 'json',
			type: $type,
			fieldName: $fieldName,
			defaultValue: $defaultValue,
			fieldState: $fieldState,
			fieldGroup: $fieldGroup,
			fieldItems: $fieldItems,
			fieldRadio: $fieldRadio,
			newGroupName: $newGroupName,
			key: $key,
			//b2jGroups: $groups,
			}),
		success: function(res){
			resetAddField();
			var obj = jQuery.parseJSON(res);
			if(jQuery("#group_"+obj.groupKey).length){
				jQuery("#group_"+obj.groupKey+" ol").append(obj.inTable);
			}else{
				html = '';
				html += '<li class="b2j-group" id="group_'+obj.groupKey+'" groupId="'+obj.groupKey+'">';
				html +=	'<div><i class="icon-menu"></i><span class="group-name">'+JText['COM_B2JCONTACT_DYNAMIC_FIELD_GROUP_LBL']+': '+obj.groupName+'</span><span class="group-action"><a href="#" class="group-edit-btn" isGroup="true" onClick="showEditField(this,'+obj.groupKey+')">'+JText['COM_B2JCONTACT_DYNAMIC_FIELD_EDIT_BTN']+'</a>&nbsp;<a group-delete-btn href="#" class="group-delete-btn" isGroup="true" onClick="deleteField(this,'+obj.groupKey+');">'+JText['COM_B2JCONTACT_DYNAMIC_FIELD_DELETE_BTN']+'</a></span></div>';
				html += '<ol>';
				html += obj.inTable;
				html += '</ol>';
				html += '</li>';

				jQuery(".sortable").append(html);
			}
			jQuery("#b2jNewFields").append(obj.html);
			var res = stringify(value);
			jQuery("#"+hiddenInputId).attr("value",res);
			jQuery('select').chosen({
				disable_search_threshold : 10,
				allow_single_deselect : true
			});
			jQuery('.hasTooltip').tooltip(); 
		}                                
	 });
}
function resetAddField(){

	jQuery("#b2jFieldsCon").html('');
	jQuery("#b2jTypeCon").html('');	
}
function datepicker($key){

	var monthNames = [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ];

	jQuery('#b2jfield'+$key).glDatePicker({
	    	cssName: "flatwhite",
	    	monthNames: true,
	    	showAlways: false,
	    	hideOnClick: true,
	    	onClick: function(target, cell, date, data) {
		        target.val(date.getDate()  + " - " +
		                    monthNames[date.getMonth()] + " - " +
		        			date.getFullYear()
		                    );
		    }
	});
	jQuery('#b2jfield'+$key).trigger('click');
	jQuery('#b2jfield'+$key).unbind('click');
}
function saveSorting(){
	serialized = jQuery('ol.sortable').nestedSortable('serialize');
	var splitByGroup = serialized.split("group[");

	var itemOrderKey = 0;
	var groupOrderKey = 1;
	for(i = 1; i < splitByGroup.length; i++){
		groupID = splitByGroup[i].substr(0,splitByGroup[i].indexOf("]"));
		var splitByItems = splitByGroup[i].split("item[");
		for(k = 1; k < splitByItems.length; k++){
			itemId = splitByItems[k].substr(0,splitByItems[k].indexOf("]"));

			value[itemId]["b2jFieldGroup"] = groupID;
			value[itemId]["b2jFieldOrdering"] = itemOrderKey;

			var res = stringify(value);

			jQuery("#"+hiddenInputId).attr("value",res);

			itemOrderKey++;
		}
		groupValue[groupID]['ordering'] = groupOrderKey;

		var res = stringify(groupValue);

		jQuery("#"+hiddenGroupId).attr("value",res);

		groupOrderKey++;

	}
	
}
function showEditField(elem,id){
	if(jQuery("#b2j_"+id).hasClass("b2jFields-open-edit") || jQuery("#b2j_group_"+id).hasClass("b2jFields-open-edit")){
		closeEdit();
		return false;
	}

	if(jQuery(".b2jFields-open-edit-div").length){
		oldOpenTr = jQuery("#dynamicfields").find(".b2jFields-open-edit-div");
		oldOpen = jQuery("#dynamicfields").find(".b2jFields-open-edit");
		oldOpen.slideUp("fast", function() {
			oldOpenTr.css("height","18");
			oldOpenTr.removeClass("b2jFields-open-edit-div");
		    oldOpen.removeClass("b2jFields-open-edit");
			var offset = jQuery(elem).offset();
			var top = offset.top;
			top = top + jQuery(elem).parent().height() + 8-jQuery("#dynamicfields").offset().top;
			jQuery(elem).parent().parent().addClass("b2jFields-open-edit-div");
			
			if(jQuery(elem).attr("isGroup") == "true"){
				jQuery(elem).parent().parent().css("height",jQuery("#b2j_group_"+id).height()+jQuery(elem).parent().height() + 57);
				jQuery("#b2j_group_"+id).css({"top": top+"px"});
				jQuery("#b2j_group_"+id).slideDown("fast", function() {
				   jQuery("#b2j_group_"+id).addClass("b2jFields-open-edit");
				});
			}else{
				jQuery(elem).parent().parent().css("height",jQuery("#b2j_"+id).height()+jQuery(elem).parent().height() + 16);
				jQuery("#b2j_"+id).css({"top": top+"px"});
				jQuery("#b2j_"+id).slideDown("fast", function() {
				   jQuery("#b2j_"+id).addClass("b2jFields-open-edit");
				});	
			}
		 });
	}else{
		var offset = jQuery(elem).offset();
		var top = offset.top;
		top = top + jQuery(elem).parent().height() + 8-jQuery("#dynamicfields").offset().top;
		jQuery(elem).parent().parent().addClass("b2jFields-open-edit-div");

		if(jQuery(elem).attr("isGroup") == "true"){
			jQuery(elem).parent().parent().css("height",jQuery("#b2j_group_"+id).height()+jQuery(elem).parent().height() + 57);
			jQuery("#b2j_group_"+id).css({"top": top+"px"});
			 jQuery("#b2j_group_"+id).slideDown("fast", function() {
			    jQuery("#b2j_group_"+id).addClass("b2jFields-open-edit");
			 });
		}else{
			jQuery(elem).parent().parent().css("height",jQuery("#b2j_"+id).height()+jQuery(elem).parent().height() + 16);
			jQuery("#b2j_"+id).css({"top": top+"px"});
			 jQuery("#b2j_"+id).slideDown("fast", function() {
			    jQuery("#b2j_"+id).addClass("b2jFields-open-edit");
			 });
		}
	}	
	} 
	function closeEdit(){
	oldOpenTr = jQuery("#dynamicfields").find(".b2jFields-open-edit-div");
	oldOpen = jQuery("#dynamicfields").find(".b2jFields-open-edit");
	oldOpen.slideUp("fast", function() {
		oldOpenTr.css("height","18");
		oldOpenTr.removeClass("b2jFields-open-edit-div");
	    oldOpen.removeClass("b2jFields-open-edit");
	 });
		removeNotValid();
	}
	function removeNotValid(){
		jQuery("#dynamicfields").find(".validFocus").removeClass("validFocus");
	}
	function deleteField(elem,itemId){
	if(jQuery(elem).attr("isGroup") == "true"){
		var answer = confirm(JText['COM_B2JCONTACT_DYNAMIC_DELETE_GORUP_QUESTION']);
		if (answer){
			closeEdit();
			groupSection = jQuery("#group_"+itemId);
			jQuery(groupSection).find(".fields").each(function(){
				itemKey = jQuery(this).attr("key");
				delete value[parseInt(itemKey)];
				var res = stringify(value);
				jQuery("#"+id).attr("value",res);
				jQuery("#item_"+itemKey).remove();
				jQuery(".fields.row"+itemKey).remove();
			});	
			jQuery(".b2j-group#group_"+itemId).remove();
			delete groupValue[parseInt(itemId)];
			var res = stringify(groupValue);
			jQuery("#"+hiddenGroupId).attr("value",res);
		}else{
			return false;
		}
	}else{
		var answer = confirm(JText['COM_B2JCONTACT_DYNAMIC_DELETE_ITEM_QUESTION']);
		if (answer) {
			closeEdit();
			delete value[parseInt(itemId)];
			var res = stringify(value);
			jQuery("#"+id).attr("value",res);
			jQuery(elem).parent().parent().remove();
			jQuery("#item_"+itemId).remove();	

		}else{
			return false;
		}
	}	
	}

	function IsNumeric(n) {
		return !isNaN(parseFloat(n)) && isFinite(n);
	}
	function stringify(arr){

	var result;
	var res;
	result="{";
	for(key in arr){
		if (typeof arr[key] !== "function" && (typeof arr[key] === "string" || IsNumeric(arr[key]))) {
			result+="\""+key+"\":"+"\""+arr[key]+"\",";
		}else{
			res="";
			var sarr = arr[key];
			for(skey in sarr){
				if (typeof sarr[skey] !== "function" && (typeof sarr[skey] === "string" || IsNumeric(sarr[skey])))
					res+="\""+skey+"\":"+"\""+sarr[skey]+"\",";
			}
			if(res!=""){
				res = res.slice(0,-1)+"}],";
				result+="\""+key+"\":[{";
				result+=res;	
			}
		}
	}
	if(result!="{") result = result.slice(0,-1)+"}";
	else result="";
	return result;

	}
	function textValidation(elem){
		if(elem.val() == ""){
		       elem.addClass("validFocus");
		       elem.focus();
		       return false;
		}else{
			return true;
		}
	}
	function saveValue(type,key){
		removeNotValid();
		if(type != "b2jGroup"){
			if(!textValidation(jQuery("#b2j_"+key+" input[fieldType='b2jFieldName']"))){
				return false;
			}
			value[parseInt(key)]["b2jFieldState"] = jQuery("#b2j_"+key+" select[fieldType='b2jFieldState']").val();
			value[parseInt(key)]["b2jFieldName"] = escapeHtml(jQuery("#b2j_"+key+" input[fieldType='b2jFieldName']").val());
			

			if(type == "b2jDynamicText"){
				value[parseInt(key)]["b2jDefaultValue"] = escapeHtml(jQuery("#b2j_"+key+" input[fieldType='b2jDefaultValue']").val());
			}
			if(type == "b2jDynamicEmail"){
				value[parseInt(key)]["b2jDefaultValue"] = escapeHtml(jQuery("#b2j_"+key+" input[fieldType='b2jDefaultValue']").val());
				value[parseInt(key)]["b2jFieldRadio"] = escapeHtml(jQuery("#b2j_"+key+" input:radio[name=b2jFieldRadio"+key+"]:checked").val());
				console.log(value[parseInt(key)]["b2jFieldRadio"]);
				if(jQuery("#b2j_"+key+" input:radio[name=b2jFieldRadio"+key+"]:checked").val() == 1){
					jQuery( ".b2jContactFields" ).find( ".email_default" ).remove();
					jQuery("li#item_"+key+" .b2j-dynamic-field-type").append('<span class="email_default"> (Default)</span>');
				}else{
					jQuery("li#item_"+key).find( ".email_default" ).remove();	
				}
			}
			if(type == "b2jDynamicDropdown"){
					if(!textValidation(jQuery("#b2j_"+key+" textarea[fieldType='b2jFieldItems']"))){
					return false;
				}
				value[parseInt(key)]["b2jDefaultValue"] = escapeHtml(jQuery("#b2j_"+key+" input[fieldType='b2jDefaultValue']").val());
				value[parseInt(key)]["b2jFieldItems"] = escapeHtml(jQuery("#b2j_"+key+" textarea[fieldType='b2jFieldItems']").val());	
			}
			if(type == "b2jDynamicTextarea"){
				value[parseInt(key)]["b2jDefaultValue"] = escapeHtml(jQuery("#b2j_"+key+" input[fieldType='b2jDefaultValue']").val());
			}
			if(type == "b2jDynamicCheckbox"){
				value[parseInt(key)]["b2jDefaultValue"] = escapeHtml(jQuery("#b2j_"+key+" select[fieldType='b2jDefaultValue']").val());	
			}
			if(type == "b2jDynamicDate"){
				value[parseInt(key)]["b2jDefaultValue"] = escapeHtml(jQuery("#b2j_"+key+" input[fieldType='b2jDefaultValue']").val());
			}

			jQuery("li#item_"+key+" .b2j-dynamic-field-name").html(jQuery("#b2j_"+key+" input[fieldType='b2jFieldName']").val());
			
			var res = stringify(value);
			jQuery("#"+id).attr("value",res);
			defaultEmail = checkDefaultEmail();	
		}else{
			if(!textValidation(jQuery("#b2j_group_"+key+" input[fieldType='title']"))){
				return false;
			}
			groupValue[parseInt(key)]['title'] = escapeHtml(jQuery("#b2j_group_"+key+" input[fieldType='title']").val());
			groupValue[parseInt(key)]['class'] = escapeHtml(jQuery("#b2j_group_"+key+" input[fieldType='class']").val());
			groupValue[parseInt(key)]['state'] = jQuery("#b2j_group_"+key+" select[fieldType='state']").val();

			jQuery("li#group_"+key+".b2j-group .group-name").html("GROUP: "+jQuery("#b2j_group_"+key+" input[fieldType='title']").val());

			var res = stringify(groupValue);
			jQuery("#"+hiddenGroupId).attr("value",res);	
	}		
	closeEdit();
}

jQuery(window).load(function(){
	defaultEmail = checkDefaultEmail();
})		
jQuery(document).ready(function(){

    jQuery(document).on('keypress', 'textarea[fieldtype="b2jFieldItems"], #b2jNewFieldItems', function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    });
	if(jQuery('.sortable').length){
	    jQuery('.sortable').nestedSortable({
	        forcePlaceholderSize: true,
	        handle: 'div',
	        helper: 'clone',
	        items: 'li',
	        listType: 'ol',
	        opacity: .6,
	        placeholder: 'placeholder',
	        revert: 250,
	        protectRoot: true,
	        tabSize: 25,
	        tolerance: 'pointer',
	        toleranceElement: '> div',
	        maxLevels: 2,
	        isTree: true,
	        expandOnHover: 700,
	        startCollapsed: true,
	    });
   	}
  	 jQuery('#serialize').click(function(){
		
	})

	jQuery('.b2j-contact-field-title').parent().css('float', 'none');	
	// radio start
				var repRadioCount = 0;
				
				var countRadio = jQuery("input[type=radio]").length;
				
				jQuery.fn.extend({
					replaceRadio: function () {
						repRadioCount++;
						
						if(jQuery(this).attr("name")===undefined){
							jQuery(this).attr("name","radio_"+repRadioCount)
						}

						if(jQuery(this).attr("disabled")=="disabled"){
							disabled = "disabled";
						}else{
							disabled = "";	
						}
						if(jQuery(this).attr("checked")=="checked"){
							var radioHTML = "<div rel='"+jQuery(this).attr("name")+"' class='radiobutton checked "+disabled+"'><div class='point'></div></div>"
						}
						else{
							var radioHTML = "<div rel='"+jQuery(this).attr("name")+"' class='radiobutton "+disabled+"'><div class='point'></div></div>"
						}
						
						jQuery(this).hide();
						jQuery(this).addClass("parsed-radio");
						jQuery(this).before(radioHTML);
					}
				});				
				for(var i=0; i<countRadio; i++){
					jQuery("input[type=radio]").eq(i).replaceRadio();
				}	
				jQuery(".radiobutton").live("click",function(event){
					if(!jQuery(this).hasClass("disabled")){
						var rel = jQuery(this).attr("rel");
						if(rel != ""){
							jQuery(".radiobutton[rel|='"+rel+"']").removeClass("checked");
							jQuery("input[name|='"+rel+"']").removeAttr("checked");
						}
						jQuery(this).addClass("checked");
						jQuery(this).next("input[type=radio]").attr("checked","checked");
					}
				})	
				jQuery("input[type=radio]").live("click",function(event){
					jQuery(this).prev(".radiobutton").trigger("click");
				})	

				jQuery(document).bind("DOMNodeInserted",function(){
					
					var newCountRadio = jQuery("input[type=radio]").length;

					if( newCountRadio != countRadio){

						var newCountRadio = jQuery("input[type=radio]:not('.parsed-radio')").length;
						
						for(var i=0; i<newCountRadio; i++){
							jQuery("input[type=radio]:not('.parsed-radio')").eq(i).replaceRadio();
						}	
					}
					
					return false;
				})	
	// radio end	
});