/**
 * Core class
 * Core.prevent(event) - prevent event
 * Core.serialize(form, selector[optional]) form for serialize, return object. if selector defined, return object with objects
 * @type {{prevent: Function, serialize: Function}}
 */
var Core = {
	prevent: function(e){
       if (!e)
         if (window.event) e = window.event;
         else return;
       if (e.cancelBubble != null) e.cancelBubble = true;
       if (e.stopPropagation) e.stopPropagation();
       if (e.preventDefault) e.preventDefault();
       if (window.event) e.returnValue = false;
       if (e.cancel != null) e.cancel = true;
	},
	serialize: function(form, selector){
		if(selector) {
			var rows = form.getAll(selector);
			var out = {};
			for(var i = 0; i< rows.length;i++){
				out[i] = Core.serialize(rows[i]);
			}
			return out;
		}
		var elems = form.getAll('input, select, textarea');
		var out = {};
		for(var i=0;i<elems.length;i++){
			var el = elems[i];
			if(el.name){
				var value = el.value;
				if(el.tagName.toLowerCase() == 'input' && (el.type == 'checkbox' || el.type=='radio')){
					value = el.checked;
				} else if(el.tagName.toLowerCase() == 'input' && el.type=='radio'){
					if(!el.checked)continue;
					else value = el.value;
				}else{
					value = el.value;
				}
				out[el.name] = el.value;
			}
		}
		return out;
	}
};


