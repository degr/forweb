/**
 * Created by rsmirnou on 7/8/2015.
 */
var Autocode = {
    loadedComponents: [],
    components: {},
    start: function(){
        Autocode.processAutocomplete();
        console.log('invoked');
    },
    /**
     * make autocomplete inputs
     */
    processAutocomplete: function(){

        if(this.isComponentLoaded('autocomplete')) {
            var ac = document.body.getAll('input[data-component="autocomplete"]');
            if(!this.components['autocomplete'])this.components['autocomplete'] = [];
            for(var i = 0; i < ac.length; i++) {
                var clb = this.generateAutocompleteHandler(ac[i]);
                this.components['autocomplete'].push(new Autocomplete(ac[i], clb));
            }
        } else {
            Core.loadScript(Core.url + 'js/components/Autocomplete.js', function(){
                Autocode.loadedComponents.push('autocomplete');
                Autocode.processAutocomplete();
            });
        }
    },
    isComponentLoaded: function(component) {
        return this.loadedComponents.indexOf(component) > -1;
    },
    generateAutocompleteHandler: function(el){
        var f = el.getAttribute('data-handler');
        if(f) {
            var args = f.split('.');
            return this.buildFunctionByArgs(args, 0, null);
        } else {
            console.log("build api func");
            return function(search, ac){
                console.log(ac.requestTime,new Date().getTime() - ac.requestTime );
                if(ac.requestTime) {
                    if(new Date().getTime() - ac.requestTime < 1000) {
                        console.log('prevented');
                        return;
                    }
                }
                ac.requestTime = new Date().getTime();

                var t = ac.element.getAttribute('data-table');
                if(!t || !ac.element.name) {
                    return null;
                }
                console.log(search);
                Ajax.request({
                    url: Core.url + "api/"+t+"/getColumn",
                    type: "post",
                    success: function(r){
                        var d = [];
                        for(var i = 0; i < r.length; i++) {
                            d.push({id: r[i], value: r[i]})
                        }
                        ac.setData(d);
                        ac.showHint(search);
                    },
                    response: 'json',
                    data: {field: el.name, filter: JSON.stringify([{field: el.name, type:'filter', value: search+'%',
                        comparation: 'like', fieldtType: 'string'}])}
                });
            }
        }
    },
    buildFunctionByArgs: function(args, i, object){
        if(!object)object = window;
        if(i == args.length - 1) {
            return typeof object[args[i]] == 'function' ? object[args[i]] : null;
        } else {
            return !object[i] ? null : this.buildFunctionByArgs(args, i+1, object[i]);
        }
    }
};
window.addEvent('load', Autocode.start);