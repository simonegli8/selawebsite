(function(a){"function"===typeof define&&define.amd?define(["jquery","./effect"],a):a(jQuery)})(function(a){return a.effects.effect.fade=function(b,d){var c=a(this),e=a.effects.setMode(c,b.mode||"toggle");c.animate({opacity:e},{queue:!1,duration:b.duration,easing:b.easing,complete:d})}});