/**
 * Created by rsmirnou on 6/19/2015.d
 */
/**
 * Class with static functions. For example:
 *
 * var input = Forms.createElement('input', {
 *   id: 'input_unique_identifier',
 *   label: 'You input description',
 *   type: 'text(your input type attribute)',
 *   attr: {any set of attributes, that will appear in your input}
 * });
 * var textarea = Forms.createElement('textarea', {
 *   id: 'input_unique_identifier',
 *   label: 'You input description',
 *   value: 'my textarea value',
 *   attr: {any set of attributes, that will appear in your input. WARN!!! value is may be specified from there, or from parent object}
 * });
 * var radio = Forms.createElement('radio', {
 *   id: 'input_unique_identifier',
 *   name: 'your radio buttons name',
 *   buttons: [
 *      {
 *        value: 'your radio button value'
 *        label: 'You input description',
 *        ..any other attributes to your radio
 *      }, ... array of same objects
 *   ]
 * });
 * var checkbox = Forms.createElement('checkbox', {
 *   id: 'input_unique_identifier',
 *   buttons: [
 *      {
 *        name: 'your checkbox buttons name',
 *        value: 'your radio button value'
 *        label: 'You input description',
 *        ..any other attributes to your radio
 *      }, ... array of same objects
 *   ]
 * });
 * var select = Forms.createElement('select', {
 *   id: 'input_unique_identifier',
 *   label: 'You input description',
 *   attr: {any set of attributes, that will appear in your select. WARN!!! value must be specified in options object!}
 *   options: [
 *      {
 *        id: 'your option value',
 *        value: 'your option inner html'
 *        selected: true
 *      },
 *   ]
 * });
 */
var Forms  = {
    createElement: function(type, options){
        if(!options)options = {};
        if(type == 'textarea') {
            return Forms.fun.buildTextarea(options);
        } else if(type == 'select') {
            return Forms.fun.buildSelect(options);
        } else if(type == 'checkbox') {
            return Forms.fun.buildCheckbox(options);
        } else if(type == 'radio') {
            return Forms.fun.buildRadio(options);
        } else {
            return Forms.fun.buildInput(type, options);
        }
    },
    fun: {
        classes: {
            wrapper: "input-wrapper clearfix",
            title: "input-title",
            body: "input-body"
        },
        buildTextarea: function(options){
            var els = [];
            if(options.label) {
                els.push(Forms.fun.buildCommonLabel(options));
            }
            var textarea = newElement('textarea', options.attr, options.value);
            var bodyWrapper = Forms.fun.buildBodyWrapper([textarea]);
            els.push(bodyWrapper);
            return Forms.fun.buildWrapper(els);
        },
        buildInput: function(type, options){
            var els = [];
            if(options.label) {
                els.push(Forms.fun.buildCommonLabel(options));
            }
            if(!options.attr)options.attr = {};
            options.attr.type = type;
            var input = newElement('input', options.attr);
            var bodyWrapper = Forms.fun.buildBodyWrapper([input]);
            els.push(bodyWrapper);
            return Forms.fun.buildWrapper(els);
        },
        buildRadio: function(options){
            return Forms.fun.buildRadioOrCheckbox(options, true);
        },
        buildCheckbox: function(options){
            return Forms.fun.buildRadioOrCheckbox(options, false);
        },
        buildSelect: function(options){
            var els = [];
            if(options.label) {
                els.push(Forms.fun.buildCommonLabel(options));
            }
            if(!options.attr)options.attr = {};
            var opts = [];
            for(var i = 0; i < options.options.length; i++) {
                var o = options.options[i];
                var oa = {value: o.id ? o.id : ''};
                if(o.selected)oa.selected = true;
                opts.pop(newElement('option', oa, o.value ? o.value : ''));
            }
            var select = newElement('select', options.attr, opts);
            var bodyWrapper = Forms.fun.buildBodyWrapper([select]);
            els.push(bodyWrapper);
            return Forms.fun.buildWrapper(els);
        },
        buildRadioOrCheckbox: function(options, isRadio){
            var name = options.name;
            var els = [];
            for(var i = 0; i < options.buttons.length; i++) {
                var b = options.buttons[i];
                b.type = isRadio ? 'radio' : 'checkbox';
                b.name = isRadio ? options.name : b.name;
                var radio = newElement('input', b);
                if(b.label) {
                    radio.removeAttribute('label');
                    var labelOpts = {label: b.label};
                    if(options.id && b.value) {
                        labelOpts['id'] = options.id + '_'+ b.value;
                        radio.setAttribute('id', options.id + '_'+ b.value);
                    }

                    var label = Forms.fun.buildCommonLabel(labelOpts);
                    if(label.childNodes.length == 0) {
                        label.appendChild(radio)
                    } else {
                        label.insertBefore(radio, label.childNodes[0])
                    }
                    els.push(label);
                } else {
                    els.push(radio)
                }
            }
            return Forms.fun.buildWrapper(els)
        },
        buildWrapper: function(els){
            return newElement('div', {'class': Forms.fun.classes.wrapper}, els);
        },
        buildBodyWrapper: function(els){
            return newElement('div', {'class': Forms.fun.classes.body}, els);
        },
        buildTitleWrapper: function(els){
            return newElement('div', {'class': Forms.fun.classes.title}, els);
        },
        buildCommonLabel: function(options){
            var label = newElement('label', {}, options.label);
            if(options.id) {
                label.setAttribute('for', options.id);
            }
            return Forms.fun.buildTitleWrapper([label]);
        }
    }
};