var Ajax = {
	request : function(obj) {
		var req = this.getXmlHttp();

		var url = obj.url || window.location.href;
		var reqType = obj.type || "GET";
		var success = obj.success||false;
		var data = obj.data || null;
		var response = obj.response || 'text';
		
		req.onloadend = function() {
			if(success){
				var answer;
				if(response == 'json'){
					answer = JSON.parse(req.responseText);
				}else{
					answer = req.responseText.replace(/(^\s*"|"\s*$)/g, "");
				}
				success(answer, req);
			}
		};

		req.open(reqType, url, true);
		//	if (reqType.toLowerCase() == "post") {
		req.setRequestHeader('Content-Type',
					'application/x-www-form-urlencoded');
		//}
		if (!data) {
			req.send(null);
		} else {
			if(typeof data == 'object'){
				var d = [];
				for(var i in data){
					d.push(encodeURIComponent(i)+'='+(typeof data[i] == 'object' ? Ajax.encodeRecoursivly(data[i]) : encodeURIComponent(data[i])));
				}
				data = d.join('&');
			}
			req.send(data);
		}

	},
	encodeRecoursivly: function(v){
		var d = [];
		for(var i in v) {
			d.push('"'+encodeURIComponent(i)+'":"'+(typeof v[i] == 'object' ? Ajax.encodeRecoursivly(v[i]) : encodeURIComponent(v[i]))+'"');
		}

		return '{'+ d.join(encodeURIComponent(',')) + '}';
	},
	getXmlHttp : function() {
			var xmlhttp;
			try {
				xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				try {
					xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (E) {
					xmlhttp = false;
				}
			}
			if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
				xmlhttp = new XMLHttpRequest();
			}
			return xmlhttp;
		}
};
var UI = {
	build: function(object){
		switch(object.type){
			case 'form':
				return UI.builder.asForm(object);
			case 'table':
				return UI.builder.asTable(object);;
			case 'list':
				return null;
			default:
				return null;
		}
	},
	/**
	 * @param str any string, or node
	 * @param type [info, success, warning, error, neutral]
	 */
	message: function(str, type){
		if(!type)type='neutral';
		var out = newElement('div', {'class': 'pop-up-message pop-up-message-' + type});
		if(typeof str != 'string')out.appendChild(str);else out.innerHTML=str;
		document.body.appendChild(out);
		setTimeout(function(){out.remove()}, 5000);
	}
}
UI.builder = {
	asForm: function(object){
		return this.addFormElements(object, 'form', this.getAttributesForForm(object));
	},
	asTable: function (object) {
		var data = [newElement("tr", {})];
		var h = object.headers;
		for(var i in h) {
			var c=newElement('th',{});
			c.innerHTML = h[i];
			data[0].appendChild(c);
		}
		var d = object.data;
		for(var i = 0; i < d.length; i++) {
			var cells = [];
			var a = {};
			for(var k in d[i].hiddenFields){
				a['data-field-' + k] = d[i].hiddenFields[k];
			}
			var r = newElement('tr', a);
			for(var k in h) {
				var c=newElement('td',{'data-name':k});
				var v = d[i].fields[k]
				c.innerHTML = v ? v : "";
				cells.push(c);
			}
			data.push(newElement('tr', a, cells));
		}
		return newElement('table', {}, data);
	},
	addFormElements: function(object, tag, attributes){
		var table = null;
		var out = newElement(tag, attributes);
		var row = null;
		var overviewHeadersAdded = false;
		for(var key in object.fields){
			var item = object.fields[key];
			if(item.tag == 'fieldset') {
				var asTable = false;
				if(item.layout == "overview" || item.layout == 'table'){
					asTable = true;
				}
				var wrapper = new newElement(asTable ? "table" : "div", {});
				out.appendChild(wrapper);

				var current = this.addFormElements(item, "fieldset", {});
				if(asTable) {
					for(var i = 0; i < current.childNodes.length; i++){
						wrapper.appendChild(current.childNodes[i]);
					}
				} else {
					wrapper.appendChild(current);
				}
				if(item.layout == "overview" && !overviewHeadersAdded){
					overviewHeadersAdded = true;
					var wrapperHeaders = newElement("tr", {});
					for(var a in item.fields){
						for(var b in item.fields[a] ){
							var th = newElement("th", {});
							th.innerHTML = item.fields[a].title;
							wrapperHeaders.appendChild(th);
							break;
						}
					}
					wrapper.insertBefore(wrapperHeaders, wrapper.get("*:first-child"));
				}
				continue;
			}
			var el = this.buildFormfield(item);

			if(item.layout == 'table'){
				if(item.attributes && item.attributes.type == 'hidden'){
					out.appendChild(el);
					continue;
				} else {
					if(table === null){
						table = newElement('table', {});

					}
					table.appendChild(el.base);
					table.appendChild(el.description);
				}
			} else if(item.layout == "overview") {

				if(!row) {
					row = newElement("tr", {});
					out.appendChild(row);
				}
				row.appendChild(el);
			} else {
				if(table !== null){
					out.appendChild(table);
					table = null;
				}
				out.appendChild(el);
			}
		}
		if(table !== null){
			out.appendChild(table);

		}

		return out;
	},

	buildFormfield: function(f){
		switch(f.layout){
			case 'grid':
				return	this.gridFormfield(f);
			case 'table':
				return this.tableFormfield(f);
			case 'block':
				return this.blockFormfield(f);
			case 'overview':
				return this.overviewFormfield(f);
			default:
				console.error("Undefined layout type: " + f.layout + " in object", f);
				throw new Exception(e);
		}
	},
	gridFormfield: function(f){
		var formfield = this.buildActiveElement(f);
		if(formfield.type == 'hidden'){
			return formfield;
		}
		var holder = this.getFormfieldHolder(f, 'div');
		
		
		var title = this.getFormfieldElement(f, 'label', 'title');
		holder.appendChild(title);
		
		var center = newElement('div', {'class': 'grid_center'});
		
		var error = this.getFormfieldElement(f, 'div', 'error')
		center.appendChild(formfield);
		center.appendChild(error);
		holder.appendChild(center);
		
		var description = this.getFormfieldElement(f, 'div', 'description')
		holder.appendChild(description);
		return holder;
	},
	tableFormfield: function(f){
		var formfield = this.buildActiveElement(f);
		if(formfield.type == 'hidden'){
			return formfield;
		}
		var rowBase = this.getFormfieldHolder(f, 'tr');
		rowBase.addClass('base');
		var rowDescription = this.getFormfieldHolder(f, 'tr');
		rowDescription.addClass('description');
		
		var title = this.getFormfieldElement(f, 'label', 'title');
		var error = this.getFormfieldElement(f, 'div', 'error')
		
		var titleHolder = newElement('td', {'class': 'title'});
		var formfieldHolder = newElement('td', {'class': 'formfield'});
		var errorHolder = newElement('td', {'class': 'error'});
		
		titleHolder.appendChild(title);
		formfieldHolder.appendChild(formfield);
		errorHolder.appendChild(error);
		
		rowBase.appendChild(titleHolder);
		rowBase.appendChild(formfieldHolder);
		rowBase.appendChild(errorHolder);
		
		var description = this.getFormfieldElement(f, 'td', 'description')
		rowDescription.appendChild(description);
		return {base: rowBase, description: rowDescription};
	},
	blockFormfield: function(f){
		var formfield = this.buildActiveElement(f);
		if(formfield.type == 'hidden'){
			return formfield;
		}
		var holder = this.getFormfieldHolder(f, 'div');
		
		var title = this.getFormfieldElementLabel(f, 'label', 'title');
		
		var error = this.getFormfieldElement(f, 'div', 'error');
		var description = this.getFormfieldElement(f, 'div', 'description');
		
		holder.appendChild(title);
		holder.appendChild(error);
		holder.appendChild(formfield);
		holder.appendChild(description);
		return holder;
	},
	overviewFormfield: function(f){
		var cell = newElement("td");
		cell.appendChild(this.buildActiveElement(f));
		return cell;
	},
	getFormfieldHolder: function(f, tag){
		var holderParams = {'class': 'form_holder '};
		if(f['class'])holderParams['class'] = holderParams['class']+f['class'];
		if(f['id'])holderParams.id = "holder_"+f.id;
		return newElement(tag, holderParams);
	},
	getFormfieldElement: function(f, tag, key){
		var out = newElement(tag, {'class': key});
		if(f[key]){
			out.innerHTML = f[key];
		} else {
			out.addClass('hidden');
		}
		
		return out
	},
	getFormfieldElementLabel: function(f, tag, key){
		var out = this.getFormfieldElement(f, tag, key);
		if(f.id)out.setAttribute('for', f.id);
		return out
	},
	buildActiveElement: function(f){
		var ffAttributes = (f.attributes) ? f.attributes : {};
		var out
		if(f.tag != 'html'){
			out = newElement(f.tag, ffAttributes);
		} else {
			out = newElement('div', {});
			if(f.value)
				out.innerHTML = f.value;
		}
		if(f.value){
			if(f.tag == 'input'){
				out.value=f.value;
			}else if(f.tag == 'textarea'){
				out.innerHTML = f.value;
			}
		}
		if(f.name)out.name=f.name;
		if(f.id)out.id=f.id;
		if(f.options && f.tag == 'select'){
			for(var i in f.options){
				var opt = newElement('option');
				opt.setAttribute('value', i);
				opt.innerHTML = f.options[i];
				if(f.value == i){
					opt.selected = true;
				}
				out.appendChild(opt);
			}
		}
		return out;
	},
	getAttributesForForm: function(object){
		var out = {};
		if(object.id)out.id = object.id;
		if(object.method)out.method = object.method;
		return out;
	}
};




