/**
 * <pre>
 * Class for work with json-formatted data and dom elements.
 * Data have simple format - {id: 10, name: '#user#', address: {id: 20, street: 'Lenina', home: 20}}
 * Each entity must exist as javascript with same name. To load entity, use Entity.load('user');
 *    in this case, you must have file 'js/entity/User.js', where user field must be defined in format
 *   Entity.defined.User = {
 *      id: 'id',  //<- primary key identifier
 *      fields: {
 *          id: 'integer',    //<- item type
 *          name: 'string',   //<- item type
 *          address: 'object' //<- item type
 *      }
 *  }
 *
 * dom elements must have special markup:
 *    absolute elements - span class="field" data-field="name" data-entity="user" data-id="10",
 *        this span describe absolute field for entity called 'User', with primary key = 10.
 *
 *    relative elements -
 *      at first define parent container:
 *          div class="entity" data-entity="user" data-id="10"
 *      than, define entity fields:
 *          span class="field" data-field="name" data-entity="user"
 *          or
 *          span class="field" data-field="name"
 *      if there is no data-entity attribute, there is can be conflict with inner entities
 *          (for example, if person have parent, and they both have field with name 'age'
 *
 *      methods declaration:
 *          Entity.load(type, callback);  <- load entity from js
 *          Entity.setData(type, data, callback); <- set data to each dom element, that binded to entity with same PK
 *          Entity.remove(type, data, callback); <- remove all dom elements, that binded to entity with same PK
 *          Entity.create(type, data, template, callback); <- create dom element, using template
 *
 *
 *      *** Example: ***
 *      var data = {id: 10, name: '#user#', address: {id: 20, street: 'Lenina', home: 20}}
 *      Entity.setData('user', data);
 *
 *
 * </pre>
 */


var Entity = {
    path: "js/entity/",
    load: function(type, callback){
        var s = document.createElement('script');
        s.src = Entity.path + type.ucfirst()+".js";
        s.setAttribute("type", "text/javascript");
        var loaded = false;
        var clb = function(){
            if(typeof Entity.defined[type.ucfirst()] == 'undefined')
                throw "Script was loaded, but data was not defined for type: '"+type+"'. Please use this code 'Entity.defined.YourType = {}'";
            if(loaded)return;
            loaded = true;

            if (typeof callback == 'function')
                callback();
        };
        s.onreadystatechange = function(){if(this.readyState != 'complete')return;clb();};
        s.onload = clb;
        document.head.appendChild(s);
    },
    defined: {},
    setData: function(type, data, callback){
        var proto = Entity.defined[type.ucfirst()];
        if(typeof proto == 'undefined'){
            Entity.load(type, function(){Entity.setData(type, data, callback)});
            return;
        }
        var targets = Entity.utils.getRelativeTargets(type, data, proto.id);
        if(targets.length > 0) {
            for(var i=0; i<targets.length;i++)
                Entity.utils.setRelativeEntityData(type, data, targets[i], proto);
        }
        Entity.utils.setAbsoluteEntityData(data, type, proto, 'set');
        if(typeof callback == 'function'){
            callback();
        }
    },
    remove: function(type, data, callback){
        var proto = Entity.defined[type.ucfirst()];
        if(typeof proto == 'undefined'){
            Entity.load(type, function(){Entity.remove(type, data, callback);});
            return;
        }
        var targets = Entity.utils.getRelativeTargets(type, data, proto.id);
        if(targets.length > 0) {
            for(var i=0; i<targets.length;i++)
                target.remove();
        }
        Entity.utils.setAbsoluteEntityData(data, type, proto, 'remove');
        if(typeof callback == 'function')
            callback();
    },
    create: function(type, data, template, callback){
        var ne = template.cloneNode(true);
        var proto = Entity.defined[type.ucfirst()];
        if(typeof proto == 'undefined'){
            Entity.load(type, function(){Entity.create(type, data, template, callback);});
            return;
        }
        ne.setAttribute('data-id', data[proto.id]);
        ne.setAttribute('data-entity', type.ucfirst());
        Entity.utils.setRelativeEntityData(type, data, ne, proto);
        return Entity.utils.toSmartObject(type, ne,data, proto);
    },
    utils: {
        toSmartObject: function(type, el,data, proto){
            var out = {};
            out.prototype = proto;
            for(var k in proto.fields){
                Entity.utils.addMethods(type,el,data, out, k);
            }
            out.element = el;
            return out;
        },
        setAbsoluteEntityData: function(data, type, proto, action){
            var ae = document.body.getAll('.field[data-entity="'+type.lcfirst()+'"][data-id="'+data[proto.id]+'"]');
            for(var i=0;i<ae.length;i++){
                switch(action){
                    case 'set':
                        var f = ae[i].getAttribute('data-field');
                        if(typeof data[f] != 'undefined'){
                            ae[i].innerHTML = data[f];
                        }
                        break;
                    case 'remove':
                        ae.remove();
                        break;
                }
            }
        },
        setRelativeEntityData: function(type, data, target, proto){
            var objects = {};
            for(var i in data){
                if(proto.fields[i] == 'object'){
                    if(typeof objects[i] == 'undefined')objects[i] = [];
                    objects[i].push(data[i]);
                }else{
                    var fields = Entity.utils.getRelativeFields(target, i, type);
                    if(fields.length > 0)
                        for(var j=0; j<fields.length;j++)
                            fields[j].innerHTML = data[i];
                }
            }
            for(var type in objects){
                for(var i=0;i<objects[type].length;i++)
                    Entity.setData(type, objects[type][i]);
            }
        },
        getRelativeFields: function(target, field, type){
            var o = [].slice.call(target.getAll('.field[data-field="'+field+'"]'));
            for(var i= o.length-1;i>=0;i--) {
                if (o[i].getAttribute('data-entity') && o[i].getAttribute('data-entity') != type){
                    o.splice(i, 1);
                }
            }
            return o;
        },
        getRelativeTargets: function(type, data, key){
            return document.body.getAll('.entity[data-entity="'+type.lcfirst()+'"][data-id="'+data[key]+'"]');
        },
        addMethods: function(type, el, data, out, k){
            var setter = "set"+ k.ucfirst();
            var getter = "get"+ k.ucfirst();
            var els = Entity.utils.getRelativeFields(el, k, type)
            out[getter] = function(){return out.data[k]};
            out[setter] = function(val){
                out.data[k] = val;
                for(var i = 0; i< els.length; i++)
                    els[i].innerHTML = val;
            }
            if(!out.data)out.data={};
            if(data[k])
                out.data[k] = data[k];
            else
                out.data[k] = null;
            out[setter](out.data[k]);
        }
    }
};