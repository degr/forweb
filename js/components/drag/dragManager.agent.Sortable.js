
/**
* Default DragManager model must be extended for:
* model.dropStore = document.getElementsByClassName('droppTarget')//Required. Dom node list, where this object can be putted
* model.opacity = 0 ... 1//Not reqired, int value;
* @see 'DragManager' doc
*/
DragManager.agentClasses.Sortable = function(){
	DragManager.agentClasses.Droppable.apply(this, arguments);
	this.clone = null;
	this.axis = [];
	this.index = -1;
	this.nodes = [];
}

with(DragManager.agentClasses){
	Sortable.prototype = Object.create(Droppable.prototype);
	Sortable.prototype.getDropClass = function(){
		return 'sortable-target';
	}
	Sortable.prototype.getHoverClass = function(){
		return 'sortable-target-hover';
	}
	Sortable.prototype.onGrab = function(o){
		this.clone = o.target.cloneNode( true );
		this.clone.className += 'sortable-clone';
		this.setOpacity(o, this.clone, 0.5, true);
		o.target.parentNode.insertBefore(this.clone, o.target);
		o.target.parentNode.appendChild(o.target);
		var nA = o.target.parentNode.childNodes;
		for(var i = 0; i < nA.length; i++){
			var n = nA[i];
			if(n.nodeType === 3)continue;
			if(o.clone === o.target.parentNode.childNodes[i]){
				this.index = i;
				break;
			}
		}
		if(!o.target.style.width){
			o.target.style.width = o.target.offsetWidth + 'px';
			o.settedWidth = true;
		}
		o.target.style.position = 'absolute';
		this.calculateAxisLines(o);
		DragManager.agentClasses.Droppable.prototype.onGrab.apply(this, [o]);
	};
	Sortable.prototype.render = function(o){
		var n = this.nodes;
		var shift = o.oX ? o._.sc.x : o._.sc.y;
		var mousePos = o._.mc[o.oX === 1 ? 'x' : 'y'] + shift;
		
		var before = null, clonePos = -1;
		for(var i = 0; i < this.axis.length;i++){
			if(n[i] === this.clone)clonePos = i;
			if(mousePos < this.axis[i] && (before == null || before > i)){
				before = i;
				continue;
			}
		}
		var elBefore = before === null ? null : n[before];
		var moved = false;
		
		if(elBefore && elBefore !== this.clone){
			elBefore.parentNode.insertBefore(this.clone, elBefore);
			n.splice(before, 0, n.splice(clonePos, 1)[0]);
		}
		if(!elBefore && this.clone !== n[n.lenght - 1]){
			this.clone.parentNode.appendChild(this.clone);
			n.splice(n.length - 1, 0, n.splice(clonePos, 1)[0]);
		}
		DragManager.agentClasses.Droppable.prototype.render.apply(this, [o]);
	};
	Sortable.prototype.getNext = function(el){
		var o = el.nextSibling;
		return o ? (o.nodeType !== 3 ? o : this.getNext(o)) : null;
	}
	Sortable.prototype.onDrop = function(o){
		if(this.clone !== null) {
			this.clone.parentNode.insertBefore(o.target, this.clone);
			this.index = -1;
			o.target.style.opacity = 1;
			this.clone.remove();
			this.clone = null;
		}
		var me = this;
		with(o.target.style) {
			position = 'static';
			setTimeout(function(){
				me.fixPositions(o);
			}, 30);
		}
		if(o.settedWidth){
			o.target.style.width = null;
		}
		DragManager.agentClasses.Simple.prototype.onDrop.apply(this, [o]);
	};
	Sortable.prototype.fixPositions = function(o) {
		var p = o.target.parentNode;
		for(var i = 0; i < p.childNodes.length;i++){
			var el = p.childNodes[i];
			if(el.nodeType === 3)continue;
			var r = DragManager.getLeftTop(el);
			el.style.left = r.left + 'px';
			el.style.top = r.top + 'px';
		}
	};
	Sortable.prototype.calculateAxisLines = function(o){
		this.axis = [];
		this.nodes = [];
		var isX = o.oX === 1;
		var p = o.target.parentNode;
		var m = isX ? 'offsetLeft' : 'offsetTop';
		var a = isX ? 'offsetWidth' : 'offsetHeight';
		//last element is a draggable element
		for(var i = 0; i < p.childNodes.length - 1;i++){
			var el = p.childNodes[i];
			if(el.nodeType === 3)continue;
			this.nodes.push(el);
			var v = DragManager.getLeftTop(el, isX, !isX, true)[isX?'left':'top'] + el[a]/2 ;
			this.axis.push(v);
		}
	}
}
DragManager.agents.sortable = new DragManager.agentClasses.Sortable();
