/**
* Default DragManager model must be extended for:
* model.dropStore = document.getElementsByClassName('droppTarget')//Required. Dom node list, where this object can be putted
* model.opacity = 0 ... 1//Not reqired, int value;
* @see 'DragManager' doc
*/
DragManager.agentClasses.Droppable = function(){
	DragManager.agentClasses.Simple.apply(this, arguments);
}

with(DragManager.agentClasses){
	Droppable.prototype = Object.create(Simple.prototype);
	Droppable.prototype.getDropClass = function(){
		return 'drop-target';
	}
	Droppable.prototype.getHoverClass = function(){
		return 'drop-target-hover';
	}
	Droppable.prototype.dropZoneStore = [];
	
	Droppable.prototype.onGrab = function(o){
		DragManager.agentClasses.Simple.prototype.onGrab.apply(this, [o]);
		o._.ret = {x:o._.el.x,y:o._.el.y};
		this.setOpacity(o, o.target, 0.5, true);
		this.dropZoneStore = [];
		for(var i = 0;i<o.dropStore.length;i++){
			o.dropStore[i].addClass(this.getDropClass());
			this.dropZoneStore.push(this.getDropZoneInfo(o.dropStore[i]));
		}
	};
	Droppable.prototype.render = function(o){
		DragManager.agentClasses.Simple.prototype.render.apply(this, [o]);
		for(var i = 0; i < this.dropZoneStore.length; i++){
			var z = this.dropZoneStore[i];
			if(this.hover(o._, z)){
				if(!z.used) {
					z.used = true;
					z.el.addClass(this.getHoverClass());
				}
			} else {
				if(z.used){
					z.used = false;
					z.el.removeClass(this.getHoverClass());
				}
			}
		}
	};
	Droppable.prototype.onDrop = function(o){
		DragManager.agentClasses.Simple.prototype.onDrop.apply(this, [o]);
		var pass = false;
		if(o.dropStore){
			var d = this.dropZoneStore;
			for(var i=0;i<d.length;i++){
				if(this.hover(o._, d[i])){
					pass = true;
					break;
				};
			}
		}
		if(!pass){
			o.target.style.left=o._.ret.x + "px";
			o.target.style.top=o._.ret.y + "px";
		}
		this.setOpacity(o, o.target, 1, false);
		for(var i = 0;i<o.dropStore.length;i++)o.dropStore[i].removeClass(this.getDropClass());
		for(var i = 0;i<this.dropZoneStore.length;i++)this.dropZoneStore[i].el.removeClass(this.getHoverClass());
	};
	
	Droppable.prototype.hover = function(p, z){
		return z.x1 - p.sc.x <= p.mc.x && z.x2 - p.sc.x > p.mc.x && z.y1- p.sc.y <= p.mc.y && z.y2- p.sc.y > p.mc.y;
	};
	Droppable.prototype.setOpacity = function(o, t, v, s){
		if(s && o.defOpacity !== 0 && !o.defOpacity ) {
			o.defOpacity = t.style.opacity;
			if(!o.defOpacity)o.defOpacity=getComputedStyle(o.target)['opacity'];
		}
		var op;
		if(!s)op = o.defOpacity === 0 || o.defOpacity ? o.defOpacity : v;
		else op = o.opacity === 0 || o.opacity? o.opacity: v;
		t.style.opacity = op;
	};
	Droppable.prototype.getDropZoneInfo = function(el){
		var r = DragManager.getLeftTop(el);
		return {
			el: el,
			x1: r.left,
			x2: r.left + el.offsetWidth,
			y1: r.top,
			y2: r.top + el.offsetHeight,
			used: false
		}
	}
}
DragManager.agents.droppable = new DragManager.agentClasses.Droppable();
