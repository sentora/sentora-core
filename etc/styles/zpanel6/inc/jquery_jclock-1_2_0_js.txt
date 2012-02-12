/*
 * jQuery jclock - Clock plugin - v 1.2.0
 * http://plugins.jquery.com/project/jclock
 *
 * Copyright (c) 2007-2008 Doug Sparling <http://www.dougsparling.com>
 * Licensed under the MIT License:
 *   http://www.opensource.org/licenses/mit-license.php
 */
(function($) {

  $.fn.jclock = function(options) {
    var version = '1.2.0';

    // options
    var opts = $.extend({}, $.fn.jclock.defaults, options);
         
    return this.each(function() {
      $this = $(this);
      $this.timerID = null;
      $this.running = false;

      var o = $.meta ? $.extend({}, opts, $this.data()) : opts;

      $this.timeNotation = o.timeNotation;
      $this.am_pm = o.am_pm;
      $this.utc = o.utc;
      $this.utc_offset = o.utc_offset;

      $this.css({
        fontFamily: o.fontFamily,
        fontSize: o.fontSize,
        backgroundColor: o.background,
        color: o.foreground
      });

      $.fn.jclock.startClock($this);

    });
  };
       
  $.fn.jclock.startClock = function(el) {
    $.fn.jclock.stopClock(el);
    $.fn.jclock.displayTime(el);
  }
  $.fn.jclock.stopClock = function(el) {
    if(el.running) {
      clearTimeout(el.timerID);
    }
    el.running = false;
  }
  $.fn.jclock.displayTime = function(el) {
    var time = $.fn.jclock.getTime(el);
    el.html(time);
    el.timerID = setTimeout(function(){$.fn.jclock.displayTime(el)},1000);
  }
  $.fn.jclock.getTime = function(el) {
    var now = new Date();
    var hours, minutes, seconds;

    if(el.utc == true) {
      var localTime = now.getTime();
      var localOffset = now.getTimezoneOffset() * 60000;
      var utc = localTime + localOffset;
      var utcTime = utc + (3600000 * el.utc_offset);
      now = new Date(utcTime);
    }
    hours = now.getHours();
    minutes = now.getMinutes();
    seconds = now.getSeconds();

    var am_pm_text = '';
    (hours >= 12) ? am_pm_text = " P.M." : am_pm_text = " A.M.";

    if (el.timeNotation == '12h') {
      hours = ((hours > 12) ? hours - 12 : hours);
    } else if (el.timeNotation == '12hh') {
      hours = ((hours > 12) ? hours - 12 : hours);
      hours   = ((hours <  10) ? "0" : "") + hours;
    } else {
      hours   = ((hours <  10) ? "0" : "") + hours;
    }

    minutes = ((minutes <  10) ? "0" : "") + minutes;
    seconds = ((seconds <  10) ? "0" : "") + seconds;

    var timeNow = hours + ":" + minutes + ":" + seconds;
    if ( (el.timeNotation == '12h' || el.timeNotation == '12hh') && (el.am_pm == true) ) {
     timeNow += am_pm_text;
    }

    return timeNow;
  };
       
  // plugin defaults
  $.fn.jclock.defaults = {
    timeNotation: '24h',
    am_pm: false,
    utc: false,
    fontFamily: '',
    fontSize: '',
    foreground: '',
    background: '',
    utc_offset: 0
  };

})(jQuery);