function DialogWindow(id, html){
	this.close = function(){
		if(this.lastDisplayed) {
			this.lastDisplayed.show();
			DialogWindow.lastDisplayed = this.lastDisplayed;
		}
		this.window.hide();
		this.overlay.hide();
	};
	this.destroy = function(){
		if(this.lastDisplayed) {
			this.lastDisplayed.show();
			DialogWindow.lastDisplayed = this.lastDisplayed;
		}
		this.window.remove();
		this.overlay.remove();
	};
	this.show = function(){
		var w = document.body.getAll('.dialog-window, .dialog-window-overlay');
		for(var i = 0; i < w.length;i++){
			w[i].addClass('under');
		}
		this.window.removeClass('under');
		this.overlay.removeClass('under');
		if(DialogWindow.lastDisplayed) {
			this.lastDisplayed = DialogWindow.lastDisplayed;
		}
		DialogWindow.lastDisplayed = this;
		this.window.show();
		this.overlay.show();
		this.window.style.height = '';
		var wh = this.window.getHeight();
		var bh = document.body.getHeight();
		var style={};
		if(bh-wh > 0) {
			style.top = (bh-wh)/2 + "px";
			style.height = "auto";
			style.overflow = "auto";
		}else{
			if(bh < 300){
				bh = 300
			}
			style.top = 0;
			style.height = bh+"px";
			style.overflow = "scroll";
		}
		this.window.setStyle(style)
	};
	this.setWidth = function(width, suffix) {
		if(!suffix)suffix="px";
		if(width == 0){
			width = 300;
			suffix = 'px';
		}
		this.window.setStyle({
			width: width+suffix,
			'margin-left': -Math.ceil(width/2) + suffix
		});
		if(width-50 > 0){
			style = {
				width: width-50+suffix,
				padding: '9px'
			}
		}else{
			style = {
				width: width+suffix,
				padding: '0'
			}
		}
	};
	
	this.setContent = function(html){
		if(typeof html == 'string'){
			this.window.innerHTML = html;
		}else if(typeof html == 'undefined' || html == null){
			this.window.innerHTML = '';
		}else{
			this.window.innerHTML = '';
			this.window.appendChild(html);
		}
	};
	this.getContentHolder = function(){
		return this.window;
	};
	this.window = newElement('div', {id: id, 'class': 'dialog-window hidden'});
	if(html){
		this.setContent(html);
	}

	this.overlay = newElement('div', {id: "overlay_"+id, 'class': 'dialog-window-overlay hidden'});
	this.overlay.setStyle({'clear': 'both'});
	document.body.appendChild(this.window);
	document.body.appendChild(this.overlay);
	
	return this;
}


