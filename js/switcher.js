/** Based on code made by Paul Sowden */

jQuery(document).ready(function($) {
  $('.alternate-css').click(function(e) {
    e.preventDefault();
    var href = $(this).attr('href');
    if (href.indexOf('#') == 0) {
      console.log(href.substring(1,href.length));
      ac_setss(href.substring(1,href.length));  
    }
    e.stopPropagation();
  });
});

/**
 * Set active stylesheet
 * @param string title Stylesheet title
 */
function ac_setss(title) {
    var i, a;
    for(i=0; (a = document.getElementsByTagName("link")[i]); i++) {
      if(a.getAttribute("rel").indexOf("style") != -1 && a.getAttribute("title")) {
        a.disabled = true;
        if(a.getAttribute("title") == title) a.disabled = false;
      }
    }
  }

  /**
   * Get active stylesheet's title
   */
  function ac_gas() {
    var i, a;
    for(i=0; (a = document.getElementsByTagName("link")[i]); i++) {
      if(a.getAttribute("rel").indexOf("style") != -1 && a.getAttribute("title") && !a.disabled) return a.getAttribute("title");
    }
    return null;
  }
  
  /**
   * Get preferred stylesheet
   */
  function ac_gpss() {
    var i, a;
    for(i=0; (a = document.getElementsByTagName("link")[i]); i++) {
      if(a.getAttribute("rel").indexOf("style") != -1
         && a.getAttribute("rel").indexOf("alt") == -1
         && a.getAttribute("title")
         ) return a.getAttribute("title");
    }
    return null;
  }
  
  /**
   * Create cookie
   * 
   * @param string name Cookie name
   * @param string value Cookie value
   * @param int days Days
   */
  function ac_cc(name,value,days) {
    if (days) {
      var date = new Date();
      date.setTime(date.getTime()+(days*24*60*60*1000));
      var expires = "; expires="+date.toGMTString();
    }
    else expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
  }
  
  /**
   * Read cookie
   * 
   * @param string name Cookie name
   */
  function ac_rc(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
      var c = ca[i];
      while (c.charAt(0)==' ') c = c.substring(1,c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
  }
  
  /**
   * On load
   * 
   * @param event e Event
   */
  window.onload = function(e) {
    var cookie = ac_rc("ac_title");
    var title = cookie ? cookie : ac_gpss();
    ac_setss(title);
  }
  
  /**
   * On unload
   * 
   * @param event e Event
   */
  window.onunload = function(e) {
    ac_cc("ac_title", ac_gas(), 365);
  }
  
  /**
   * Let's kickstart this...
   */
  var cookie = ac_rc("ac_title");
  var title = cookie ? cookie : ac_gpss();
  ac_setss(title);
