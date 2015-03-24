String.prototype.lcfirst = function(){
    return this.charAt(0).toLowerCase() + this.substring(1, this.length);
}
String.prototype.ucfirst = function(){
    return this.charAt(0).toUpperCase() + this.substring(1, this.length);
}