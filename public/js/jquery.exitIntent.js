(function($){
    $.exitIntent = function(el, callback, options){
        var base = this;
        base.delayTimer = null;
        base.$el = $(el);
        base.el = el;
        base.disabled = false;
        
        base.$el.data("exitIntent", base);
        
        base.init = function(){
            base.options = $.extend({},$.exitIntent.defaultOptions, options);
            
            base.$el.mouseleave(function(e) {
                if (e.clientY > 0 || Math.abs(e.clientY) < base.options.minexitspeed || (base.disabled && !base.options.repeat)) return;
                
                base.delayTimer = setTimeout(base.runCallback, base.options.delay);
            }).mouseenter(function(event) {
                if (base.delayTimer) {
                  clearTimeout(base.delayTimer);
                  base.delayTimer = null;
                }
            });
            if (base.options.keyboard) {
                base.$el.keydown(function(e) {
                    if (base.disabled && !base.options.repeat) return;
                    else if (e.keyCode !== 8 && (!e.metaKey || e.keyCode !== 76)) return;
                    
                    base.runCallback();
                });
            }
        };
        base.runCallback = function() {
            if (typeof callback == 'function') {
                callback.call(this);
            }
            base.disabled = true;
        };
        
        base.init();
    };
    
    $.exitIntent.defaultOptions = {
        minexitspeed: 0,
        delay: 0,
        repeat: false,
        keyboard: true // capture ctrl + L
    };
    
    $.fn.exitIntent = function(callback, options){
        return this.each(function(){
            (new $.exitIntent(this, callback, options));
        });
    };
    
})(jQuery);
