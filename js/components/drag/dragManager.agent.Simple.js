/**
var model = {
	key: document.getElementById('node1'),//Required. This node will init key.
	target: document.getElementById('node2'),//Not required. This node will be dragged, when you click key node. If null, key will be used
	oX:false,//Not required. if you no need drag on oX axis. Only 'false'(bool) valid value. All other will be used as true.
	oY:false,//Not required. if you no need drag on oY axis. Only 'false'(bool) valid value. All other will be used as true.
	agent: 'drag'//Requred. Define draggable type. Use this for all defined agents: Object.keys(DragManager.agents)
};
DragManager.consume(model);
*/
DragManager.agentClasses.Simple = function(){};
with(DragManager.agentClasses){
	Simple.prototype.onGrab = function(o){
		o.target.addClass('no-selection');
	};
	Simple.prototype.render = function(o){
		var t = o.target;
		with(DragManager) {
			if(o.oX===1)t.style.left=(calculate(o, 'x'))+'px';
			if(o.oY===1)t.style.top=(calculate(o, 'y'))+'px';
		}
	};
	Simple.prototype.onDrop = function(o){
		o.target.removeClass('no-selection');
	};
}
DragManager.agents.simple = new DragManager.agentClasses.Simple();