/**
 * styled confirm window, clb must be a function, it will be called in any cases,
 * recive first param - true if ok was pressed, false if cancel was pressed
 * okText, cancelText can be undefined.
 * clb can be undefined too, but function lose sense.
*/
DialogWindow.Confirm = function(id, text, clb, okText, cancelText){
		this.window = new DialogWindow(id);

		this.id = id;
		if(!okText){
			okText = "OK";
		}
		if(!cancelText){
			cancelText = "Cancel";
		}
		var content = 
				'<div class="confirm">' + 
					'<div class="text">'+text+'<div>'+
					'<div class="controls">'+
						'<input type="button" class="cancel light" value="'+cancelText+'">'+
						'<input type="button" class="ok" value="'+okText+'">'+
					'</div>'+
				'</div>';
				
		this.window.setContent = function(html){
			var el = this.window.get('div.text');
			if(!el) {
				this.window.innerHTML = content;
				return;
			}
			if(typeof html == 'string'){
				el.innerHTML = html;
			}else{
				el.innerHTML = '';
				el.appendChild(html);
			}
		};
		this.window.getContentHolder = function(){
			return this.window.get('div.text');
		};
		this.window.setContent(content);
		var win = this.window;
		Event.add(win.window.get('input.light.cancel'), 'click', function(){
			win.destroy();
			if(typeof clb == 'function'){
				clb(false)
			}
		});
		Event.add(win.window.get('input.ok'),'click', function(){
			win.destroy();
			if(typeof clb == 'function'){
				clb(true)
			}
		});
		this.window.show();
		//warning! return DialogWindow instance
		return this.window;
	}
/**
 * styled confirm window, clb must be a function, it will be called in any cases,
 * recive first param - true if ok was pressed, false if cancel was pressed
 * okText, cancelText can be undefined.
 * clb can be undefined too, but function lose sense.
 */
DialogWindow.Prompt = function(id, text, clb, okText, cancelText){
	this.window = new DialogWindow(id);

	this.id = id;
	if(!okText){
		okText = "OK";
	}
	if(!cancelText){
		cancelText = "Cancel";
	}
	var content =
		'<div class="confirm">' +
		'<div class="text">'+text+'<div>'+
		'<input type="text" class="prompt">'+
		'<div class="controls">'+
		'<input type="button" class="cancel light" value="'+cancelText+'">'+
		'<input type="button" class="ok" value="'+okText+'">'+
		'</div>'+
		'</div>';

	this.window.setContent = function(html){
		var el = this.window.get('div.text');
		if(!el) {
			this.window.innerHTML = content;
			return;
		}
		if(typeof html == 'string'){
			el.innerHTML = html;
		}else{
			el.innerHTML = '';
			el.appendChild(html);
		}
	};
	this.window.getContentHolder = function(){
		return this.window.get('div.text');
	};
	this.window.setContent(content);
	var win = this.window;
	Event.add(win.window.get('input.light.cancel'), 'click', function(){
		win.destroy();
		if(typeof clb == 'function'){
			clb(false, '')
		}
	});
	Event.add(win.window.get('input.ok'),'click', function(){
		var text = win.window.get('input.prompt').value;
		win.destroy();
		if(typeof clb == 'function'){
			clb(true, text);
		}
	});
	this.window.show();
	//warning! return DialogWindow instance
	return this.window;
}
/**
 * styled alert window, clb must be a function
 * clb can be undefined too.
*/
DialogWindow.Alert = function(id, text, clb, close){
	this.window = new DialogWindow(id);
	this.id = id;
	
	var content = 
			'<div class="alert">' + 
				'<div class="text">'+text+'<div>'+
			'</div>';
		
	this.window.window.innerHTML = content;
	this.window.setContent = function(html){
		var el = this.window.get('div.text');
		if(typeof html == 'string'){
			el.innerHTML = html;
		}else{
			el.innerHTML = '';
			el.appendChild(html);
		}
	};
	this.window.appendContent = function(html){
		this.window.get('div.text').appendChild(html);
	};
	this.window.getContentHolder = function(){
		return this.window.get('div.text');
	};
	
	this.window.setContent(text);
	var win = this.window;

	Event.add(win.overlay, 'click', function(){
		if(close) {
			win.close();
		}else{
			win.destroy();
		}
		if(typeof clb == 'function'){
			clb()
		}
	});
	this.window.show();

	//warning! return DialogWindow instance
	return this.window;
};


Event = (function() {
	var guid = 0;
	function fixEvent(event) {
		event = event || window.event;
			if (event.isFixed )return event;
			event.isFixed = true ;
			event.preventDefault = event.preventDefault || function(){this.returnValue = false};
			event.stopPropagation = event.stopPropagaton || function(){this.cancelBubble = true};
			
			if (!event.target)
				event.target = event.srcElement;
		
			if (!event.relatedTarget && event.fromElement)
				event.relatedTarget = event.fromElement == event.target ? event.toElement : event.fromElement;
			
		
			if ( event.pageX == null && event.clientX != null ) {
				var html = document.documentElement, body = document.body;
				event.pageX = event.clientX + (html && html.scrollLeft || body && body.scrollLeft || 0) - (html.clientLeft || 0);
				event.pageY = event.clientY + (html && html.scrollTop || body && body.scrollTop || 0) - (html.clientTop || 0);
			}
			if ( !event.which && event.button ) {
				event.which = (event.button & 1 ? 1 : ( event.button & 2 ? 3 : ( event.button & 4 ? 2 : 0 ) ));
		}
		return event
	}	

	function commonHandle(event) {
		event = fixEvent(event);
		var handlers = this.events[event.type];
		for ( var g in handlers ) {
			var handler = handlers[g];

			var ret = handler.call(this, event);
			if ( ret === false ) {
				event.preventDefault();
				event.stopPropagation()
			}
		}
	}
	
	return {
		add: function(elem, type, handler) {
			if(!elem)throw new Error("Can't add event, because element is undefined or null");
			if(!type)throw new Error("Can't add event, because event type is undefined or null");
			if(!handler)throw new Error("Can't add event, because event handler is undefined or null");
			if (elem.setInterval && ( elem != window && !elem.frameElement ) ) {
			elem = window;
			}
			
			if (!handler.guid) {
			handler.guid = ++guid
			}
			
			if (!elem.events) {
			elem.events = {};
				elem.handle = function(event) {
				if (typeof Event !== "undefined") {
				return commonHandle.call(elem, event)
				}
			}
			}
			
			if (!elem.events[type]) {
			elem.events[type] = {};
			
			if (elem.addEventListener)
				elem.addEventListener(type, elem.handle, false);
			else if (elem.attachEvent)
				elem.attachEvent("on" + type, elem.handle)
			}
			
			elem.events[type][handler.guid] = handler
		},
		remove: function(elem, type, handler) {
			var handlers = elem.events && elem.events[type];
			
			if (!handlers) return;
			
			delete handlers[handler.guid];
			
			for(var any in handlers) return;
			if (elem.removeEventListener)
			elem.removeEventListener(type, elem.handle, false);
			else if (elem.detachEvent)
			elem.detachEvent("on" + type, elem.handle);
			
			delete elem.events[type];
		
			
			for (var any in elem.events) return
			try {
				delete elem.handle;
				delete elem.events 
			} catch(e) { // IE
				elem.removeAttribute("handle");
				elem.removeAttribute("events")
			}
		} 
	};
}())
